#!/usr/bin/env bash

# 执行更新
cd $DOCUMENT_ROOT/

if [ -f .env ]; then
    php artisan migrate --force
fi