<?php

return [
    /**
     * AppID
     */
    'app_id' => env('WECHAT_APP_ID', ''),

    /**
     * AppSecret
     */
    'secret' => env('WECHAT_SECRET', ''),

    /**
     * 公众号消息校验Token
     */
    'token' => env('WECHAT_TOKEN', ''),

    /**
     * 公众号消息加解密Key
     */
    'key' => env('WECHAT_KEY', ''),
];