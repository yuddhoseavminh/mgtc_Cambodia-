@extends('app')

@section('body')
    @php
        /** @var \App\Models\PortalContent $portalContent */
        $staffHeaderLogo = $portalContent?->staff_logo_path
            ? route('portal.staff-logo-image')
            : asset('images/logo_admin.jpg');
        $staffHeaderTitle = $portalContent?->staff_title ?: 'ប្រព័ន្ធគ្រប់គ្រងការចុះឈ្មោះយោធា';
        $staffHeaderSubtitle = trim((string) ($portalContent?->staff_subtitle ?? ''));

        $profileCompletion = max(0, min(100, (int) ($profileCompletion ?? 0)));

        $indexedDocuments = $documents
            ->map(fn ($document, $index) => [...$document, 'document_index' => $index])
            ->values();

        $documentsByRequirementSlug = $indexedDocuments
            ->filter(fn ($document) => filled($document['requirement_slug'] ?? null))
            ->groupBy(fn ($document) => $document['requirement_slug']);

        $otherDocuments = $indexedDocuments
            ->filter(fn ($document) => blank($document['requirement_slug'] ?? null))
            ->values();

        $identityFields = [
            ['label' => 'គោត្តនាម-នាម', 'value' => $staff->name_kh],
            ['label' => 'ឈ្មោះឡាតាំង', 'value' => $staff->name_latin],
            ['label' => 'ភេទ', 'value' => match (strtolower((string) $staff->gender)) {
                'male' => 'ប្រុស',
                'female' => 'ស្រី',
                default => $staff->gender,
            }],
            ['label' => 'អត្តលេខ', 'value' => $staff->id_number],
            ['label' => 'ឋានន្តរស័ក្តិយោធា', 'value' => $staff->military_rank],
            ['label' => 'លេខទូរស័ព្ទ', 'value' => $staff->phone_number],
            ['label' => 'ថ្ងៃខែឆ្នាំកំណើត', 'value' => optional($staff->dob)?->khFormat('d/m/Y')],
            ['label' => 'ថ្ងៃចូលបម្រើកងទ័ព', 'value' => optional($staff->date_of_enlistment)?->khFormat('d/m/Y')],
        ];

        $identityFieldRows = collect($identityFields)
            ->values()
            ->map(function (array $field, int $index): array {
                $iconName = match ($index) {
                    0 => 'user',
                    1 => 'signature',
                    2 => 'gender',
                    3 => 'id',
                    4 => 'rank',
                    5 => 'phone',
                    default => 'calendar',
                };

                $tone = match ($index) {
                    0 => ['bg' => '#e0f2fe', 'text' => '#0369a1'],
                    1 => ['bg' => '#e0e7ff', 'text' => '#4338ca'],
                    2 => ['bg' => '#f3e8ff', 'text' => '#7e22ce'],
                    3 => ['bg' => '#ccfbf1', 'text' => '#0f766e'],
                    4 => ['bg' => '#fef3c7', 'text' => '#b45309'],
                    5 => ['bg' => '#ffe4e6', 'text' => '#be123c'],
                    default => ['bg' => '#dcfce7', 'text' => '#166534'],
                };

                return $field + [
                    'iconName' => $iconName,
                    'tone' => $tone,
                    'isNumericValue' => in_array($iconName, ['id', 'phone'], true),
                ];
            })
            ->chunk(2)
            ->map(fn ($row) => $row->values())
            ->values();

        $avatarSeed = trim((string) ($staff->name_latin ?: $staff->name_kh ?: 'S'));
        $avatarInitial = strtoupper(substr($avatarSeed, 0, 1));

        $statusMeta = function (?string $status): array {
            $value = strtolower(trim((string) $status));

            if (str_contains($value, 'approve') || str_contains($value, 'accept') || str_contains($value, 'verified')) {
                return ['key' => 'approved', 'label' => 'អនុម័ត', 'class' => 'bg-emerald-100 text-emerald-700'];
            }

            if (str_contains($value, 'reject') || str_contains($value, 'denied')) {
                return ['key' => 'rejected', 'label' => 'បដិសេធ', 'class' => 'bg-rose-100 text-rose-700'];
            }

            return ['key' => 'pending', 'label' => 'កំពុងរង់ចាំ', 'class' => 'bg-amber-100 text-amber-700'];
        };

        $requirementSummaries = $documentRequirements
            ->map(function ($requirement) use ($documentsByRequirementSlug, $statusMeta) {
                $reqDocs = $documentsByRequirementSlug->get($requirement->slug, collect())->values();
                $latestDoc = $reqDocs->last();
                $badge = $statusMeta($latestDoc['status'] ?? null);

                return [
                    'requirement' => $requirement,
                    'reqDocs' => $reqDocs,
                    'latestDoc' => $latestDoc,
                    'badge' => $badge,
                    'isCompleted' => $reqDocs->isNotEmpty(),
                ];
            })
            ->values();

        $requiredDocumentCount = $requirementSummaries->count();
        $uploadedRequiredCount = $requirementSummaries->where('isCompleted', true)->count();
        $pendingRequiredCount = $requirementSummaries->where('badge.key', 'pending')->count();
        $approvedRequiredCount = $requirementSummaries->where('badge.key', 'approved')->count();
        $rejectedRequiredCount = $requirementSummaries->where('badge.key', 'rejected')->count();
        $filterCounts = [
            'all' => $requiredDocumentCount,
            'pending' => $pendingRequiredCount,
            'approved' => $approvedRequiredCount,
            'rejected' => $rejectedRequiredCount,
        ];

        $formatFileSize = function (?string $path): ?string {
            if (! filled($path)) {
                return null;
            }

            try {
                $bytes = \App\Support\UploadStorage::size($path);
            } catch (\Throwable) {
                return null;
            }

            if ($bytes === null) {
                return null;
            }

            if ($bytes >= 1048576) {
                return number_format($bytes / 1048576, 1).' MB';
            }

            if ($bytes >= 1024) {
                return number_format($bytes / 1024, 1).' KB';
            }

            return $bytes.' B';
        };

        $formatUploadedAt = function (?string $value): string {
            if (! filled($value)) {
                return '--';
            }

            try {
                return \Illuminate\Support\Carbon::parse($value)->format('d/m/Y');
            } catch (\Throwable) {
                return '--';
            }
        };
    @endphp

    <style>
        html {
            font-size: 17px;
        }

        .profile-shell {
            --ui-bg: #f3f6fb;
            --ui-surface: #ffffff;
            --ui-surface-soft: #f8fbff;
            --ui-border: #dbe7f5;
            --ui-text: #0f172a;
            --ui-muted: #64748b;
            --ui-primary: #2563eb;
            --ui-primary-soft: #eaf2ff;
            --ui-danger-soft: #fff1f2;
            --ui-radius: 12px;
            --ui-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
            --ui-shadow-soft: 0 2px 10px rgba(15, 23, 42, 0.05);
            background: var(--ui-bg);
        }

        .ui-card {
            border: 1px solid var(--ui-border);
            border-radius: var(--ui-radius);
            background: var(--ui-surface);
            box-shadow: var(--ui-shadow-soft);
        }

        .ui-subcard {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: var(--ui-surface-soft);
        }

        .filter-chip {
            border: 1px solid #cbd5e1;
            border-radius: 999px;
            background: #fff;
            color: #475569;
            transition: all 0.18s ease;
        }

        .filter-chip[data-active='true'] {
            border-color: #2563eb;
            background: #eaf2ff;
            color: #1d4ed8;
        }

        .file-row {
            border: 1px solid #dbe5f1;
            border-radius: 10px;
            background: #fff;
            transition: all 0.18s ease;
        }

        .file-row:hover {
            border-color: #bfdbfe;
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.08);
        }

        .file-row-main {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .file-row-meta {
            min-width: 0;
            flex: 1;
        }

        .file-row-actions {
            margin-top: 6px;
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 6px;
        }

        .file-row-actions .btn-action {
            height: 24px;
            padding: 0 8px;
            font-size: 10px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 28px;
            border-radius: 8px;
            padding: 0 10px;
            font-size: 11px;
            font-weight: 600;
            line-height: 1;
            transition: all 0.16s ease;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .btn-outline {
            border-color: #cbd5e1;
            background: #fff;
            color: #334155;
        }

        .btn-outline:hover {
            border-color: #94a3b8;
            background: #f8fafc;
        }

        .btn-soft {
            border-color: #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .btn-soft:hover {
            background: #dbeafe;
        }

        .btn-danger {
            border-color: #fecdd3;
            background: var(--ui-danger-soft);
            color: #be123c;
        }

        .btn-danger:hover {
            background: #ffe4e6;
        }

        .profile-personal-card {
            border-color: #c8d6e8;
            background: #f8fbff;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }

        .profile-account-card {
            border-color: #c8d6e8;
            background: #f8fbff;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
        }

        .identity-list-mobile-compact {
            border: 1px solid #c9d3e2;
            border-radius: 18px;
            overflow: hidden;
            background: #ffffff;
        }

        .identity-grid-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .identity-grid-row + .identity-grid-row {
            border-top: 1px solid #d7dee8;
        }

        .identity-grid-cell {
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 96px;
            padding: 16px 20px;
        }

        .identity-grid-cell + .identity-grid-cell {
            border-left: 1px solid #d7dee8;
        }

        .identity-grid-label {
            font-size: 12px;
            font-weight: 700;
            color: #475569;
        }

        .identity-grid-value {
            margin-top: 3px;
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.25;
            word-break: break-word;
        }

        .identity-grid-value.is-numeric {
            white-space: nowrap;
            word-break: normal;
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.01em;
        }

        .profile-account-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 8px 20px;
            font-size: 15px;
            font-weight: 700;
        }

        .profile-account-actions {
            margin-top: auto;
            margin-bottom: 70px;
            display: grid;
            width: 100%;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            padding-top: 22px;
        }

        .profile-account-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 58px;
            border-radius: 16px;
            border: 1px solid #c3d0e0;
            font-size: 16px;
            font-weight: 700;
            transition: all 0.16s ease;
        }

        .profile-account-action.edit {
            background: #f3f8ff;
            color: #1d4ed8;
        }

        .profile-account-action.edit:hover {
            background: #e2eeff;
        }

        .profile-account-action.logout {
            background: #fff3f4;
            color: #be123c;
        }

        .profile-account-action.logout:hover {
            background: #ffe6ea;
        }

        .identity-row {
            border: 1.5px solid #1f2937;
            background: #ffffff;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.06);
            transition: all 0.18s ease;
        }

        .identity-row:hover {
            border-color: #2563eb;
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.12);
        }

        .identity-icon-box {
            border-radius: 8px;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.75);
        }

        .identity-value-chip {
            border-radius: 8px;
            box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.14);
        }

        .profile-details-box {
            border: 1px solid #dbe6f2;
            border-radius: 24px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 251, 255, 0.96) 100%);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.07);
            padding: 18px 20px;
        }

        .profile-progress-box {
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }

        .khmer-profile-font {
            font-family: "Kantumruy Pro", "Noto Sans Khmer", "Hanuman", var(--admin-font-sans), sans-serif;
            letter-spacing: 0.012em;
        }

        .profile-avatar-shell {
            aspect-ratio: 1 / 1;
            border: 3px solid #bfdbfe;
            border-radius: 9999px;
            background: radial-gradient(circle at 30% 30%, #eff6ff 0%, #dbeafe 58%, #c7d2fe 100%);
            box-shadow: 0 12px 26px rgba(37, 99, 235, 0.2);
            overflow: hidden;
        }

        .profile-avatar-core {
            aspect-ratio: 1 / 1;
            border: 2px solid #ffffff;
            border-radius: 9999px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.16);
            overflow: hidden;
        }

        .profile-avatar-core img,
        .profile-avatar-core > div {
            aspect-ratio: 1 / 1;
            border-radius: 9999px;
        }

        .profile-detail-line {
            border: 0;
            border-radius: 0;
            padding: 2px 0;
            line-height: 1.25;
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            background: transparent;
            transition: none;
        }

        .profile-detail-line:nth-child(odd) {
            background: transparent;
            border-color: transparent;
        }

        .profile-detail-line:hover {
            border-color: transparent;
            transform: none;
        }

        .profile-name-kh {
            font-size: 30px;
            line-height: 1.15;
            font-weight: 800;
            margin-top: 12px;
        }

        .profile-id-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #bfdbfe;
            border-radius: 9999px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 900;
            letter-spacing: 0.12em;
            color: #1d4ed8;
            text-transform: uppercase;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .profile-name-latin {
            margin-top: 8px;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.02em;
            color: #334155;
        }

        .profile-meta-list {
            margin-top: 14px;
            display: grid;
            gap: 10px;
        }

        .profile-meta-pill {
            display: block;
            border: 1px solid #d9e4ef;
            border-radius: 18px;
            background: #ffffff;
            padding: 10px 14px;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.05);
        }

        .profile-meta-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #64748b;
        }

        .profile-meta-value {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            font-family: "Kantumruy Pro", "Noto Sans Khmer", "Hanuman", var(--admin-font-sans), sans-serif;
        }

        .doc-panel {
            border-color: #cfe0f3;
            background: linear-gradient(180deg, #fbfdff 0%, #f4f8ff 100%);
        }

        .doc-requirement-card {
            border: 1px solid #d4e2f1;
            border-radius: 14px;
            background: #f8fbff;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.05);
            transition: all 0.18s ease;
        }

        .doc-requirement-card:hover {
            border-color: #93c5fd;
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.1);
            transform: translateY(-1px);
        }

        .doc-title {
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
        }

        .doc-status-chip {
            border: 1px solid rgba(148, 163, 184, 0.25);
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 10px;
            font-weight: 700;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65);
        }

        .doc-upload-zone {
            border: 1px dashed #c7d7ea;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px;
        }

        .doc-cta {
            height: 54px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .profile-header {
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid #dbe5f0;
            background:
                radial-gradient(circle at left top, rgba(59, 130, 246, 0.14), transparent 28%),
                linear-gradient(135deg, #ffffff 0%, #f8fbff 52%, #f1f6fd 100%);
        }

        .profile-header::after {
            content: '';
            position: absolute;
            inset: auto -60px -80px auto;
            width: 180px;
            height: 180px;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.14) 0%, rgba(14, 165, 233, 0) 72%);
            pointer-events: none;
        }

        .profile-header-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .profile-header-brand {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 14px;
        }

        .profile-header-logo {
            display: grid;
            height: 58px;
            width: 58px;
            flex-shrink: 0;
            place-items: center;
            overflow: hidden;
            border: 1px solid #dbe6f2;
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            padding: 6px;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
        }

        .profile-header-logo img {
            height: 100%;
            width: 100%;
            object-fit: contain;
        }

        .profile-header-copy {
            min-width: 0;
        }

        .profile-header-kicker {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #2563eb;
        }

        .profile-header-title {
            margin-top: 4px;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.25;
            color: #0f172a;
        }

        .profile-header-subtitle {
            margin-top: 4px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748b;
        }

        .profile-header-actions {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            gap: 10px;
        }

        .modal-footer-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            padding-top: 6px;
        }

        .modal-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 40px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            transition: all 0.16s ease;
        }

        .modal-btn-cancel {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
        }

        .modal-btn-cancel:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            transform: translateY(-1px);
        }

        .modal-btn-save {
            border: 1px solid #2563eb;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.25);
        }

        .modal-btn-save:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
        }

        .modal-btn-danger {
            border: 1px solid #e11d48;
            background: #e11d48;
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(225, 29, 72, 0.25);
        }

        .modal-btn-danger:hover {
            background: #be123c;
            border-color: #be123c;
            transform: translateY(-1px);
        }

        .logout-wrap {
            padding: 2px;
            border-radius: 9999px;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 45%, #c7d2fe 100%);
            box-shadow: 0 10px 18px rgba(59, 130, 246, 0.14);
        }

        .logout-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-radius: 9999px;
            background: #0f172a;
            padding: 8px 14px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: #ffffff;
            transition: all 0.16s ease;
        }

        .logout-btn:hover {
            background: #1e293b;
            transform: translateY(-1px);
        }

        @media (max-width: 1024px) {
            .identity-grid-row {
                grid-template-columns: 1fr;
            }

            .identity-grid-cell {
                min-height: 64px;
                padding: 10px 12px;
                gap: 8px;
            }

            .identity-grid-cell + .identity-grid-cell {
                border-left: 0;
                border-top: 1px solid #d7dee8;
            }

            .identity-grid-label {
                font-size: 9px;
            }

            .identity-grid-value {
                margin-top: 2px;
                font-size: 12px;
                line-height: 1.2;
            }

            .identity-grid-value.is-numeric {
                font-size: 11px;
            }

            .profile-account-actions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
                padding-top: 14px;
            }

            .profile-account-action {
                height: 40px;
                font-size: 11px;
                border-radius: 12px;
                gap: 5px;
            }

            .profile-account-action svg {
                width: 14px;
                height: 14px;
            }
        }

        @media (max-width: 420px) {
            .profile-account-actions {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .profile-header-inner {
                align-items: flex-start;
                gap: 12px;
            }

            .profile-header-brand {
                gap: 10px;
            }

            .profile-header-logo {
                height: 46px;
                width: 46px;
                border-radius: 14px;
                padding: 5px;
            }

            .profile-header-title {
                font-size: 13px;
            }

            .profile-header-subtitle {
                font-size: 9px;
            }

            .logout-btn {
                padding: 7px 11px;
                font-size: 10px;
            }

            .file-row-actions {
                justify-content: flex-start;
            }

            .modal-footer-actions {
                grid-template-columns: 1fr;
            }

            .identity-list-mobile-compact {
                overflow-y: auto;
                padding-right: 4px;
                border-radius: 14px;
            }

            .profile-personal-card {
                padding: 10px;
            }

            .profile-personal-card > h2 {
                font-size: 12px;
            }

            .identity-grid-cell {
                min-height: 58px;
                padding: 8px 10px;
                gap: 8px;
            }

            .identity-grid-cell .identity-icon-box {
                width: 34px;
                height: 34px;
                border-radius: 10px;
            }

            .identity-grid-cell .identity-icon-box svg {
                width: 15px;
                height: 15px;
            }

            .identity-grid-label {
                font-size: 8px;
            }

            .identity-grid-value {
                margin-top: 1px;
                font-size: 10px;
                line-height: 1.2;
                word-break: normal;
            }

            .identity-grid-value.is-numeric {
                font-size: 10px;
            }

            .profile-name-kh {
                font-size: 20px;
            }

            .profile-details-box {
                border-radius: 18px;
                padding: 14px 12px;
            }

            .profile-id-chip {
                padding: 6px 12px;
                font-size: 11px;
            }

            .profile-name-latin {
                font-size: 13px;
            }

            .profile-meta-list {
                gap: 8px;
            }

            .profile-meta-pill {
                padding: 6px 10px;
            }

            .profile-meta-label {
                font-size: 8px;
            }

            .profile-meta-value {
                font-size: 10px;
            }
        }
    </style>

    <div class="profile-shell min-h-screen py-5 font-[var(--admin-font-sans)] text-slate-900 md:py-8">
        <div class="mx-auto w-full max-w-6xl px-2 md:px-6">
            <div class="w-full">
                <div class="w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-50 shadow-sm">
                <header class="profile-header px-3 py-3 md:px-4 md:py-3.5">
                    <div class="profile-header-inner">
                        <div class="profile-header-brand">
                            <div class="profile-header-logo">
                                <img src="{{ $staffHeaderLogo }}" alt="និមិត្តសញ្ញា">
                            </div>
                            <div class="profile-header-copy">
                                <p class="profile-header-kicker">Staff Profile</p>
                                <p class="profile-header-title truncate">{{ $staffHeaderTitle }}</p>
                                @if ($staffHeaderSubtitle !== '')
                                    <p class="profile-header-subtitle truncate">{{ $staffHeaderSubtitle }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="profile-header-actions">
                            <form method="POST" action="{{ route('staff.logout') }}">
                                @csrf
                                <button type="submit" class="logout-wrap">
                                    <span class="logout-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                            <polyline points="16 17 21 12 16 7"></polyline>
                                            <line x1="21" y1="12" x2="9" y2="12"></line>
                                        </svg>
                                    ចាកចេញ
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="space-y-5 p-3 pb-6 sm:p-4 md:p-5">

                    <section class="grid grid-cols-2 items-stretch gap-3 md:grid-cols-[1.45fr_0.9fr] md:gap-6">
                        <article class="ui-card profile-personal-card flex h-full flex-col p-3 sm:p-4 md:p-6" data-profile-personal-card>
                            <h2 class="px-1 text-[13px] font-black tracking-[0.02em] text-slate-800 md:text-[18px]">ព័ត៌មានផ្ទាល់ខ្លួន</h2>
                            <div class="identity-list-mobile-compact mt-4 flex-1 md:mt-6" data-profile-personal-scroll>
                                @foreach ($identityFieldRows as $row)
                                    <div class="identity-grid-row">
                                        @for ($cell = 0; $cell < 2; $cell++)
                                            @php
                                                $field = $row->get($cell);
                                            @endphp
                                            <div class="identity-grid-cell">
                                                @if ($field)
                                                    <div class="identity-icon-box grid h-11 w-11 shrink-0 place-items-center rounded-2xl" style="{{ "background-color: {$field['tone']['bg']}; color: {$field['tone']['text']};" }}">
                                                        @if ($field['iconName'] === 'user')
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M20 21a8 8 0 1 0-16 0"></path>
                                                                <circle cx="12" cy="7" r="4"></circle>
                                                            </svg>
                                                        @elseif ($field['iconName'] === 'signature')
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                                                <path d="M7 10h10M7 14h6"></path>
                                                            </svg>
                                                        @elseif ($field['iconName'] === 'gender')
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <circle cx="10" cy="14" r="5"></circle>
                                                                <path d="M14 10l6-6M16 4h4v4"></path>
                                                            </svg>
                                                        @elseif ($field['iconName'] === 'id')
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <rect x="3" y="6" width="18" height="12" rx="2"></rect>
                                                                <circle cx="8" cy="12" r="2"></circle>
                                                                <path d="M13 10h5M13 14h5"></path>
                                                            </svg>
                                                        @elseif ($field['iconName'] === 'rank')
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3 6.4 20.2l1.1-6.2L3 9.6l6.2-.9Z"></path>
                                                            </svg>
                                                        @elseif ($field['iconName'] === 'phone')
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M22 16.9v3a2 2 0 0 1-2.2 2A19.8 19.8 0 0 1 11.2 19a19.3 19.3 0 0 1-6-6 19.8 19.8 0 0 1-2.9-8.7A2 2 0 0 1 4.2 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.7a2 2 0 0 1-.5 2L8 9.8a16 16 0 0 0 6.2 6.2l1.4-1.3a2 2 0 0 1 2-.5c.9.3 1.8.5 2.7.6a2 2 0 0 1 1.7 2Z"></path>
                                                            </svg>
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M8 2v4"></path>
                                                                <path d="M16 2v4"></path>
                                                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                                                <path d="M3 10h18"></path>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="identity-grid-label">{{ $field['label'] }}</p>
                                                        <p class="identity-grid-value {{ $field['isNumericValue'] ? 'is-numeric' : '' }}">{{ $field['value'] ?: '-' }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endfor
                                    </div>
                                @endforeach
                            </div>
                        </article>

                        <article class="ui-card profile-account-card h-full p-3 sm:p-4 md:p-6" data-profile-account-card>
                            <h2 class="text-center text-[13px] font-black tracking-[0.02em] text-slate-800 md:text-[18px]">បទឧទ្ទេសនាម </h2>

                            <div class="khmer-profile-font mt-3 flex h-full flex-col items-center md:mt-4">
                                <div class="profile-avatar-shell grid h-26 w-26 place-items-center rounded-full p-[4px] md:h-44 md:w-44">
                                    <div class="profile-avatar-core h-full w-full overflow-hidden rounded-full bg-slate-100">
                                        @if ($staff->hasStoredAvatar())
                                            <img src="{{ route('staff.profile.avatar') }}" alt="រូបប្រវត្តិរូប" class="h-full w-full rounded-full object-cover aspect-square">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center rounded-full text-xl font-black text-slate-400 md:text-5xl">{{ $avatarInitial }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="profile-details-box mt-3 w-full text-center md:mt-6">
                                    <p class="profile-id-chip">{{ $staff->id_number ?: '---' }}</p>
                                    <p class="profile-name-kh mt-1 line-clamp-1">{{ $staff->name_kh ?: '-' }}</p>
                                    <p class="profile-name-latin line-clamp-1">{{ $staff->name_latin ?: '-' }}</p>
                                    <div class="profile-meta-list">
                                        <div class="profile-meta-pill">
                                            <span class="profile-meta-label"></span>
                                            <span class="profile-meta-value">{{ $staff->position ?: '-' }}</span>
                                        </div>
                                        
                                        <div class="profile-meta-pill">
                                            <span class="profile-meta-label"></span>
                                            <span class="profile-meta-value">{{ $staff->role ?: '-' }}</span>
                                        </div>
                                        
                                    </div>
                                </div>

                                <span class="profile-account-status mt-4 {{ $staff->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    {{ $staff->is_active ? 'Active' : 'Inactive' }}
                                </span>

                                <div class="profile-account-actions">
                                    <button type="button" class="profile-account-action edit" data-upload-open>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                                        </svg>
                                        Upload
                                    </button>
                                    <form method="POST" action="{{ route('staff.logout') }}">
                                        @csrf
                                        <button type="submit" class="profile-account-action logout w-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                                <polyline points="16 17 21 12 16 7"></polyline>
                                                <line x1="21" y1="12" x2="9" y2="12"></line>
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>


                        </article>
                    </section>

                    <section class="ui-card doc-panel p-4 md:p-5">
                        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-slate-800 md:text-base">ការបញ្ចូលឯកសារ</h3>
                                <p class="mt-0.5 text-[11px] font-medium text-slate-500 md:text-xs">គ្រប់គ្រងឯកសារសិក្ខាកាមតម្រូវការ និងអនុវត្តសកម្មភាពបានរហ័ស។</p>
                            </div>
                           
                        </div>

                        <div class="mb-4 flex flex-wrap items-center gap-2">
                            <button type="button" class="filter-chip px-3 py-1.5 text-[11px] font-semibold" data-doc-filter data-filter-value="all" data-active="true">
                                ទាំងអស់ ({{ $filterCounts['all'] }})
                            </button>
                            <button type="button" class="filter-chip px-3 py-1.5 text-[11px] font-semibold" data-doc-filter data-filter-value="pending" data-active="false">
                                រង់ចាំ ({{ $filterCounts['pending'] }})
                            </button>
                            <button type="button" class="filter-chip px-3 py-1.5 text-[11px] font-semibold" data-doc-filter data-filter-value="approved" data-active="false">
                                អនុម័ត ({{ $filterCounts['approved'] }})
                            </button>
                            <button type="button" class="filter-chip px-3 py-1.5 text-[11px] font-semibold" data-doc-filter data-filter-value="rejected" data-active="false">
                                បដិសេធ ({{ $filterCounts['rejected'] }})
                            </button>
                            <p class="ml-auto text-[11px] font-semibold text-slate-500">បង្ហាញ <span data-doc-visible-count>{{ $requiredDocumentCount }}</span> / {{ $requiredDocumentCount }}</p>
                        </div>

                        @if ($documentRequirements->isNotEmpty())
                            <div class="grid grid-cols-2 gap-3">
                                @foreach ($requirementSummaries as $summary)
                                    @php
                                        $requirement = $summary['requirement'];
                                        $reqDocs = $summary['reqDocs'];
                                        $badge = $summary['badge'];
                                        $isCompleted = $summary['isCompleted'];
                                    @endphp
                                    <article
                                        class="doc-requirement-card cursor-pointer p-3"
                                        role="button"
                                        tabindex="0"
                                        data-upload-card
                                        data-doc-card
                                        data-doc-status="{{ $badge['key'] }}"
                                        data-upload-requirement="{{ $requirement->id }}"
                                    >
                                        <div class="flex items-start justify-between gap-2">
                                            <p class="doc-title line-clamp-2 leading-relaxed">{{ $requirement->name_kh }}</p>
                                            <span class="doc-status-chip shrink-0 {{ $badge['class'] }}">
                                                {{ $badge['label'] }}
                                            </span>
                                        </div>

                                        <div class="doc-upload-zone mt-3 space-y-2">
                                            @if ($reqDocs->isNotEmpty())
                                                @foreach ($reqDocs as $doc)
                                                    @php
                                                        $fileName = $doc['original_name'] ?? '-';
                                                        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                        $typeCode = strtoupper($extension !== '' ? $extension : 'file');
                                                        $typeCode = strlen($typeCode) > 4 ? substr($typeCode, 0, 4) : $typeCode;
                                                        $sizeText = $formatFileSize($doc['path'] ?? null) ?: '--';
                                                        $dateText = $formatUploadedAt($doc['uploaded_at'] ?? null);
                                                    @endphp
                                                    <div class="file-row px-2.5 py-2">
                                                        <div class="file-row-main">
                                                            <div class="grid h-8 w-8 shrink-0 place-items-center rounded-md border border-slate-200 bg-slate-50 text-[9px] font-bold uppercase text-slate-500">
                                                                {{ $typeCode }}
                                                            </div>
                                                            <div class="file-row-meta">
                                                                <p class="truncate text-[11px] font-semibold text-slate-700">{{ $fileName }}</p>
                                                                <p class="text-[10px] font-medium text-slate-500">{{ $sizeText }} / {{ $dateText }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="file-row-actions">
                                                            <button type="button" class="btn-action btn-outline" data-doc-preview-open data-doc-preview-url="{{ route('staff.profile.documents.show', $doc['document_index']) }}" data-doc-preview-name="{{ $fileName }}">មើល</button>
                                                            <a href="{{ route('staff.profile.documents.download', $doc['document_index']) }}" class="btn-action btn-soft">ទាញយក</a>
                                                            @if (($doc['uploaded_by'] ?? null) === 'staff' && strtolower((string) ($doc['status'] ?? 'pending')) !== 'approved')
                                                                <form method="POST" action="{{ route('staff.profile.documents.destroy', $doc['document_index']) }}" data-doc-delete-form>
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" class="btn-action btn-danger" data-doc-delete-trigger data-doc-delete-name="{{ $fileName }}">លុប</button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-3 py-4 text-center">
                                                    <div class="mx-auto grid h-10 w-10 place-items-center rounded-full bg-white text-slate-400 shadow-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                            <path d="M7 10l5-5 5 5"></path>
                                                            <path d="M12 5v12"></path>
                                                        </svg>
                                                    </div>
                                                    <p class="mt-2 text-[11px] font-semibold text-slate-600">មិនទាន់មានឯកសារបញ្ចូល</p>
                                                    <p class="mt-0.5 text-[10px] font-medium text-slate-500">ចុចបញ្ចូល ដើម្បីភ្ជាប់ឯកសារតម្រូវការ។</p>
                                                </div>
                                            @endif
                                        </div>

                                        <button type="button" class="btn-action doc-cta mt-3 w-full {{ $isCompleted ? 'btn-outline' : 'btn-soft' }}" data-upload-open data-upload-requirement="{{ $requirement->id }}">
                                            {{ $isCompleted ? 'បញ្ចូលបន្ថែម' : 'បញ្ចូលឥឡូវ' }}
                                        </button>
                                    </article>
                                @endforeach
                            </div>

                            <div class="mt-3 hidden rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center" data-doc-filter-empty>
                                <div class="mx-auto grid h-11 w-11 place-items-center rounded-full bg-white text-slate-400 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="m21 21-4.3-4.3"></path>
                                    </svg>
                                </div>
                                <p class="mt-2 text-sm font-bold text-slate-700">មិនមានឯកសារក្នុងតម្រងនេះ</p>
                                <p class="mt-1 text-xs font-medium text-slate-500">សូមជ្រើសស្ថានភាពផ្សេង ឬបញ្ចូលឯកសារថ្មី។</p>
                                <button type="button" class="btn-action btn-soft mx-auto mt-3" data-upload-open>បញ្ចូលឯកសារ</button>
                            </div>
                        @else
                            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                                <div class="mx-auto grid h-11 w-11 place-items-center rounded-full bg-white text-slate-400 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 4h16v16H4z"></path>
                                        <path d="M8 12h8"></path>
                                    </svg>
                                </div>
                                <p class="mt-2 text-sm font-bold text-slate-700">មិនមានតម្រូវការឯកសារ</p>
                                <p class="mt-1 text-xs font-medium text-slate-500">សូមទាក់ទងអ្នកគ្រប់គ្រង ដើម្បីកំណត់តម្រូវការឯកសារ។</p>
                                <button type="button" class="btn-action btn-soft mx-auto mt-3" data-upload-open>បញ្ចូលឯកសារផ្សេង</button>
                            </div>
                        @endif

                        @if ($otherDocuments->isNotEmpty())
                            <div class="ui-subcard mt-4 p-3">
                                <div class="mb-2 flex items-center justify-between">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-600">ឯកសារផ្សេងៗ</p>
                                    <p class="text-[11px] font-semibold text-slate-500">{{ $otherDocuments->count() }} ឯកសារ</p>
                                </div>
                                <div class="space-y-2">
                                    @foreach ($otherDocuments as $doc)
                                        @php
                                            $fileName = $doc['original_name'] ?? '-';
                                            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                            $typeCode = strtoupper($extension !== '' ? $extension : 'file');
                                            $typeCode = strlen($typeCode) > 4 ? substr($typeCode, 0, 4) : $typeCode;
                                            $sizeText = $formatFileSize($doc['path'] ?? null) ?: '--';
                                            $dateText = $formatUploadedAt($doc['uploaded_at'] ?? null);
                                        @endphp
                                        <div class="file-row px-2.5 py-2">
                                            <div class="file-row-main">
                                                <div class="grid h-8 w-8 shrink-0 place-items-center rounded-md border border-slate-200 bg-slate-50 text-[9px] font-bold uppercase text-slate-500">{{ $typeCode }}</div>
                                                <div class="file-row-meta">
                                                    <p class="truncate text-[11px] font-semibold text-slate-700">{{ $doc['label'] ?? 'ឯកសារ' }} - {{ $fileName }}</p>
                                                    <p class="text-[10px] font-medium text-slate-500">{{ $sizeText }} / {{ $dateText }}</p>
                                                </div>
                                            </div>
                                            <div class="file-row-actions">
                                                <button type="button" class="btn-action btn-outline" data-doc-preview-open data-doc-preview-url="{{ route('staff.profile.documents.show', $doc['document_index']) }}" data-doc-preview-name="{{ $fileName }}">មើល</button>
                                                <a href="{{ route('staff.profile.documents.download', $doc['document_index']) }}" class="btn-action btn-soft">ទាញយក</a>
                                                @if (($doc['uploaded_by'] ?? null) === 'staff' && strtolower((string) ($doc['status'] ?? 'pending')) !== 'approved')
                                                    <form method="POST" action="{{ route('staff.profile.documents.destroy', $doc['document_index']) }}" data-doc-delete-form>
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn-action btn-danger" data-doc-delete-trigger data-doc-delete-name="{{ $fileName }}">លុប</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </section>
                </main>
            </div>
            </div>
        </div>

        <button type="button" class="fixed bottom-4 left-1/2 z-40 inline-flex -translate-x-1/2 items-center justify-center rounded-full border border-sky-200 bg-sky-600 px-5 py-3 text-xs font-bold uppercase tracking-[0.08em] text-white shadow-lg shadow-sky-600/25 transition hover:bg-sky-700 md:hidden" data-upload-open>
            បញ្ចូលឯកសារ
        </button>
    </div>

    <div class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/45 p-4 opacity-0 backdrop-blur-[3px] transition-opacity duration-300" data-preview-modal aria-hidden="true">
        <div class="absolute inset-0" data-preview-close></div>
        <div class="relative z-10 w-full max-w-5xl translate-y-4 overflow-hidden rounded-2xl border border-slate-200 bg-white opacity-0 shadow-2xl transition-all duration-300" data-preview-content>
            <div class="flex items-center justify-between gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3">
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500">មើលឯកសារ</p>
                    <p class="truncate text-sm font-bold text-slate-700" data-preview-title>ឯកសារ</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="#" target="_blank" rel="noopener" class="inline-flex items-center gap-1 rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-600 transition hover:bg-slate-100" data-preview-open-external>
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M14 3h7v7"></path>
                            <path d="M10 14 21 3"></path>
                            <path d="M21 14v7h-7"></path>
                            <path d="M3 10V3h7"></path>
                            <path d="M3 21h7v-7"></path>
                        </svg>
                        បើកផ្ទាំងថ្មី
                    </a>
                    <button type="button" class="grid h-8 w-8 place-items-center rounded-md border border-slate-300 bg-white text-slate-500 transition hover:bg-slate-100" data-preview-close>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="h-[72vh] bg-slate-100 p-2">
                <div class="h-full w-full overflow-hidden rounded-lg border border-slate-200 bg-white">
                    <img src="" alt="" class="hidden h-full w-full object-contain bg-slate-50" loading="lazy" data-preview-image>
                    <iframe src="about:blank" class="h-full w-full border-0" loading="lazy" data-preview-frame></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-[65] hidden items-center justify-center bg-slate-900/45 p-4 opacity-0 backdrop-blur-[3px] transition-opacity duration-300" data-delete-modal aria-hidden="true">
        <div class="absolute inset-0" data-delete-close></div>
        <div class="relative z-10 w-full max-w-sm translate-y-4 overflow-hidden rounded-2xl border border-slate-200 bg-white opacity-0 shadow-2xl transition-all duration-300" data-delete-content>
            <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
                <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-rose-600">បញ្ជាក់ការលុប</p>
                <p class="mt-1 text-sm font-bold text-slate-700" data-delete-title>តើអ្នកចង់លុបឯកសារនេះមែនទេ?</p>
            </div>
            <div class="space-y-3 px-4 py-4">
                <p class="text-[11px] font-medium leading-relaxed text-slate-600">
                    ការលុបនេះមិនអាចត្រឡប់ក្រោយបានទេ។ សូមបញ្ជាក់ម្តងទៀតមុនបន្ត។
                </p>
                <div class="modal-footer-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" data-delete-close>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        បោះបង់
                    </button>
                    <button type="button" class="modal-btn modal-btn-danger" data-delete-confirm>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                        លុបឯកសារ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/35 p-4 opacity-0 backdrop-blur-[2px] transition-opacity duration-300" data-upload-modal aria-hidden="true">
        <div class="absolute inset-0" data-upload-close></div>
        <div class="relative z-10 w-full max-w-md translate-y-4 overflow-hidden rounded-xl border border-slate-200 bg-white opacity-0 shadow-xl transition-all duration-300" data-upload-content>
            <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-[0.08em] text-slate-700">បញ្ចូលឯកសារ</h3>
                        <p class="text-[10px] font-medium text-slate-500">ជ្រើសប្រភេទ ហើយជ្រើសឯកសារ</p>
                    </div>
                    <button type="button" class="grid h-8 w-8 place-items-center rounded-md border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-100" data-upload-close>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('staff.profile.documents.store') }}" enctype="multipart/form-data" class="space-y-4 p-4" data-upload-form>
                @csrf
                @if ($documentRequirements->isNotEmpty())
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500">ប្រភេទឯកសារ</label>
                        <div class="relative">
                            <select id="document_requirement_id" name="document_requirement_id" class="w-full appearance-none rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10" required data-upload-requirement-select>
                                <option value="">--- ជ្រើសប្រភេទឯកសារ ---</option>
                                @foreach ($documentRequirements as $req)
                                    <option value="{{ $req->id }}">{{ $req->name_kh }}</option>
                                @endforeach
                            </select>
                    57308  <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="space-y-1.5">
                        <label for="document_title" class="ml-1 text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500">ចំណងជើងឯកសារ</label>
                        <input id="document_title" name="document_title" type="text" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10" placeholder="សូមបញ្ចូលចំណងជើងឲ្យច្បាស់" required>
                    </div>
                @endif

                <div class="space-y-1.5">
                    <label class="ml-1 text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500">ឯកសារបញ្ចូល</label>
                    <label class="group flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-7 text-center transition hover:border-sky-400 hover:bg-sky-50/50" data-upload-dropzone>
                        <div class="grid h-12 w-12 place-items-center rounded-xl bg-white text-slate-400 shadow-sm transition group-hover:text-sky-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="17 8 12 3 7 8" />
                                <line x1="12" y1="3" x2="12" y2="15" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-800" data-upload-file-name>អូសឯកសារមកទីនេះ ឬចុចដើម្បីជ្រើស (ច្រើនឯកសារ)</span>
                            <span class="mt-1 block text-[9px] font-bold uppercase tracking-[0.1em] text-slate-400">PDF, JPG, PNG, DOC, DOCX (អតិបរមា 10MB ក្នុងមួយឯកសារ)</span>
                        </div>
                        <input type="file" name="document_files[]" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" multiple required data-upload-input>
                    </label>
                </div>

                <div class="modal-footer-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" data-upload-close>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        បោះបង់
                    </button>
                    <button type="submit" class="modal-btn modal-btn-save" data-upload-submit>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        រក្សាទុក
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.querySelector('[data-upload-modal]');
            const content = document.querySelector('[data-upload-content]');
            const requirementSelect = document.querySelector('[data-upload-requirement-select]');
            const fileInput = document.querySelector('[data-upload-input]');
            const fileName = document.querySelector('[data-upload-file-name]');
            const dropzone = document.querySelector('[data-upload-dropzone]');
            const uploadForm = document.querySelector('[data-upload-form]');
            const uploadSubmit = document.querySelector('[data-upload-submit]');
            const filterButtons = Array.from(document.querySelectorAll('[data-doc-filter]'));
            const docCards = Array.from(document.querySelectorAll('[data-doc-card]'));
            const visibleCount = document.querySelector('[data-doc-visible-count]');
            const filterEmpty = document.querySelector('[data-doc-filter-empty]');
            const previewModal = document.querySelector('[data-preview-modal]');
            const previewContent = document.querySelector('[data-preview-content]');
            const previewFrame = document.querySelector('[data-preview-frame]');
            const previewImage = document.querySelector('[data-preview-image]');
            const previewTitle = document.querySelector('[data-preview-title]');
            const previewOpenExternal = document.querySelector('[data-preview-open-external]');
            const deleteModal = document.querySelector('[data-delete-modal]');
            const deleteContent = document.querySelector('[data-delete-content]');
            const deleteTitle = document.querySelector('[data-delete-title]');
            const deleteConfirm = document.querySelector('[data-delete-confirm]');
            const personalCard = document.querySelector('[data-profile-personal-card]');
            const personalScroll = document.querySelector('[data-profile-personal-scroll]');
            const accountCard = document.querySelector('[data-profile-account-card]');
            let pendingDeleteForm = null;

            if (!modal || !content) {
                return;
            }

            const syncBodyLock = () => {
                const isUploadOpen = !modal.classList.contains('hidden');
                const isPreviewOpen = !!previewModal && !previewModal.classList.contains('hidden');
                const isDeleteOpen = !!deleteModal && !deleteModal.classList.contains('hidden');
                document.body.classList.toggle('overflow-hidden', isUploadOpen || isPreviewOpen || isDeleteOpen);
            };

            const syncTopCardHeights = () => {
                if (!personalCard || !accountCard) {
                    return;
                }

                if (window.matchMedia('(max-width: 767px)').matches) {
                    const targetHeight = Math.round(accountCard.getBoundingClientRect().height);
                    if (targetHeight > 0) {
                        personalCard.style.height = `${targetHeight}px`;
                        if (personalScroll) {
                            personalScroll.style.minHeight = '0';
                        }
                    }
                    return;
                }

                personalCard.style.height = '';
                if (personalScroll) {
                    personalScroll.style.minHeight = '';
                }
            };

            const resetFileState = () => {
                if (!fileName) {
                    return;
                }

                if (fileInput && fileInput.files && fileInput.files.length > 0) {
                    if (fileInput.files.length === 1) {
                        fileName.textContent = fileInput.files[0].name;
                    } else {
                        fileName.textContent = `${fileInput.files.length} ឯកសារត្រូវបានជ្រើស`;
                    }
                    dropzone?.classList.add('border-sky-400', 'bg-sky-50/50');
                    return;
                }

                fileName.textContent = 'អូសឯកសារមកទីនេះ ឬចុចដើម្បីជ្រើស (ច្រើនឯកសារ)';
                dropzone?.classList.remove('border-sky-400', 'bg-sky-50/50');
            };

            const openModal = (requirementId = '') => {
                if (requirementSelect) {
                    requirementSelect.value = requirementId || '';
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
                syncBodyLock();

                requestAnimationFrame(() => {
                    modal.classList.remove('opacity-0');
                    content.classList.remove('translate-y-4', 'opacity-0');
                });
            };

            const closeModal = () => {
                modal.classList.add('opacity-0');
                content.classList.add('translate-y-4', 'opacity-0');
                modal.setAttribute('aria-hidden', 'true');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    syncBodyLock();
                }, 220);
            };

            const setPreviewMode = (mode = 'frame') => {
                if (previewImage) {
                    previewImage.classList.toggle('hidden', mode !== 'image');
                }
                if (previewFrame) {
                    previewFrame.classList.toggle('hidden', mode === 'image');
                }
            };

            const openPreview = (url = '', docName = '') => {
                if (!url) {
                    return;
                }

                if (!previewModal || !previewContent || !previewFrame) {
                    window.open(url, '_blank', 'noopener');
                    return;
                }

                previewFrame.src = url;
                if (previewTitle) {
                    previewTitle.textContent = docName || 'ឯកសារ';
                }
                if (previewOpenExternal) {
                    previewOpenExternal.href = url;
                }

                const previewSource = `${docName} ${url}`.toLowerCase();
                const isImageFile = /\.(png|jpe?g|gif|webp|bmp|svg)([?#]|$)/.test(previewSource);

                if (isImageFile && previewImage) {
                    previewImage.src = url;
                    previewImage.alt = docName || 'Document';
                    if (previewFrame) {
                        previewFrame.src = 'about:blank';
                    }
                    setPreviewMode('image');
                } else {
                    if (previewImage) {
                        previewImage.src = '';
                    }
                    previewFrame.src = url;
                    setPreviewMode('frame');
                }

                previewModal.classList.remove('hidden');
                previewModal.classList.add('flex');
                previewModal.setAttribute('aria-hidden', 'false');
                syncBodyLock();

                requestAnimationFrame(() => {
                    previewModal.classList.remove('opacity-0');
                    previewContent.classList.remove('translate-y-4', 'opacity-0');
                });
            };

            const closePreview = () => {
                if (!previewModal || !previewContent) {
                    return;
                }

                previewModal.classList.add('opacity-0');
                previewContent.classList.add('translate-y-4', 'opacity-0');
                previewModal.setAttribute('aria-hidden', 'true');

                setTimeout(() => {
                    previewModal.classList.add('hidden');
                    previewModal.classList.remove('flex');
                    if (previewFrame) {
                        previewFrame.src = 'about:blank';
                    }
                    if (previewImage) {
                        previewImage.src = '';
                    }
                    setPreviewMode('frame');
                    syncBodyLock();
                }, 220);
            };

            const openDeleteModal = (form, docName = '') => {
                if (!form || !deleteModal || !deleteContent) {
                    form?.submit();
                    return;
                }

                pendingDeleteForm = form;
                if (deleteTitle) {
                    deleteTitle.textContent = docName
                        ? `តើអ្នកចង់លុប "${docName}" មែនទេ?`
                        : 'តើអ្នកចង់លុបឯកសារនេះមែនទេ?';
                }

                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
                deleteModal.setAttribute('aria-hidden', 'false');
                syncBodyLock();

                requestAnimationFrame(() => {
                    deleteModal.classList.remove('opacity-0');
                    deleteContent.classList.remove('translate-y-4', 'opacity-0');
                });
            };

            const closeDeleteModal = () => {
                if (!deleteModal || !deleteContent) {
                    return;
                }

                deleteModal.classList.add('opacity-0');
                deleteContent.classList.add('translate-y-4', 'opacity-0');
                deleteModal.setAttribute('aria-hidden', 'true');

                setTimeout(() => {
                    deleteModal.classList.add('hidden');
                    deleteModal.classList.remove('flex');
                    pendingDeleteForm = null;
                    if (deleteConfirm) {
                        deleteConfirm.removeAttribute('disabled');
                        deleteConfirm.classList.remove('opacity-70', 'cursor-not-allowed');
                    }
                    syncBodyLock();
                }, 220);
            };

            document.querySelectorAll('[data-upload-open]').forEach((button) => {
                button.addEventListener('click', () => {
                    openModal(button.dataset.uploadRequirement || '');
                });
            });

            document.querySelectorAll('[data-upload-card]').forEach((card) => {
                card.addEventListener('click', (event) => {
                    if (event.target.closest('a, button, input, select, textarea, label, form')) {
                        return;
                    }

                    openModal(card.dataset.uploadRequirement || '');
                });

                card.addEventListener('keydown', (event) => {
                    if (event.key !== 'Enter' && event.key !== ' ') {
                        return;
                    }

                    event.preventDefault();
                    openModal(card.dataset.uploadRequirement || '');
                });
            });

            document.querySelectorAll('[data-doc-preview-open]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    openPreview(button.dataset.docPreviewUrl || '', button.dataset.docPreviewName || 'ឯកសារ');
                });
            });

            document.querySelectorAll('[data-preview-close]').forEach((button) => {
                button.addEventListener('click', closePreview);
            });

            document.querySelectorAll('[data-doc-delete-trigger]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    const form = button.closest('form[data-doc-delete-form]');
                    openDeleteModal(form, button.dataset.docDeleteName || '');
                });
            });

            document.querySelectorAll('[data-delete-close]').forEach((button) => {
                button.addEventListener('click', closeDeleteModal);
            });

            if (deleteConfirm) {
                deleteConfirm.addEventListener('click', () => {
                    if (!pendingDeleteForm) {
                        closeDeleteModal();
                        return;
                    }

                    deleteConfirm.setAttribute('disabled', 'disabled');
                    deleteConfirm.classList.add('opacity-70', 'cursor-not-allowed');
                    pendingDeleteForm.submit();
                });
            }

            const applyDocFilter = (filterValue = 'all') => {
                let visibleItems = 0;

                docCards.forEach((card) => {
                    const status = card.dataset.docStatus || 'pending';
                    const shouldShow = filterValue === 'all' || filterValue === status;
                    card.classList.toggle('hidden', !shouldShow);
                    if (shouldShow) {
                        visibleItems += 1;
                    }
                });

                if (visibleCount) {
                    visibleCount.textContent = String(visibleItems);
                }

                if (filterEmpty) {
                    filterEmpty.classList.toggle('hidden', visibleItems !== 0 || docCards.length === 0);
                }

                filterButtons.forEach((button) => {
                    const isActive = (button.dataset.filterValue || 'all') === filterValue;
                    button.dataset.active = isActive ? 'true' : 'false';
                });
            };

            if (filterButtons.length && docCards.length) {
                filterButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        applyDocFilter(button.dataset.filterValue || 'all');
                    });
                });

                const defaultButton = filterButtons.find((button) => button.dataset.active === 'true');
                applyDocFilter(defaultButton?.dataset.filterValue || 'all');
            }

            document.querySelectorAll('[data-upload-close]').forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            window.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && previewModal && !previewModal.classList.contains('hidden')) {
                    closePreview();
                    return;
                }
                if (event.key === 'Escape' && deleteModal && !deleteModal.classList.contains('hidden')) {
                    closeDeleteModal();
                    return;
                }
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });

            if (fileInput) {
                fileInput.addEventListener('change', resetFileState);
            }

            if (dropzone) {
                ['dragenter', 'dragover'].forEach((eventName) => {
                    dropzone.addEventListener(eventName, (event) => {
                        event.preventDefault();
                        dropzone.classList.add('border-sky-400', 'bg-sky-50/50');
                    });
                });

                ['dragleave', 'drop'].forEach((eventName) => {
                    dropzone.addEventListener(eventName, (event) => {
                        event.preventDefault();
                        if (eventName === 'drop' && fileInput && event.dataTransfer?.files?.length) {
                            fileInput.files = event.dataTransfer.files;
                        }
                        resetFileState();
                    });
                });
            }

            if (uploadForm && uploadSubmit) {
                uploadForm.addEventListener('submit', () => {
                    uploadSubmit.setAttribute('disabled', 'disabled');
                    uploadSubmit.classList.add('opacity-70', 'cursor-not-allowed');
                    uploadSubmit.textContent = 'កំពុងបញ្ចូល...';
                });
            }
            window.addEventListener('resize', syncTopCardHeights);
            window.addEventListener('load', syncTopCardHeights);
            if (document.fonts?.ready) {
                document.fonts.ready.then(syncTopCardHeights).catch(() => {});
            }
            requestAnimationFrame(syncTopCardHeights);
        })();
    </script>
@endsection

