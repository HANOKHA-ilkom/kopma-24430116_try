<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nomor_anggota')->unique();
            $table->string('nim')->unique();
            $table->string('program_studi');
            $table->string('fakultas');
            $table->string('angkatan', 4);
            $table->enum('status', ['aktif', 'non_aktif', 'alumni'])->default('aktif');
            $table->date('tanggal_bergabung');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
