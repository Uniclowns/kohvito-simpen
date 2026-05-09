<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-sm">
        <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm">
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold text-gray-900">{{ config('app.name') }}</h1>
                <p class="text-sm text-gray-500 mt-1">Masuk ke akun Anda</p>
            </div>

            @if ($errors->has('loginError'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    {{ $errors->first('loginError') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                        Username
                    </label>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        value="{{ old('username') }}"
                        required
                        autofocus
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('username') border-red-400 @enderror"
                        placeholder="Masukkan username"
                    >
                    @error('username')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('password') border-red-400 @enderror"
                        placeholder="Masukkan password"
                    >
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full bg-gray-900 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
                >
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>
</html>
