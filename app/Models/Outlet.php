<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'owner_name',
        'phone',
        'emergency_phone',
        'join_date',
        'front_photo',
        'display_photo',
        'status',
        'region',
    ];

    protected $casts = [
        'join_date' => 'date',
    ];

    // Relasi dengan StockLog
    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    // Relasi dengan ReturnLog
    public function returnLogs()
    {
        return $this->hasMany(ReturnLog::class);
    }

    // Relasi dengan SalesLog
    public function salesLogs()
    {
        return $this->hasMany(SalesLog::class);
    }
}