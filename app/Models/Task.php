<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    
    protected $table = 'tasks';
    protected $primaryKey = 'id_task';
    
    protected $fillable = [
        'id_project', 
        'id_member', 
        'nama_task', 
        'deskripsi', 
        'deadline', 
        'status', 
        'bobot', 
        'prasyarat'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project', 'id_project');
    }

    public function member()
    {
        return $this->belongsTo(TeamMember::class, 'id_member', 'id_member');
    }
}