<?php

use App\Models\Course;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('is_protected')->default(false)->after('is_active');
        });

        Course::query()
            ->where('name', Course::PROTECTED_DEFAULT_COURSE_NAME)
            ->update(['is_protected' => true]);
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('is_protected');
        });
    }
};
