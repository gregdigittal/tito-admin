<?php

namespace App\Livewire;

use App\Services\TitoGraphQLService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class StaffList extends Component
{
    use WithPagination;

    public string $searchText = '';
    public string $statusFilter = '';

    public function queryStaff(): array
    {
        $query = <<<'GQL'
query SearchStaffMembers($searchText: String, $status: LoginStatus, $page: Int, $size: Int) {
  searchStaffMembers(searchText: $searchText, status: $status, page: $page, size: 15) {
    id
    email
    firstName
    lastName
    msisdn
    loginStatus
    securityGroupId
    department
    createdDate
  }
}
GQL;
        $status = $this->statusFilter ?: null;
        $result = app(TitoGraphQLService::class)->query($query, [
            'searchText' => $this->searchText ?: null,
            'status' => $status,
            'page' => $this->getPage() - 1,
            'size' => 15,
        ]);

        $errors = $result['errors'] ?? [];
        if (!empty($errors)) {
            return ['members' => [], 'error' => $errors[0]['message'] ?? 'Failed to load staff'];
        }
        $members = $result['data']['searchStaffMembers'] ?? [];
        return ['members' => $members, 'error' => null];
    }

    public function render()
    {
        $data = $this->queryStaff();
        return view('livewire.staff-list', [
            'members' => $data['members'],
            'loadError' => $data['error'],
        ]);
    }
}
