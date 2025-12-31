<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiUrl extends Model
{
    protected $table = 'api_urls';

    protected $fillable = [
        'movie_name', 
        'url', 
        'is_used'
    ];
}
