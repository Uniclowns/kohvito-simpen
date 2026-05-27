{{-- Detail Menu Konsumen
    Route: konsumen.menu.detail (/menu/{id}/detail)
    Controller: BerandaKonsumenController@detail
    Variables: $menu, $cartCount
--}}
<x-layouts.konsumen
    :title="$menu->nama_menu . ' - ' . config('app.name')"
    bodyClass="min-h-screen bg-[#F6F6F6] font-sans text-brand-black kvt-konsumen-mobile-view">
    @include('konsumen.partials.detail-menu-content')
</x-layouts.konsumen>
