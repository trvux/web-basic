FROM php:8-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli

# Bản chạy thật: code nằm sẵn trong image.
# Khi dev, docker-compose.yml mount ./src đè lên nên sửa code vẫn thấy ngay.
COPY src/ /var/www/html/
