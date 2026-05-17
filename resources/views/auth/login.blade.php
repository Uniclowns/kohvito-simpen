<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen relative overflow-hidden bg-[#1a1a1a]">

    {{-- Background photo (cafe interior) --}}
    <img src="{{ asset('images/bg/SIMPEN LOGIN.jpg') }}" alt=""
         class="absolute inset-0 w-full h-full object-cover pointer-events-none select-none">

    {{-- Centered split card --}}
    <main class="relative min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-[1129px] flex items-stretch shadow-[2px_4px_8px_rgba(0,0,0,0.25)] rounded-[18px] overflow-hidden">

            {{-- Left panel: glass greeting + figure --}}
            <div class="hidden md:flex relative w-[567px] h-[613px] bg-white/10 backdrop-blur-sm px-8 py-12 overflow-hidden">
                {{-- Figure illustration (standing man holding coffee) --}}
                <img src="{{ asset('images/illustration/login figure.svg') }}" alt=""
                     class="absolute left-[111px] top-[227px] w-[343px] h-auto pointer-events-none select-none opacity-95">

                <div class="relative flex flex-col gap-3 text-white max-w-[449px] z-10">
                    <div class="font-bold text-[30px] leading-[36px] tracking-[1.5px]">
                        <p class="m-0">Halo,</p>
                        <p class="m-0">Selamat Datang Admin!!</p>
                    </div>
                    <p class="text-[14px] leading-[20px] tracking-[0.7px]">
                        Kelola menu, pesanan, dan laporan penjualan secara efisien dalam satu sistem pemesanan online.
                    </p>
                </div>
            </div>

            {{-- Right panel: dark maroon login form --}}
            <div class="flex-1 bg-[#460001] px-8 py-10 flex flex-col justify-center min-h-[613px]">
                <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-[26px] w-full">
                    @csrf

                    <h1 class="font-bold text-white text-[36px] leading-[40px] tracking-[1.8px]">
                        Log In
                    </h1>

                    @if ($errors->has('loginError'))
                        <div class="flex items-center gap-2 bg-[#E52E2D]/15 border border-[#E52E2D]/40 text-white text-[12px] rounded-[6px] px-3 py-2">
                            <svg class="w-4 h-4 flex-shrink-0 text-[#E52E2D]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ $errors->first('loginError') }}</span>
                        </div>
                    @endif

                    {{-- Username --}}
                    <div class="flex flex-col">
                        <label for="username" class="text-white text-[20px] leading-[28px] tracking-[1px]">
                            Username
                        </label>
                        <input
                            id="username"
                            name="username"
                            type="text"
                            value="{{ old('username') }}"
                            required
                            autofocus
                            placeholder="Masukkan Username Anda"
                            class="w-full bg-transparent text-white placeholder:text-white/60 text-[14px] tracking-[0.7px] px-[10px] py-[10px] border-b border-white/70 focus:border-white focus:outline-none transition-colors">
                        @error('username')
                            <div class="flex items-center gap-[5px] mt-[3px]">
                                <svg class="w-[15px] h-[15px] text-[#E52E2D] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-[#E52E2D] text-[10px] leading-[12px] tracking-[0.5px]">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="flex flex-col">
                        <label for="password" class="text-white text-[20px] leading-[28px] tracking-[1px]">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                placeholder="Masukkan Password Anda"
                                class="w-full bg-transparent text-white placeholder:text-white/60 text-[14px] tracking-[0.7px] px-[10px] py-[10px] pr-10 border-b border-white/70 focus:border-white focus:outline-none transition-colors">
                            <button type="button"
                                    onclick="togglePasswordVisibility()"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-white/80 hover:text-white transition-colors p-1"
                                    aria-label="Tampilkan/sembunyikan password">
                                <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eye-closed" class="hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <div class="flex items-center gap-[5px] mt-[3px]">
                                <svg class="w-[15px] h-[15px] text-[#E52E2D] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-[#E52E2D] text-[10px] leading-[12px] tracking-[0.5px]">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    {{-- Log In button --}}
                    <div class="flex justify-end mt-2">
                        <button type="submit"
                                class="bg-white text-[#460001] px-[12px] py-[6px] rounded-[9px] text-[14px] tracking-[0.7px] shadow-[2px_4px_4px_rgba(0,0,0,0.25)] hover:bg-gray-100 transition-colors">
                            Log In
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
