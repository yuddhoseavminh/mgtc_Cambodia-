@extends('app')

@section('body')
    @php
        $badge = $portalContent?->badge ?: 'ប្រព័ន្ធចុះឈ្មោះ';
        $title = $portalContent?->title ?: 'ការចុះឈ្មោះសម្រាប់វគ្គសិក្សា និងបុគ្គលិកសាកល្បង';
        $description = $portalContent?->description ?: 'សូមជ្រើសរើសប្រភេទសេវាដែលអ្នកចង់ចុះឈ្មោះ។ ទិន្នន័យលើទំព័រនេះត្រូវបានគ្រប់គ្រងពីផ្នែករដ្ឋបាល ដើម្បីឲ្យអ្នកប្រើប្រាស់មើលឃើញព័ត៌មានដែលបានធ្វើបច្ចុប្បន្នភាព។';
    @endphp

    <div class="public-page">
        <div class="public-home-shell">
            @if ($portalContent?->banner_image_path)
                <section class="public-banner-card">
                    <img src="{{ route('portal.banner-image') }}" alt="Portal banner" class="h-auto w-full object-contain">
                </section>
            @else
                <section class="public-banner-card relative">
                    <div class="absolute left-0 top-0 h-14 w-14 rounded-br-full bg-amber-400/90 sm:h-20 sm:w-20"></div>
                    <div class="absolute bottom-0 right-0 h-16 w-16 rounded-tl-full bg-amber-400/90 sm:h-24 sm:w-24"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(15,23,42,0.05),transparent_46%),linear-gradient(180deg,rgba(255,255,255,0.98),rgba(248,250,252,0.96))]"></div>
                    <div class="relative grid gap-3 px-4 py-4 sm:px-7 sm:py-5 lg:grid-cols-[130px_1fr] lg:items-center">
                        <div class="flex justify-center lg:justify-start">
                            <div class="flex h-[82px] w-[82px] items-center justify-center rounded-full border-4 border-yellow-300 bg-[radial-gradient(circle_at_30%_30%,#2563eb,#1d4ed8_38%,#dc2626_39%,#dc2626_70%,#facc15_71%,#facc15_100%)] shadow-[0_12px_28px_rgba(15,23,42,0.18)] sm:h-[110px] sm:w-[110px] sm:border-[5px]">
                                <div class="flex h-[60px] w-[60px] items-center justify-center rounded-full border-[3px] border-sky-200/90 bg-white/10 text-center text-[10px] font-bold uppercase tracking-[0.14em] text-white backdrop-blur sm:h-[82px] sm:w-[82px] sm:border-4 sm:text-[11px] sm:tracking-[0.18em]">
                                    RCAF
                                </div>
                            </div>
                        </div>
                        <div class="text-center lg:text-left">
                            <p class="text-lg font-semibold leading-snug text-slate-900 sm:text-[34px]">សាលាហ្វឹកហ្វឺនយោធា</p>
                            <p class="mt-1 text-base font-semibold leading-snug text-slate-800 sm:mt-2 sm:text-[30px]">ប្រព័ន្ធចុះឈ្មោះតាមអនឡាញ</p>
                            <p class="mt-2 text-xs text-slate-500 sm:text-sm">សូមជ្រើសរើសផ្នែកចុះឈ្មោះខាងក្រោម ដើម្បីបន្តទៅកាន់ទម្រង់ដែលត្រឹមត្រូវ។</p>
                        </div>
                    </div>
                </section>
            @endif

            <section class="public-home-intro">
                <span class="public-home-badge">{{ $badge }}</span>
                <h1 class="public-home-title">{{ $title }}</h1>
                <p class="public-home-description">{{ $description }}</p>
            </section>

            <section class="public-home-grid">
                <a href="{{ route('registration.form') }}" class="public-home-card">
                    <span class="public-home-card-icon" aria-hidden="true">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"></path>
                        </svg>
                    </span>
                    <h2 class="public-home-card-title">ចុះឈ្មោះវគ្គសិក្សា</h2>
                    <p class="public-home-card-text">បំពេញទម្រង់សម្រាប់ចុះឈ្មោះវគ្គសិក្សា ដោយប្រើទិន្នន័យ និងឯកសារភ្ជាប់ដែលតម្រូវដោយរដ្ឋបាល។</p>
                    <span class="public-home-card-link">
                        បើកទម្រង់ចុះឈ្មោះ
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14"></path>
                            <path d="m13 5 7 7-7 7"></path>
                        </svg>
                    </span>
                </a>

                <a href="{{ route('test-taking-staff.form') }}" class="public-home-card">
                    <span class="public-home-card-icon" aria-hidden="true">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                    </span>
                    <h2 class="public-home-card-title">ចុះឈ្មោះបុគ្គលិកសាកល្បង</h2>
                    <p class="public-home-card-text">បំពេញព័ត៌មានបុគ្គលិកសាកល្បង ជាមួយរូបថត អាយុ និងឯកសារដែលបានកំណត់ពីផ្នែករដ្ឋបាល Catalog 3។</p>
                    <span class="public-home-card-link">
                        បើកទម្រង់បុគ្គលិកសាកល្បង
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14"></path>
                            <path d="m13 5 7 7-7 7"></path>
                        </svg>
                    </span>
                </a>
            </section>
        </div>
    </div>
@endsection
