<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'id_dse',
        'password',
        'role',
        'region',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * INI YANG PERLU DITAMBAHKAN!
     * Memberi tahu Laravel bahwa identifier-nya adalah id_dse, bukan email
     */
    public function getAuthIdentifierName()
    {
        return 'id_dse';
    }

    /**
     * Opsional: Untuk custom password field jika diperlukan
     */
    public function getAuthPassword()
    {
        return $this->password;
    }
}