# Development: wp-content and wp-config.php are mounted from the host, WordPress core is baked in.
# Versions come from the build args in docker-compose.yml.
ARG PHP_VERSION
ARG NGINX_VERSION

# ---- WordPress core, checksum verified ----
FROM php:${PHP_VERSION}-fpm-trixie AS wordpress
ARG WORDPRESS_VERSION
ARG WORDPRESS_SHA1
RUN set -eux; \
    curl -fsSL -o /tmp/wordpress.tar.gz "https://wordpress.org/wordpress-${WORDPRESS_VERSION}.tar.gz"; \
    echo "${WORDPRESS_SHA1}  /tmp/wordpress.tar.gz" | sha1sum -c -; \
    tar -xzf /tmp/wordpress.tar.gz -C /usr/src; \
    rm /tmp/wordpress.tar.gz; \
    # wp-content comes from the repo only, so drop the bundled themes and plugins.
    rm -rf /usr/src/wordpress/wp-content; \
    mkdir /usr/src/wordpress/wp-content

# ---- php-fpm ----
FROM php:${PHP_VERSION}-fpm-trixie AS php
ARG APP_UID
ARG APP_GID

# PHP extensions WordPress uses. Build headers are removed, runtime libraries kept.
RUN set -eux; \
    savedAptMark="$(apt-mark showmanual)"; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libavif-dev libfreetype-dev libicu-dev libjpeg62-turbo-dev libpng-dev libwebp-dev libzip-dev; \
    docker-php-ext-configure gd --with-avif --with-freetype --with-jpeg --with-webp; \
    docker-php-ext-install -j"$(nproc)" bcmath exif gd intl mysqli opcache zip; \
    apt-mark auto '.*' > /dev/null; \
    apt-mark manual $savedAptMark; \
    ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
        | awk '/=>/ { so = $(NF-1); if (index(so, "/usr/local/") == 1) { next }; gsub("^/(usr/)?", "", so); printf "*%s\n", so }' \
        | sort -u | xargs -r dpkg-query --search | cut -d: -f1 | sort -u | xargs -rt apt-mark manual; \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
    rm -rf /var/lib/apt/lists/*

# Dev tools: wp-cli and the database client it needs.
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends mariadb-client; \
    rm -rf /var/lib/apt/lists/*; \
    curl -fsSL -o /usr/local/bin/wp https://github.com/wp-cli/wp-cli/releases/download/v2.12.0/wp-cli-2.12.0.phar; \
    echo "be928f6b8ca1e8dfb9d2f4b75a13aa4aee0896f8a9a0a1c45cd5d2c98605e6172e6d014dda2e27f88c98befc16c040cbb2bd1bfa121510ea5cdf5f6a30fe8832  /usr/local/bin/wp" | sha512sum -c -; \
    chmod +x /usr/local/bin/wp

# Run the workers as the host user, so files WordPress writes to the mount stay yours.
RUN set -eux; \
    groupmod -o -g "${APP_GID}" www-data; \
    usermod -o -u "${APP_UID}" -g "${APP_GID}" www-data

RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY docker/local/php.ini $PHP_INI_DIR/conf.d/zz-app.ini

COPY --from=wordpress --chown=www-data:www-data /usr/src/wordpress /var/www/html

# ---- nginx ----
FROM nginx:${NGINX_VERSION}-alpine AS nginx
RUN set -eux; \
    sed -i -e '/^user /d' -e 's,^pid .*;,pid /tmp/nginx.pid;,' /etc/nginx/nginx.conf; \
    chown -R nginx:nginx /var/cache/nginx
COPY docker/local/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=wordpress /usr/src/wordpress /var/www/html
USER nginx
EXPOSE 8080
