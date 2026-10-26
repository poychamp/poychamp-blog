# Production: core, wp-content and wp-config.php are copied in. Runs as non-root, no dev tools.
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
    rm -rf /usr/src/wordpress/wp-content
COPY wp-config.php /usr/src/wordpress/wp-config.php
COPY wp-content /usr/src/wordpress/wp-content

# ---- php-fpm ----
FROM php:${PHP_VERSION}-fpm-trixie AS php

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

RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/production/php.ini $PHP_INI_DIR/conf.d/zz-app.ini

# Code is owned by root and read-only to the app. Only uploads is writable.
COPY --from=wordpress /usr/src/wordpress /var/www/html
RUN set -eux; \
    mkdir -p /var/www/html/wp-content/uploads; \
    chown www-data:www-data /var/www/html/wp-content/uploads
VOLUME /var/www/html/wp-content/uploads

USER www-data

# ---- nginx ----
FROM nginx:${NGINX_VERSION}-alpine AS nginx
RUN set -eux; \
    sed -i -e '/^user /d' -e 's,^pid .*;,pid /tmp/nginx.pid;,' /etc/nginx/nginx.conf; \
    chown -R nginx:nginx /var/cache/nginx
COPY docker/production/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=wordpress /usr/src/wordpress /var/www/html
RUN rm /var/www/html/wp-config.php
USER nginx
EXPOSE 8080
