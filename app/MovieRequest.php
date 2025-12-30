<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovieRequest extends Model
{
    protected $table = 'movie_requests';

    protected $fillable = ['user_id', 'movie_name', 'language', 'message', 'email', 'status'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
