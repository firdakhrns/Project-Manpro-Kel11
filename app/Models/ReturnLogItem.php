<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnLogItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_log_id',
        'product_id',
        'quantity',
    ];

    // Relasi dengan ReturnLog
    public function returnLog()
    {
        return $this->belongsTo(ReturnLog::class);
    }

    // Relasi dengan Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}