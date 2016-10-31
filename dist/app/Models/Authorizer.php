<?php

namespace Wechat\Models;

use Illuminate\Database\Eloquent\Model;

class Authorizer extends Model
{
    protected $table = 'authorizers';
    protected $guarded = ['id'];
}
