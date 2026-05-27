{{-- Order Tutup Konsumen
    Route: konsumen.order-tutup (/order-tutup)
    Controller: inline route closure in routes/web.php
    Variables: none
--}}
<x-layouts.konsumen
    :title="'Pemesanan Ditutup - ' . config('app.name')"
    bodyClass="min-h-screen bg-gray-50 px-4 flex items-center justify-center">

    <div class="w-full max-w-sm text-center" data-anim="fade-up">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-10">
            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-amber-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-600" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>

            <h1 class="mb-3 text-xl font-bold text-gray-900">Pemesanan Ditutup</h1>

            <p class="text-sm leading-relaxed text-gray-600">
                Maaf, pemesanan sedang tidak tersedia saat ini.<br>
                Silakan datang kembali nanti.
            </p>
        </div>

        <p class="mt-6 text-xs text-gray-400">{{ config('app.name') }}</p>
    </div>
</x-layouts.konsumen>
