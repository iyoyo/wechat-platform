#+++++++++++++++++++++++++++++++++++++++
# Dockerfile for iyoyo/wechat-platform
#            -- 轻风 --
#+++++++++++++++++++++++++++++++++++++++

FROM registry.cn-hangzhou.aliyuncs.com/iyoyo/php:centos-7

ENV APP_ENV production
ENV APP_DEBUG false
ENV APP_LOG_LEVEL debug
ENV APP_URL http://wechat.test.ibd.so/

ENV DB_HOST 127.0.0.1
ENV DB_PORT 3306
ENV DB_DATABASE wechat
ENV DB_USERNAME wechat
ENV DB_PASSWORD ''

ENV REDIS_HOST redis
ENV REDIS_PASSWORD null
ENV REDIS_PORT_1 6379

ENV WECHAT_APP_ID ''
ENV WECHAT_SECRET ''
ENV WECHAT_TOKEN ''
ENV WECHAT_AES_KEY ''

# 复制文件
COPY dist/ $DOCUMENT_ROOT/
RUN set -ex \
    && cd $DOCUMENT_ROOT \
    && composer install

# 复制配置
COPY conf/ /opt/conf/