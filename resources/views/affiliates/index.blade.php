<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affiliate Locator</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { background: #333; color: white; padding: 20px; margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .error { color: red; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Affiliate Locator</h1>
        <p>Affiliates within 100km of our Dublin office</p>
    </div>

    @if(isset($error))
        <div class="error">{{ $error }}</div>
    @endif

    <h2>Results</h2>
    <p><strong>Found {{ $affiliates->count() }} affiliate(s) within 100km of Dublin office</strong></p>

    @if($affiliates->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Affiliate ID</th>
                    <th>Name</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Distance from Dublin (km)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($affiliates as $affiliate)
                    <tr>
                        <td><strong>{{ $affiliate->affiliate_id }}</strong></td>
                        <td>{{ $affiliate->name }}</td>
                        <td>{{ number_format($affiliate->latitude, 6) }}</td>
                        <td>{{ number_format($affiliate->longitude, 6) }}</td>
                        <td>{{ number_format($affiliate->distance, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No affiliates found within 100km of Dublin office.</p>
    @endif
</body>
</html>
