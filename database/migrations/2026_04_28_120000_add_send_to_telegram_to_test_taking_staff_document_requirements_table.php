<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_taking_staff_document_requirements', function (Blueprint $table) {
            $table->boolean('send_to_telegram')->default(true);
        });

        DB::table('test_taking_staff_document_requirements')
            ->whereNull('send_to_telegram')
            ->update(['send_to_telegram' => true]);
    }

    public function down(): void
    {
        Schema::table('test_taking_staff_document_requirements', function (Blueprint $table) {
            $table->dropColumn('send_to_telegram');
        });
    }
};

