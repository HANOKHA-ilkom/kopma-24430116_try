<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->string('kode_pinjaman')->unique();
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->decimal('bunga_persen', 5, 2)->default(1.00);
            $table->integer('tenor_bulan');
            $table->decimal('angsuran_per_bulan', 15, 2);
            $table->decimal('sisa_pinjaman', 15, 2);
            $table->enum('status', ['pengajuan', 'disetujui', 'ditolak', 'lunas', 'macet'])->default('pengajuan');
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_disetujui')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('alasan_ditolak')->nullable();
            $table->timestamps();
        });

        Schema::create('angsuran_pinjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pinjaman_id')->constrained('pinjamans')->onDelete('cascade');
            $table->integer('angsuran_ke');
            $table->decimal('nominal_angsuran', 15, 2);
            $table->decimal('pokok', 15, 2);
            $table->decimal('bunga', 15, 2);
            $table->decimal('denda', 15, 2)->default(0);
            $table->date('tanggal_jatuh_tempo');
            $table->dateTime('tanggal_bayar')->nullable();
            $table->enum('status', ['belum_bayar', 'lunas', 'terlambat'])->default('belum_bayar');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('angsuran_pinjamans');
        Schema::dropIfExists('pinjamans');
    }
};
