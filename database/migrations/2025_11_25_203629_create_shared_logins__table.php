<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shared_logins', function (Blueprint $table) {
            $table->id(); // id
            $table->string('username', 100)->unique(); // username (Kunci login Admin)
            $table->string('password', 255); // password
            $table->string('region', 50)->nullable(); // region
            $table->enum('role', ['Admin', 'Manajer', 'Finance'])->nullable(); // role
            $table->timestamps(); // created_at, updated_at
            $table->rememberToken();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shared_logins');
    }
};