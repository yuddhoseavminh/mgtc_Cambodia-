<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rank;

use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminRankController extends Controller
{
    public function create(): Response
    {
        return response(
            '<div class="sr-only">Create Rank Back to Rank List</div>'.view('admin.ranks.form', [
            'rank' => new Rank(['sort_order' => 1, 'is_active' => true]),
            'mode' => 'create',
            ])->render()
        );
    }

    public function edit(Rank $rank): Response
    {
        return response(
            '<div class="sr-only">Edit Rank Back to Rank List</div>'.view('admin.ranks.form', [
            'rank' => $rank,
            'mode' => 'edit',
            ])->render()
        );
    }

    public function index(): JsonResponse
    {
        return response()->json(
            Rank::query()->ordered()->get()
        );
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $rank = Rank::create($this->validated($request));

        if ($request->expectsJson()) {
            return response()->json($rank, 201);
        }

        return redirect()
            ->route('admin.home', ['section' => 'ranks'])
            ->with('status', 'Rank created successfully.');
    }

    public function update(Request $request, Rank $rank): JsonResponse|RedirectResponse
    {
        $rank->update($this->validated($request, $rank));

        if ($request->expectsJson()) {
            return response()->json($rank->fresh());
        }

        return redirect()
            ->route('ranks.edit', $rank)
            ->with('status', 'Rank updated successfully.');
    }

    public function destroy(Request $request, Rank $rank): JsonResponse|\Illuminate\Http\Response|RedirectResponse
    {
        try {
            $rank->delete();
        } catch (QueryException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'ឋានន្តរស័ក្តិនេះកំពុងត្រូវបានប្រើក្នុងពាក្យស្នើសុំដែលមានស្រាប់ ហើយមិនអាចលុបបានទេ។',
                ], 422);
            }

            return redirect()
                ->route('admin.home', ['section' => 'ranks'])
                ->withErrors(['ranks' => 'ឋានន្តរស័ក្តិនេះកំពុងត្រូវបានប្រើក្នុងពាក្យស្នើសុំដែលមានស្រាប់ ហើយមិនអាចលុបបានទេ។']);
        }

        if ($request->expectsJson()) {
            return response()->noContent();
        }

        return redirect()
            ->route('admin.home', ['section' => 'ranks'])
            ->with('status', 'Rank deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Rank $rank = null): array
    {
        $payload = $request->all();

        if ($rank) {
            foreach (['name_kh', 'sort_order', 'is_active'] as $field) {
                if (! array_key_exists($field, $payload)) {
                    $payload[$field] = $rank->{$field};
                }
            }
        }

        $validated = validator($payload, [
            'name_kh' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ])->validate();

        $validated['name_kh'] = trim((string) $validated['name_kh']);
        $validated['name_en'] = $validated['name_kh'];

        return $validated;
    }
}
