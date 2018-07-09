FROM php:7.1-fpm-alpine

RUN  apk update && apk add openrc &&\
# Tell openrc its running inside a container, till now that has meant LXC
    sed -i 's/#rc_sys=""/rc_sys="lxc"/g' /etc/rc.conf &&\
# Tell openrc loopback and net are already there, since docker handles the networking
    echo 'rc_provide="loopback net"' >> /etc/rc.conf &&\
# no need for loggers
    sed -i 's/^#\(rc_logger="YES"\)$/\1/' /etc/rc.conf &&\
# can't get ttys unless you run the container in privileged mode
    sed -i '/tty/d' /etc/inittab &&\
# can't set hostname since docker sets it
    sed -i 's/hostname $opts/# hostname $opts/g' /etc/init.d/hostname &&\
# can't mount tmpfs since not privileged
    sed -i 's/mount -t tmpfs/# mount -t tmpfs/g' /lib/rc/sh/init.sh &&\
# can't do cgroups
    sed -i 's/cgroup_add_service /# cgroup_add_service /g' /lib/rc/sh/openrc-run.sh &&\
# clean apk cache
    rm -rf /var/cache/apk/*
CMD ["/sbin/init"]

# docker-entrypoint.sh dependencies
RUN apk add --no-cache \
# in theory, docker-entrypoint.sh is POSIX-compliant, but priority is a working, consistent image
		bash \
# BusyBox sed is not sufficient for some of our sed expressions
		sed

# install the PHP extensions we need
RUN set -ex; \
	\
	apk add --update --no-cache --virtual .build-deps \
		libjpeg-turbo-dev \
		libpng-dev \
	; \
	\
	docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr; \
	docker-php-ext-install gd mysqli opcache zip; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --virtual .wordpress-phpexts-rundeps $runDeps; \
	apk del .build-deps

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=2'; \
		echo 'opcache.fast_shutdown=1'; \
		echo 'opcache.enable_cli=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

VOLUME /var/www/html

ENV WORDPRESS_VERSION 4.9.7

RUN set -ex; \
	curl -o wordpress.tar.gz -fSL "https://wordpress.org/wordpress-${WORDPRESS_VERSION}.tar.gz"; \
# upstream tarballs include ./wordpress/ so this gives us /usr/src/wordpress
	tar -xzf wordpress.tar.gz -C /usr/src/; \
	rm wordpress.tar.gz; \
	chown -R www-data:www-data /usr/src/wordpress

RUN apk add nginx

RUN mkdir /etc/nginx/sites-available && \
	mkdir /etc/nginx/sites-enabled

COPY ./wordpress /etc/nginx/sites-available

COPY ./nginx.conf /etc/nginx/

COPY ./www.conf /usr/local/etc/php-fpm.d/

COPY ./wp-config.php /var/www/html

COPY ./php.ini /usr/local/etc/php/

RUN	mkdir /var/www/logs && \
	mkdir -p /run/nginx && \
	ln -s /etc/nginx/sites-available/wordpress /etc/nginx/sites-enabled/wordpress

COPY ./docker-entrypoint.sh /usr/local/bin/

ADD amazon-s3-and-cloudfront /var/www/html/wp-content/plugins/amazon-s3-and-cloudfront/

RUN chmod +x /usr/local/bin/docker-entrypoint.sh && \
	chown -R www-data:www-data /var/www/html/wp-content && \
	touch /var/log/fpm-php.www.log && \
	chmod 777 /var/log/fpm-php.www.log

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]