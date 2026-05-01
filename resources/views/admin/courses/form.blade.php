<div class="flex items-center justify-between border-b border-slate-100 pb-4">
    <div>
        <h3 class="text-xl font-semibold tracking-tight text-slate-900">
            {{ $mode === 'edit' ? 'កែប្រែវគ្គសិក្សា' : 'បង្កើតវគ្គសិក្សា' }}
        </h3>
        <p class="mt-1 text-sm text-slate-500">
            {{ $mode === 'edit' ? 'កែប្រែឈ្មោះ រយៈពេល ការពិពណ៌នា និងស្ថានភាពវគ្គសិក្សា។' : 'បន្ថែមវគ្គសិក្សាថ្មីទៅក្នុងប្រព័ន្ធ។' }}
        </p>
    </div>
    <button type="button" data-course-modal-close
        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

<form
    method="POST"
    action="{{ $mode === 'edit' ? route('courses.update', $course) : route('courses.store') }}"
    class="mt-6 space-y-5"
    data-action-flow-bound="false"
    data-action-success-title="ជោគជ័យ"
    data-action-success-text="{{ $mode === 'edit' ? 'បានកែប្រែវគ្គសិក្សាដោយជោគជ័យ។' : 'បានបង្កើតវគ្គសិក្សាដោយជោគជ័យ។' }}"
>
    @csrf
    @if ($mode === 'edit')
        @method('PUT')
    @endif

    {{-- Name --}}
    <div>
        <label class="form-label" for="course-form-name">
            ឈ្មោះវគ្គសិក្សា <span class="text-rose-500">*</span>
        </label>
        <input
            type="text"
            name="name"
            id="course-form-name"
            value="{{ old('name', $course->name) }}"
            required
            maxlength="255"
            class="form-input bg-[#f8fafc]"
            placeholder="ឧ. វគ្គបណ្តុះបណ្តាលយោធា"
            autofocus
        >
        <p class="mt-1.5 hidden text-sm text-rose-500" data-field-error="name"></p>
    </div>

    {{-- Duration --}}
    <div>
        <label class="form-label" for="course-form-duration">
            រយៈពេល <span class="text-rose-500">*</span>
        </label>
        <input
            type="text"
            name="duration"
            id="course-form-duration"
            value="{{ old('duration', $course->duration) }}"
            required
            maxlength="100"
            class="form-input bg-[#f8fafc]"
            placeholder="ឧ. ៦ ខែ"
        >
        <p class="mt-1.5 hidden text-sm text-rose-500" data-field-error="duration"></p>
    </div>

    {{-- Description --}}
    <div>
        <label class="form-label" for="course-form-description">
            ការពិពណ៌នា
        </label>
        <textarea
            name="description"
            id="course-form-description"
            maxlength="1500"
            rows="4"
            class="form-input bg-[#f8fafc]"
            placeholder="សរសេរការពិពណ៌នាអំពីវគ្គសិក្សានេះ..."
        >{{ old('description', $course->description) }}</textarea>
        <p class="mt-1.5 hidden text-sm text-rose-500" data-field-error="description"></p>
    </div>

    {{-- Is Active --}}
    <div>
        <label class="form-label" for="course-form-is-active">
            ស្ថានភាព <span class="text-rose-500">*</span>
        </label>
        <select
            name="is_active"
            id="course-form-is-active"
            class="form-input bg-[#f8fafc]"
        >
            <option value="1" @selected(old('is_active', $course->is_active) == true)>សកម្ម</option>
            <option value="0" @selected(old('is_active', $course->is_active) == false)>មិនសកម្ម</option>
        </select>
        <p class="mt-1.5 hidden text-sm text-rose-500" data-field-error="is_active"></p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
        <input type="hidden" name="is_protected" value="0">
        <label for="course-form-is-protected" class="flex cursor-pointer items-start gap-3">
            <input
                type="checkbox"
                name="is_protected"
                id="course-form-is-protected"
                value="1"
                @checked((string) old('is_protected', (int) $course->is_protected) === '1')
                class="mt-1 h-4 w-4 rounded border-slate-300 text-[#356AE6] focus:ring-[#356AE6]"
            >
            <span>
                <span class="block text-sm font-semibold text-slate-900">Protect This Course</span>
                <span class="mt-1 block text-sm leading-6 text-slate-500">Protected courses cannot be deleted from the admin list.</span>
            </span>
        </label>
        <p class="mt-1.5 hidden text-sm text-rose-500" data-field-error="is_protected"></p>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
        <button
            type="button"
            data-course-modal-close
            class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
        >
            បដិសេធ
        </button>
        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-xl bg-[#356AE6] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#204ec7] disabled:opacity-60"
        >
            {{ $mode === 'edit' ? 'រក្សាទុក' : 'បង្កើតវគ្គសិក្សា' }}
        </button>
    </div>
</form>
