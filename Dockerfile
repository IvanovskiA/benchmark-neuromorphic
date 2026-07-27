FROM php:8.3-fpm-bookworm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev nginx nodejs npm \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=python:3.10-slim-bookworm /usr/local /usr/local

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY python/requirements.txt /tmp/python-requirements.txt
RUN pip3 install --no-cache-dir --default-timeout=1000 --retries 10 \
    torch --index-url https://download.pytorch.org/whl/cpu \
    && pip3 install --no-cache-dir --default-timeout=1000 --retries 10 -r /tmp/python-requirements.txt \
    && pip3 install --no-cache-dir --default-timeout=1000 --retries 10 lava-nc || echo "lava-nc optional install skipped"

COPY docker/nginx/default.conf /etc/nginx/sites-available/default
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
