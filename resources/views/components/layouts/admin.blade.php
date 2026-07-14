@props([
    'title'     => '',
    'pageTitle' => '',
])

@php
    $isSuperadmin = auth()->check() && auth()->user()->id_role === 3;
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?: $pageTitle }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen lg:flex {{ $isSuperadmin ? 'bg-[#F3F0EB] text-[#281D1D]' : 'bg-[#F6F6F6]' }}">

    <div data-sidebar-overlay class="fixed inset-0 z-40 hidden lg:hidden {{ $isSuperadmin ? 'bg-[#180405]/60 backdrop-blur-sm' : 'bg-black/45' }}"></div>

    <x-sidebar />

    <div class="flex min-h-screen min-w-0 flex-1 flex-col">

        @if ($isSuperadmin)
            <header class="px-4 pb-4 pt-5 sm:px-6 lg:px-10 lg:pb-6 lg:pt-8">
                <div class="mx-auto flex w-full max-w-[1480px] flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button"
                                data-sidebar-toggle
                                aria-controls="superadmin-navigation"
                                aria-expanded="false"
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#35090A] text-white shadow-[0_8px_24px_rgba(53,9,10,0.18)] transition hover:bg-[#571719] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#7A1F1F] focus-visible:ring-offset-2 lg:hidden"
                                aria-label="Buka navigasi">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" d="M5 7h14M5 12h14M5 17h14" />
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <p class="mb-1 text-[11px] font-bold uppercase tracking-[0.2em] text-[#725E5A]">KOHVITO / Superadmin</p>
                            <h1 class="truncate text-2xl font-bold tracking-[-0.035em] text-[#281D1D] sm:text-3xl">{{ $pageTitle }}</h1>
                        </div>
                    </div>

                    @if (isset($headerEnd))
                        {{ $headerEnd }}
                    @else
                        <div class="flex items-center gap-3 self-start rounded-2xl border border-[#DED5CD] bg-white/70 px-3 py-2 sm:self-auto">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#3A0A0B] text-sm font-bold text-white">
                                {{ mb_strtoupper(mb_substr(auth()->user()->nama_lengkap, 0, 1)) }}
                            </div>
                            <div class="hidden sm:block">
                                <p class="max-w-44 truncate text-sm font-bold text-[#342526]">{{ auth()->user()->nama_lengkap }}</p>
                                <p class="text-xs text-[#725E5A]">{{ now()->translatedFormat('d M Y') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </header>
        @else
            <header class="px-4 pb-4 pt-4 sm:px-6 lg:px-8 lg:pt-8">
                <div class="mx-auto flex w-full max-w-[1400px] flex-col justify-between gap-3 sm:flex-row sm:items-center sm:gap-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button"
                                data-sidebar-toggle
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[9px] bg-brand-dark text-white shadow-[2px_4px_2px_rgba(0,0,0,0.25)] lg:hidden"
                                aria-label="Buka navigasi">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                            </svg>
                        </button>
                        <h1 class="truncate text-2xl font-bold text-brand-black sm:text-3xl">{{ $pageTitle }}</h1>
                    </div>
                    @if (isset($headerEnd))
                        {{ $headerEnd }}
                    @else
                        <span class="hidden text-sm text-brand-gray sm:inline">{{ now()->translatedFormat('l, d F Y') }}</span>
                    @endif
                </div>
            </header>
        @endif

        <main class="flex-1 px-4 py-2 sm:px-6 {{ $isSuperadmin ? 'lg:px-10' : 'lg:px-8' }}">
            <div class="mx-auto w-full {{ $isSuperadmin ? 'max-w-[1480px]' : 'max-w-[1400px]' }}">
                {{ $slot }}
            </div>
        </main>

        @if (isset($pageFooter))
            {{ $pageFooter }}
        @elseif ($isSuperadmin)
            <footer class="mx-auto mt-8 flex w-full max-w-[1480px] flex-col gap-1 px-4 py-6 text-xs text-[#725E5A] sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-10">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
                <p>System console · akses terbatas Superadmin</p>
            </footer>
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
