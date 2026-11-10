<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id('id_task'); // PK
            $table->unsignedBigInteger('id_project'); // FK
            $table->unsignedBigInteger('id_member')->nullable(); // FK (boleh kosong)
            $table->string('nama_task');
            $table->text('deskripsi')->nullable();
            $table->date('deadline');
            $table->string('status')->default('To Do');
            $table->integer('bobot')->default(0);
            $table->string('prasyarat')->nullable(); // Buat fitur Task Dependency
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};