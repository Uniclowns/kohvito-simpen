@extends('admin.layouts.app')

@section('title', 'Kelola Pengguna Kasir')
@section('page-title', 'Kelola Pengguna Kasir')

@section('content')

{{-- Flash message --}}
@if (session('success'))
    <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg px-4 py-3">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg px-4 py-3">
        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <ul class="space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Card --}}
<div class="bg-white rounded-xl border border-brand-gray-extralight shadow-sm">

    {{-- Card header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-brand-gray-extralight">
        <div>
            <p class="font-semibold text-brand-black">Daftar Kasir</p>
            <p class="text-xs text-brand-gray mt-0.5">{{ $kasirs->count() }} akun terdaftar</p>
        </div>
        <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')"
                class="flex items-center gap-2 bg-brand-red hover:bg-brand-dark text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kasir
        </button>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-brand-light border-b border-brand-gray-extralight">
                    <th class="text-left text-brand-gray-dark font-semibold px-6 py-3 w-12">No</th>
                    <th class="text-left text-brand-gray-dark font-semibold px-6 py-3">Nama Lengkap</th>
                    <th class="text-left text-brand-gray-dark font-semibold px-6 py-3">Username</th>
                    <th class="text-left text-brand-gray-dark font-semibold px-6 py-3 w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-gray-extralight">
                @forelse ($kasirs as $index => $kasir)
                    <tr class="hover:bg-brand-light/50 transition-colors">
                        <td class="px-6 py-4 text-brand-gray">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-brand-black font-medium">{{ $kasir->nama_lengkap }}</td>
                        <td class="px-6 py-4 text-brand-gray-dark font-mono">{{ $kasir->username }}</td>
                        <td class="px-6 py-4">
                            <form method="POST"
                                  action="{{ route('admin.pengguna-kasir.destroy', $kasir->id_users) }}"
                                  onsubmit="return confirm('Hapus akun kasir {{ $kasir->nama_lengkap }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-800 font-medium transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-brand-gray">
                            Belum ada akun kasir yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah Kasir --}}
<div id="modal-tambah"
     class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">

        <div class="flex items-center justify-between px-6 py-4 border-b border-brand-gray-extralight">
            <h2 class="font-semibold text-brand-black">Tambah Akun Kasir</h2>
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                    class="text-brand-gray hover:text-brand-black transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.pengguna-kasir.store') }}" class="px-6 py-5 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-brand-black mb-1.5">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                       placeholder="contoh: Budi Santoso"
                       class="w-full border border-brand-gray-light rounded-lg px-3 py-2.5 text-sm text-brand-black placeholder-brand-gray
                              focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 transition">
                @error('nama_lengkap')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-brand-black mb-1.5">Username</label>
                <input type="text" name="username" value="{{ old('username') }}"
                       placeholder="contoh: kasir01"
                       class="w-full border border-brand-gray-light rounded-lg px-3 py-2.5 text-sm text-brand-black placeholder-brand-gray
                              focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 transition">
                @error('username')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-brand-black mb-1.5">Password</label>
                <input type="password" name="password"
                       placeholder="Minimal 6 karakter"
                       class="w-full border border-brand-gray-light rounded-lg px-3 py-2.5 text-sm text-brand-black placeholder-brand-gray
                              focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 transition">
                @error('password')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-brand-black mb-1.5">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                       placeholder="Ulangi password"
                       class="w-full border border-brand-gray-light rounded-lg px-3 py-2.5 text-sm text-brand-black placeholder-brand-gray
                              focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20 transition">
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="flex-1 bg-brand-red hover:bg-brand-dark text-white text-sm font-medium py-2.5 rounded-lg transition-colors">
                    Simpan
                </button>
                <button type="button"
                        onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                        class="flex-1 border border-brand-gray-light text-brand-gray-dark hover:bg-brand-light text-sm font-medium py-2.5 rounded-lg transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
