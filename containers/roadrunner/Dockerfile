ARG RR_VERSION
ARG RR_IMAGE=spiralscout/roadrunner
ARG PHP_IMAGE_VERSION
ARG PHP_IMAGE=php:8.0.14-cli-alpine3.15

FROM ${RR_IMAGE} as rr

FROM ${PHP_IMAGE}

RUN apk update && apk add --no-cache \
  vim \
  libzip-dev \
  unzip \
  bash

# Install PHP Extensions
RUN docker-php-ext-install zip \
  && docker-php-ext-install sockets \
  && docker-php-ext-install opcache \
  && docker-php-ext-enable opcache \
  && docker-php-ext-install mysqli \
  && docker-php-ext-enable mysqli

# Copy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy RoadRunner
COPY --from=rr /usr/bin/rr /usr/bin/rr

# Copy RoadRunner config
COPY --from=rr /etc/rr.yaml /etc/rr.yaml

CMD ["/usr/bin/rr", "serve", "-d", "-c", "/etc/rr.yaml"]