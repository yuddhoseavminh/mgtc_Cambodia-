<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portal_contents', function (Blueprint $table) {
            $table->string('course_page_banner_image_path')->nullable()->after('course_page_description');
            $table->string('course_page_banner_image_original_name')->nullable()->after('course_page_banner_image_path');
            $table->string('test_taking_staff_page_banner_image_path')->nullable()->after('test_taking_staff_page_description');
            $table->string('test_taking_staff_page_banner_image_original_name')->nullable()->after('test_taking_staff_page_banner_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('portal_contents', function (Blueprint $table) {
            $table->dropColumn([
                'course_page_banner_image_path',
                'course_page_banner_image_original_name',
                'test_taking_staff_page_banner_image_path',
                'test_taking_staff_page_banner_image_original_name',
            ]);
        });
    }
};
