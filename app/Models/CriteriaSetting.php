<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriteriaSetting extends Model
{
    protected $fillable = [
        'column_name',
        'weight',
        'type',
    ];
}
