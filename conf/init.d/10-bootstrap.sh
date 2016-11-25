#!/usr/bin/env bash

cd $DOCUMENT_ROOT/

# 生成配置文件
cp .env.example .env
replaceEnv .env

# 部署本地存储
if [ ! -d $DATA_SHARE/storage ]; then
    cp -r storage $DATA_SHARE/
fi
rm -rf storage
ln -s $DATA_SHARE/storage storage

# 更新数据库
php artisan migrate --force

# 设置权限
chown -R www:www storage bootstrap/cache
