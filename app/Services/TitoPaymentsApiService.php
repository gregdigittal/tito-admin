<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

/**
 * Phase 8-8: REST client for j-payments deployment config, EOD trigger, and reconciliation read.
 * Uses TITO_API_URL as base (e.g. https://tito-api.onrender.com).
 */
class TitoPaymentsApiService
{
    public function __construct(
        protected string $baseUrl,
        protected int $timeout = 30
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            config('tito.api_url'),
            (int) config('tito.timeout', 30)
        );
    }

    /** GET /api/v1/config/deployment — no auth. */
    public function getDeploymentConfig(): array
    {
        $response = $this->request('get', '/api/v1/config/deployment');
        $response->throw();
        return $response->json();
    }

    /** POST /api/v1/eod/run?date=YYYY-MM-DD — optional staff auth when j-payments enforces it. */
    public function triggerEodRun(?string $date = null): array
    {
        $url = '/api/v1/eod/run';
        if ($date !== null && $date !== '') {
            $url .= '?date=' . urlencode($date);
        }
        $response = $this->request('post', $url);
        $response->throw();
        return $response->json();
    }

    /** GET /api/v1/reconciliation?date=YYYY-MM-DD — optional staff auth. */
    public function getReconciliationRuns(?string $date = null): array
    {
        $url = '/api/v1/reconciliation';
        if ($date !== null && $date !== '') {
            $url .= '?date=' . urlencode($date);
        }
        $response = $this->request('get', $url);
        $response->throw();
        return $response->json();
    }

    protected function request(string $method, string $path): \Illuminate\Http\Client\Response
    {
        $url = rtrim($this->baseUrl, '/') . $path;
        $headers = ['Accept' => 'application/json'];
        $token = Session::get('tito_access_token');
        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }
        return Http::timeout($this->timeout)
            ->withHeaders($headers)
            ->{strtolower($method)}($url);
    }
}
