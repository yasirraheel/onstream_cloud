<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'message',
        'is_active',
        'show_as_popup',
        'view_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_as_popup' => 'boolean',
        'view_count' => 'integer'
    ];

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
}
