<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_taking_staff_registration_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('test_taking_staff_registration_id');
            $table->unsignedBigInteger('test_taking_staff_document_requirement_id');
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->timestamps();

            $table->foreign('test_taking_staff_registration_id', 'tts_reg_doc_reg_fk')
                ->references('id')
                ->on('test_taking_staff_registrations')
                ->cascadeOnDelete();

            $table->foreign('test_taking_staff_document_requirement_id', 'tts_reg_doc_req_fk')
                ->references('id')
                ->on('test_taking_staff_document_requirements')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_taking_staff_registration_documents');
    }
};
