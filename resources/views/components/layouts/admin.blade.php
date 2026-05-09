@props([
    'title'     => '',
    'pageTitle' => '',
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?: $pageTitle }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F6F6F6] min-h-screen flex">

    <x-sidebar />

    <div class="flex-1 flex flex-col min-h-screen overflow-hidden">

        <header class="px-8 pt-8 pb-4 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-brand-black">{{ $pageTitle }}</h1>
            @if (isset($headerEnd))
                {{ $headerEnd }}
            @else
                <span class="text-sm text-brand-gray">{{ now()->translatedFormat('l, d F Y') }}</span>
            @endif
        </header>

        <main class="flex-1 px-8 py-2">
            {{ $slot }}
        </main>

        @if (isset($pageFooter))
            {{ $pageFooter }}
        @else
            <x-footer />
        @endif

    </div>

    @if (isset($scripts))
        {{ $scripts }}
    @endif

</body>
</html>
