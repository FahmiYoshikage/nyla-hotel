FROM php:8.2-apache

# Enable Apache modules typically used by PHP apps
RUN a2enmod rewrite

# Install system dependencies and PHP extensions needed (mysqli, pdo_mysql, gd, zip, intl as common needs)
RUN apt-get update \
	 && apt-get install -y \
		 libjpeg62-turbo-dev \
		 libpng-dev \
		 libfreetype6-dev \
		 libzip-dev \
		 libonig-dev \
		 libicu-dev \
		 unzip \
	 && docker-php-ext-configure gd --with-freetype --with-jpeg \
	 && docker-php-ext-install \
		 gd \
		 mysqli \
		 pdo \
		 pdo_mysql \
		 intl \
		 zip \
	 && rm -rf /var/lib/apt/lists/*

# Set working dir to Apache docroot
WORKDIR /var/www/html

# Copy project files into the image
COPY . /var/www/html

# Ensure proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# Expose Apache port
EXPOSE 80

# Use Apache's default start command
CMD ["apache2-foreground"]
