# use PHP 8.3
FROM php:8.3-fpm as builder

# Copy 
COPY ./docker/php.ini /usr/local/etc/php/

# Installing common dependencies
RUN apt update && apt install -y \
    libmemcached-dev \
    git \
    curl \
    libonig-dev \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    zlib1g-dev \
    libzip-dev \
    libz-dev \
    libpq-dev \
    libssl-dev \
    libmemcached11 \
    libmemcachedutil2 \
    build-essential \
    unzip \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# Installing and configuring the PHP dependencies
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath zip gettext intl

# Installing Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs


# Installing Memcached extension and its PHP extension
RUN pecl install memcached && docker-php-ext-enable memcached

# Downloading composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Moving to the work directory and copying the Laravel App
WORKDIR /var/www/app
COPY . /var/www/app

# Assign the right privileges
RUN chown -R www-data:www-data /var/www/app
RUN chmod -R 775 /var/www/app/storage

# Installing composer
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

COPY . /var/www/app

# Exposing the port 9000
EXPOSE 9000
CMD ["php-fpm"]