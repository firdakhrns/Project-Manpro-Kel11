<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_logs', function (Blueprint $table) {
            $table->id(); // id
            $table->string('username_id', 50); // username_id (FK ke id_dse/username)
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade'); // outlet_id
            $table->date('date'); // date
            $table->decimal('total_sales', 12, 2); // total_sales
            $table->timestamps(); // created_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_logs');
    }
};