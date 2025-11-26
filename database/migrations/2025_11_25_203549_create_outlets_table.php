<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('address')->nullable();
            $table->string('owner_name', 100);
            $table->string('phone', 20)->nullable(); // TAMBAH
            $table->string('emergency_phone', 20)->nullable(); // TAMBAH
            $table->date('join_date')->nullable(); // TAMBAH
            $table->string('front_photo')->nullable(); // TAMBAH
            $table->string('display_photo')->nullable(); // TAMBAH
            $table->enum('status', ['Aktif', 'Non-Aktif'])->default('Aktif');
            $table->string('region', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};