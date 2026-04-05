<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->index('application_id');
            $table->index('document_requirement_id');
            $table->dropUnique('app_docs_app_req_unique');
        });
    }

    public function down(): void
    {
        Schema::table('application_documents', function (Blueprint $table) {
            $table->unique(['application_id', 'document_requirement_id'], 'app_docs_app_req_unique');
            $table->dropIndex(['application_id']);
            $table->dropIndex(['document_requirement_id']);
        });
    }
};
