<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'product_code',
        'price',
    ];

    // Relasi dengan StockLogItem
    public function stockLogItems()
    {
        return $this->hasMany(StockLogItem::class);
    }

    // Relasi dengan ReturnLogItem
    public function returnLogItems()
    {
        return $this->hasMany(ReturnLogItem::class);
    }
}