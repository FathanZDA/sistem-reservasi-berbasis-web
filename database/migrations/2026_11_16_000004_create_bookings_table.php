<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('kode_booking', 20)->unique();
            $table->string('jenis', 20);
            $table->string('nama_pelanggan', 100);
            $table->string('no_whatsapp', 20);
            $table->text('catatan')->nullable();
            $table->date('tanggal_reservasi');
            $table->foreignId('time_slot_id')->nullable()->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('jumlah_orang')->nullable();
            $table->unsignedInteger('total_harga');
            $table->string('status', 30)->default('menunggu_pembayaran');
            $table->text('alasan_pembatalan')->nullable();
            $table->dateTime('expires_at');
            $table->timestamps();

            $table->index('status');
            $table->index('tanggal_reservasi');
            $table->index(['tanggal_reservasi', 'time_slot_id']);
            $table->index('expires_at');
            $table->index('jenis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
