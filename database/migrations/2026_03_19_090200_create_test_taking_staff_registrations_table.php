<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_taking_staff_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('test_taking_staff_rank_id');
            $table->string('name_kh');
            $table->string('name_latin');
            $table->date('date_of_birth');
            $table->date('military_service_day');
            $table->string('phone_number', 30);
            $table->string('avatar_path');
            $table->string('avatar_original_name')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->foreign('test_taking_staff_rank_id', 'tts_reg_rank_fk')
                ->references('id')
                ->on('test_taking_staff_ranks')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_taking_staff_registrations');
    }
};
