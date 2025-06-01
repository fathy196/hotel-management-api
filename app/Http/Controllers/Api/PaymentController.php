<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\StripePaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected StripePaymentService $paymentService)
    {
    }

    public function createCheckout(Booking $booking)
    {
        try {
            if ($booking->user_id !== auth()->id()) {
                throw new \Exception('Unauthorized access to booking');
            }

            if ($booking->payment_status === 'paid') {
                throw new \Exception('Booking already paid');
            }

            $checkout = $this->paymentService->createCheckoutSession($booking);

            return ApiResponseHelper::apiResponse(
                true,
                [
                    'checkout_url' => $checkout['checkout_url'],
                    'amount' => $checkout['amount']
                ],
                'Checkout session created successfully'
            );

        } catch (\Exception $e) {
            return ApiResponseHelper::apiResponse(
                false,
                null,
                $e->getMessage(),
                400
            );
        }
    }

    public function handleSuccess(Request $request, $bookingId)
    {
        try {
            $booking = $this->paymentService->handleCheckoutSuccess(
                $request->query('session_id')
            );

            return view('payments.success', [
                'booking' => $booking
            ]);

        } catch (\Exception $e) {
            return view('payments.failed', [
                'booking' => Booking::findOrFail($bookingId)
            ]);
        }
    }

    public function handleCancel($bookingId)
    {
        return view('payments.failed', [
            'booking' => Booking::findOrFail($bookingId)
        ]);
    }
}