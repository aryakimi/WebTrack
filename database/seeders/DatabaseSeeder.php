<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;       
use Illuminate\Support\Facades\Hash;    
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         // 1. Bikin Akun Admin (Pemilik Web)
        DB::table('admins')->insert([
            'nama' => 'Pemilik WebTrack',
            'email' => 'admin@webtrack.com',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Bikin Akun Team Member
        DB::table('team_members')->insert([
            'nama_member' => 'Andi Pratama',
            'email' => 'andi@webtrack.com',
            'password' => Hash::make('password123'),
            'posisi' => 'Frontend Developer',
            'status' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
