<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_taking_staff_registrations', function (Blueprint $table) {
            $table->string('id_number', 100)->nullable()->after('name_latin')->unique();
        });
    }

    public function down(): void
    {
        Schema::table('test_taking_staff_registrations', function (Blueprint $table) {
            $table->dropUnique(['id_number']);
            $table->dropColumn('id_number');
        });
    }
};
