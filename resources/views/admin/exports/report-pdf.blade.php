<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Report' }}</title>
    <style>
        @page {
            margin: 40px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333333;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        /* Header Layout */
        .report-header {
            width: 100%;
            border-bottom: 2px solid #333333;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .report-header table {
            width: 100%;
            border: none;
        }
        .report-header td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            color: #555555;
            margin-top: 5px;
        }
        .date-range {
            font-size: 11px;
            color: #777777;
            text-align: right;
        }

        /* Summary Section */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #dddddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
            margin-top: 30px;
            color: #111111;
        }
        
        .summary-boxes {
            width: 100%;
            margin-bottom: 25px;
        }
        .summary-boxes td {
            width: 33.33%;
            border: 1px solid #cccccc;
            padding: 15px;
            text-align: center;
            background-color: #f9f9f9;
        }
        .summary-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #555555;
            font-weight: bold;
        }
        .summary-value {
            font-size: 22px;
            font-weight: bold;
            color: #000000;
            margin-top: 5px;
        }

        /* Chart Placeholder (DomPDF can only render basic divs or images) */
        .chart-placeholder {
            width: 100%;
            height: 150px;
            border: 1px dashed #999999;
            background-color: #f4f4f4;
            text-align: center;
            margin-bottom: 25px;
        }
        .chart-placeholder table {
            width: 100%;
            height: 100%;
        }
        .chart-placeholder td {
            text-align: center;
            vertical-align: middle;
            color: #888888;
            font-weight: bold;
            font-size: 14px;
            letter-spacing: 1px;
        }

        /* Data Table Layout */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .data-table th, .data-table td {
            border: 1px solid #bbbbbb;
            padding: 8px 10px;
            text-align: left;
        }
        .data-table th {
            background-color: #eeeeee;
            font-weight: bold;
            font-size: 11px;
            color: #333333;
            text-transform: uppercase;
        }
        .data-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        .text-center {
            text-align: center !important;
        }
        
        .status-badge {
            font-weight: bold;
            padding: 2px 6px;
            border: 1px solid #555555;
            border-radius: 3px;
            font-size: 10px;
            text-transform: uppercase;
        }

        /* Footer Layout */
        .report-footer {
            position: fixed;
            bottom: -10px;
            width: 100%;
            border-top: 1px solid #dddddd;
            padding-top: 10px;
        }
        .report-footer table {
            width: 100%;
            border: none;
        }
        .report-footer td {
            font-size: 9px;
            color: #777777;
            border: none;
        }
        .footer-left {
            text-align: left;
        }
        .footer-right {
            text-align: right;
        }
        .page-number:before {
            content: "Page " counter(page);
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="report-header">
        <table cellspacing="0" cellpadding="0">
            <tr>
                <td style="width: 70%;">
                    <div class="company-name">{{ config('app.name', 'MINISTRY OF DEFENSE') }}</div>
                    <div class="report-title">{{ $title ?? 'Trainee Registration Report' }}</div>
                </td>
                <td style="width: 30%;" class="date-range">
                    <strong style="color:#333;">Reporting Period:</strong><br>
                    {{ $startDate ?? '01/01/2026' }} - {{ $endDate ?? '31/01/2026' }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Summary Section -->
    <div class="section-title">Summary Overview</div>
    <table class="summary-boxes" cellspacing="0" cellpadding="0">
        <tr>
            <!-- Left Box -->
            <td style="border-right: none;">
                <div class="summary-label">Total Records</div>
                <div class="summary-value">{{ $summary['total'] ?? 120 }}</div>
            </td>
            <!-- Middle Box -->
            <td style="border-right: none;">
                <div class="summary-label">Active / Valid</div>
                <div class="summary-value">{{ $summary['active'] ?? 95 }}</div>
            </td>
            <!-- Right Box -->
            <td>
                <div class="summary-label">Inactive / Pending</div>
                <div class="summary-value">{{ $summary['inactive'] ?? 25 }}</div>
            </td>
        </tr>
    </table>

    <!-- Chart Placeholder -->
    <div class="section-title">Visual Analytics</div>
    <div class="chart-placeholder">
        <table cellspacing="0" cellpadding="0">
            <tr>
                <td>[ Chart Visualization Area ]<br><span style="font-size:9px;font-weight:normal;color:#aaa;">Included via image encoding or backend generation</span></td>
            </tr>
        </table>
    </div>

    <!-- Data Table Section -->
    <div class="section-title">Detailed Data Table</div>
    <table class="data-table" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th style="width: 15%;">ID CODE</th>
                <th style="width: 25%;">NAME</th>
                <th style="width: 30%;">DEPARTMENT / TEAM</th>
                <th style="width: 15%;">DATE</th>
                <th style="width: 15%; text-align: center;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                <td><strong>{{ $row['id'] }}</strong></td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['department'] }}</td>
                <td>{{ $row['date'] }}</td>
                <td class="text-center">
                    <span class="status-badge">{{ $row['status'] }}</span>
                </td>
            </tr>
            @empty
            <!-- Placeholder rows if no direct $data array is passed -->
            <tr>
                <td><strong>#TR-001</strong></td>
                <td>Sok Sao</td>
                <td>Engineering Department (IT)</td>
                <td>04 Apr 2026</td>
                <td class="text-center"><span class="status-badge">ACTIVE</span></td>
            </tr>
            <tr>
                <td><strong>#TR-002</strong></td>
                <td>Khmer Angkor</td>
                <td>Human Resources (HR)</td>
                <td>05 Apr 2026</td>
                <td class="text-center"><span class="status-badge">INACTIVE</span></td>
            </tr>
            <tr>
                <td><strong>#TR-003</strong></td>
                <td>Bopha Nary</td>
                <td>Operations Unit</td>
                <td>08 Apr 2026</td>
                <td class="text-center"><span class="status-badge">ACTIVE</span></td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer Section -->
    <div class="report-footer">
        <table cellspacing="0" cellpadding="0">
            <tr>
                <td class="footer-left">
                    Generated by: System Administrator | Generated on: {{ date('Y-m-d H:i:s') }}
                </td>
                <td class="footer-right">
                    <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
