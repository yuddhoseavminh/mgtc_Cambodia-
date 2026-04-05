<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ឯកសារមិនមាន</title>
        <style>
            body {
                margin: 0;
                min-height: 100vh;
                display: grid;
                place-items: center;
                background: #f8fafc;
                color: #0f172a;
                font-family: "Kantumruy Pro", "Noto Sans Khmer", Arial, sans-serif;
            }

            .panel {
                width: min(100%, 32rem);
                margin: 1.5rem;
                padding: 2rem;
                border: 1px solid #cbd5e1;
                border-radius: 1rem;
                background: #ffffff;
                box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
            }

            h1 {
                margin: 0 0 0.75rem;
                font-size: 1.25rem;
            }

            p {
                margin: 0;
                line-height: 1.6;
                color: #475569;
            }

            .meta {
                margin-top: 0.75rem;
                font-size: 0.95rem;
                color: #64748b;
            }
        </style>
    </head>
    <body>
        @php
            $requirementName = $applicationDocument->documentRequirement?->name_kh
                ?? $applicationDocument->documentRequirement?->name_en
                ?? 'ឯកសារដែលបានស្នើសុំ';
            $message = ($mode ?? 'preview') === 'download'
                ? 'មិនអាចទាញយកឯកសារនេះបានទេ ព្រោះមិនទាន់មានការអាប់ឡូត ឬឯកសារដែលបានរក្សាទុកបាត់។'
                : 'មិនអាចមើលឯកសារនេះបានទេ ព្រោះមិនទាន់មានការអាប់ឡូត ឬឯកសារដែលបានរក្សាទុកបាត់។';
        @endphp
        <section class="panel">
            <h1>ឯកសារមិនមាន</h1>
            <p>{{ $message }}</p>
            <p class="meta">ពាក្យស្នើសុំ #{{ $application->id }} | ឯកសារ #{{ $applicationDocument->id }} | {{ $requirementName }}</p>
        </section>
    </body>
</html>
