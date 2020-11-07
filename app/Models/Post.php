<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'post';

    public function getImageAttribute($value)
    {
        if ($value != null) {
            return '\images\posts\\' . $value;
        }
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function postStatus()
    {
        return $this->hasOne('App\Models\PostStatus', 'postId');
    }

    public function bids()
    {
        return $this->hasMany('App\Models\Bid', 'postId')->with('user:id,first_name,last_name')->orderBy('id', 'desc')->take(3);
    }

    public function bidsForSinglePost()
    {
        return $this->hasMany('App\Models\Bid', 'postId')->with('user:id,first_name,last_name,remember_token')->orderBy('id', 'desc');
    }
}
