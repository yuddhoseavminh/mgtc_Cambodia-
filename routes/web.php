<?php

use App\Http\Controllers\Admin\AdminApplicationController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminCulturalLevelController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDocumentController;
use App\Http\Controllers\Admin\AdminDocumentRequirementController;
use App\Http\Controllers\Admin\AdminPortalContentController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminRankController;
use App\Http\Controllers\Admin\AdminTestTakingStaffDocumentRequirementController;
use App\Http\Controllers\Admin\AdminTestTakingStaffRankController;
use App\Http\Controllers\Admin\AdminTeamStaffController;
use App\Http\Controllers\Admin\AdminTeamStaffDocumentRequirementController;
use App\Http\Controllers\Admin\AdminTeamStaffRankController;
use App\Http\Controllers\Admin\AdminTestTakingStaffRegistrationController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\PortalBannerImageController;
use App\Http\Controllers\PublicApplicationController;
use App\Http\Controllers\PublicFormOptionController;
use App\Http\Controllers\PublicPortalPageController;
use App\Http\Controllers\PublicRegistrationPageController;
use App\Http\Controllers\PublicTestTakingStaffPageController;
use App\Http\Controllers\PublicTestTakingStaffRegistrationController;
use App\Http\Middleware\EnsureAdmin;
use App\Models\Application;
use Illuminate\Support\Facades\Route;

Route::get('/', PublicPortalPageController::class)->name('portal.home');
Route::get('/registration/course', PublicRegistrationPageController::class)->name('registration.form');
Route::get('/registration/test-taking-staff', PublicTestTakingStaffPageController::class)->name('test-taking-staff.form');
Route::get('/portal-banner-image', PortalBannerImageController::class)->name('portal.banner-image');
Route::get('/portal-banner-image/course', [PortalBannerImageController::class, '__invoke'])->defaults('type', 'course')->name('portal.course-banner-image');
Route::get('/portal-banner-image/test-taking-staff', [PortalBannerImageController::class, '__invoke'])->defaults('type', 'test-taking-staff')->name('portal.test-taking-staff-banner-image');

Route::get('/form-options', [PublicFormOptionController::class, 'index'])->name('form-options');
Route::post('/applications', [PublicApplicationController::class, 'store'])->middleware('throttle:public-submissions')->name('applications.store');
Route::post('/test-taking-staff-registrations', [PublicTestTakingStaffRegistrationController::class, 'store'])->middleware('throttle:public-submissions')->name('test-taking-staff.store');

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
    Route::get('/session', [AdminAuthController::class, 'show'])->name('admin.session');
    Route::post('/login', [AdminAuthController::class, 'store'])->middleware('throttle:admin-login')->name('admin.login');
    Route::post('/logout', [AdminAuthController::class, 'destroy'])->middleware('auth')->name('admin.logout');

    Route::middleware(['auth', EnsureAdmin::class])->group(function () {
        Route::get('/', [AdminPageController::class, 'index'])->name('admin.home');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/portal-content', [AdminPortalContentController::class, 'show'])->name('admin.portal-content.show');
        Route::put('/portal-content', [AdminPortalContentController::class, 'update'])->name('admin.portal-content.update');
        Route::put('/portal-content/course-template', [AdminPortalContentController::class, 'updateCourseTemplate'])->name('admin.portal-content.course-template.update');
        Route::put('/portal-content/test-taking-staff-template', [AdminPortalContentController::class, 'updateTestTakingStaffTemplate'])->name('admin.portal-content.test-taking-staff-template.update');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
        Route::get('/applications', [AdminApplicationController::class, 'index'])->name('admin.applications.index');
        Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('admin.applications.show');
        Route::patch('/applications/{application}', [AdminApplicationController::class, 'update'])->name('admin.applications.update');
        Route::get('/applications/{application}/documents/{applicationDocument}', [AdminDocumentController::class, 'show'])
            ->name('admin.documents.show');
        Route::get('/applications/{application}/documents/{applicationDocument}/download', [AdminDocumentController::class, 'download'])
            ->name('admin.documents.download');
        Route::get('/team-staff', [AdminTeamStaffController::class, 'index'])->name('team-staff.index');
        Route::get('/team-staff/{teamStaff}/avatar', [AdminTeamStaffController::class, 'avatar'])->name('team-staff.avatar');
        Route::get('/team-staff/{teamStaff}/documents/{documentIndex}/download', [AdminTeamStaffController::class, 'downloadDocument'])
            ->whereNumber('documentIndex')
            ->name('team-staff.documents.download');
        Route::patch('/team-staff/{teamStaff}/military-rank', [AdminTeamStaffController::class, 'updateMilitaryRank'])
            ->name('team-staff.update-military-rank');
        Route::get('/test-taking-staff-registrations/{testTakingStaffRegistration}/avatar', [AdminTestTakingStaffRegistrationController::class, 'avatar'])
            ->name('test-taking-staff-registrations.avatar');
        Route::get('/test-taking-staff-registrations/{testTakingStaffRegistration}/documents/{document}/download', [AdminTestTakingStaffRegistrationController::class, 'downloadDocument'])
            ->name('test-taking-staff-registrations.documents.download');

        Route::get('/document-requirements/create', [AdminDocumentRequirementController::class, 'create'])->name('document-requirements.create');
        Route::get('/document-requirements/{documentRequirement}/edit', [AdminDocumentRequirementController::class, 'edit'])->name('document-requirements.edit');
        Route::get('/ranks/create', [AdminRankController::class, 'create'])->name('ranks.create');
        Route::get('/ranks/{rank}/edit', [AdminRankController::class, 'edit'])->name('ranks.edit');
        Route::get('/test-taking-staff-ranks/create', [AdminTestTakingStaffRankController::class, 'create'])->name('test-taking-staff-ranks.create');
        Route::get('/test-taking-staff-ranks/{testTakingStaffRank}/edit', [AdminTestTakingStaffRankController::class, 'edit'])->name('test-taking-staff-ranks.edit');
        Route::get('/team-staff-ranks/create', [AdminTeamStaffRankController::class, 'create'])->name('team-staff-ranks.create');
        Route::get('/team-staff-ranks/{teamStaffRank}/edit', [AdminTeamStaffRankController::class, 'edit'])->name('team-staff-ranks.edit');
        Route::get('/test-taking-staff-document-requirements/create', [AdminTestTakingStaffDocumentRequirementController::class, 'create'])->name('test-taking-staff-document-requirements.create');
        Route::get('/test-taking-staff-document-requirements/{documentRequirement}/edit', [AdminTestTakingStaffDocumentRequirementController::class, 'edit'])->name('test-taking-staff-document-requirements.edit');
        Route::get('/team-staff-document-requirements/create', [AdminTeamStaffDocumentRequirementController::class, 'create'])->name('team-staff-document-requirements.create');
        Route::get('/team-staff-document-requirements/{documentRequirement}/edit', [AdminTeamStaffDocumentRequirementController::class, 'edit'])->name('team-staff-document-requirements.edit');
        Route::get('/courses/create', [AdminCourseController::class, 'create'])->name('courses.create');
        Route::get('/courses/{course}/edit', [AdminCourseController::class, 'edit'])->name('courses.edit');
        Route::get('/cultural-levels/create', [AdminCulturalLevelController::class, 'create'])->name('cultural-levels.create');
        Route::get('/cultural-levels/{culturalLevel}/edit', [AdminCulturalLevelController::class, 'edit'])->name('cultural-levels.edit');
        Route::apiResource('document-requirements', AdminDocumentRequirementController::class)->except(['create', 'edit', 'show']);
        Route::apiResource('ranks', AdminRankController::class)->except(['create', 'edit', 'show']);
        Route::apiResource('test-taking-staff-ranks', AdminTestTakingStaffRankController::class)->except(['create', 'edit', 'show']);
        Route::apiResource('team-staff-ranks', AdminTeamStaffRankController::class)->except(['create', 'edit', 'show']);
        Route::apiResource('test-taking-staff-document-requirements', AdminTestTakingStaffDocumentRequirementController::class)
            ->parameters(['test-taking-staff-document-requirements' => 'documentRequirement'])
            ->except(['create', 'edit', 'show']);
        Route::apiResource('team-staff-document-requirements', AdminTeamStaffDocumentRequirementController::class)
            ->parameters(['team-staff-document-requirements' => 'documentRequirement'])
            ->except(['create', 'edit', 'show']);
        Route::apiResource('courses', AdminCourseController::class)->except(['create', 'edit', 'show']);
        Route::apiResource('cultural-levels', AdminCulturalLevelController::class)->except(['create', 'edit', 'show']);
        Route::resource('team-staff', AdminTeamStaffController::class)->except(['index']);
    });
});
