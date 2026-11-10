<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    
    protected $table = 'projects';
    
    // INI DIA TAMBAHANNYA: Kasih tau Laravel kalau PK-nya id_project
    protected $primaryKey = 'id_project'; 

    protected $fillable = [
        'id_admin', 
        'id_client', 
        'nama_project', 
        'deskripsi', 
        'tanggal_mulai', 
        'deadline', 
        'status'
    ];
}