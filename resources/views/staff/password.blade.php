@extends('app')

@section('body')
    <div class="min-h-screen bg-[radial-gradient(circle_at_top,#3b0b10_0%,#17070a_34%,#020304_100%)] px-4 py-6 sm:px-6">
        <div class="mx-auto flex min-h-[calc(100vh-3rem)] w-full max-w-md items-center">
            <div class="w-full overflow-hidden rounded-[2rem] border border-white/10 bg-white/95 shadow-[0_30px_80px_rgba(0,0,0,0.35)] backdrop-blur">
                <div class="bg-[linear-gradient(135deg,#111827,#7f1d1d)] px-5 py-6 text-white sm:px-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-200">សុវត្ថិភាព</p>
                    <h1 class="mt-3 text-3xl font-semibold tracking-tight">ផ្លាស់ប្តូរលេខសម្ងាត់</h1>
                    <p class="mt-2 text-sm leading-7 text-slate-200">
                        {{ $staff?->must_change_password ? 'បានរកឃើញការចូលប្រព័ន្ធជាលើកដំបូង។ លោកអ្នកត្រូវតែបង្កើតលេខសម្ងាត់ថ្មីមួយ មុនពេលអាចបើកមើលទំព័រប្រវត្តិរូបបាន។' : 'ធ្វើបច្ចុប្បន្នភាពលេខសម្ងាត់របស់អ្នក ដើម្បីរក្សាសុវត្ថិភាពគណនីបុគ្គលិករបស់លោកអ្នក។' }}
                    </p>
                </div>

                <div class="space-y-5 px-5 py-6 sm:px-6">
                    @if ($errors->any())
                        <div class="rounded-[1.4rem] border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="rounded-[1.5rem] border border-amber-200 bg-amber-50 px-4 py-4">
                        <p class="text-sm font-semibold text-amber-900">គោលការណ៍លេខសម្ងាត់</p>
                        <p class="mt-1 text-sm leading-6 text-amber-700">
                            ប្រើប្រាស់យ៉ាងហោចណាស់ ៨ តួអក្សរ ដោយមានទាំងអក្សរ និងលេខ។ សូមកុំប្រើអត្តលេខរបស់អ្នកធ្វើជាលេខសម្ងាត់ថ្មី។
                        </p>
                    </div>

                    <form method="POST" action="{{ route('staff.password.update') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        @if (! $staff?->must_change_password)
                            <div>
                                <label for="current_password" class="mb-2 block text-sm font-semibold text-slate-900">លេខសម្ងាត់បច្ចុប្បន្ន</label>
                                <div class="relative">
                                    <input id="current_password" name="current_password" type="password" class="form-input min-h-[3.35rem] w-full bg-white pr-12" autocomplete="current-password" required data-password-input placeholder="បញ្ចូលលេខសម្ងាត់បច្ចុប្បន្ន">
                                <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none" data-password-toggle title="បង្ហាញ/លាក់ លេខសម្ងាត់">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-open"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-closed hidden"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="3" y1="3" x2="21" y2="21"></line></svg>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-slate-900">លេខសម្ងាត់ថ្មី</label>
                            <div class="relative">
                                <input id="password" name="password" type="password" class="form-input min-h-[3.35rem] w-full bg-white pr-12" autocomplete="new-password" required data-password-input placeholder="បញ្ចូលលេខសម្ងាត់ថ្មី">
                                <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none" data-password-toggle title="បង្ហាញ/លាក់ លេខសម្ងាត់">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-open"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-closed hidden"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="3" y1="3" x2="21" y2="21"></line></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-900">បញ្ជាក់លេខសម្ងាត់ថ្មី</label>
                            <div class="relative">
                                <input id="password_confirmation" name="password_confirmation" type="password" class="form-input min-h-[3.35rem] w-full bg-white pr-12" autocomplete="new-password" required data-password-input placeholder="បញ្ចូលលេខសម្ងាត់ថ្មីម្តងទៀត">
                                <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none" data-password-toggle title="បង្ហាញ/លាក់ លេខសម្ងាត់">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-open"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-closed hidden"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="3" y1="3" x2="21" y2="21"></line></svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="inline-flex min-h-[3.5rem] w-full items-center justify-center rounded-[1.35rem] bg-[linear-gradient(135deg,#7f1d1d,#dc2626)] px-5 text-base font-semibold text-white shadow-[0_18px_35px_rgba(127,29,29,0.35)] transition hover:opacity-95">
                            រក្សាទុកលេខសម្ងាត់ថ្មី
                        </button>
                    </form>

                    <form method="POST" action="{{ route('staff.logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex min-h-[3.2rem] w-full items-center justify-center rounded-[1.25rem] border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            ចាកចេញ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const toggles = document.querySelectorAll('[data-password-toggle]');
            toggles.forEach(toggle => {
                toggle.addEventListener('click', () => {
                    const container = toggle.closest('.relative');
                    const input = container.querySelector('[data-password-input]');
                    const eyeOpen = toggle.querySelector('.eye-open');
                    const eyeClosed = toggle.querySelector('.eye-closed');

                    if (!input) return;

                    const isHidden = input.type === 'password';
                    input.type = isHidden ? 'text' : 'password';

                    if (eyeOpen && eyeClosed) {
                        if (isHidden) {
                            eyeOpen.classList.add('hidden');
                            eyeClosed.classList.remove('hidden');
                        } else {
                            eyeOpen.classList.remove('hidden');
                            eyeClosed.classList.add('hidden');
                        }
                    }
                });
            });
        })();
    </script>
@endsection
