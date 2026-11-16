<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('jenis', 20);
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('harga');
            $table->string('kategori', 30)->nullable();
            $table->string('hari', 10)->nullable();
            $table->string('foto', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['jenis', 'is_active']);
            $table->index('hari');
            $table->index('urutan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
