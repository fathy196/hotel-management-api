<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .success { color: #4CAF50; font-size: 24px; }
        .info { margin: 20px 0; }
    </style>
</head>
<body>
    <div class="success">✓ Payment Successful</div>
    <div class="info">Booking #{{ $booking->id }} has been confirmed.</div>
    <div class="info">Amount Paid: ${{ number_format($booking->total_amount, 2) }}</div>
    {{-- <div class="info">Payment Reference: {{ $booking->payment_reference }}</div> --}}
</body>
</html>