# syntax=docker/dockerfile:1
# Dev image untuk SIMPEN Kohvito (Laravel 13).
# Source code di-bind-mount dari host (lihat compose.yaml), image ini hanya
# menyiapkan runtime PHP + ekstensi + composer.
FROM php:8.5-cli

# Ekstensi yang dibutuhkan proyek:
# - pdo_mysql : database MySQL
# - gd        : simple-qrcode / image handling (ImageCompressController)
# - intl      : formatting lokal (DomPDF, Laravel)
# - zip       : fast-excel / composer
# - bcmath    : perhitungan presisi
# - exif      : deteksi orientasi gambar menu
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip curl \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libicu-dev libzip-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql gd intl zip bcmath exif \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
