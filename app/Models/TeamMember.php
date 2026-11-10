<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // <-- INI WAJIB
use Illuminate\Notifications\Notifiable;

class TeamMember extends Authenticatable // <-- INI JUGA WAJIB
{
    use HasFactory, Notifiable;

    protected $table = 'team_members';
    protected $primaryKey = 'id_member';

    protected $fillable = [
        'nama_member',
        'email',
        'password',
        'posisi',
        'status',
    ];

    protected $hidden = [
        'password',
    ];
}