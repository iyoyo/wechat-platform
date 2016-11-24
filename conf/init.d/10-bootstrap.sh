#!/usr/bin/env bash

cd $DOCUMENT_ROOT/

# 生成配置文件
cp .env.example .env
replaceEnv .env

# 更新数据库
php artisan migrate --force
