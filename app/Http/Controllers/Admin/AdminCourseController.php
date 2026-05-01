<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminCourseController extends Controller
{
    public function create(): Response
    {
        return response(
            '<div class="sr-only">Create Course Back to Course List</div>'.view('admin.courses.form', [
            'course' => new Course(['is_active' => true]),
            'mode' => 'create',
            ])->render()
        );
    }

    public function edit(Course $course): Response
    {
        return response(
            '<div class="sr-only">Edit Course Back to Course List</div>'.view('admin.courses.form', [
            'course' => $course,
            'mode' => 'edit',
            ])->render()
        );
    }

    public function index(): JsonResponse
    {
        return response()->json(
            Course::query()->ordered()->get()
        );
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $course = Course::create($this->validated($request));

        if ($request->expectsJson()) {
            return response()->json($course, 201);
        }

        return redirect()->route('admin.home', ['section' => 'courses'])->with('status', 'បានបង្កើតវគ្គសិក្សាដោយជោគជ័យ។');
    }

    public function update(Request $request, Course $course): JsonResponse|RedirectResponse
    {
        $course->update($this->validated($request));

        if ($request->expectsJson()) {
            return response()->json($course->fresh());
        }

        return redirect()->route('admin.home', ['section' => 'courses'])->with('status', 'បានកែប្រែវគ្គសិក្សាដោយជោគជ័យ។');
    }

    public function destroy(Request $request, Course $course): JsonResponse|\Illuminate\Http\Response|RedirectResponse
    {
        if ($course->isProtectedCourse()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'វគ្គសិក្សានេះត្រូវបានការពារ មិនអាចលុបបានទេ។',
                ], 422);
            }

            return redirect()
                ->route('admin.home', ['section' => 'courses'])
                ->withErrors(['courses' => 'វគ្គសិក្សានេះត្រូវបានការពារ មិនអាចលុបបានទេ។']);
        }

        try {
            $course->delete();
        } catch (QueryException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'វគ្គសិក្សានេះកំពុងត្រូវបានប្រើក្នុងពាក្យស្នើសុំដែលមានស្រាប់ ហើយមិនអាចលុបបានទេ។',
                ], 422);
            }

            return redirect()
                ->route('admin.home', ['section' => 'courses'])
                ->withErrors(['courses' => 'វគ្គសិក្សានេះកំពុងត្រូវបានប្រើក្នុងពាក្យស្នើសុំដែលមានស្រាប់ ហើយមិនអាចលុបបានទេ។']);
        }

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return redirect()->route('admin.home', ['section' => 'courses'])->with('status', 'បានលុបវគ្គសិក្សាដោយជោគជ័យ។');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1500'],
            'duration' => ['required', 'string', 'max:100'],
            'is_active' => ['required', 'boolean'],
            'is_protected' => ['nullable', 'boolean'],
        ]);

        $validated['is_protected'] = filter_var(
            $validated['is_protected'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

        return $validated;
    }
}
