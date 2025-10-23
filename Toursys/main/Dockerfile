# 使用官方PHP运行环境作为基础镜像
FROM php:8.1-apache

# 设置工作目录
WORKDIR /var/www/html
#必要依赖
RUN apt-get update && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev libzip-dev zlib1g-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp 

# 安装PHP扩展
RUN docker-php-ext-install pdo pdo_mysql mysqli gd

# 启用Apache的重写模块
RUN a2enmod rewrite

# 创建目录并设置权限
RUN mkdir -p /var/www/html/images
RUN mkdir -p /var/www/html/img
RUN mkdir -p /var/www/html/admin
RUN mkdir -p /var/www/html/avatars
RUN chown -R www-data:www-data /var/www/html/images
RUN chown -R www-data:www-data /var/www/html/img
RUN chown -R www-data:www-data /var/www/html/admin
RUN chown -R www-data:www-data /var/www/html/avatars
RUN chmod -R 775 /var/www/html/images
RUN chmod -R 775 /var/www/html/img
RUN chmod -R 775 /var/www/html/admin
RUN chmod -R 775 /var/www/html/avatars

# 复制项目文件到容器
COPY . /var/www/html

# 设置文件权限
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
RUN chmod 775 /var/www/html/images
RUN chmod 775 /var/www/html/img
RUN chmod 775 /var/www/html/admin
RUN chmod 775 /var/www/html/avatars

# 设置PHP上传限制
RUN echo "file_uploads = On" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "upload_max_filesize = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# 暴露端口
EXPOSE 80

# 启动Apache服务
CMD ["apache2-foreground"]
