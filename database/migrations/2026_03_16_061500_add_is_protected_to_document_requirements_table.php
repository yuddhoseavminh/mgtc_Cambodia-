<?php

use App\Models\DocumentRequirement;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_requirements', function (Blueprint $table) {
            $table->boolean('is_protected')->default(false)->after('is_active');
        });

        DocumentRequirement::query()
            ->where('slug', DocumentRequirement::PROTECTED_TELEGRAM_REQUIREMENT_SLUG)
            ->update(['is_protected' => true]);
    }

    public function down(): void
    {
        Schema::table('document_requirements', function (Blueprint $table) {
            $table->dropColumn('is_protected');
        });
    }
};
