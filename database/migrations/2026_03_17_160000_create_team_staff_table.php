<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sequence_no')->unique();
            $table->string('military_rank', 120);
            $table->string('name_kh', 255);
            $table->string('name_latin', 255);
            $table->string('id_number', 100)->unique();
            $table->string('avatar_path');
            $table->string('avatar_original_name')->nullable();
            $table->string('gender', 20);
            $table->string('position', 255);
            $table->string('role', 50);
            $table->string('phone_number', 30);
            $table->json('documents')->nullable();
            $table->timestamps();

            $table->index(['military_rank', 'role']);
            $table->index(['gender', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_staff');
    }
};
