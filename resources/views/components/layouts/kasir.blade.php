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

    <div data-sidebar-overlay class="fixed inset-0 z-40 hidden bg-black/45 lg:hidden"></div>

    <div class="flex min-h-screen">
        <x-sidebar variant="kasir" />

        <div class="flex min-w-0 flex-1 flex-col">

            <header class="px-4 pt-4 pb-4 sm:px-6 lg:px-10 lg:pt-10 lg:pb-6">
                <div class="mx-auto flex w-full items-center justify-between gap-4" style="max-width: {{ $contentWidth }};">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button"
                                data-sidebar-toggle
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[9px] bg-brand-dark text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] lg:hidden"
                                aria-label="Buka navigasi">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                            </svg>
                        </button>
                        <h1 class="truncate text-2xl font-bold leading-8 tracking-[1px] text-brand-black sm:text-3xl lg:text-[40px] lg:leading-[48px] lg:tracking-[1.8px]">{{ $pageTitle }}</h1>
                    </div>
                    @if (isset($headerEnd))
                        {{ $headerEnd }}
                    @else
                        <span class="hidden text-[18px] leading-7 text-brand-gray sm:inline">{{ now()->translatedFormat('l, d F Y') }}</span>
                    @endif
                </div>
            </header>

            <main class="flex-1 px-4 pt-2 pb-20 sm:px-6 lg:px-10 lg:pt-4">
                <div class="mx-auto w-full" style="max-width: {{ $contentWidth }};">
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
