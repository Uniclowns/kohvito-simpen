<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-light min-h-screen flex">

    <x-sidebar />

    <div class="flex-1 flex flex-col min-h-screen overflow-hidden">

        <header class="bg-white border-b border-brand-gray-extralight px-8 py-4 flex items-center justify-between">
            <h1 class="text-lg font-bold text-brand-black">@yield('page-title')</h1>
            @hasSection('header-end')
                @yield('header-end')
            @else
                <span class="text-sm text-brand-gray">{{ now()->translatedFormat('l, d F Y') }}</span>
            @endif
        </header>

        <main class="flex-1 px-8 py-6">
            @yield('content')
        </main>

        @hasSection('footer-override')
            @yield('footer-override')
        @else
            <x-footer />
        @endif

    </div>

    @stack('scripts')
</body>
</html>
