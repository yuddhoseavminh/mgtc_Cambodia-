<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('name_kh');
            $table->string('name_en');
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_requirement_id')->constrained()->restrictOnDelete();
            $table->string('status');
            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->timestamps();

            $table->unique(['application_id', 'document_requirement_id'], 'app_docs_app_req_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_documents');
        Schema::dropIfExists('document_requirements');
    }
};
