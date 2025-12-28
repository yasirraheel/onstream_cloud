<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdClickLog extends Model
{
    protected $fillable = ['product_id', 'clicked_at', 'ip_address', 'user_agent'];

    public $timestamps = false;

    /**
     * Log a click for a product
     */
    public static function logClick($productId, $ipAddress = null, $userAgent = null)
    {
        return self::create([
            'product_id' => $productId,
            'clicked_at' => now(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    /**
     * Get total click count for a product
     */
    public static function getTotalClicks($productId)
    {
        return self::where('product_id', $productId)->count();
    }

    /**
     * Get click count for a product in the last X minutes
     */
    public static function getClicksInLastMinutes($productId, $minutes)
    {
        return self::where('product_id', $productId)
            ->where('clicked_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Get click statistics for a product
     */
    public static function getClickStats($productId)
    {
        $total = self::getTotalClicks($productId);
        $last5min = self::getClicksInLastMinutes($productId, 5);
        $last30min = self::getClicksInLastMinutes($productId, 30);

        return [
            'total' => $total,
            'last_5_min' => $last5min,
            'last_30_min' => $last30min,
        ];
    }

    /**
     * Get all product stats in bulk
     */
    public static function getAllProductStats()
    {
        $stats = [];

        // Get all product IDs
        $productIds = self::distinct()->pluck('product_id');

        foreach ($productIds as $productId) {
            $stats[$productId] = self::getClickStats($productId);
        }

        return $stats;
    }

    /**
     * Get overall statistics for all products combined
     */
    public static function getOverallStats()
    {
        $total = self::count();
        $last5min = self::where('clicked_at', '>=', now()->subMinutes(5))->count();
        $last30min = self::where('clicked_at', '>=', now()->subMinutes(30))->count();

        return [
            'total' => $total,
            'last_5_min' => $last5min,
            'last_30_min' => $last30min,
        ];
    }
}
