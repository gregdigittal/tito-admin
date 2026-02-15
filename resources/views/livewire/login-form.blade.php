<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-xl font-semibold text-gray-800 mb-4">Staff login</h1>

    @if ($step === 'login')
        <form wire:submit="submitLogin" class="space-y-4">
            @if ($error)
                <p class="text-sm text-red-600">{{ $error }}</p>
            @endif
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" wire:model="username" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required autofocus>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" wire:model="password" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
            </div>
            <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Login</button>
        </form>
    @else
        <form wire:submit="submitMfa" class="space-y-4">
            @if ($error)
                <p class="text-sm text-red-600">{{ $error }}</p>
            @endif
            <p class="text-sm text-gray-600">Enter the code sent to your device.</p>
            <div>
                <label for="mfaCode" class="block text-sm font-medium text-gray-700">Code</label>
                <input type="text" id="mfaCode" wire:model="mfaCode" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required autofocus>
            </div>
            <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Verify</button>
        </form>
    @endif
</div>
