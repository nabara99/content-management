<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->decimal('title_x_percent', 5, 2)->default(50)->after('caption_font_color');
            $table->decimal('title_y_percent', 5, 2)->default(85)->after('title_x_percent');
            $table->decimal('caption_x_percent', 5, 2)->default(50)->after('title_y_percent');
            $table->decimal('caption_y_percent', 5, 2)->default(92)->after('caption_x_percent');
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn(['title_x_percent', 'title_y_percent', 'caption_x_percent', 'caption_y_percent']);
        });
    }
};
