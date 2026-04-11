# Dockerfile
# Usando PHP 7.3-FPM (Versão estável para Laravel 5.6)
FROM php:7.3-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    smbclient \
    libxml2-dev

# Limpar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões do PHP necessárias para o Laravel e Excel
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd
RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www

# Copiar arquivos do projeto
COPY . /var/www

# Dar permissão para as pastas de cache e storage
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expor a porta 9000
EXPOSE 9000
CMD ["php-fpm"]