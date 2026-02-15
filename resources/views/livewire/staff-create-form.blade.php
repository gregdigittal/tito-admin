<div>
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">Create staff member</h1>

    @if ($error)
        <p class="text-red-600 mb-4">{{ $error }}</p>
    @endif
    @if ($success)
        <p class="text-green-600 mb-4">{{ $success }}</p>
    @endif

    <form wire:submit="submit" class="space-y-4 max-w-xl">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                <input type="email" id="email" wire:model="email" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
            </div>
            <div>
                <label for="firstName" class="block text-sm font-medium text-gray-700">First name *</label>
                <input type="text" id="firstName" wire:model="firstName" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
            </div>
            <div>
                <label for="lastName" class="block text-sm font-medium text-gray-700">Last name</label>
                <input type="text" id="lastName" wire:model="lastName" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2">
            </div>
            <div>
                <label for="idNumber" class="block text-sm font-medium text-gray-700">ID number *</label>
                <input type="text" id="idNumber" wire:model="idNumber" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
            </div>
            <div>
                <label for="idNumberType" class="block text-sm font-medium text-gray-700">ID type *</label>
                <input type="text" id="idNumberType" wire:model="idNumberType" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
            </div>
            <div>
                <label for="msisdn" class="block text-sm font-medium text-gray-700">MSISDN *</label>
                <input type="text" id="msisdn" wire:model="msisdn" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
            </div>
            <div>
                <label for="department" class="block text-sm font-medium text-gray-700">Department *</label>
                <input type="text" id="department" wire:model="department" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
            </div>
            <div>
                <label for="securityGroupId" class="block text-sm font-medium text-gray-700">Security group ID *</label>
                <input type="text" id="securityGroupId" wire:model="securityGroupId" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2" required>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" id="sendEmail" wire:model="sendEmail" class="rounded border-gray-300">
            <label for="sendEmail" class="text-sm text-gray-700">Send activation email</label>
        </div>
        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Create</button>
    </form>
</div>
