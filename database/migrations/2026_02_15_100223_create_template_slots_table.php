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
        Schema::create('template_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('slot_number');
            $table->decimal('x_percent', 5, 2)->default(0);
            $table->decimal('y_percent', 5, 2)->default(0);
            $table->decimal('width_percent', 5, 2)->default(30);
            $table->decimal('height_percent', 5, 2)->default(40);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_slots');
    }
};
