<x-app-layout title="Regional top-up config">
    <h1 class="mb-4 text-2xl font-semibold text-gray-900 dark:text-white">Regional top-up config</h1>
    <p class="mb-4 text-gray-600 dark:text-gray-400">Configure which top-up providers (e.g. MPESA, Emola) are available per country. Values are read from the backend via <code class="rounded bg-gray-200 px-1 dark:bg-gray-700">GET /api/v1/config/deployment</code>.</p>

    @if($deployment === null)
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-800 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-200">
            <p>Could not load deployment config. Check that <code>TITO_API_URL</code> points to the j-payments API and the endpoint is reachable.</p>
        </div>
    @else
        <div class="mb-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-gray-800">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Country</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $deployment['countryCode'] ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Default currency</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $deployment['defaultCurrencyCode'] ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Top-up providers</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                            @if(!empty($deployment['topUpProviderIds']))
                                {{ implode(', ', $deployment['topUpProviderIds']) }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-gray-600 dark:text-gray-400">To change these values, update deployment config in the backend (e.g. <code>ice.cash.deployment.*</code> or admin config API when available).</p>
    @endif
</x-app-layout>
