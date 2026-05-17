<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('UPDATE menu SET komposisi = deskripsi WHERE komposisi IS NULL');

        DB::table('menu')->where('nama_menu', 'Angguro')->update([
            'komposisi' => 'Espresso // Wine Reduction // Sweet Base',
        ]);

        $flavors = [
            1 => 'Kopi susu signature creamy dengan espresso bold dipadu manis lembut khas Kohvito.',
            2 => 'V Loco signature versi 1 liter - creamy dan smooth, pas untuk dibagi bareng teman.',
            3 => 'Matcha pekat dengan susu lembut, menghadirkan rasa earthy umami yang menenangkan.',
            4 => 'Kopi anggur signature: aroma anggur segar berpadu kopi pekat, menyegarkan dan ringan di tenggorokan.',
            5 => 'Mocktail tropical segar, perpaduan jeruk manis, semangka juicy, dan kesegaran mint.',
            6 => 'Arabica murni dengan aksen floral plum, light body dan after-taste bunga yang elegan.',
            7 => 'Arabica klasik dengan profil fruity natural, asam halus dan body yang clean.',
            8 => 'Americano disegarkan jus citrus dan zoda, pahit kopi diimbangi asam citrus yang fresh.',
            9 => 'Americano dengan jus peach dan zoda, manis buah peach mengalir di atas bold espresso.',
            10 => 'Kopi susu aren creamy dengan manis gula aren khas yang smoky dan rich.',
            11 => 'Kopi susu pisang dengan irisan pisang asli, manis natural dan creamy lembut.',
            12 => 'Kopi susu cinnamon hangat, aroma rempah kayu manis yang cozy dan warming.',
            13 => 'Kopi susu salted caramel, balance manis-asin yang adiktif dan smooth.',
            14 => 'Latte lembut dengan susu ringan dan foam tipis - kopi tetap terasa namun tetap creamy.',
            15 => 'Cappuccino klasik dengan foam tebal, aroma kopi pekat dipadu silky milk foam.',
            16 => 'Kopi tiga susu khas Indonesia, manis legit dengan dingin yang menyegarkan.',
            17 => 'Versi fresh dari mocktail tropical: jeruk, semangka, dan mint yang menyegarkan tanpa kafein.',
            18 => 'Teh dingin dengan lychee manis dan kesegaran mint, refreshing untuk siang hari.',
            19 => 'Teh apel dengan sentuhan mint, segar dan manis tanpa perlu gula tambahan.',
            20 => 'Teh sereh dengan mint, herbal segar dan menenangkan dengan aroma khas Indonesia.',
            21 => 'Susu berry dengan gummy candy, manis fruity dan playful dengan kunyahan kenyal.',
            22 => 'Matcha latte creamy versi premium, susu lebih kental menghasilkan tekstur yang silky.',
            23 => 'Matcha latte dengan saus berry, balance earthy matcha dan tart berry yang fruity.',
            24 => 'Cokelat susu dengan biscuit crumble, manis cokelat creamy dan crunch biscuit.',
            25 => 'Nasi goreng spesial Kohvito dengan sosis, baso sapi, telur, dan salad segar - savory dan filling.',
            26 => 'Ayam strip crispy dengan dua sambal: bawang gurih dan ijo pedas segar.',
            27 => 'Chicken katsu disiram kuah green curry kental dengan terong, harum rempah Thailand.',
            28 => 'Ayam saus telur asin creamy dengan parutan kuning telur asin dan aroma jeruk basil segar.',
            29 => 'Wonton lembut dalam kuah ayam pedas dengan chili oil dan daun bawang - hangat dan kaya umami.',
            30 => 'Ayam Korean dengan saus balado pedas dan aroma daun jeruk - fusion Korea-Indonesia yang nendang.',
            31 => 'Sayap ayam crispy dengan saus pedas balado dan daun jeruk - gurih pedas menggugah selera.',
            32 => 'Pasta fettuccine creamy dengan smoked beef gurih dan taburan parsley segar.',
            33 => 'Chicken burger dengan saus house-made disandingkan kentang slice crispy - classic comfort food.',
            34 => 'Mix ubi ungu dan kuning goreng - manis natural dengan tekstur lembut di dalam, crispy di luar.',
            35 => 'Chicken finger panggang dengan saus BBQ merah dan mayonaise - smoky, creamy, dan filling.',
            36 => 'Pisang goreng renyah dengan dua saus: cokelat dan keju - manis-gurih cocok untuk teman ngopi.',
            37 => 'Churros crispy luar lembut dalam dengan saus cokelat dan keju - dessert yang adiktif.',
            38 => 'Singkong goreng tabur balado - gurih pedas crunchy yang nagih.',
            39 => 'Tahu goreng crispy dengan irisan cabe dan daun jeruk - savory pedas yang fresh dan ringan.',
        ];

        foreach ($flavors as $id => $description) {
            DB::table('menu')->where('id_menu', $id)->update([
                'deskripsi' => $description,
            ]);
        }
    }

    public function down(): void
    {
        DB::statement('UPDATE menu SET deskripsi = komposisi WHERE komposisi IS NOT NULL');
        DB::statement('UPDATE menu SET komposisi = NULL');
    }
};
