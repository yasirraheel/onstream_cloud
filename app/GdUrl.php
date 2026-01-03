<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GdUrl extends Model
{
    protected $table = 'gd_urls';

    protected $fillable = [
        'file_name', 
        'url', 
        'file_id',
        'folder_id',
        'file_size',
        'mime_type',
        'is_used'
    ];
}
