<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Initialize Stripe with the secret key.
     */
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Create a Payment Intent for the order.
     */
    public function createPaymentIntent(Request $request)
    {
        $user = Auth::user();

        // Retrieve the latest pending order
        $order = $user->orders()->where('status', 'pending')->latest()->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'No pending order found.',
            ], 404);
        }

        // Check if the order total is valid (must be > 0)
        if ($order->total <= 0) {
            Log::error("Invalid order total for Order ID {$order->id}. Order total: {$order->total}");
            return response()->json([
                'success' => false,
                'message' => 'Invalid order total.',
            ], 400);
        }

        try {
            // Create a Payment Intent
            $paymentIntent = PaymentIntent::create([
                'amount' => intval($order->total * 100), // Amount in cents
                'currency' => 'usd',
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id'  => $user->id,
                ],
            ]);

            return response()->json([
                'success'       => true,
                'client_secret' => $paymentIntent->client_secret,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Stripe PaymentIntent creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment intent.',
            ], 500);
        }
    }

    /**
     * New Endpoint: Update Order Status Based on Payment Outcome.
     */
    public function updateOrderStatus(Request $request)
    {
        $orderId = $request->input('order_id');
        $status = $request->input('status'); // Expected values: 'paid' or 'failed'

        if (!$orderId || !$status) {
            return response()->json([
                'success' => false,
                'message' => 'Order ID and status are required.',
            ], 400);
        }

        $order = Order::find($orderId);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        // Update the order status.
        $order->status = $status;
        $order->save();

        Log::info("Order ID {$orderId} updated to status: {$status}");

        return response()->json(['success' => true]);
    }

    /**
     * Handle Stripe Webhook Events.
     *
     * You may still keep this method if you want to support webhook events,
     * but it is not used in the client-based approach.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentSucceeded($paymentIntent);
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentFailed($paymentIntent);
                break;
            default:
                Log::info('Received unknown event type ' . $event->type);
        }

        return response()->json(['status' => 'success'], 200);
    }

    protected function handlePaymentSucceeded($paymentIntent)
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if (!$orderId) {
            Log::error('Order ID not found in PaymentIntent metadata.');
            return;
        }

        $order = Order::find($orderId);

        if (!$order) {
            Log::error("Order with ID {$orderId} not found.");
            return;
        }

        $order->status = 'paid';
        $order->save();

        Log::info("Order ID {$orderId} has been marked as paid.");
    }

    protected function handlePaymentFailed($paymentIntent)
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if (!$orderId) {
            Log::error('Order ID not found in PaymentIntent metadata.');
            return;
        }

        $order = Order::find($orderId);

        if (!$order) {
            Log::error("Order with ID {$orderId} not found.");
            return;
        }

        // You can choose to update the order as 'failed' or keep it as 'pending'
        $order->status = 'pending';
        $order->save();

        Log::info("Payment for Order ID {$orderId} failed.");
    }
}
