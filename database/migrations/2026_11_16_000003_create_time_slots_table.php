<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unsignedSmallInteger('kapasitas')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'jam_mulai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
