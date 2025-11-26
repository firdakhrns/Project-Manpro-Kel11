<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'username_id',
        'outlet_id',
        'date',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relasi dengan User (DSE)
    public function user()
    {
        return $this->belongsTo(User::class, 'username_id', 'id_dse');
    }

    // Relasi dengan Outlet
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    // Relasi dengan StockLogItem
    public function items()
    {
        return $this->hasMany(StockLogItem::class);
    }
}