<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_simpanans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('tipe', ['pokok', 'wajib', 'sukarela']);
            $table->decimal('nominal_minimal', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('simpanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->foreignId('jenis_simpanan_id')->constrained('jenis_simpanans')->onDelete('cascade');
            $table->string('nomor_rekening')->unique();
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('transaksi_simpanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simpanan_id')->constrained('simpanans')->onDelete('cascade');
            $table->string('kode_transaksi')->unique();
            $table->enum('jenis_transaksi', ['setoran', 'penarikan']);
            $table->decimal('jumlah', 15, 2);
            $table->dateTime('tanggal_transaksi');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_simpanans');
        Schema::dropIfExists('simpanans');
        Schema::dropIfExists('jenis_simpanans');
    }
};
