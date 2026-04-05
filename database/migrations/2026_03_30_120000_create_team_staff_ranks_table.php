<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_staff_ranks', function (Blueprint $table) {
            $table->id();
            $table->string('name_kh');
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $existingRanks = DB::table('team_staff')
            ->whereNotNull('military_rank')
            ->select('military_rank')
            ->distinct()
            ->orderBy('military_rank')
            ->pluck('military_rank')
            ->map(fn ($rank) => trim((string) $rank))
            ->filter()
            ->values();

        if ($existingRanks->isEmpty()) {
            return;
        }

        $now = now();

        DB::table('team_staff_ranks')->insert(
            $existingRanks
                ->map(fn (string $rank, int $index) => [
                    'name_kh' => $rank,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
                ->all()
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('team_staff_ranks');
    }
};
