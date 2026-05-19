@props([
    'title'     => '',
    'pageTitle' => '',
    'contentWidth' => '1400px',
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
<body class="bg-[#F6F6F6] min-h-screen">

    <div class="flex min-h-screen">
        <x-sidebar variant="kasir" />

        <div class="flex-1 flex flex-col min-w-0">

            <header class="px-10 pt-10 pb-6 flex items-center justify-between">
                <div class="w-full mx-auto flex items-center justify-between" style="max-width: {{ $contentWidth }};">
                    <h1 class="text-[40px] font-bold leading-[48px] tracking-[1.8px] text-brand-black">{{ $pageTitle }}</h1>
                    @if (isset($headerEnd))
                        {{ $headerEnd }}
                    @else
                        <span class="text-[18px] leading-7 text-brand-gray">{{ now()->translatedFormat('l, d F Y') }}</span>
                    @endif
                </div>
            </header>

            <main class="flex-1 px-10 pt-4 pb-20">
                <div class="w-full mx-auto" style="max-width: {{ $contentWidth }};">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @if (isset($pageFooter))
        {{ $pageFooter }}
    @else
        <x-footer />
    @endif

    <x-image-lightbox />

    @if (isset($scripts))
        {{ $scripts }}
    @endif

</body>
</html>
