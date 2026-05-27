@props([
    'id',
    'mode' => 'add',
    'user' => null,
    'submitUrl',
    'submitMethod' => 'POST'
])

@php
    $isEdit = $mode === 'edit' && $user;
    $title = $isEdit ? 'Edit Pengguna Kasir' : 'Tambah Pengguna Kasir';
    $needsMethodSpoof = in_array(strtoupper($submitMethod), ['PUT', 'PATCH']);
@endphp

<div id="{{ $id }}" data-form-modal
    class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-[2px] flex items-center justify-center p-4"
    onclick="if(event.target === this) closeAppModal('{{ $id }}')">
    <div class="kvt-modal-panel relative w-full max-w-[460px] overflow-y-auto rounded-2xl bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.15)] sm:p-8">
        {{-- Close Icon (X) --}}
        <button type="button" class="absolute right-5 top-5 text-[#380000] transition-colors hover:text-[#681F1F] sm:right-8 sm:top-7"
            onclick="closeAppModal('{{ $id }}')" aria-label="Tutup popup">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h2 class="text-xl font-bold text-brand-dark mb-6">{{ $title }}</h2>

        <form id="{{ $id }}-form" method="POST" action="{{ $submitUrl }}">
            @csrf
            @if ($needsMethodSpoof)
                @method($submitMethod)
            @endif
            <input type="hidden" name="_open_modal" value="{{ $id }}">

            {{-- Nama Lengkap --}}
            <div class="mb-5">
                <label class="text-xs font-bold text-brand-dark mb-2 block">Nama Lengkap Pengguna</label>
                <input type="text" name="nama_lengkap" placeholder="Masukkan Nama Lengkap Pengguna"
                    value="{{ old('nama_lengkap', $user->nama_lengkap ?? '') }}"
                    class="w-full bg-[#EBE4E0]/40 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#380000]">
                @foreach ($errors->get('nama_lengkap') as $err)
                    <p class="mt-1.5 text-xs text-[#E03131] flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $err }}
                    </p>
                @endforeach
            </div>

            {{-- Username --}}
            <div class="mb-5">
                <label class="text-xs font-bold text-brand-dark mb-2 block">Username Pengguna</label>
                <input type="text" name="username" placeholder="Masukkan Username Pengguna"
                    value="{{ old('username', $user->username ?? '') }}"
                    class="w-full bg-[#EBE4E0]/40 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#380000]">
                @foreach ($errors->get('username') as $err)
                    <p class="mt-1.5 text-xs text-[#E03131] flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $err }}
                    </p>
                @endforeach
            </div>

            {{-- Password --}}
            <div class="mb-8">
                <label class="text-xs font-bold text-brand-dark mb-2 block">Password</label>
                <div class="relative">
                    <input type="password" id="{{ $id }}-password-input" name="password"
                        placeholder="Masukkan Password Pengguna"
                        class="w-full bg-[#EBE4E0]/40 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#380000]">
                    <button type="button" data-toggle-password="{{ $id }}-password-input"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-brand-gray hover:text-brand-dark transition-colors">
                        <svg id="{{ $id }}-eye-closed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                        <svg id="{{ $id }}-eye-open" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                @foreach ($errors->get('password') as $err)
                    <p class="mt-1.5 text-xs text-[#E03131] flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $err }}
                    </p>
                @endforeach
            </div>

            {{-- Action Buttons --}}
            <div class="kvt-modal-actions flex justify-end gap-3">
                <button type="button" onclick="closeAppModal('{{ $id }}')"
                    class="bg-[#EBE4E0] text-[#380000] px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-[#DFD4CF] transition-colors">
                    Batal
                </button>
                <button type="button" onclick="openAppModal('confirm-simpan-{{ $mode }}-{{ $id }}')"
                    class="bg-[#380000] text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-[#2A0000] transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Confirm Save Modal --}}
<x-confirm-modal
    id="confirm-simpan-{{ $mode }}-{{ $id }}"
    title="Apakah anda yakin ingin menyimpan perubahan data pengguna Ini?"
    subtitle="Data pengguna yang diubah tidak dapat dikembalikan"
    confirmLabel="Ya, Simpan"
    variant="primary"
    onConfirm="document.getElementById('{{ $id }}-form').submit()" />

@once
<script>
    // Password toggle handler
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-toggle-password]');
        if (!btn) return;

        const inputId = btn.dataset.togglePassword;
        const input = document.getElementById(inputId);
        const eyeClosed = btn.querySelector('svg[id$="-eye-closed"]');
        const eyeOpen = btn.querySelector('svg[id$="-eye-open"]');

        if (input.type === 'password') {
            input.type = 'text';
            eyeClosed.classList.add('hidden');
            eyeOpen.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeClosed.classList.remove('hidden');
            eyeOpen.classList.add('hidden');
        }
    });
</script>
@endonce

@if ($errors->any() && old('_open_modal') === $id)
<script>
    document.addEventListener('DOMContentLoaded', () => {
        openAppModal('{{ $id }}');
    });
</script>
@endif
