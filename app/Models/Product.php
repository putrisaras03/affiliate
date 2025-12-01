<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'itemid';
    public $incrementing = false; // karena itemid bukan auto increment
    protected $keyType = 'int';   // atau 'string' kalau Shopee pakai string

    protected $fillable = [
        'live_account_id',
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
        'ctime',
    ];

    public function liveAccount()
    {
        return $this->belongsTo(LiveAccount::class, 'live_account_id', 'id');
    }

    // Accessor contoh
    protected $appends = ['commission_value'];
    public function getCommissionValueAttribute()
    {
        $price = $this->price_min ?? 0;
        $commission = $this->seller_commission ?? 0;

        return $price * ($commission / 100);
    }
}
