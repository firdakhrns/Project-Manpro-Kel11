<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'username_id',
        'outlet_id',
        'date',
        'total_sales',
    ];

    protected $casts = [
        'date' => 'date',
        'total_sales' => 'decimal:2',
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
}