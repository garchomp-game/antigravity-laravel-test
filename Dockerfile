FROM php:8.3-fpm-alpine

# Build arguments for user mapping
ARG UID=1000
ARG GID=1000

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-dev \
    icu-dev \
    oniguruma-dev \
    linux-headers \
    shadow

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    opcache

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create application user with matching UID/GID
RUN addgroup -g ${GID} appgroup \
    && adduser -u ${UID} -G appgroup -D -s /bin/sh appuser

# Set working directory
WORKDIR /var/www/html

# Change ownership of working directory
RUN chown -R appuser:appgroup /var/www/html

# Switch to non-root user
USER appuser

# Expose port
EXPOSE 9000

CMD ["php-fpm"]
