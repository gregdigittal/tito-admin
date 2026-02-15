<div>
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">Staff</h1>

    @if ($loadError)
        <p class="text-red-600 mb-4">{{ $loadError }}</p>
    @endif

    <div class="mb-4 flex gap-4">
        <input type="text" wire:model.live.debounce.300ms="searchText" placeholder="Search..." class="rounded-md border border-gray-300 shadow-sm px-3 py-2">
        <select wire:model.live="statusFilter" class="rounded-md border border-gray-300 shadow-sm px-3 py-2">
            <option value="">All statuses</option>
            <option value="ACTIVE">Active</option>
            <option value="INACTIVE">Inactive</option>
        </select>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">MSISDN</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($members as $m)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $m['email'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ trim(($m['firstName'] ?? '') . ' ' . ($m['lastName'] ?? '')) ?: '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $m['msisdn'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $m['loginStatus'] ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $m['department'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">No staff members found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-2 border-t border-gray-200">
            {{ $members ? $this->links() : '' }}
        </div>
    </div>
</div>
