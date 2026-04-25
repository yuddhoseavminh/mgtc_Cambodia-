<div class="flex items-center justify-between border-b border-slate-100 pb-4">
    <div>
        <h3 class="text-xl font-semibold tracking-tight text-slate-900">
            {{ $mode === 'edit' ? 'កែប្រែឋានន្តរស័ក្តិ' : 'បង្កើតឋានន្តរស័ក្តិ' }}
        </h3>
        <p class="mt-1 text-sm text-slate-500">
            {{ $mode === 'edit' ? 'ធ្វើបច្ចុប្បន្នភាពឈ្មោះ និងស្ថានភាពឋានន្តរស័ក្តិ។' : 'បន្ថែមជម្រើសឋានន្តរស័ក្តិថ្មីសម្រាប់ទម្រង់ចុះឈ្មោះ។' }}
        </p>
    </div>
    <button type="button" data-rank-modal-close
        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

<form
    method="POST"
    action="{{ $mode === 'edit' ? route('ranks.update', $rank) : route('ranks.store') }}"
    class="mt-6 space-y-5"
    data-action-flow-bound="false"
    data-action-success-title="ជោគជ័យ"
    data-action-success-text="{{ $mode === 'edit' ? 'បានកែប្រែឋានន្តរស័ក្តិដោយជោគជ័យ។' : 'បានបង្កើតឋានន្តរស័ក្តិដោយជោគជ័យ។' }}"
>
    @csrf
    @if ($mode === 'edit')
        @method('PUT')
    @endif

    {{-- Rank Name --}}
    <div>
        <label class="form-label" for="rank-form-name">
            ឈ្មោះឋានន្តរស័ក្តិ <span class="text-rose-500">*</span>
        </label>
        <input
            type="text"
            name="name_kh"
            id="rank-form-name"
            value="{{ old('name_kh', $rank->name_kh) }}"
            required
            maxlength="255"
            class="form-input bg-[#f8fafc]"
            placeholder="ឧ. ឧត្តមសេនីយ៍"
            autofocus
        >
        <p class="mt-1.5 hidden text-sm text-rose-500" data-field-error="name_kh"></p>
    </div>

    {{-- Sort Order --}}
    <div>
        <label class="form-label" for="rank-form-sort-order">
            លំដាប់ <span class="text-rose-500">*</span>
        </label>
        <input
            type="number"
            name="sort_order"
            id="rank-form-sort-order"
            value="{{ old('sort_order', $rank->sort_order) }}"
            min="1"
            required
            class="form-input bg-[#f8fafc]"
        >
        <p class="mt-1.5 hidden text-sm text-rose-500" data-field-error="sort_order"></p>
    </div>

    {{-- Status --}}
    <div>
        <label class="form-label" for="rank-form-is-active">
            ស្ថានភាព <span class="text-rose-500">*</span>
        </label>
        <select name="is_active" id="rank-form-is-active" class="form-input bg-[#f8fafc]">
            <option value="1" @selected((string) old('is_active', (int) $rank->is_active) === '1')>សកម្ម</option>
            <option value="0" @selected((string) old('is_active', (int) $rank->is_active) === '0')>មិនសកម្ម</option>
        </select>
        <p class="mt-1.5 hidden text-sm text-rose-500" data-field-error="is_active"></p>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
        <button type="button" data-rank-modal-close
            class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
            បដិសេធ
        </button>
        <button type="submit"
            class="inline-flex items-center justify-center rounded-xl bg-[#356AE6] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#204ec7] disabled:opacity-60">
            {{ $mode === 'edit' ? 'រក្សាទុក' : 'បង្កើតឋានន្តរស័ក្តិ' }}
        </button>
    </div>
</form>
