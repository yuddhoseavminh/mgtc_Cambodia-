<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CulturalLevel;
use Illuminate\Contracts\View\View;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminCulturalLevelController extends Controller
{
    public function create(): Response
    {
        return response(
            '<div class="sr-only">Create Cultural Level Back to Cultural Level List</div>'.view('admin.cultural-levels.form', [
            'level' => new CulturalLevel(['sort_order' => 1, 'is_active' => true]),
            'mode' => 'create',
            ])->render()
        );
    }

    public function edit(CulturalLevel $culturalLevel): View
    {
        return view('admin.cultural-levels.form', [
            'level' => $culturalLevel,
            'mode' => 'edit',
        ]);
    }

    public function index(): JsonResponse
    {
        return response()->json(
            CulturalLevel::query()->ordered()->get()
        );
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $level = CulturalLevel::create($this->validated($request));

        if ($request->expectsJson()) {
            return response()->json($level, 201);
        }

        return redirect()->route('cultural-levels.edit', $level)->with('status', 'បានបង្កើតកម្រិតវប្បធម៌ដោយជោគជ័យ។');
    }

    public function update(Request $request, CulturalLevel $culturalLevel): JsonResponse|RedirectResponse
    {
        $culturalLevel->update($this->validated($request, $culturalLevel));

        if ($request->expectsJson()) {
            return response()->json($culturalLevel->fresh());
        }

        return redirect()->route('cultural-levels.edit', $culturalLevel)->with('status', 'បានកែប្រែកម្រិតវប្បធម៌ដោយជោគជ័យ។');
    }

    public function destroy(Request $request, CulturalLevel $culturalLevel): JsonResponse|\Illuminate\Http\Response|RedirectResponse
    {
        try {
            $culturalLevel->delete();
        } catch (QueryException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'កម្រិតវប្បធម៌នេះកំពុងត្រូវបានប្រើក្នុងពាក្យស្នើសុំដែលមានស្រាប់ ហើយមិនអាចលុបបានទេ។',
                ], 422);
            }

            return redirect()
                ->route('admin.home', ['section' => 'levels'])
                ->withErrors(['levels' => 'កម្រិតវប្បធម៌នេះកំពុងត្រូវបានប្រើក្នុងពាក្យស្នើសុំដែលមានស្រាប់ ហើយមិនអាចលុបបានទេ។']);
        }

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return redirect()->route('cultural-levels.edit', $culturalLevel)->with('status', 'បានលុបកម្រិតវប្បធម៌ដោយជោគជ័យ។');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?CulturalLevel $culturalLevel = null): array
    {
        $payload = $request->all();

        if ($culturalLevel) {
            foreach (['name', 'sort_order', 'is_active'] as $field) {
                if (! array_key_exists($field, $payload)) {
                    $payload[$field] = $culturalLevel->{$field};
                }
            }
        }

        return validator($payload, [
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ])->validate();
    }
}
