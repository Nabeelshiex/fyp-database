<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $table = 'bid';

    public function user()
    {
        return $this->belongsTo('App\User', 'userId');
    }

    public function post()
    {
        return $this->belongsTo('App\Models\Post', 'postId');
    }
}
