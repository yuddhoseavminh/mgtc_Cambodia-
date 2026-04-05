<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        Schema::create('ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name_kh');
            $table->string('name_en');
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('duration');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cultural_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('khmer_name');
            $table->string('latin_name');
            $table->string('id_number');
            $table->foreignId('rank_id')->constrained()->restrictOnDelete();
            $table->date('date_of_birth');
            $table->date('date_of_enlistment');
            $table->string('position');
            $table->string('unit');
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->foreignId('cultural_level_id')->constrained()->restrictOnDelete();
            $table->string('place_of_birth');
            $table->text('current_address');
            $table->string('family_situation');
            $table->string('phone_number');
            $table->string('status')->default('Pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('id_card_path');
            $table->string('id_card_original_name');
            $table->string('family_book_path');
            $table->string('family_book_original_name');
            $table->string('certificate_path');
            $table->string('certificate_original_name');
            $table->string('other_document_path')->nullable();
            $table->string('other_document_original_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
        Schema::dropIfExists('cultural_levels');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('ranks');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
