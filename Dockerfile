FROM php:8.1-cli

# Instala dependências para compilar extensões PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    libpcre3-dev \
    libxml2-dev \
    build-essential \
    autoconf \
    && git clone --depth=1 -b v6.0.2 https://github.com/swoole/swoole-src.git /usr/src/swoole \
    && cd /usr/src/swoole \
    && phpize \
    && ./configure --enable-sockets=no --enable-openssl=no --enable-swoole-curl=no --enable-brotli=no \
    && make -j"$(nproc)" && make install \
    && docker-php-ext-enable swoole \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /usr/src/swoole

RUN docker-php-ext-enable swoole