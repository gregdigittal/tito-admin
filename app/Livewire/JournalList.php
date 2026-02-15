<?php

namespace App\Livewire;

use App\Services\TitoGraphQLService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class JournalList extends Component
{
    use WithPagination;

    public string $journalStatus = 'PENDING'; // PENDING | ACCEPTED | REJECTED
    public int $days = 30;

    public function acceptJournal(int $journalId): void
    {
        $query = <<<'GQL'
mutation AcceptJournal($journalId: ID!) {
  acceptJournal(journalId: $journalId) {
    id
    status
  }
}
GQL;
        app(TitoGraphQLService::class)->query($query, ['journalId' => (string) $journalId]);
        $this->dispatch('journal-updated');
    }

    public function rejectJournal(int $journalId): void
    {
        $query = <<<'GQL'
mutation RejectJournal($journalId: ID!) {
  rejectJournal(journalId: $journalId) {
    id
    status
  }
}
GQL;
        app(TitoGraphQLService::class)->query($query, ['journalId' => (string) $journalId]);
        $this->dispatch('journal-updated');
    }

    public function queryJournals(): array
    {
        $query = <<<'GQL'
query Journals($journalStatus: JournalStatus!, $days: Int!, $page: Int, $size: Int) {
  journals(journalStatus: $journalStatus, days: $days, page: $page, size: 15) {
    total
    content {
      id
      status
      amount
      drAccountId
      crAccountId
      transactionCodeId
      details
      notes
      createdDate
    }
  }
}
GQL;
        $result = app(TitoGraphQLService::class)->query($query, [
            'journalStatus' => $this->journalStatus,
            'days' => $this->days,
            'page' => $this->getPage() - 1,
            'size' => 15,
        ]);

        $errors = $result['errors'] ?? [];
        if (!empty($errors)) {
            return ['total' => 0, 'content' => [], 'error' => $errors[0]['message'] ?? 'Failed to load journals'];
        }
        $pageable = $result['data']['journals'] ?? [];
        return [
            'total' => $pageable['total'] ?? 0,
            'content' => $pageable['content'] ?? [],
            'error' => null,
        ];
    }

    public function render()
    {
        $data = $this->queryJournals();
        return view('livewire.journal-list', [
            'journals' => $data['content'],
            'total' => $data['total'],
            'loadError' => $data['error'],
        ]);
    }
}
