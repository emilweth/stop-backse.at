FROM ghcr.io/emilweth/symfony:8.0

# persistent / runtime deps
RUN apk add --no-cache \
    freetype \
	git \
    openssh-client \
    yarn \
	;

ARG APCU_VERSION=5.1.19
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		libzip-dev \
		icu-dev \
		zlib-dev \
	; \
	\
	docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
		intl \
		zip \
		mysqli \
		pdo \
		pdo_mysql \
	; \
	pecl install \
		apcu-${APCU_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
        mysqli \
        pdo \
        pdo_mysql \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .api-phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

# build for production
ARG APP_ENV=prod

# prevent the reinstallation of vendors at every changes in the source code
COPY --chown=webuser:webgroup composer.json composer.lock symfony.lock package.json yarn.lock ./

RUN composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
	composer clear-cache; \
	yarn install

# Handle file to exclude with a .dockerignore
COPY --chown=webuser:webgroup . .

RUN set -eu; \
    mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer run-script --no-dev post-install-cmd; \
	yarn build; \
	chown -R webuser:webgroup var; \
	chmod +x bin/console; \
	sync
