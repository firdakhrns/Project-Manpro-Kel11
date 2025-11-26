<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
        $table->id();
        $table->string('cse_id'); // Yang ngasih feedback
        $table->string('dse_target'); // DSE yang dikasih feedback  
        $table->enum('type', ['kritik', 'saran']);
        $table->text('message');
        $table->boolean('is_urgent')->default(false);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
