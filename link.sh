#!/bin/bash

#
# 链接文件目录
#

ROOT=$(cd `dirname $0`; pwd)

APP=${ROOT##*/}
RUNTIME=$ROOT/runtime
SOURCE=$ROOT/dist

# NGINX 配置文件
ln -sf $RUNTIME/nginx.conf /opt/nginx/vhosts/$APP.conf
ls -l /opt/nginx/vhosts/$APP.conf

# APP 配置文件
ln -sf $RUNTIME/.env $SOURCE/.env
ls -l $SOURCE/.env

# APP 数据目录
chmod -R 777 $SOURCE/storage $SOURCE/bootstrap/cache