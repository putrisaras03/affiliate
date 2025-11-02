<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'itemid';
    public $incrementing = false; // karena itemid bukan auto increment
    protected $keyType = 'unsignedBigInteger';

    protected $fillable = [
        'itemid',
        'name',
        'image',
        'product_link',
        'seller_commission',
        'historical_sold',
        'price_min',
        'price_max',
        'rating_star',
        'shop_rating',
    ];

    public function liveAccount()
    {
        return $this->belongsTo(LiveAccount::class, 'live_account_id', 'user_id');
    }

}
