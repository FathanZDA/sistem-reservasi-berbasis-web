<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('label', 50)->nullable();
            $table->unsignedSmallInteger('min_orang');
            $table->unsignedSmallInteger('max_orang')->nullable();
            $table->unsignedInteger('tarif_per_orang');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_tiers');
    }
};
