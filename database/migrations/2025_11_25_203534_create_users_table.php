<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // id
            $table->string('name', 100); // name
            $table->string('id_dse', 100)->unique(); // id_dse (Pengganti email untuk login)
            $table->string('password', 255); // password
            $table->enum('role', ['DSE', 'Admin', 'Manajer'])->default('DSE'); // role
            $table->string('region', 50)->nullable(); // region
            $table->timestamps(); // created_at, updated_at
            $table->rememberToken(); // Tambahan untuk otentikasi
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};