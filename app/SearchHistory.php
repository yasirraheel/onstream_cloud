<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    protected $table = 'search_history';

    protected $fillable = ['keyword', 'user_id', 'ip_address', 'country', 'country_code'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
