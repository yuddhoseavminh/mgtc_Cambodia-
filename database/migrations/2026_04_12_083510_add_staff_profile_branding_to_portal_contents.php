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
        Schema::table('portal_contents', function (Blueprint $table) {
            $table->string('staff_logo_path')->nullable();
            $table->string('staff_logo_original_name')->nullable();
            $table->string('staff_title')->nullable();
            $table->string('staff_subtitle')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('portal_contents', function (Blueprint $table) {
            $table->dropColumn([
                'staff_logo_path',
                'staff_logo_original_name',
                'staff_title',
                'staff_subtitle'
            ]);
        });
    }
};
