<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('order_id', 50)->unique();
            $table->string('transaction_id', 100)->nullable();
            $table->string('snap_token', 255)->nullable();
            $table->string('snap_redirect_url', 255)->nullable();
            $table->string('metode_pembayaran', 50)->nullable();
            $table->unsignedInteger('gross_amount');
            $table->string('status', 20)->default('pending');
            $table->json('payload')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
