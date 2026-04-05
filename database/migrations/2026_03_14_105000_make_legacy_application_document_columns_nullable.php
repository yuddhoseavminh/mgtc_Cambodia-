<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('id_card_path')->nullable()->change();
            $table->string('id_card_original_name')->nullable()->change();
            $table->string('family_book_path')->nullable()->change();
            $table->string('family_book_original_name')->nullable()->change();
            $table->string('certificate_path')->nullable()->change();
            $table->string('certificate_original_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('id_card_path')->nullable(false)->change();
            $table->string('id_card_original_name')->nullable(false)->change();
            $table->string('family_book_path')->nullable(false)->change();
            $table->string('family_book_original_name')->nullable(false)->change();
            $table->string('certificate_path')->nullable(false)->change();
            $table->string('certificate_original_name')->nullable(false)->change();
        });
    }
};
