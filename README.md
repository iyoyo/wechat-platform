## 微信第三方平台

1. 基于 [overtrue/wechat](https://github.com/overtrue/wechat) 进行扩展开发
2. [微信第三方平台概述](https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419318292&token=&lang=zh_CN)

### 描述
实现了微信第三方平台的授权机制, 在公众号完成授权后, 可以使用本通道操作公众号API。暂未提供管理界面，只提供 API 接口说明，管理后台可自行开发


### 安装
1. 下载源码并安装
```
git clone git@github.com:iyoyo/wechat-platform.git
cd wechat-platform/dist
composer install -vvv
chmod -R 777 storage
chmod -R 777 bootstrap/cache
cp .env.example .env
php artisan key:generate
```
2. 配置 .env 
```
-- 微信开放平台账号管理中能够找到相关信息并配置好
WECHAT_APP_ID=
WECHAT_SECRET=
WECHAT_TOKEN=
WECHAT_AES_KEY=
```

3. 配置 nginx
```
server {

  listen 80;
  server_name wechat.platform.com; # 改成自己的域名
  access_log /data/wwwlogs/wechat-platform_nginx.log combined;
  index index.html index.htm index.php;
  root /data/wwwroot/wechat-platform/dist/public;

  include /usr/local/nginx/conf/rewrite/laravel.conf;  # laravel 重定向配置
  
  #error_page 404 /404.html;
  #error_page 502 /502.html;
  
  location ~ [^/]\.php(/|$) {
    #fastcgi_pass remote_php_ip:9000;
    fastcgi_pass unix:/dev/shm/php-cgi.sock;
    fastcgi_index index.php;
    include fastcgi.conf;
  }
  
  location ~ .*\.(gif|jpg|jpeg|png|bmp|swf|flv|mp4|ico)$ {
    expires 30d;
    access_log off;
  }
  
  location ~ .*\.(js|css)?$ {
    expires 7d;
    access_log off;
  }
  
  location ~ /\.ht {
    deny all;
  }
}

```
4. 创建数据库表
```
php artisan migrate
```
5. 生产 passport client
```
php artisan passport:install
php artisan passport:client --password
```

### 使用

#### 授权流程
1. 进入第三方授权页面，网址为 http://wechat.platform.com/platform/auth
2. 使用公众号绑定的个人微信号扫描二维码并完成授权
3. 页面跳转提示授权成功
