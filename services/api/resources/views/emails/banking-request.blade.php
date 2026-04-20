<!DOCTYPE html>
<html>
<head>
    <title>Banking Update Request</title>
</head>
<body>
    <h2>Banking Update Request</h2>
    <p><strong>Driver Name:</strong> {{ $driverName }}</p>

    <h3>New Banking Details:</h3>
    <ul>
        <li><strong>Bank Name:</strong> {{ $details['Bank_Name'] ?? 'N/A' }}</li>
        <li><strong>Bank Account:</strong> {{ $details['Bank_Account'] ?? 'N/A' }}</li>
        <li><strong>Institution Number:</strong> {{ $details['Institution'] ?? 'N/A' }}</li>
        <li><strong>Transit Number:</strong> {{ $details['Transit'] ?? 'N/A' }}</li>
    </ul>
</body>
</html>