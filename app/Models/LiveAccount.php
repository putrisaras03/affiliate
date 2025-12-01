<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveAccount extends Model
{
    use HasFactory;

    protected $table = 'live_accounts';

    // Hanya field yang bisa di mass assign
    protected $fillable = [
        'nama',
        'studio_id',
    ];

    // Relasi ke Studio
    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'live_account_id', 'id');
    }
}
