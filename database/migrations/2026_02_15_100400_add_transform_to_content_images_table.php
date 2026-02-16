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
        Schema::table('content_images', function (Blueprint $table) {
            $table->float('offset_x')->default(0)->after('slot_number');
            $table->float('offset_y')->default(0)->after('offset_x');
            $table->float('scale')->default(1)->after('offset_y');
        });
    }

    public function down(): void
    {
        Schema::table('content_images', function (Blueprint $table) {
            $table->dropColumn(['offset_x', 'offset_y', 'scale']);
        });
    }
};
