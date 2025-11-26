<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLogItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_log_id',
        'product_id',
        'quantity',
    ];

    // Relasi dengan StockLog
    public function stockLog()
    {
        return $this->belongsTo(StockLog::class);
    }

    // Relasi dengan Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}