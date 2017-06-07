<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Models;

use Illuminate\Database\Eloquent\Model;

class Oauth2Token extends Model
{
    protected $table = 'oauth2_tokens';
    protected $guarded = ['id'];
}
