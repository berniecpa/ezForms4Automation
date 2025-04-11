FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql

# Enable Apache modules - especially rewrite
RUN a2enmod rewrite

# Create Apache configuration specifically to enable .htaccess files
RUN { \
    echo '<Directory /var/www/html>'; \
    echo '  Options Indexes FollowSymLinks'; \
    echo '  AllowOverride All'; \
    echo '  Require all granted'; \
    echo '</Directory>'; \
} > /etc/apache2/conf-available/htaccess-override.conf \
&& a2enconf htaccess-override

# Copy PHP files - this step happens when building the image
COPY . /var/www/html/

# Grant access to the webserver user
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# Restart Apache to apply changes
CMD ["apache2-foreground"]