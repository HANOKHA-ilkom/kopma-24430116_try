<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_produks', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_produk_id')->constrained('kategori_produks')->onDelete('cascade');
            $table->string('kode_produk')->unique();
            $table->string('nama_produk');
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('harga_jual', 15, 2);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimal')->default(5);
            $table->string('satuan')->default('pcs');
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('set null');
            $table->foreignId('kasir_id')->constrained('users')->onDelete('cascade');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('total_akhir', 15, 2);
            $table->decimal('bayar', 15, 2);
            $table->decimal('kembali', 15, 2);
            $table->enum('metode_pembayaran', ['tunai', 'qris', 'saldo_kopma', 'transfer'])->default('tunai');
            $table->enum('status_pembayaran', ['lunas', 'pending', 'batal'])->default('lunas');
            $table->dateTime('tanggal_transaksi');
            $table->timestamps();
        });

        Schema::create('detail_penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualans')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->decimal('harga_satuan', 15, 2);
            $table->integer('jumlah');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penjualans');
        Schema::dropIfExists('penjualans');
        Schema::dropIfExists('produks');
        Schema::dropIfExists('kategori_produks');
    }
};
