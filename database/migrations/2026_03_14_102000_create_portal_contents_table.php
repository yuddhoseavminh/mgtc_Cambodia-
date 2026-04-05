<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_contents', function (Blueprint $table) {
            $table->id();
            $table->string('badge');
            $table->string('title');
            $table->text('description');
            $table->string('feature_one_title');
            $table->text('feature_one_description');
            $table->string('feature_two_title');
            $table->text('feature_two_description');
            $table->string('feature_three_title');
            $table->text('feature_three_description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_contents');
    }
};
