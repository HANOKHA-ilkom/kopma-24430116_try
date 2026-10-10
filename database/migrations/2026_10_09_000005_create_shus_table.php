<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembagian_shus', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_buku', 4);
            $table->decimal('total_shu', 15, 2);
            $table->decimal('persen_jasa_modal', 5, 2)->default(40.00);
            $table->decimal('persen_jasa_usaha', 5, 2)->default(60.00);
            $table->enum('status', ['draft', 'final', 'didistribusikan'])->default('draft');
            $table->date('tanggal_pembagian')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_shu_anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembagian_shu_id')->constrained('pembagian_shus')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->decimal('shu_jasa_modal', 15, 2);
            $table->decimal('shu_jasa_usaha', 15, 2);
            $table->decimal('total_shu_diterima', 15, 2);
            $table->enum('status_pencairan', ['belum_dicairkan', 'sudah_dicairkan'])->default('belum_dicairkan');
            $table->dateTime('tanggal_pencairan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_shu_anggotas');
        Schema::dropIfExists('pembagian_shus');
    }
};
