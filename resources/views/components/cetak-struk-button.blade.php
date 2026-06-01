@props([
    'noPesanan',
])

{{--
    Tombol "Cetak Struk" yang mencetak langsung ke printer termal jaringan.
    Penanganan klik + toast ada di resources/js/cetak-struk.js (delegasi global
    pada elemen [data-cetak-struk]). Kelas styling diwariskan dari pemanggil
    sehingga tampilannya menyesuaikan tiap layar.
--}}
<button
    type="button"
    data-cetak-struk
    data-cetak-url="{{ route('kasir.struk.cetak', $noPesanan) }}"
    {{ $attributes }}>
    <span data-cetak-text>Cetak Struk</span>
</button>
