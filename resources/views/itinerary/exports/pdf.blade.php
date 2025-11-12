<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $route->title }} - Route Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            border-bottom: 2px solid #0053FF;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0053FF;
            margin: 0;
        }
        .meta {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .meta-item {
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .meta-label {
            font-weight: bold;
            color: #666;
            font-size: 12px;
        }
        .meta-value {
            font-size: 16px;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #0053FF;
            color: white;
        }
        .section {
            margin: 30px 0;
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #0053FF;
            margin-bottom: 15px;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $route->title }}</h1>
        @if($route->description)
            <p>{{ $route->description }}</p>
        @endif
    </div>

    <div class="meta">
        <div class="meta-item">
            <div class="meta-label">Region</div>
            <div class="meta-value">{{ $route->region ?: '—' }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Difficulty</div>
            <div class="meta-value">{{ $route->difficulty ?: '—' }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Season</div>
            <div class="meta-value">{{ $route->season ?: '—' }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Duration</div>
            <div class="meta-value">{{ $route->duration_days }} days</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Distance</div>
            <div class="meta-value">{{ number_format($route->distance_nm, 2) }} NM</div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Status</div>
            <div class="meta-value">{{ ucfirst($route->status) }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Route Stops</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Coordinates</th>
                    <th>Stay Duration</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($route->stops as $stop)
                    <tr>
                        <td>{{ $stop->sequence }}</td>
                        <td>{{ $stop->name }}</td>
                        <td>{{ $stop->location_label ?: '—' }}</td>
                        <td>
                            @if($stop->latitude && $stop->longitude)
                                {{ $stop->latitude }}, {{ $stop->longitude }}
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $stop->stay_duration_hours ? $stop->stay_duration_hours . ' hrs' : '—' }}</td>
                        <td>{{ $stop->notes ?: '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($route->legs->isNotEmpty())
        <div class="section">
            <div class="section-title">Route Legs</div>
            <table>
                <thead>
                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Distance (NM)</th>
                        <th>Estimated Hours</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($route->legs as $leg)
                        <tr>
                            <td>{{ optional($leg->from)->name ?? '—' }}</td>
                            <td>{{ optional($leg->to)->name ?? '—' }}</td>
                            <td>{{ number_format($leg->distance_nm, 2) }}</td>
                            <td>{{ $leg->estimated_hours ? $leg->estimated_hours . ' hrs' : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="section">
        <p style="color: #666; font-size: 12px;">
            Generated on {{ now()->format('F d, Y \a\t H:i') }} by YachtCrew Itinerary System
        </p>
    </div>
</body>
</html>

