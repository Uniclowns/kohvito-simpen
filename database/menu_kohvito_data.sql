-- ============================================
-- KOHVITO MENU - Insert satu per satu (bukan seeder)
-- Jalankan via Tinker: DB::unprepared(file_get_contents('database/menu_kohvito_data.sql'));
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE detail_pesanan;
TRUNCATE TABLE pesanan;
TRUNCATE TABLE menu_kategori;
TRUNCATE TABLE menu;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- SIGNATURE SERIES
-- ============================================
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('V Loco', 'Kopi susu signature creamy dengan espresso bold dipadu manis lembut khas Kohvito.', 'Espresso // Milk // Signature Base', 30000, 'vloco.png', 'Tersedia', 'Minuman', NULL, 'Dingin');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('V Loco 1 Liter', 'V Loco signature versi 1 liter - creamy dan smooth, pas untuk dibagi bareng teman.', 'Espresso // Milk // Signature Base (Ukuran 1 Liter)', 99000, 'vloco.png', 'Tersedia', 'Minuman', NULL, 'Dingin');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Just Matcha', 'Matcha pekat dengan susu lembut, menghadirkan rasa earthy umami yang menenangkan.', 'Milk // Matcha', 27000, 'just_matcha.png', 'Tersedia', 'Minuman', NULL, 'Keduanya');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Angguro', 'Kopi anggur signature: aroma anggur segar berpadu kopi pekat, menyegarkan dan ringan di tenggorokan.', 'Espresso // Wine Reduction // Sweet Base', 28000, 'angguro.png', 'Tersedia', 'Minuman', NULL, 'Dingin');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Florida Summer', 'Mocktail tropical segar, perpaduan jeruk manis, semangka juicy, dan kesegaran mint.', 'Orange // Watermelon // Mint', 33000, 'florida_summer.png', 'Tersedia', 'Minuman', NULL, 'Dingin');

-- ============================================
-- BLACK COFFEE
-- ============================================
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Black Bloom', 'Arabica murni dengan aksen floral plum, light body dan after-taste bunga yang elegan.', 'Arabica Shot // White Flower Plum', 29000, 'black_bloom.png', 'Tersedia', 'Minuman', NULL, 'Dingin');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Classic', 'Arabica klasik dengan profil fruity natural, asam halus dan body yang clean.', 'Arabica // Fruity', 26000, 'americano.png', 'Tersedia', 'Minuman', NULL, 'Keduanya');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Golden Hour', 'Americano disegarkan jus citrus dan zoda, pahit kopi diimbangi asam citrus yang fresh.', 'Americano // Citrus Jus // Zoda', 27000, 'golden_hour.png', 'Tersedia', 'Minuman', NULL, 'Dingin');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Sunset', 'Americano dengan jus peach dan zoda, manis buah peach mengalir di atas bold espresso.', 'Americano // Peach Jus // Zoda', 28000, 'sunset.png', 'Tersedia', 'Minuman', NULL, 'Dingin');

-- ============================================
-- KOPI SUSU SERIES
-- ============================================
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Arena', 'Kopi susu aren creamy dengan manis gula aren khas yang smoky dan rich.', 'Espresso // Milk // Signature Syrup // Aren', 26000, 'arena.png', 'Tersedia', 'Minuman', NULL, 'Keduanya');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Banana', 'Kopi susu pisang dengan irisan pisang asli, manis natural dan creamy lembut.', 'Espresso // Milk // Signature Syrup // Sliced Banana', 27000, 'banana.png', 'Tersedia', 'Minuman', NULL, 'Keduanya');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Cinnamon', 'Kopi susu cinnamon hangat, aroma rempah kayu manis yang cozy dan warming.', 'Espresso // Milk // Signature Syrup // Cinnamon', 27000, 'cinnamon.png', 'Tersedia', 'Minuman', NULL, 'Keduanya');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Salted', 'Kopi susu salted caramel, balance manis-asin yang adiktif dan smooth.', 'Espresso // Milk // Signature Syrup // Salted Caramel', 28000, 'salted.png', 'Tersedia', 'Minuman', NULL, 'Keduanya');

-- ============================================
-- WHITE COFFEE
-- ============================================
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Latte', 'Latte lembut dengan susu ringan dan foam tipis - kopi tetap terasa namun tetap creamy.', 'Arabica Shot // Light Milk // Light Foam', 27000, 'latte.png', 'Tersedia', 'Minuman', NULL, 'Keduanya');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Cappucino', 'Cappuccino klasik dengan foam tebal, aroma kopi pekat dipadu silky milk foam.', 'Arabica Shot // Milk // Thick Foam', 28000, 'latte.png', 'Tersedia', 'Minuman', NULL, 'Keduanya');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Con Hielo', 'Kopi tiga susu khas Indonesia, manis legit dengan dingin yang menyegarkan.', 'Espresso // 3 Ways Milk', 26000, 'con_hielo.png', 'Tersedia', 'Minuman', NULL, 'Dingin');

-- ============================================
-- FRESH SERIES
-- ============================================
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Florida Summer (Fresh)', 'Versi fresh dari mocktail tropical: jeruk, semangka, dan mint yang menyegarkan tanpa kafein.', 'Orange // Watermelon // Mint', 33000, 'florida_summer.png', 'Tersedia', 'Minuman', NULL, 'Dingin');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Lychee Tea', 'Teh dingin dengan lychee manis dan kesegaran mint, refreshing untuk siang hari.', 'Lychee // Tea // Mint', 24000, 'lychee_tea.png', 'Tersedia', 'Minuman', NULL, 'Dingin');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Applelieble', 'Teh apel dengan sentuhan mint, segar dan manis tanpa perlu gula tambahan.', 'Apple // Tea // Mint', 24000, 'applelieble.png', 'Tersedia', 'Minuman', NULL, 'Dingin');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Jareh', 'Teh sereh dengan mint, herbal segar dan menenangkan dengan aroma khas Indonesia.', 'Lemongrass // Tea // Mint', 24000, 'jareh.png', 'Tersedia', 'Minuman', NULL, 'Dingin');

-- ============================================
-- CREAMY SERIES
-- ============================================
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Cupid', 'Susu berry dengan gummy candy, manis fruity dan playful dengan kunyahan kenyal.', 'Milk // Berry // Gummy Candy', 26000, 'cupid.png', 'Tersedia', 'Minuman', NULL, 'Keduanya');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Just Matcha (Creamy)', 'Matcha latte creamy versi premium, susu lebih kental menghasilkan tekstur yang silky.', 'Milk // Matcha', 27000, 'just_matcha.png', 'Tersedia', 'Minuman', NULL, 'Keduanya');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Matcha Berry', 'Matcha latte dengan saus berry, balance earthy matcha dan tart berry yang fruity.', 'Milk // Matcha // Berry', 30000, 'matcha_berry.png', 'Tersedia', 'Minuman', NULL, 'Dingin');
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Choco Bisquit', 'Cokelat susu dengan biscuit crumble, manis cokelat creamy dan crunch biscuit.', 'Milk // Chocolate // Bisquit', 27000, 'choco_bisquit.png', 'Tersedia', 'Minuman', NULL, 'Dingin');

-- ============================================
-- MAKANAN UTAMA
-- ============================================
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Nasi Goreng Kohvito', 'Nasi goreng spesial Kohvito dengan sosis, baso sapi, telur, dan salad segar - savory dan filling.', 'Rice // Salad // Egg // Sausage // Beef Baso', 37000, 'nasi_goreng_kohvito.png', 'Tersedia', 'Makanan', 'Pedas', NULL);
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Ayam Duo Sambal', 'Ayam strip crispy dengan dua sambal: bawang gurih dan ijo pedas segar.', 'Rice // Chicken Strip // Salad // Sambal Bawang // Sambal Ijo', 39000, 'ayam_duo_sambal.png', 'Tersedia', 'Makanan', 'Pedas', NULL);
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Green Curry', 'Chicken katsu disiram kuah green curry kental dengan terong, harum rempah Thailand.', 'Rice // Chicken Katsu // Green Curry // Aubergine', 39000, 'green_curry.png', 'Tersedia', 'Makanan', 'Pedas', NULL);
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Chicken Salted Egg', 'Ayam saus telur asin creamy dengan parutan kuning telur asin dan aroma jeruk basil segar.', 'Rice // Egg // Chicken Salted Egg // Grated Salted Egg Yolk // Fresh Lemon Basi', 38000, 'chicken_salted_egg.png', 'Tersedia', 'Makanan', 'Tidak Pedas', NULL);
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Wonton Ori / Kuah Pedas', 'Wonton lembut dalam kuah ayam pedas dengan chili oil dan daun bawang - hangat dan kaya umami.', 'Wonton // Chicken Broth // Chili Oil // Chives', 29000, 'wonton_pedas.png', 'Tersedia', 'Makanan', 'Pedas', NULL);
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Korean Chicken', 'Ayam Korean dengan saus balado pedas dan aroma daun jeruk - fusion Korea-Indonesia yang nendang.', 'Chicken // Korean Sauce // Balado // Lime Leaves', 63000, 'korean_chicken.png', 'Tersedia', 'Makanan', 'Pedas', NULL);
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Chicken Wings', 'Sayap ayam crispy dengan saus pedas balado dan daun jeruk - gurih pedas menggugah selera.', 'Chicken Wings // Spicy Sauce // Balado // Lime Leaves', 35000, 'chicken_wings.png', 'Tersedia', 'Makanan', 'Pedas', NULL);
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Smoked Beef Pasta', 'Pasta fettuccine creamy dengan smoked beef gurih dan taburan parsley segar.', 'Pasta Fettuchini // Smoked Beef // Milk // Parsley', 42000, 'smoked_beef_pasta.png', 'Tersedia', 'Makanan', 'Tidak Pedas', NULL);
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Burger & Chips', 'Chicken burger dengan saus house-made disandingkan kentang slice crispy - classic comfort food.', 'Potato Slice // Chicken Burger // House Made Souce', 42000, 'burger_chips.png', 'Tersedia', 'Makanan', 'Tidak Pedas', NULL);

-- ============================================
-- SNACKS / SIDE DISHES
-- ============================================
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Potato Mix', 'Mix ubi ungu dan kuning goreng - manis natural dengan tekstur lembut di dalam, crispy di luar.', 'Purple // Yellow Sweet Potato', 28000, 'potato_mix.png', 'Tersedia', 'Makanan', 'Tidak Pedas', NULL);
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Grilled Chicken Finger', 'Chicken finger panggang dengan saus BBQ merah dan mayonaise - smoky, creamy, dan filling.', 'Chicken // Red BBQ // Mayo', 35000, 'chicken_strip.png', 'Tersedia', 'Makanan', 'Tidak Pedas', NULL);
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Banana Frites', 'Pisang goreng renyah dengan dua saus: cokelat dan keju - manis-gurih cocok untuk teman ngopi.', 'Fried Banana // Chocolate Sauce // Cheese Sauce', 28000, 'banana_frites.png', 'Tersedia', 'Makanan', 'Tidak Pedas', NULL);
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Churros', 'Churros crispy luar lembut dalam dengan saus cokelat dan keju - dessert yang adiktif.', 'Churros // Chocolate Sauce // Cheese Sauce', 35000, 'churros.png', 'Tersedia', 'Makanan', 'Tidak Pedas', NULL);
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Tela-Tela', 'Singkong goreng tabur balado - gurih pedas crunchy yang nagih.', 'Cassava // Balado Powder', 28000, 'tela_tela.png', 'Tersedia', 'Makanan', 'Pedas', NULL);
INSERT INTO menu (nama_menu, deskripsi, komposisi, harga, gambar_menu, status_ketersediaan, jenis_menu, kategori_makanan, tipe_minuman) VALUES ('Tahu Cabe Garam', 'Tahu goreng crispy dengan irisan cabe dan daun jeruk - savory pedas yang fresh dan ringan.', 'Fried Tofu // Chili Slice // Lime Leaves', 27000, 'tahu_cabe_garam.png', 'Tersedia', 'Makanan', 'Pedas', NULL);

-- ============================================
-- PIVOT: menu_kategori (multi-category support)
-- IDs: 1-5 Signature, 6-9 Black Coffee, 10-13 Kopi Susu, 14-16 White Coffee,
--      17-20 Fresh, 21-24 Creamy, 25-33 Makanan Utama, 34-39 Snacks
-- ============================================
INSERT INTO menu_kategori (id_menu, id_kategori) VALUES
  (1, 1),   -- V Loco → Kopi
  (2, 1),   -- V Loco 1 Liter → Kopi
  (3, 2),   -- Just Matcha → Non-Kopi
  (4, 1),   -- Angguro → Kopi
  (5, 5),   -- Florida Summer → Minuman Segar
  (6, 1),   -- Black Bloom → Kopi
  (7, 1),   -- Classic → Kopi
  (8, 1),   -- Golden Hour → Kopi
  (9, 1),   -- Sunset → Kopi
  (10, 1),  -- Arena → Kopi
  (11, 1),  -- Banana → Kopi
  (12, 1),  -- Cinnamon → Kopi
  (13, 1),  -- Salted → Kopi
  (14, 1),  -- Latte → Kopi
  (15, 1),  -- Cappucino → Kopi
  (16, 1),  -- Con Hielo → Kopi
  (17, 5),  -- Florida Summer (Fresh) → Minuman Segar
  (18, 5),  -- Lychee Tea → Minuman Segar
  (19, 5),  -- Applelieble → Minuman Segar
  (20, 5),  -- Jareh → Minuman Segar
  (21, 2),  -- Cupid → Non-Kopi
  (22, 2),  -- Just Matcha (Creamy) → Non-Kopi
  (23, 2),  -- Matcha Berry → Non-Kopi
  (24, 2),  -- Choco Bisquit → Non-Kopi
  (25, 3),  -- Nasi Goreng Kohvito → Makanan Berat
  (26, 3),  -- Ayam Duo Sambal → Makanan Berat
  (27, 3),  -- Green Curry → Makanan Berat
  (28, 3),  -- Chicken Salted Egg → Makanan Berat
  (29, 3),  -- Wonton Ori / Kuah Pedas → Makanan Berat
  (30, 3),  -- Korean Chicken → Makanan Berat
  (31, 3),  -- Chicken Wings → Makanan Berat
  (32, 3),  -- Smoked Beef Pasta → Makanan Berat
  (33, 3),  -- Burger & Chips → Makanan Berat
  (34, 4),  -- Potato Mix → Snack
  (35, 4),  -- Grilled Chicken Finger → Snack
  (36, 4),  -- Banana Frites → Snack
  (37, 4),  -- Churros → Snack
  (38, 4),  -- Tela-Tela → Snack
  (39, 4);  -- Tahu Cabe Garam → Snack
