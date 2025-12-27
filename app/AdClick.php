<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdClick extends Model
{
    protected $fillable = ['product_id', 'click_count'];

    /**
     * Increment click count for a product
     */
    public static function incrementClick($productId)
    {
        $adClick = self::firstOrCreate(
            ['product_id' => $productId],
            ['click_count' => 0]
        );

        $adClick->increment('click_count');

        return $adClick;
    }

    /**
     * Get click count for a product
     */
    public static function getClickCount($productId)
    {
        $adClick = self::where('product_id', $productId)->first();

        return $adClick ? $adClick->click_count : 0;
    }
}
