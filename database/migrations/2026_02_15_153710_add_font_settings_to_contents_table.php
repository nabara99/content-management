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
        Schema::table('contents', function (Blueprint $table) {
            $table->string('title_font_family')->default('Arial')->after('caption');
            $table->unsignedSmallInteger('title_font_size')->default(24)->after('title_font_family');
            $table->boolean('title_font_bold')->default(false)->after('title_font_size');
            $table->boolean('title_font_italic')->default(false)->after('title_font_bold');
            $table->boolean('title_font_underline')->default(false)->after('title_font_italic');
            $table->string('title_font_color', 7)->default('#000000')->after('title_font_underline');

            $table->string('caption_font_family')->default('Arial')->after('title_font_color');
            $table->unsignedSmallInteger('caption_font_size')->default(16)->after('caption_font_family');
            $table->boolean('caption_font_bold')->default(false)->after('caption_font_size');
            $table->boolean('caption_font_italic')->default(false)->after('caption_font_bold');
            $table->boolean('caption_font_underline')->default(false)->after('caption_font_italic');
            $table->string('caption_font_color', 7)->default('#000000')->after('caption_font_underline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn([
                'title_font_family', 'title_font_size', 'title_font_bold',
                'title_font_italic', 'title_font_underline', 'title_font_color',
                'caption_font_family', 'caption_font_size', 'caption_font_bold',
                'caption_font_italic', 'caption_font_underline', 'caption_font_color',
            ]);
        });
    }
};
