<?php

namespace App\Livewire;

use App\Services\TitoGraphQLService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class StaffCreateForm extends Component
{
    public string $email = '';
    public string $firstName = '';
    public string $lastName = '';
    public string $idNumber = '';
    public string $idNumberType = '1';
    public string $msisdn = '';
    public string $department = '';
    public string $securityGroupId = '1';
    public string $locale = 'en';
    public bool $sendEmail = true;
    public string $error = '';
    public string $success = '';

    public function submit(): void
    {
        $this->error = '';
        $this->success = '';
        $this->validate([
            'email' => 'required|email',
            'firstName' => 'required|string',
            'lastName' => 'nullable|string',
            'idNumber' => 'required|string',
            'idNumberType' => 'required|string',
            'msisdn' => 'required|string',
            'department' => 'required|string',
            'securityGroupId' => 'required|string',
        ]);

        $query = <<<'GQL'
mutation NewStaffMember($staffMember: NewStaffMemberInput!, $sendEmail: Boolean!) {
  newStaffMember(staffMember: $staffMember, sendEmail: $sendEmail) {
    id
    email
    firstName
    lastName
  }
}
GQL;
        $result = app(TitoGraphQLService::class)->query($query, [
            'staffMember' => [
                'email' => $this->email,
                'firstName' => $this->firstName,
                'lastName' => $this->lastName ?: null,
                'idNumber' => $this->idNumber,
                'idNumberType' => (int) $this->idNumberType,
                'msisdn' => $this->msisdn,
                'department' => $this->department,
                'securityGroupId' => (int) $this->securityGroupId,
                'locale' => $this->locale,
            ],
            'sendEmail' => $this->sendEmail,
        ]);

        $errors = $result['errors'] ?? [];
        if (!empty($errors)) {
            $this->error = $errors[0]['message'] ?? 'Create failed';
            return;
        }
        $this->success = 'Staff member created.';
        $this->reset(['email', 'firstName', 'lastName', 'idNumber', 'msisdn', 'department']);
    }

    public function render()
    {
        return view('livewire.staff-create-form');
    }
}
