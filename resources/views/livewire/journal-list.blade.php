<div>
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">Journals</h1>

    @if ($loadError)
        <p class="text-red-600 mb-4">{{ $loadError }}</p>
    @endif

    <div class="mb-4 flex gap-4 items-center">
        <span class="text-sm text-gray-600">Status:</span>
        <select wire:model.live="journalStatus" class="rounded-md border border-gray-300 shadow-sm px-3 py-2">
            <option value="PENDING">Pending</option>
            <option value="ACCEPTED">Accepted</option>
            <option value="REJECTED">Rejected</option>
        </select>
        <span class="text-sm text-gray-600">Days:</span>
        <input type="number" wire:model.live="days" min="1" max="365" class="rounded-md border border-gray-300 shadow-sm px-3 py-2 w-20">
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Details</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                    @if ($journalStatus === 'PENDING')
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($journals as $j)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $j['id'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $j['status'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $j['amount'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ Str::limit($j['details'] ?? '-', 40) }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $j['createdDate'] ?? '-' }}</td>
                        @if ($journalStatus === 'PENDING' && isset($j['id']))
                            <td class="px-4 py-2">
                                <button wire:click="acceptJournal({{ $j['id'] }})" class="text-green-600 hover:text-green-800 text-sm mr-2">Accept</button>
                                <button wire:click="rejectJournal({{ $j['id'] }})" class="text-red-600 hover:text-red-800 text-sm">Reject</button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $journalStatus === 'PENDING' ? 6 : 5 }}" class="px-4 py-6 text-center text-gray-500">No journals found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-2 border-t border-gray-200">
            {{ $journals ? $this->links() : '' }}
        </div>
    </div>
</div>
