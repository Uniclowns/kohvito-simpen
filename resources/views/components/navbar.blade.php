@props(['pageTitle' => ''])

<header class="bg-white border-b border-brand-gray-extralight px-8 py-4 flex items-center justify-between">
    <h1 class="text-lg font-bold text-brand-black">{{ $pageTitle }}</h1>
    <span class="text-sm text-brand-gray">{{ now()->translatedFormat('l, d F Y') }}</span>
</header>
