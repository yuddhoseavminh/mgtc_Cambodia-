<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $usedUsernames = [];

        DB::table('team_staff')
            ->orderBy('id')
            ->get()
            ->each(function (object $staff) use (&$usedUsernames): void {
                $base = Str::of((string) ($staff->name_latin ?? ''))
                    ->replaceMatches('/\s+/', ' ')
                    ->trim()
                    ->limit(120, '')
                    ->value();

                if ($base === '') {
                    $base = 'Staff';
                }

                $username = $base;
                $suffix = 2;

                while (in_array($username, $usedUsernames, true)) {
                    $suffixLabel = ' '.$suffix;
                    $username = Str::of($base)
                        ->limit(120 - strlen($suffixLabel), '')
                        ->rtrim()
                        ->append($suffixLabel)
                        ->value();
                    $suffix++;
                }

                $usedUsernames[] = $username;

                DB::table('team_staff')
                    ->where('id', $staff->id)
                    ->update(['username' => $username]);
            });
    }

    public function down(): void
    {
    }
};
