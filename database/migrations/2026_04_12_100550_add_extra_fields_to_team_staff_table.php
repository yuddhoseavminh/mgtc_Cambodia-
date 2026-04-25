<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_staff', function (Blueprint $table) {
            $table->date('dob')->nullable()->after('phone_number');
            $table->string('pob')->nullable()->after('dob');
            $table->string('training_code')->nullable()->after('pob');
            $table->string('leader_ref')->nullable()->after('training_code');
            $table->string('origin_ref')->nullable()->after('leader_ref');
        });
    }

    public function down(): void
    {
        Schema::table('team_staff', function (Blueprint $table) {
            $table->dropColumn(['dob', 'pob', 'training_code', 'leader_ref', 'origin_ref']);
        });
    }
};
