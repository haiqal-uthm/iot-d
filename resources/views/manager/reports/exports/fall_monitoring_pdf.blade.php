<!DOCTYPE html>
<html>
<head>
    <title>Fall Monitoring Report - {{ now()->format('Y-m-d') }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .filter-info { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Fall Monitoring Report</h1>
        <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    @if($filters['start_date'] || $filters['end_date'])
    <div class="filter-info">
        <h3>Filters Applied:</h3>
        <p>Date Range: {{ $filters['start_date'] }} to {{ $filters['end_date'] }}</p>
        <p>Device ID: {{ $filters['device_id'] ?? 'All Devices' }}</p>
        <p>Orchard ID: {{ $filters['orchard_id'] ?? 'All Orchards' }}</p>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>Orchard Name</th>
                <th>Device</th>
                <th>Total Falls</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vibrationLogs as $log)
            <tr>
                <td>{{ $log->timestamp->format('Y-m-d H:i:s') }}</td>
                <td>{{ $log->orchard->orchardName ?? 'N/A' }}</td>
                <td>{{ $log->device->name ?? 'Unknown Device' }} (ID: {{ $log->device->device_id ?? $log->device_id }})</td>
                <td>{{ $log->fall_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>