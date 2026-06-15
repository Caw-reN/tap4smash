FROM php:8.3-apache

# Install ekstensi PHP yang dibutuhkan CodeIgniter 4
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl mysqli pdo pdo_mysql gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Aktifkan modul rewrite Apache
RUN a2enmod rewrite

# Ubah DocumentRoot Apache agar mengarah ke folder public/ milik CI4
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Salin seluruh file proyek ke dalam kontainer
COPY . /var/www/html/

# Hapus folder wa-service karena ia berjalan di kontainer terpisah
RUN rm -rf /var/www/html/wa-service

# Perbaiki izin folder writable (wajib untuk CodeIgniter)
RUN chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable

EXPOSE 80
