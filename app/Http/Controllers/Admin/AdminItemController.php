<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemVersion;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminItemController extends Controller
{
    public function index(): View
    {
        return view('admin.items.index', [
            'items' => Item::query()
                ->with(['updater:id,name'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.items.create', [
            'item' => new Item(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request, imageRequired: true);
        $image = $this->storeImage($request->file('image'));
        $userId = $request->user()?->id;

        try {
            $item = DB::transaction(function () use ($validated, $image, $userId): Item {
                $item = Item::create([
                    'title' => $validated['title'],
                    'description' => $validated['description'] ?? null,
                    'price' => $validated['price'],
                    'image_path' => $image['path'],
                    'image_filename' => $image['filename'],
                    'version_no' => 1,
                    'updated_by' => $userId,
                ]);

                $this->createVersion($item, $validated, $image, 1, true, $userId);

                return $item;
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($image['path']);

            throw $exception;
        }

        return redirect()
            ->route('admin.items.show', $item)
            ->with('status', 'Item created with version 1.');
    }

    public function show(Item $item): View
    {
        $item->load(['versions.updater:id,name', 'updater:id,name']);

        return view('admin.items.show', [
            'item' => $item,
        ]);
    }

    public function edit(Item $item): View
    {
        return view('admin.items.edit', [
            'item' => $item,
        ]);
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $this->validated($request, imageRequired: false);
        $newImage = $request->hasFile('image') ? $this->storeImage($request->file('image')) : null;
        $image = $newImage ?? [
            'path' => $item->image_path,
            'filename' => $item->image_filename,
        ];
        $nextVersionNo = $item->version_no + 1;
        $userId = $request->user()?->id;

        try {
            DB::transaction(function () use ($item, $validated, $image, $nextVersionNo, $userId): void {
                $item->versions()->update(['is_current' => false]);

                $this->createVersion($item, $validated, $image, $nextVersionNo, true, $userId);

                $item->update([
                    'title' => $validated['title'],
                    'description' => $validated['description'] ?? null,
                    'price' => $validated['price'],
                    'image_path' => $image['path'],
                    'image_filename' => $image['filename'],
                    'version_no' => $nextVersionNo,
                    'updated_by' => $userId,
                ]);
            });
        } catch (\Throwable $exception) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage['path']);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.items.show', $item)
            ->with('status', "Item updated. Version {$nextVersionNo} created.");
    }

    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();

        return redirect()
            ->route('admin.items.index')
            ->with('status', 'Item deleted. Stored image files were left untouched.');
    }

    /**
     * @return array{title: string, description?: string|null, price: numeric-string, change_note?: string|null}
     */
    private function validated(Request $request, bool $imageRequired): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'change_note' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    /**
     * @return array{path: string, filename: string}
     */
    private function storeImage(?UploadedFile $file): array
    {
        if (! $file) {
            abort(422, 'Image is required.');
        }

        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('items/images', $filename, 'public');

        if (! is_string($path) || $path === '') {
            abort(500, 'Unable to store uploaded image.');
        }

        return [
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
        ];
    }

    /**
     * @param array{title: string, description?: string|null, price: numeric-string, change_note?: string|null} $validated
     * @param array{path: string|null, filename: string|null} $image
     */
    private function createVersion(
        Item $item,
        array $validated,
        array $image,
        int $versionNo,
        bool $isCurrent,
        ?int $userId,
    ): ItemVersion {
        return $item->versions()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'image_path' => $image['path'],
            'image_filename' => $image['filename'],
            'version_no' => $versionNo,
            'is_current' => $isCurrent,
            'updated_by' => $userId,
            'change_note' => $validated['change_note'] ?? null,
        ]);
    }
}
