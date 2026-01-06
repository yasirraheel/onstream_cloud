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
        'view_count',
        'cta_click_count',
        'image',
        'cta_text',
        'cta_url',
        'cta_target'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_as_popup' => 'boolean',
        'view_count' => 'integer',
        'cta_click_count' => 'integer'
    ];

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function incrementCTAClickCount()
    {
        $this->increment('cta_click_count');
    }
}
