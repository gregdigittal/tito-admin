<x-app-layout title="Settlement / Sweep">
    <h1 class="mb-4 text-2xl font-semibold text-gray-900 dark:text-white">Settlement &amp; Sweep</h1>
    <p class="mb-4 text-gray-600 dark:text-gray-400">EOD settlement runs daily (settlement → fee → sweep). You can trigger a run manually and view reconciliation results below.</p>

    @if(session('message'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-200">
            {{ session('message') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-8 rounded-lg border border-gray-200 bg-white p-4 shadow dark:border-gray-700 dark:bg-gray-800">
        <h2 class="mb-3 text-lg font-medium text-gray-900 dark:text-white">Run EOD</h2>
        <form action="{{ route('config.settlement.eod-run') }}" method="post" class="flex flex-wrap items-end gap-4">
            @csrf
            <div>
                <label for="eod-date" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Business date (optional, default: yesterday)</label>
                <input type="date" id="eod-date" name="date" value="{{ old('date') }}"
                    class="rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
            </div>
            <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                Run EOD
            </button>
        </form>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-gray-800">
        <h2 class="border-b border-gray-200 px-4 py-3 text-lg font-medium text-gray-900 dark:border-gray-700 dark:text-white">Reconciliation runs</h2>
        <form method="get" action="{{ route('config.settlement') }}" class="flex flex-wrap items-end gap-4 border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <div>
                <label for="filter-date" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Filter by date (optional)</label>
                <input type="date" id="filter-date" name="date" value="{{ $filterDate }}"
                    class="rounded border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
            </div>
            <button type="submit" class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500">
                Filter
            </button>
        </form>
        <div class="overflow-x-auto">
            @if(empty($reconciliationRuns))
                <p class="px-4 py-6 text-gray-500 dark:text-gray-400">No reconciliation runs found, or the API is unreachable.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Business date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Started</th>
                            <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Server / Device / Matched / Mismatch</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($reconciliationRuns as $run)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $run['id'] ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $run['businessDate'] ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $run['status'] ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $run['startedAt'] ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ ($run['serverCount'] ?? '—') }} / {{ $run['deviceCount'] ?? '—' }} / {{ $run['matchedCount'] ?? '—' }} / {{ $run['mismatchCount'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>
