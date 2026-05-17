<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemesanan Ditutup — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">

    <div class="max-w-sm w-full text-center">
        <div class="bg-white rounded-2xl border border-gray-200 p-10 shadow-sm">
            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-amber-600" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>

            <h1 class="text-xl font-bold text-gray-900 mb-3">Pemesanan Ditutup</h1>

            <p class="text-sm text-gray-600 leading-relaxed">
                Maaf, pemesanan sedang tidak tersedia saat ini.<br>
                Silakan datang kembali nanti.
            </p>
        </div>

        <p class="text-xs text-gray-400 mt-6">{{ config('app.name') }}</p>
    </div>

</body>
</html>
