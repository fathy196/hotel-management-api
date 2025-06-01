<?php

namespace App\Services;

use Stripe\StripeClient;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class StripePaymentService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Booking $booking): array
    {
        try {
            // Create or retrieve Stripe customer
            $customerId = $booking->user->stripe_id ?? $this->createCustomer($booking->user);

            // Calculate amount
            $amount = $this->calculateBookingTotal($booking);
            $booking->update(['total_amount' => $amount]);

            // Create checkout session
            $session = $this->stripe->checkout->sessions->create([
                'customer' => $customerId,
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => "Booking #{$booking->id}",
                                'description' => "Room {$booking->room->room_number} ({$booking->check_in} to {$booking->check_out})",
                            ],
                            'unit_amount' => $amount * 100,
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => url("/api/payments/success/{$booking->id}?session_id={CHECKOUT_SESSION_ID}"),
                'cancel_url' => url("/api/payments/cancel/{$booking->id}"),
                'metadata' => [
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id
                ],
            ]);

            return [
                'checkout_url' => $session->url,
                'session_id' => $session->id,
                'amount' => $amount
            ];

        } catch (\Exception $e) {
            Log::error('Stripe checkout creation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function createCustomer(User $user): string
    {
        $customer = $this->stripe->customers->create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => ['user_id' => $user->id]
        ]);

        $user->update(['stripe_id' => $customer->id]);
        return $customer->id;
    }

    protected function calculateBookingTotal(Booking $booking): float
    {
        $nights = $booking->check_in->diffInDays($booking->check_out);
        return $nights * $booking->room->price_per_night;
    }

    public function handleCheckoutSuccess(string $sessionId): Booking
    {
        $session = $this->stripe->checkout->sessions->retrieve($sessionId, [
            'expand' => ['payment_intent']
        ]);

        $booking = Booking::findOrFail($session->metadata->booking_id);

        $booking->update([
            'payment_status' => 'paid',
            'payment_reference' => $session->payment_intent->id,
            'payment_method' => $session->payment_intent->payment_method_types[0] ?? 'card',
            'paid_at' => now()
        ]);

        return $booking;
    }
}