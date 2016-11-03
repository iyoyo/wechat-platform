#### 下载代码
```
cd /data/www
git clone git@code.aliyun.com:iyoyocc/wechat-platform.git
cd wechat-platform
chmod +x *.sh
```

#### 修改配置文件
```
cp -r runtime.sample runtime
vim runtime/nginx.conf
vim runtime/.env
```

#### 链接配置文件
```
./link.sh
service nginx restart
```

# 接口调用说明

1. 公众号授权，请直接访问 http://wecom.ibrand.cc/wecom/auth ，根据提示完成授权。
1. 获取openid，请在应用中引导用户访问 http://wecom.ibrand.cc/wecom/wx4410337599121213/oauth?redirect=http://www.funtasy.com.cn/
   用户授权后，会将openid放在url后面跳转到redirect地址。
1. 获取用户信息 http://wecom.ibrand.cc/api/user/info?appid=wxdea745d4f3fc823c&openid=ovNizjljOb2KsS-XgyLYhJHQcPQo
1. 发送模版消息
   ```shell
   curl -H "Content-Type: application/json" -X POST -d '{"touser":"oTnA2wkreDPZ5WAfDGOgbaQJNQ8A","template_id":"M91Pk5uS17ujzUlzxq5q8KPSIxoEw1RF1Sc08seLXu8","data":{"productType":{"value":"Product","color":"#173177"},"name":{"value":"Summit","color":"#173177"},"number":{"value":"100","color":"#173177"}}}' http://wecom.ibrand.cc/api/notice/send?appid=wxdea745d4f3fc823c
   ```
   参考 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1433751277&token=&lang=zh_CN

授权
http://wechat.beta.ibd.so/platform/auth

Oauth
http://wechat.beta.ibd.so/api/oauth?appid=wxdea745d4f3fc823c&redirect=http://www.thenorhtface.com.cn

获取用户信息
http://wechat.beta.ibd.so/api/oauth/user?appid=wxdea745d4f3fc823c&openid=oTnA2wkreDPZ5WAfDGOgbaQJNQ8A