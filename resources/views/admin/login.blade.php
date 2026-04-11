@extends('app')

@section('body')
    <div class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
        <div class="w-full max-w-xl">
            <section class="rounded-[32px] border border-slate-200 bg-white p-8 shadow-[0_24px_60px_rgba(15,23,42,0.08)] sm:p-10">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-slate-400">Admin Login</p>
                <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">Sign in to continue</h2>
                <p class="mt-2 text-sm leading-7 text-slate-500">Use your administrator Email, Login ID, or Username together with your password.</p>

                <form method="POST" action="{{ route('admin.login') }}" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label class="form-label">Email / Login ID / Username</label>
                        <input type="text" name="email" value="{{ old('email', old('login', old('username'))) }}" class="form-input" placeholder="admin@gmail.com or username">
                        @include('partials.field-error', ['name' => 'email'])
                    </div>
                    <div>
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input" placeholder="Enter password">
                        @include('partials.field-error', ['name' => 'password'])
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Sign In
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection
