FROM php:8.4-cli

# Instalar dependencias del sistema y Node.js
RUN apt-get update && apt-get install -y \
    git unzip zip libpng-dev libonig-dev libxml2-dev curl sqlite3 libsqlite3-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql mbstring exif pcntl bcmath gd

# Copiar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Instalar dependencias de Node y compilar assets
RUN npm install --legacy-peer-deps
RUN npm run build || true

# Preparar carpetas de almacenamiento y base de datos
RUN mkdir -p database storage bootstrap/cache
RUN touch database/database.sqlite
RUN chmod -R 777 storage bootstrap/cache database

EXPOSE 8080

# Comando de arranque: ejecuta migraciones con seeders y levanta el servidor
CMD sh -c "touch database/database.sqlite && chmod 777 database/database.sqlite && php artisan migrate:fresh --seed --force && php artisan serve --host=0.0.0.0 --port=8080"
