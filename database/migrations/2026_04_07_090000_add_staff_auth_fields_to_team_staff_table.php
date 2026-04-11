<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_staff', function (Blueprint $table) {
            $table->string('username', 120)->nullable()->after('id_number');
            $table->string('password')->nullable()->after('username');
            $table->boolean('is_active')->default(true)->after('documents');
            $table->boolean('must_change_password')->default(true)->after('is_active');
            $table->timestamp('password_changed_at')->nullable()->after('must_change_password');
            $table->timestamp('last_login_at')->nullable()->after('password_changed_at');
            $table->rememberToken();
        });

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

                $idNumber = trim((string) ($staff->id_number ?? ''));

                if ($idNumber === '') {
                    $idNumber = str_pad((string) ($staff->sequence_no ?: $staff->id), 6, '0', STR_PAD_LEFT);
                }

                DB::table('team_staff')
                    ->where('id', $staff->id)
                    ->update([
                        'username' => $username,
                        'password' => Hash::make($idNumber),
                        'is_active' => true,
                        'must_change_password' => true,
                    ]);
            });

        Schema::table('team_staff', function (Blueprint $table) {
            $table->unique('username');
            $table->index(['username', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('team_staff', function (Blueprint $table) {
            $table->dropIndex(['username', 'is_active']);
            $table->dropUnique(['username']);
            $table->dropColumn([
                'username',
                'password',
                'is_active',
                'must_change_password',
                'password_changed_at',
                'last_login_at',
                'remember_token',
            ]);
        });
    }
};
