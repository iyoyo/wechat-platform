#+++++++++++++++++++++++++++++++++++++++
# Dockerfile for iyoyo/wechat
#            -- 轻风 --
#+++++++++++++++++++++++++++++++++++++++

FROM registry.cn-hangzhou.aliyuncs.com/iyoyo/php:7.0

# 环境变量
ENV DOCUMENT_PUBLIC /data/www/public/public

# 复制文件
COPY dist/ $DOCUMENT_ROOT/

RUN set -ex \
    && cd $DOCUMENT_ROOT \
    # 安装模块
    && composer install \
    # 设置目录权限
    && chmod -R 777 bootstrap/cache storage \
    # 链接公开文件
    && ln -s $DOCUMENT_ROOT/storage/app/public public/storage

# 复制配置
COPY conf/ /opt/conf/