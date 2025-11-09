<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'outlet_id',
        'date',
        'total_sales',
    ];
}
