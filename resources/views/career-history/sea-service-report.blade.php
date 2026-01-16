<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sea Service Report - {{ $user->first_name }} {{ $user->last_name }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #0053FF;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0053FF;
            margin: 0;
            font-size: 28px;
        }
        .header h2 {
            color: #666;
            margin: 10px 0 0 0;
            font-size: 18px;
            font-weight: normal;
        }
        .user-info {
            background: #f5f6fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .user-info p {
            margin: 5px 0;
        }
        .summary {
            background: #e6f2ff;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
            text-align: center;
        }
        .summary h3 {
            color: #0053FF;
            margin-top: 0;
        }
        .summary .total {
            font-size: 32px;
            font-weight: bold;
            color: #0053FF;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background: #0053FF;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background: #f9f9f9;
        }
        .duration {
            font-weight: bold;
            color: #0053FF;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .disclaimer {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-top: 30px;
            font-size: 12px;
        }
        .no-entries {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sea Service Report</h1>
        <h2>Yacht Workers Council</h2>
    </div>

    <div class="user-info">
        <p><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Report Generated:</strong> {{ $generatedAt }}</p>
    </div>

    <div class="summary">
        <h3>Total Sea Service</h3>
        <div class="total">
            @if($totalYears > 0)
                {{ $totalYears }} {{ $totalYears == 1 ? 'Year' : 'Years' }}
            @endif
            @if($totalMonths > 0)
                {{ $totalMonths }} {{ $totalMonths == 1 ? 'Month' : 'Months' }}
            @endif
            @if($finalDays > 0 && $totalYears == 0 && $totalMonths == 0)
                {{ $finalDays }} {{ $finalDays == 1 ? 'Day' : 'Days' }}
            @endif
            @if($totalYears == 0 && $totalMonths == 0 && $finalDays == 0)
                Less than 1 month
            @endif
        </div>
        <p style="margin: 5px 0; color: #666;">Total Days: {{ number_format($totalDays) }}</p>
    </div>

    @if(count($entries) > 0)
    <h3 style="color: #0053FF; margin-bottom: 15px;">Career History Entries</h3>
    <table>
        <thead>
            <tr>
                <th>Vessel Name</th>
                <th>Position</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Duration</th>
                <th>Sea Service Days</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $item)
            <tr>
                <td>{{ $item['entry']->vessel_name }}</td>
                <td>{{ $item['entry']->position_title }}</td>
                <td>{{ $item['entry']->start_date->format('M d, Y') }}</td>
                <td>{{ $item['entry']->end_date ? $item['entry']->end_date->format('M d, Y') : 'Current' }}</td>
                <td class="duration">
                    @if($item['years'] > 0)
                        {{ $item['years'] }} {{ $item['years'] == 1 ? 'yr' : 'yrs' }}
                    @endif
                    @if($item['months'] > 0)
                        {{ $item['months'] }} {{ $item['months'] == 1 ? 'mo' : 'mos' }}
                    @endif
                </td>
                <td>{{ number_format($item['days']) }} days</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-entries">
        <p>No career history entries with qualifying sea service found.</p>
    </div>
    @endif

    <div class="disclaimer">
        <strong>Disclaimer:</strong> This report is generated based on the career history entries provided by the crew member. 
        The sea service calculations are based on the dates and positions recorded. For official certification purposes, 
        this report may need to be verified by maritime authorities or certification bodies. Supporting documentation 
        (contracts, sign-off papers, reference letters) should be provided when submitting this report for official purposes.
    </div>

    <div class="footer">
        <p>Generated by Yacht Workers Council</p>
        <p>{{ $generatedAt }}</p>
    </div>
</body>
</html>
