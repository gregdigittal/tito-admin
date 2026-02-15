<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-14">
                <div class="flex items-center gap-6">
                    <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-gray-800">Tito Admin</a>
                    <a href="{{ route('staff.index') }}" class="text-gray-600 hover:text-gray-900">Staff</a>
                    <a href="{{ route('staff.create') }}" class="text-gray-600 hover:text-gray-900">Create staff</a>
                    <a href="{{ route('journals.index') }}" class="text-gray-600 hover:text-gray-900">Journals</a>
                </div>
                <div class="flex items-center">
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if (session('flash.message'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('flash.message') }}</div>
        @endif
        {{ $slot }}
    </div>
    @livewireScripts
</body>
</html>
