# 微信第三方平台

## 描述
本项目实现了微信第三方平台的授权机制, 在公众号完成授权后, 可以使用本通道操作公众号API。

## 创建client_id与client_secret
依次执行下面两行命令
php artisan passport:install
php artisan passport:client --password