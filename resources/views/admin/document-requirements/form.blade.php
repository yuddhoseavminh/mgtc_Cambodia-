<div class="flex items-center justify-between border-b border-slate-100 pb-4">
    <div>
        <h3 class="text-xl font-semibold tracking-tight text-slate-900">
            {{ $mode === 'edit' ? 'កែប្រែតម្រូវការឯកសារ' : 'បង្កើតតម្រូវការឯកសារ' }}
        </h3>
        <p class="mt-1 text-sm text-slate-500">
            {{ $mode === 'edit' ? 'ធ្វើបច្ចុប្បន្នភាពស្លាក លំដាប់ និងស្ថានភាពឯកសារ។' : 'បន្ថែមតម្រូវការឯកសារថ្មីសម្រាប់ទម្រង់ចុះឈ្មោះ។' }}
        </p>
    </div>
    <button type="button" data-document-modal-close
        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

<form
    method="POST"
    action="{{ $mode === 'edit' ? route('document-requirements.update', $documentRequirement) : route('document-requirements.store') }}"
    class="mt-6 space-y-5"
    data-action-flow-bound="false"
    data-action-success-title="ជោគជ័យ"
    data-action-success-text="{{ $mode === 'edit' ? 'បានកែប្រែតម្រូវការឯកសារដោយជោគជ័យ។' : 'បានបង្កើតតម្រូវការឯកសារដោយជោគជ័យ។' }}"
>
    @csrf
    @if ($mode === 'edit')
        @method('PUT')
    @endif

    {{-- Name --}}
    <div>
        <label class="form-label" for="document-form-name">
            ឈ្មោះជាភាសាខ្មែរ <span class="text-rose-500">*</span>
        </label>
        <input
            type="text"
            name="name_kh"
            id="document-form-name"
            value="{{ old('name_kh', $documentRequirement->name_kh) }}"
            required
            maxlength="255"
            class="form-input bg-[#f8fafc]"
            placeholder="ឧ. លិខិតអំណាចប្រគល់"
            autofocus
        >
        <p class="mt-1.5 hidden text-sm text-rose-500" data-field-error="name_kh"></p>
    </div>



    {{-- Sort Order --}}
    <div>
        <label class="form-label" for="document-form-sort-order">
            លំដាប់ <span class="text-rose-500">*</span>
        </label>
        <input
            type="number"
            name="sort_order"
            id="document-form-sort-order"
            value="{{ old('sort_order', $documentRequirement->sort_order) }}"
            min="1"
            required
            class="form-input bg-[#f8fafc]"
        >
        <p class="mt-1.5 hidden text-sm text-rose-500" data-field-error="sort_order"></p>
    </div>

    {{-- Status --}}
    <div>
        <label class="form-label" for="document-form-is-active">
            ស្ថានភាព <span class="text-rose-500">*</span>
        </label>
        <select name="is_active" id="document-form-is-active" class="form-input bg-[#f8fafc]">
            <option value="1" @selected((string) old('is_active', (int) $documentRequirement->is_active) === '1')>សកម្ម</option>
            <option value="0" @selected((string) old('is_active', (int) $documentRequirement->is_active) === '0')>មិនសកម្ម</option>
        </select>
        <p class="mt-1.5 hidden text-sm text-rose-500" data-field-error="is_active"></p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
        <input type="hidden" name="is_protected" value="0">
        <label for="document-form-is-protected" class="flex cursor-pointer items-start gap-3">
            <input
                type="checkbox"
                name="is_protected"
                id="document-form-is-protected"
                value="1"
                @checked((string) old('is_protected', (int) $documentRequirement->is_protected) === '1')
                class="mt-1 h-4 w-4 rounded border-slate-300 text-[#356AE6] focus:ring-[#356AE6]"
            >
            <span>
                <span class="block text-sm font-semibold text-slate-900">Send This Document To Telegram</span>
                <span class="mt-1 block text-sm leading-6 text-slate-500">Only one document type should be marked. New registrations will send this file to Telegram when available.</span>
            </span>
        </label>
        <p class="mt-1.5 hidden text-sm text-rose-500" data-field-error="is_protected"></p>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
        <button type="button" data-document-modal-close
            class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
            បដិសេធ
        </button>
        <button type="submit"
            class="inline-flex items-center justify-center rounded-xl bg-[#356AE6] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#204ec7] disabled:opacity-60">
            {{ $mode === 'edit' ? 'រក្សាទុក' : 'បង្កើតតម្រូវការឯកសារ' }}
        </button>
    </div>
</form>
