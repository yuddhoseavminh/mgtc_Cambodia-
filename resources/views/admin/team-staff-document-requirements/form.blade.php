<div class="flex items-center justify-between border-b border-slate-100 pb-4">
    <div>
        <h3 class="text-xl font-semibold tracking-tight text-slate-900">
            {{ $mode === 'edit' ? 'កែប្រែឯកសារបុគ្គលិកក្រុមការងារទី៣' : 'បង្កើតឯកសារបុគ្គលិកក្រុមការងារទី៣' }}
        </h3>
        <p class="mt-1 text-sm text-slate-500">
            {{ $mode === 'edit' ? 'ធ្វើបច្ចុប្បន្នភាពឈ្មោះ ស្លាកតំណ និងលំដាប់សម្រាប់ប្រភេទឯកសារបុគ្គលិកក្រុមការងារទី៣។' : 'បន្ថែមប្រភេទឯកសារថ្មី សម្រាប់ប្រើនៅក្នុងទម្រង់បុគ្គលិកក្រុម។' }}
        </p>
    </div>
    <button type="button" data-staff-doc-modal-close
        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

<form
    method="POST"
    action="{{ $mode === 'edit' ? route('team-staff-document-requirements.update', $documentRequirement) : route('team-staff-document-requirements.store') }}"
    class="mt-6 space-y-5"
    data-action-flow-bound="false"
    data-action-success-title="ជោគជ័យ"
    data-action-success-text="{{ $mode === 'edit' ? 'បានកែប្រែប្រភេទឯកសារដោយជោគជ័យ។' : 'បានបង្កើតប្រភេទឯកសារដោយជោគជ័យ។' }}"
>
    @csrf
    @if ($mode === 'edit')
        @method('PUT')
    @endif

    {{-- Name --}}
    <div>
        <label class="form-label" for="staff-doc-form-name">
            ឈ្មោះជាភាសាខ្មែរ <span class="text-rose-500">*</span>
        </label>
        <input
            type="text"
            name="name_kh"
            id="staff-doc-form-name"
            value="{{ old('name_kh', $documentRequirement->name_kh) }}"
            required
            maxlength="255"
            class="form-input bg-[#f8fafc]"
            placeholder="បញ្ចូលឈ្មោះឯកសារ"
            autofocus
        >
        <p class="mt-1.5 hidden text-sm text-rose-500" data-field-error="name_kh"></p>
    </div>



    {{-- Sort Order --}}
    <div>
        <label class="form-label" for="staff-doc-form-sort-order">
            លំដាប់ <span class="text-rose-500">*</span>
        </label>
        <input
            type="number"
            name="sort_order"
            id="staff-doc-form-sort-order"
            value="{{ old('sort_order', $documentRequirement->sort_order) }}"
            min="1"
            required
            class="form-input bg-[#f8fafc]"
        >
        <p class="mt-1.5 hidden text-sm text-rose-500" data-field-error="sort_order"></p>
    </div>

    {{-- Status --}}
    <div>
        <label class="form-label" for="staff-doc-form-is-active">
            ស្ថានភាព <span class="text-rose-500">*</span>
        </label>
        <select name="is_active" id="staff-doc-form-is-active" class="form-input bg-[#f8fafc]">
            <option value="1" @selected((string) old('is_active', (int) $documentRequirement->is_active) === '1')>សកម្ម</option>
            <option value="0" @selected((string) old('is_active', (int) $documentRequirement->is_active) === '0')>មិនសកម្ម</option>
        </select>
        <p class="mt-1.5 hidden text-sm text-rose-500" data-field-error="is_active"></p>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
        <button type="button" data-staff-doc-modal-close
            class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
            បោះបង់
        </button>
        <button type="submit"
            class="inline-flex items-center justify-center rounded-xl bg-[#356AE6] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#204ec7] disabled:opacity-60">
            {{ $mode === 'edit' ? 'រក្សាទុកការកែប្រែ' : 'បង្កើតប្រភេទឯកសារ' }}
        </button>
    </div>
</form>
