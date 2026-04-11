<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role',
        'permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->hasAdminAccess();
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function hasAdminAccess(): bool
    {
        return $this->isSuperAdmin() || ! empty($this->permissions);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($permission, $this->permissions ?? [], true);
    }

    public function hasAnyPermission(): bool
    {
        return ! empty($this->permissions);
    }

    public function canAccessSection(string $section): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($section === 'profile') {
            return true;
        }

        $sectionPermissionMap = [
            'overview' => 'dashboard.read',
            'reports' => 'reports.read',
            'applications' => 'applications.read',
            'courses' => 'courses.read',
            'ranks' => 'ranks.read',
            'levels' => 'levels.read',
            'documents' => 'documents.read',
            'design-template' => 'design-template.read',
            'course-template' => 'course-template.read',
            'staff-team' => 'staff-team.read',
            'staff-management' => 'staff-management.read',
            'staff-team-documents' => 'staff-team-documents.read',
            'staff-team-ranks' => 'staff-team-ranks.read',
            'test-taking-staff' => 'test-taking-staff.read',
            'test-taking-staff-template' => 'test-taking-staff-template.read',
            'test-taking-staff-ranks' => 'test-taking-staff-ranks.read',
            'test-taking-staff-documents' => 'test-taking-staff-documents.read',
            'register-staff' => 'register-staff.read',
            'users' => 'users.read',
            'portal-content' => 'design-template.read',
        ];

        $permission = $sectionPermissionMap[$section] ?? null;

        if (! $permission) {
            return false;
        }

        return $this->hasPermission($permission);
    }

    public function canDo(string $module, string $action): bool
    {
        return $this->hasPermission($module . '.' . $action);
    }
}
