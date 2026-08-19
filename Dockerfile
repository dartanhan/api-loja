FROM php:7.4-fpm

ARG user=www
ARG uid=1000

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libicu-dev libzip-dev \
    libfreetype6-dev libjpeg62-turbo-dev zip unzip nginx supervisor \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip xml tokenizer

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && chown -R $user:$user /home/$user

WORKDIR /var/www

COPY docker/nginx/default.conf /etc/nginx/sites-available/default
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY --chown=$user:www-data . .

RUN chown -R $user:www-data /var/www
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

USER $user
EXPOSE 80
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
