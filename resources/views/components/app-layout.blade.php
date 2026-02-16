@props(['title' => 'Tito Admin'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-100 dark:bg-gray-900">
    @if (session()->has('tito_access_token'))
        @php
            $roles = session('tito_staff_roles', []);
            $canStaff = empty($roles) || in_array('BACKOFFICE', $roles) || in_array('TITO_ADMIN', $roles);
            $canJournals = empty($roles) || in_array('BACKOFFICE', $roles) || in_array('TITO_ADMIN', $roles) || in_array('FINANCE_ADMIN', $roles);
        @endphp
        <nav class="border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-4">
                <a href="{{ route('dashboard') }}" class="font-semibold text-gray-900 dark:text-white">Tito Admin</a>
                <div class="flex items-center gap-4">
                    @if ($canStaff)
                        <a href="{{ route('staff.index') }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Staff</a>
                    @endif
                    @if ($canJournals)
                        <a href="{{ route('journals.index') }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Journals</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Log out</button>
                    </form>
                </div>
            </div>
        </nav>
    @endif
    <main class="mx-auto max-w-7xl px-4 py-8">
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>
