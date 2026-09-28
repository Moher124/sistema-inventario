FROM php:8.4-cli

# Instalar dependencias del sistema y Node.js
RUN apt-get update && apt-get install -y \
    git unzip zip libpng-dev libonig-dev libxml2-dev curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Copiar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copiar archivos
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Configurar entorno de Node y compilar assets
ENV NODE_ENV=production
RUN npm ci || npm install --legacy-peer-deps
RUN npm run build

EXPOSE 8080

CMD php artisan serve --host=0.0.0.0 --port=8080
