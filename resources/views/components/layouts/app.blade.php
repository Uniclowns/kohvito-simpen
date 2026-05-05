@props(['title' => '', 'pageTitle' => ''])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?: $pageTitle }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-light min-h-screen flex">

    <x-sidebar />

    <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
        <x-navbar :page-title="$pageTitle" />
        <x-alert />
        <main class="flex-1 px-8 py-6">
            {{ $slot }}
        </main>
        <x-footer />
    </div>

</body>
</html>
