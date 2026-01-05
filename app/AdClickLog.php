<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdClickLog extends Model
{
    protected $table = 'ad_click_logs';

    public $timestamps = false; // Using clicked_at manually or we can set it to true if we map it

    protected $fillable = ['product_id', 'clicked_at', 'ip_address', 'user_agent'];

    protected $dates = ['clicked_at'];

    /**
     * Log a click event
     */
    public static function logClick($productId, $ipAddress = null, $userAgent = null)
    {
        return self::create([
            'product_id' => $productId,
            'clicked_at' => now(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);
    }

    /**
     * Get overall statistics
     */
    public static function getOverallStats()
    {
        return [
            'total' => self::count(),
            'last_30_min' => self::where('clicked_at', '>=', now()->subMinutes(30))->count(),
            'last_5_min' => self::where('clicked_at', '>=', now()->subMinutes(5))->count(),
        ];
    }
}
