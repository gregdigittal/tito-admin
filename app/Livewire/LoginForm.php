<?php

namespace App\Livewire;

use App\Services\TitoGraphQLService;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class LoginForm extends Component
{
    public string $username = '';
    public string $password = '';
    public string $mfaCode = '';
    public string $error = '';
    public string $step = 'login'; // 'login' | 'mfa'
    public ?string $mfaUsername = null;

    public function mount(): void
    {
        if (Session::has('tito_access_token')) {
            redirect()->route('dashboard');
        }
    }

    public function submitLogin(): void
    {
        $this->error = '';
        $this->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $query = <<<'GQL'
mutation LoginStaffMember($request: LoginUserRequest!) {
  loginStaffMember(request: $request) {
    status
    mfaType
    msisdn
    accessToken { token refreshToken expiresIn }
  }
}
GQL;
        $service = app(TitoGraphQLService::class);
        $result = $service->query($query, [
            'request' => ['username' => $this->username, 'password' => $this->password],
        ], false);

        $errors = $result['errors'] ?? [];
        if (!empty($errors)) {
            $this->error = $errors[0]['message'] ?? 'Login failed';
            return;
        }

        $data = $result['data']['loginStaffMember'] ?? null;
        if (!$data) {
            $this->error = 'Invalid response from server';
            return;
        }

        $status = $data['status'] ?? '';
        if ($status === 'MFA_REQUIRED') {
            $this->step = 'mfa';
            $this->mfaUsername = $this->username;
            return;
        }

        $accessToken = $data['accessToken'] ?? null;
        if ($accessToken && !empty($accessToken['token'])) {
            $service->storeTokens(
                $accessToken['token'],
                $accessToken['refreshToken'] ?? '',
                $accessToken['expiresIn'] ?? null
            );
            redirect()->route('dashboard');
            return;
        }

        $this->error = 'No token received';
    }

    public function submitMfa(): void
    {
        $this->error = '';
        $this->validate(['mfaCode' => 'required|string|min:4']);

        $query = <<<'GQL'
mutation EnterLoginMfaCode($mfaRequest: LoginMfaRequest!) {
  enterLoginMfaCode(mfaRequest: $mfaRequest) {
    status
    accessToken { token refreshToken expiresIn }
  }
}
GQL;
        $service = app(TitoGraphQLService::class);
        $result = $service->query($query, [
            'mfaRequest' => ['username' => $this->mfaUsername ?? $this->username, 'code' => $this->mfaCode],
        ], false);

        $errors = $result['errors'] ?? [];
        if (!empty($errors)) {
            $this->error = $errors[0]['message'] ?? 'MFA failed';
            return;
        }

        $data = $result['data']['enterLoginMfaCode'] ?? null;
        $accessToken = $data['accessToken'] ?? null;
        if ($accessToken && !empty($accessToken['token'])) {
            $service->storeTokens(
                $accessToken['token'],
                $accessToken['refreshToken'] ?? '',
                $accessToken['expiresIn'] ?? null
            );
            redirect()->route('dashboard');
            return;
        }

        $this->error = 'MFA verification failed';
    }

    public function render()
    {
        return view('livewire.login-form');
    }
}
