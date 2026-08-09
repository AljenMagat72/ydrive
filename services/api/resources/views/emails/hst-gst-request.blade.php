<!DOCTYPE html>
<html>
<body>
    <h2>HST/GST Update Request</h2>
    <p><strong>Driver Name:</strong> {{ $driverName }}</p>
    <p><strong>New HST/GST Number:</strong> {{ $details['HSTGST'] ?? $details['HST_GST'] ?? 'N/A' }}</p>
</body>
</html>