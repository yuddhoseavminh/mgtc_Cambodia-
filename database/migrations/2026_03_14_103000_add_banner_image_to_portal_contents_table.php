<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portal_contents', function (Blueprint $table) {
            $table->string('banner_image_path')->nullable()->after('description');
            $table->string('banner_image_original_name')->nullable()->after('banner_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('portal_contents', function (Blueprint $table) {
            $table->dropColumn([
                'banner_image_path',
                'banner_image_original_name',
            ]);
        });
    }
};
