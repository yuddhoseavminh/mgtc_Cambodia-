<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portal_contents', function (Blueprint $table) {
            $table->string('course_page_title')->nullable()->after('feature_three_description');
            $table->string('course_page_subtitle')->nullable()->after('course_page_title');
            $table->text('course_page_description')->nullable()->after('course_page_subtitle');
            $table->string('test_taking_staff_page_title')->nullable()->after('course_page_description');
            $table->string('test_taking_staff_page_subtitle')->nullable()->after('test_taking_staff_page_title');
            $table->text('test_taking_staff_page_description')->nullable()->after('test_taking_staff_page_subtitle');
        });
    }

    public function down(): void
    {
        Schema::table('portal_contents', function (Blueprint $table) {
            $table->dropColumn([
                'course_page_title',
                'course_page_subtitle',
                'course_page_description',
                'test_taking_staff_page_title',
                'test_taking_staff_page_subtitle',
                'test_taking_staff_page_description',
            ]);
        });
    }
};
