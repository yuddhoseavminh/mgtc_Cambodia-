<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_staff', function (Blueprint $table) {
            $table->date('date_of_enlistment')->nullable()->after('dob');
        });
    }

    public function down(): void
    {
        Schema::table('team_staff', function (Blueprint $table) {
            $table->dropColumn('date_of_enlistment');
        });
    }
};
