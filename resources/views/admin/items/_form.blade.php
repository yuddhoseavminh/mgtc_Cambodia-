@csrf

@if ($item->exists)
    @method('PUT')
@endif

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label class="form-label">Title</label>
        <input type="text" name="title" value="{{ old('title', $item->title) }}" class="form-input bg-[#f8fafc]" required>
        @error('title')
            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="form-label">Price</label>
        <input type="number" name="price" value="{{ old('price', $item->price) }}" step="0.01" min="0" class="form-input bg-[#f8fafc]" required>
        @error('price')
            <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label class="form-label">Description</label>
    <textarea name="description" rows="5" class="form-input bg-[#f8fafc]">{{ old('description', $item->description) }}</textarea>
    @error('description')
        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="form-label">{{ $item->exists ? 'New image (optional)' : 'Image' }}</label>
    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-input bg-[#f8fafc]" @required(! $item->exists)>
    <p class="mt-2 text-sm text-slate-500">Allowed: JPG, PNG, WEBP. Maximum 5 MB. Existing images are never overwritten.</p>
    @error('image')
        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
    @enderror

    @if ($item->image_url)
        <div class="mt-4 flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4">
            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="h-20 w-20 rounded-xl object-cover">
            <div>
                <p class="text-sm font-semibold text-slate-900">Current image</p>
                <p class="mt-1 text-xs text-slate-500">{{ $item->image_filename }}</p>
            </div>
        </div>
    @endif
</div>

<div>
    <label class="form-label">Change note</label>
    <textarea name="change_note" rows="3" class="form-input bg-[#f8fafc]" placeholder="Optional note explaining this version">{{ old('change_note') }}</textarea>
    @error('change_note')
        <p class="mt-2 text-sm text-rose-500">{{ $message }}</p>
    @enderror
</div>

<div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-[#356AE6] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#204ec7]">
        {{ $item->exists ? 'Create New Version' : 'Create Item' }}
    </button>
    <a href="{{ $item->exists ? route('admin.items.show', $item) : route('admin.items.index') }}" class="inline-flex items-center rounded-2xl px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
        Cancel
    </a>
</div>
