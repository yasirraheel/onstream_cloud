<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CountryRestriction extends Model
{
    protected $fillable = [
        'country_code',
        'country_name',
        'is_blocked'
    ];

    protected $casts = [
        'is_blocked' => 'boolean'
    ];

    public static function isCountryBlocked($countryCode)
    {
        $restriction = self::where('country_code', $countryCode)
            ->where('is_blocked', 1)
            ->first();
        
        return $restriction ? true : false;
    }

    public static function getBlockedCountries()
    {
        return self::where('is_blocked', 1)->pluck('country_code')->toArray();
    }
}
