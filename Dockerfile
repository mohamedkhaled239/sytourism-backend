FROM php:8.2-apache

# تثبيت الحزم المطلوبة لـ Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# تفعيل Apache Rewrite
RUN a2enmod rewrite

# تحديد public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# تحديد مكان المشروع
WORKDIR /var/www/html

# نسخ ملفات المشروع
COPY . .

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تثبيت dependencies
RUN composer install --no-dev --optimize-autoloader

# Laravel optimization
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# صلاحيات Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# فتح بورت Apache
EXPOSE 80

# تشغيل Apache
CMD ["apache2-foreground"]