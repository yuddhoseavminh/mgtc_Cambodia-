@extends('app')

@section('body')
    <div class="min-h-screen bg-[radial-gradient(circle_at_top,#3b0b10_0%,#17070a_34%,#020304_100%)] px-4 py-6 sm:px-6">
        <div class="mx-auto flex min-h-[calc(100vh-3rem)] w-full max-w-md items-center">
            <div class="w-full overflow-hidden rounded-[2rem] border border-white/10 bg-white/95 shadow-[0_30px_80px_rgba(0,0,0,0.35)] backdrop-blur">
                <div class="bg-[linear-gradient(135deg,#111827,#7f1d1d)] px-5 py-6 text-white sm:px-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-200">ផតថលបុគ្គលិក</p>
                    <h1 class="mt-3 text-3xl font-semibold tracking-tight">ចូលគណនីបុគ្គលិក</h1>
                    <p class="mt-2 text-sm leading-7 text-slate-200">
                        ប្រើប្រាស់ឈ្មោះអ្នកប្រើប្រាស់ និងលេខសម្ងាត់របស់អ្នកដើម្បីបើកប្រវត្តិរូបអ្នក និងបញ្ចូលឯកសារផ្សេងៗ។
                    </p>
                </div>

                <div class="space-y-5 px-5 py-6 sm:px-6">
                    @if (session('status'))
                        <div class="rounded-[1.4rem] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="rounded-[1.4rem] border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    

                    <form method="POST" action="{{ route('staff.login.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="username" class="mb-2 block text-sm font-semibold text-slate-900">ឈ្មោះគណនី</label>
                            <input id="username" name="username" type="text" value="{{ old('username') }}" class="form-input min-h-[3.35rem] bg-white" placeholder="សូមបញ្ចូលឈ្មោះគណនី" autocomplete="username" required>
                        </div>

                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <label for="password" class="block text-sm font-semibold text-slate-900">លេខសម្ងាត់</label>
                                <button type="button" class="text-xs font-semibold text-rose-700" data-password-toggle>
                                    បង្ហាញ
                                </button>
                            </div>
                            <input id="password" name="password" type="password" class="form-input min-h-[3.35rem] bg-white" placeholder="បញ្ចូលលេខសម្ងាត់" autocomplete="current-password" required data-password-input>
                        </div>

                        <button type="submit" class="inline-flex min-h-[3.5rem] w-full items-center justify-center rounded-[1.35rem] bg-[linear-gradient(135deg,#7f1d1d,#dc2626)] px-5 text-base font-semibold text-white shadow-[0_18px_35px_rgba(127,29,29,0.35)] transition hover:opacity-95">
                            ចូលគណនី
                        </button>
                    </form>

                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">ត្រូវការជំនួយមែនទេ?</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            ប្រសិនបើឈ្មោះប្រើប្រាស់ ឬលេខសម្គាល់បុគ្គលិករបស់អ្នកមិនត្រឹមត្រូវ សូមទាក់ទងមកក្រុមរដ្ឋបាល ដើម្បីផ្ទៀងផ្ទាត់។
                        </p>
                    </div>

                    <a href="https://t.me/Seavminh17" target="_blank" class="inline-flex gap-2.5 min-h-[3.2rem] w-full items-center justify-center rounded-[1.25rem] border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        <img src="{{ asset('images/telegram.png') }}" class="h-5 w-5 object-contain" alt="Telegram Logo">
                        ទំនាក់ទំនងមកក្រុមរដ្ឋបាល (Telegram)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const passwordInput = document.querySelector('[data-password-input]');
            const passwordToggle = document.querySelector('[data-password-toggle]');

            if (!passwordInput || !passwordToggle) {
                return;
            }

            passwordToggle.addEventListener('click', () => {
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                passwordToggle.textContent = isHidden ? 'លាក់' : 'បង្ហាញ';
            });
        })();
    </script>
@endsection
