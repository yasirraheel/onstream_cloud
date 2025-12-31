<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideoView extends Model
{
    protected $table = 'video_views';

    protected $fillable = [
        'video_id',
        'video_type',
        'user_id',
        'ip_address',
        'country',
        'country_code'
    ];
}
