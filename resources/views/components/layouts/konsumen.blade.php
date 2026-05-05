@props(['title' => ''])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?: config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-light min-h-screen">

    {{-- Header — override with <x-slot:header> for custom headers --}}
    @if (!$header->isEmpty())
        {{ $header }}
    @else
        <header class="bg-brand-dark sticky top-0 z-10">
            <div class="max-w-lg mx-auto px-4 py-4 flex items-center justify-between">
                <h1 class="text-lg font-bold text-brand-light">{{ config('app.name') }}</h1>
            </div>
        </header>
    @endif

    {{-- Flash messages --}}
    @if (session('success') || session('error'))
        <div class="max-w-lg mx-auto px-4 pt-4 space-y-2">
            @if (session('success'))
                <div class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
        </div>
    @endif

    <main class="max-w-lg mx-auto px-4 py-6">
        {{ $slot }}
    </main>

    <x-footer />

</body>
</html>
