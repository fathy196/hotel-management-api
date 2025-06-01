<!DOCTYPE html>
<html>
<head>
    <title>Payment Failed</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .error { color: #F44336; font-size: 24px; }
        .info { margin: 20px 0; }
    </style>
</head>
<body>
    <div class="error">✗ Payment Failed</div>
    <div class="info">Booking #{{ $booking->id }} could not be processed.</div>
    <div class="info">Please try again or contact support.</div>
</body>
</html>