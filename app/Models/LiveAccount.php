<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveAccount extends Model
{
    use HasFactory;

    protected $table = 'live_accounts';
    protected $primaryKey = 'user_id';
    public $incrementing = false; 
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'shopee_user_name',
        'affiliate_id',
        'cookies',
        'status'
    ];

    public function studio (){
        return $this->belongsTo(Studio::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'live_account_id', 'user_id');
    }
}

