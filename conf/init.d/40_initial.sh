#!/usr/bin/env bash

cd $DATA_SHARE

# 复制配置文件
if [ ! -f env ]; then
    cp -r $DOCUMENT_ROOT/.env.example ./env
fi

# 复制存储目录
if [ ! -d storage ]; then
    cp -r $DOCUMENT_ROOT/storage ./
fi

cd $DOCUMENT_ROOT

# 做好软连接
ln -sf $DATA_SHARE/env .env

rm -rf storage
ln -sf $DATA_SHARE/storage storage