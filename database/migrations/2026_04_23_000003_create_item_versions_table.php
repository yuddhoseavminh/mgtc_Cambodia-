<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->string('image_path')->nullable();
            $table->string('image_filename')->nullable();
            $table->unsignedInteger('version_no');
            $table->boolean('is_current')->default(false)->index();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('change_note')->nullable();
            $table->timestamps();

            $table->unique(['item_id', 'version_no']);
            $table->index(['item_id', 'is_current']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_versions');
    }
};
