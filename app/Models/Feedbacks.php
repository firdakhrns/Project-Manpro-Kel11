<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedbacks extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cse_id',
        'dse_target', 
        'type',
        'message',
        'is_urgent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_urgent' => 'boolean',
    ];

    public function dseTarget(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dse_target', 'id_dse');
    }
}