<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SharedLogin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'shared_logins';

    protected $fillable = [
        'username',
        'password',
        'role',
        'region',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * INI JUGA PERLU DITAMBAHKAN!
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }
}