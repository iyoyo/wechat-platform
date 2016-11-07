#+++++++++++++++++++++++++++++++++++++++
# Dockerfile for iyoyo/wechat-platform
#            -- 轻风 --
#+++++++++++++++++++++++++++++++++++++++

FROM iyoyo/php:centos-7

ENV PHP_VERSION 7.0.12

# 安装依赖
RUN yum -y install gcc gcc-c++ autoconf automake libtool make \
    libxml2-devel openssl-devel curl-devel file-devel freetype-devel libmcrypt-devel libtidy-devel \
    libjpeg-devel libpng-devel \

    # 下载源码
    && mkdir -p /opt/src \
    && cd /opt/src \
    && wget http://cn2.php.net/get/php-$PHP_VERSION.tar.gz/from/this/mirror -O php-$PHP_VERSION.tar.gz \
    && tar zxvf php-$PHP_VERSION.tar.gz \

    # 编译安装
    && cd /opt/src/php-$PHP_VERSION \
    && ./configure \
        --prefix=/opt/php \
        --with-config-file-path=/opt/conf/php \
        --with-config-file-scan-dir=/opt/conf/php/conf.d \
        # MYSQL
        --with-mysqli=shared,mysqlnd \
        --with-pdo-mysql=shared,mysqlnd \
        # FPM
        --enable-fpm \
        --with-fpm-user=www \
        --with-fpm-group=www \
        # 禁用
        --disable-cgi \
        --disable-phpdbg \
        # 其他模块
        --with-mcrypt=/usr/include \
        --with-mhash \
        --with-openssl \
        --with-gd \
        --with-iconv \
        --with-zlib \
        --enable-zip \
        --enable-inline-optimization \
        --disable-debug \
        --disable-rpath \
        --enable-shared \
        --enable-xml \
        --enable-bcmath \
        --enable-shmop \
        --enable-sysvsem \
        --enable-mbregex \
        --enable-mbstring \
        --enable-ftp \
        --enable-gd-native-ttf \
        --enable-pcntl \
        --enable-sockets \
        --with-xmlrpc \
        --enable-soap \
        --without-pear \
        --with-gettext \
        --enable-session \
        --with-curl \
        --with-jpeg-dir \
        --with-freetype-dir \
        --enable-opcache \
        --without-gdbm \
        --disable-fileinfo \
        --with-tidy \
    && make \
    && make install \

    # 瘦身
    && yum -y autoremove gcc gcc-c++ autoconf automake libtool make \
    && yum clean all \
    && rm -rf /opt/src

# 复制配置文件
COPY conf/ /opt/conf/
COPY public/ /data/www/public/