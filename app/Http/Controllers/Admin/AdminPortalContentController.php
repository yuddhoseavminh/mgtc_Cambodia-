<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalContent;
use App\Support\UploadStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPortalContentController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json(
            PortalContent::firstOrCreateDefault()
        );
    }

    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'badge' => ['required', 'string', 'max:120'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'banner_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_banner_image' => ['nullable', 'boolean'],
            'feature_one_title' => ['required', 'string', 'max:255'],
            'feature_one_description' => ['required', 'string', 'max:500'],
            'feature_two_title' => ['required', 'string', 'max:255'],
            'feature_two_description' => ['required', 'string', 'max:500'],
            'feature_three_title' => ['required', 'string', 'max:255'],
            'feature_three_description' => ['required', 'string', 'max:500'],
            'course_page_title' => ['nullable', 'string', 'max:255'],
            'course_page_subtitle' => ['nullable', 'string', 'max:255'],
            'course_page_description' => ['nullable', 'string', 'max:1000'],
            'test_taking_staff_page_title' => ['nullable', 'string', 'max:255'],
            'test_taking_staff_page_subtitle' => ['nullable', 'string', 'max:255'],
            'test_taking_staff_page_description' => ['nullable', 'string', 'max:1000'],
            'staff_title' => ['nullable', 'string', 'max:255'],
            'staff_subtitle' => ['nullable', 'string', 'max:255'],
            'staff_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_staff_logo' => ['nullable', 'boolean'],
            'redirect_section' => ['nullable', 'string', 'in:design-template,staff-team-template'],
        ]);
    
        $content = PortalContent::firstOrCreateDefault();
        $payload = collect($validated)
            ->except(['banner_image', 'remove_banner_image', 'staff_logo', 'remove_staff_logo'])
            ->all();
    
        if ($request->boolean('remove_banner_image') && $content->banner_image_path) {
            UploadStorage::delete($content->banner_image_path);
            $payload['banner_image_path'] = null;
            $payload['banner_image_original_name'] = null;
        }

        if ($request->boolean('remove_staff_logo') && $content->staff_logo_path) {
            UploadStorage::delete($content->staff_logo_path);
            $payload['staff_logo_path'] = null;
            $payload['staff_logo_original_name'] = null;
        }
    
        if ($request->hasFile('banner_image')) {
            if ($content->banner_image_path) {
                UploadStorage::delete($content->banner_image_path);
            }
    
            $file = $request->file('banner_image');
            $path = UploadStorage::storeAs(
                $file,
                'portal-content',
                'banner-'.Str::uuid().'.'.$file->getClientOriginalExtension(),
            );
    
            $payload['banner_image_path'] = $path;
            $payload['banner_image_original_name'] = $file->getClientOriginalName();
        }

        if ($request->hasFile('staff_logo')) {
            if ($content->staff_logo_path) {
                UploadStorage::delete($content->staff_logo_path);
            }
    
            $file = $request->file('staff_logo');
            $path = UploadStorage::storeAs(
                $file,
                'portal-content',
                'staff-logo-'.Str::uuid().'.'.$file->getClientOriginalExtension(),
            );
    
            $payload['staff_logo_path'] = $path;
            $payload['staff_logo_original_name'] = $file->getClientOriginalName();
        }

        $content->update($payload);

        if ($request->expectsJson()) {
            return response()->json($content->fresh());
        }

        $templateTab = $request->input('template_tab') === 'staff' ? 'staff' : 'portal';
        $redirectSection = $request->input('redirect_section') === 'staff-team-template'
            ? 'staff-team-template'
            : 'design-template';

        return redirect()
            ->route('admin.home', ['section' => $redirectSection, 'template_tab' => $templateTab])
            ->with('status', 'បានកែប្រែខ្លឹមសារគម្របគេហទំព័រដោយជោគជ័យ។');
    }

    public function updateCourseTemplate(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'course_page_title' => ['required', 'string', 'max:255'],
            'course_page_subtitle' => ['required', 'string', 'max:255'],
            'course_page_description' => ['required', 'string', 'max:1000'],
            'course_page_banner_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_course_page_banner_image' => ['nullable', 'boolean'],
        ]);

        $content = PortalContent::firstOrCreateDefault();
        $payload = collect($validated)
            ->except(['course_page_banner_image', 'remove_course_page_banner_image'])
            ->all();

        if ($request->boolean('remove_course_page_banner_image') && $content->course_page_banner_image_path) {
            UploadStorage::delete($content->course_page_banner_image_path);
            $payload['course_page_banner_image_path'] = null;
            $payload['course_page_banner_image_original_name'] = null;
        }

        if ($request->hasFile('course_page_banner_image')) {
            if ($content->course_page_banner_image_path) {
                UploadStorage::delete($content->course_page_banner_image_path);
            }

            $file = $request->file('course_page_banner_image');
            $path = UploadStorage::storeAs(
                $file,
                'portal-content',
                'course-banner-'.Str::uuid().'.'.$file->getClientOriginalExtension(),
            );

            $payload['course_page_banner_image_path'] = $path;
            $payload['course_page_banner_image_original_name'] = $file->getClientOriginalName();
        }

        $content->update($payload);

        if ($request->expectsJson()) {
            return response()->json($content->fresh());
        }

        return redirect()
            ->route('admin.home', ['section' => 'course-template'])
            ->with('status', 'បានកែប្រែទម្រង់ចុះឈ្មោះវគ្គសិក្សាដោយជោគជ័យ។');
    }

    public function updateTestTakingStaffTemplate(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'test_taking_staff_page_title' => ['required', 'string', 'max:255'],
            'test_taking_staff_page_subtitle' => ['required', 'string', 'max:255'],
            'test_taking_staff_page_description' => ['required', 'string', 'max:1000'],
            'test_taking_staff_page_banner_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_test_taking_staff_page_banner_image' => ['nullable', 'boolean'],
        ]);

        $content = PortalContent::firstOrCreateDefault();
        $payload = collect($validated)
            ->except(['test_taking_staff_page_banner_image', 'remove_test_taking_staff_page_banner_image'])
            ->all();

        if ($request->boolean('remove_test_taking_staff_page_banner_image') && $content->test_taking_staff_page_banner_image_path) {
            UploadStorage::delete($content->test_taking_staff_page_banner_image_path);
            $payload['test_taking_staff_page_banner_image_path'] = null;
            $payload['test_taking_staff_page_banner_image_original_name'] = null;
        }

        if ($request->hasFile('test_taking_staff_page_banner_image')) {
            if ($content->test_taking_staff_page_banner_image_path) {
                UploadStorage::delete($content->test_taking_staff_page_banner_image_path);
            }

            $file = $request->file('test_taking_staff_page_banner_image');
            $path = UploadStorage::storeAs(
                $file,
                'portal-content',
                'test-taking-staff-banner-'.Str::uuid().'.'.$file->getClientOriginalExtension(),
            );

            $payload['test_taking_staff_page_banner_image_path'] = $path;
            $payload['test_taking_staff_page_banner_image_original_name'] = $file->getClientOriginalName();
        }

        $content->update($payload);

        if ($request->expectsJson()) {
            return response()->json($content->fresh());
        }

        return redirect()
            ->route('admin.home', ['section' => 'test-taking-staff-template'])
            ->with('status', 'បានកែប្រែទម្រង់បុគ្គលិកសាកល្បងដោយជោគជ័យ។');
    }
}
