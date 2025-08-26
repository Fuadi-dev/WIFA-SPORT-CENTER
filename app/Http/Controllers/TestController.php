<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\MidtransService;

class TestController extends Controller
{
    // Test webhook manually (for development only)
    public function testMidtransWebhook(Request $request)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $sampleData = [
            "transaction_time" => "2024-01-01 12:00:00",
            "transaction_status" => "settlement",
            "transaction_id" => "test-123456",
            "status_message" => "midtrans payment notification",
            "status_code" => "200",
            "signature_key" => "sample_signature_key",
            "payment_type" => "credit_card",
            "order_id" => $request->input('order_id', 'WIFA-1-' . time()),
            "merchant_id" => "G123456789",
            "masked_card" => "481111-1114",
            "gross_amount" => $request->input('amount', '150000.00'),
            "fraud_status" => "accept",
            "eci" => "05",
            "currency" => "IDR",
            "approval_code" => "1578569243",
            "acquirer" => "bca"
        ];

        Log::info('Test Webhook Called', $sampleData);

        try {
            $midtransService = new MidtransService();
            $result = $midtransService->handleNotification($sampleData);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Test webhook processed',
                'data' => $sampleData,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => $sampleData
            ], 500);
        }
    }

    // Check ngrok status and provide setup instructions
    public function ngrokInfo()
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $appUrl = config('app.url');
        $ngrokUrl = env('NGROK_URL');
        
        return response()->json([
            'app_url' => $appUrl,
            'ngrok_url' => $ngrokUrl,
            'webhook_endpoint' => ($ngrokUrl ?: $appUrl) . '/midtrans/notification',
            'test_webhook_endpoint' => ($ngrokUrl ?: $appUrl) . '/test/midtrans-webhook',
            'midtrans_settings' => [
                'server_key' => env('MIDTRANS_SERVER_KEY') ? 'Set ✅' : 'Not Set ❌',
                'client_key' => env('MIDTRANS_CLIENT_KEY') ? 'Set ✅' : 'Not Set ❌',
                'is_production' => env('MIDTRANS_IS_PRODUCTION', false) ? 'Production' : 'Sandbox',
            ],
            'instructions' => [
                '1. Start ngrok: ngrok http 8000',
                '2. Update NGROK_URL in .env file',
                '3. Set notification URL in Midtrans dashboard',
                '4. Test payment flow'
            ]
        ]);
    }

    // Debug Midtrans integration for specific booking
    public function debugMidtrans($bookingId)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $booking = \App\Models\Booking::findOrFail($bookingId);
        
        return response()->json([
            'booking_info' => [
                'id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'payment_method' => $booking->payment_method,
                'status' => $booking->status,
                'total_price' => $booking->total_price,
                'midtrans_order_id' => $booking->midtrans_order_id,
                'midtrans_snap_token' => $booking->midtrans_snap_token,
            ],
            'midtrans_config' => [
                'server_key' => env('MIDTRANS_SERVER_KEY') ? 'Set (length: ' . strlen(env('MIDTRANS_SERVER_KEY')) . ')' : 'Not Set',
                'client_key' => env('MIDTRANS_CLIENT_KEY') ? 'Set (length: ' . strlen(env('MIDTRANS_CLIENT_KEY')) . ')' : 'Not Set',
                'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
                'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
                'is_3ds' => env('MIDTRANS_IS_3DS', true),
            ],
            'snap_config' => [
                'snap_js_url' => 'https://app.sandbox.midtrans.com/snap/snap.js',
                'client_key' => config('midtrans.client_key'),
            ],
            'recommendations' => [
                'Check if snap token exists in database',
                'Verify Midtrans credentials',
                'Check Laravel logs for errors',
                'Test snap token generation manually'
            ]
        ]);
    }
}
