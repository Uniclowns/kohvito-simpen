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
            <div class="w-full max-w-[1400px] mx-auto flex items-center justify-between">
                <h1 class="text-3xl font-bold text-brand-black">{{ $pageTitle }}</h1>
                @if (isset($headerEnd))
                    {{ $headerEnd }}
                @else
                    <span class="text-sm text-brand-gray">{{ now()->translatedFormat('l, d F Y') }}</span>
                @endif
            </div>
        </header>

        <main class="flex-1 px-8 py-2">
            <div class="w-full max-w-[1400px] mx-auto">
                {{ $slot }}
            </div>
        </main>

        @if (isset($pageFooter))
            {{ $pageFooter }}
        @else
            <x-footer />
        @endif

    </div>

    <x-image-lightbox />

    @if (isset($scripts))
        {{ $scripts }}
    @endif

</body>
</html>
