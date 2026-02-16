<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class TitoGraphQLService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('tito.api_url'),
            'connect_timeout' => 10,
            'timeout' => 60,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Run a GraphQL operation (query or mutation). Adds Bearer token from session when present.
     * On 401, attempts refresh then retries once.
     *
     * @param string $query GraphQL document (e.g. "mutation LoginStaffMember(...) { loginStaffMember(request: $request) { ... } }")
     * @param array<string, mixed> $variables
     * @param bool $useToken If false, do not attach token (for login/refresh/MFA)
     * @return array{ data?: array, errors?: array }
     */
    public function query(string $query, array $variables = [], bool $useToken = true): array
    {
        $token = $useToken ? Session::get('tito_access_token') : null;
        $response = $this->post($query, $variables, $token);

        if ($response['status'] === 401 && $useToken && Session::has('tito_refresh_token')) {
            if ($this->refreshToken()) {
                $token = Session::get('tito_access_token');
                $response = $this->post($query, $variables, $token);
            } else {
                $this->clearTokens();
                throw new \Illuminate\Auth\AuthenticationException('Session expired. Please log in again.');
            }
        }

        return $response['body'] ?? ['errors' => [['message' => 'No response from API']]];
    }

    /**
     * POST to GraphQL endpoint.
     *
     * @param string $query
     * @param array<string, mixed> $variables
     * @param string|null $bearerToken
     * @return array{ status: int, body: array }
     */
    protected function post(string $query, array $variables, ?string $bearerToken): array
    {
        $headers = [];
        if ($bearerToken) {
            $headers['Authorization'] = 'Bearer ' . $bearerToken;
        }

        try {
            $res = $this->client->post(config('tito.graphql_endpoint'), [
                'headers' => $headers,
                'json' => [
                    'query' => $query,
                    'variables' => $variables,
                ],
            ]);
        } catch (GuzzleException $e) {
            Log::warning('Tito GraphQL request failed', ['message' => $e->getMessage()]);
            $code = 500;
            $body = ['errors' => [['message' => $e->getMessage()]]];
            if ($e instanceof RequestException && $e->hasResponse()) {
                $code = $e->getResponse()->getStatusCode();
                $decoded = json_decode($e->getResponse()->getBody()->getContents(), true);
                if (is_array($decoded)) {
                    $body = $decoded;
                }
            }
            return ['status' => $code, 'body' => $body];
        }

        $body = json_decode($res->getBody()->getContents(), true) ?? [];
        return ['status' => $res->getStatusCode(), 'body' => $body];
    }

    /**
     * Refresh access token using session refresh token. Updates session on success.
     */
    public function refreshToken(): bool
    {
        $refreshToken = Session::get('tito_refresh_token');
        if (!$refreshToken) {
            return false;
        }

        $query = <<<'GQL'
mutation RefreshStaffMemberAccessToken($refreshToken: String!) {
  refreshStaffMemberAccessToken(refreshToken: $refreshToken) {
    status
    accessToken { token refreshToken expiresIn }
  }
}
GQL;
        $response = $this->post($query, ['refreshToken' => $refreshToken], null);
        $body = $response['body'] ?? [];
        $data = $body['data']['refreshStaffMemberAccessToken'] ?? null;
        if ($data && !empty($data['accessToken']['token'])) {
            $token = $data['accessToken']['token'];
            Session::put('tito_access_token', $token);
            if (!empty($data['accessToken']['refreshToken'])) {
                Session::put('tito_refresh_token', $data['accessToken']['refreshToken']);
            }
            $this->storeRolesFromToken($token);
            return true;
        }
        Session::forget(['tito_access_token', 'tito_refresh_token']);
        return false;
    }

    /**
     * Store tokens in session after login. Decodes JWT to extract realm roles for RBAC.
     */
    public function storeTokens(string $accessToken, string $refreshToken, ?int $expiresIn = null): void
    {
        Session::put('tito_access_token', $accessToken);
        Session::put('tito_refresh_token', $refreshToken);
        if ($expiresIn !== null) {
            Session::put('tito_token_expires_in', $expiresIn);
        }
        $this->storeRolesFromToken($accessToken);
    }

    /**
     * Decode JWT and store realm roles in session for RBAC.
     */
    protected function storeRolesFromToken(string $accessToken): void
    {
        $parts = explode('.', $accessToken);
        if (count($parts) >= 2) {
            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
            $roles = is_array($payload) ? ($payload['realm_access']['roles'] ?? []) : [];
            Session::put('tito_staff_roles', $roles);
        }
    }

    /**
     * Clear tokens (logout).
     */
    public function clearTokens(): void
    {
        Session::forget(['tito_access_token', 'tito_refresh_token', 'tito_token_expires_in', 'tito_staff_roles']);
    }
}
