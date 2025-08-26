<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        Config::$is3ds = env('MIDTRANS_IS_3DS', true);
    }

    public function createTransaction(Booking $booking)
    {
        try {
            $orderId = 'WIFA-' . $booking->id . '-' . time();
            
            $transactionDetails = [
                'order_id' => $orderId,
                'gross_amount' => (int) $booking->total_price,
            ];

            $itemDetails = [
                [
                    'id' => 'booking-' . $booking->id,
                    'price' => (int) $booking->total_price,
                    'quantity' => 1,
                    'name' => $booking->sport->name . ' - ' . $booking->court->name,
                    'brand' => 'WIFA Sport Center',
                    'category' => 'Sport Booking',
                ]
            ];

            $customerDetails = [
                'first_name' => $booking->user->name,
                'email' => $booking->user->email,
                'phone' => $booking->user->telp ?? '',
            ];

            $transactionData = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'enabled_payments' => [
                    'credit_card',
                    'bca_va',
                    'bni_va',
                    'bri_va',
                    'echannel',
                    'permata_va',
                    'other_va',
                    'gopay',
                    'ovo',
                    'dana',
                    'shopeepay',
                    'indomaret',
                    'alfamart'
                ]
            ];

            $snapToken = Snap::getSnapToken($transactionData);
            
            // Update booking with Midtrans data
            $booking->update([
                'midtrans_order_id' => $orderId,
                'midtrans_snap_token' => $snapToken
            ]);

            Log::info('Midtrans transaction created successfully', [
                'booking_id' => $booking->id,
                'order_id' => $orderId,
                'snap_token' => $snapToken
            ]);

            return $snapToken; // Return snap token string directly

        } catch (\Exception $e) {
            Log::error('Midtrans transaction creation failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function handleNotification()
    {
        try {
            $notification = new Notification();
            
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;
            $orderId = $notification->order_id;

            Log::info('Midtrans notification received', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus
            ]);

            // Find booking by midtrans_order_id
            $booking = Booking::where('midtrans_order_id', $orderId)->first();
            
            if (!$booking) {
                Log::error('Booking not found for Midtrans order ID', ['order_id' => $orderId]);
                return false;
            }

            switch ($transactionStatus) {
                case 'capture':
                    if ($fraudStatus == 'challenge') {
                        // Transaction is challenged by FDS
                        $this->updateBookingStatus($booking, 'pending_payment');
                    } else if ($fraudStatus == 'accept') {
                        // Transaction is successful
                        $this->updateBookingStatus($booking, 'paid');
                    }
                    break;

                case 'settlement':
                    // Transaction is successful
                    $this->updateBookingStatus($booking, 'paid');
                    break;

                case 'pending':
                    // Transaction is pending
                    $this->updateBookingStatus($booking, 'pending_payment');
                    break;

                case 'deny':
                case 'expire':
                case 'cancel':
                    // Transaction is failed
                    $this->updateBookingStatus($booking, 'cancelled');
                    break;

                default:
                    Log::warning('Unknown Midtrans transaction status', [
                        'order_id' => $orderId,
                        'status' => $transactionStatus
                    ]);
                    break;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Midtrans notification handling failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    private function updateBookingStatus(Booking $booking, string $status)
    {
        $updateData = ['status' => $status];
        
        if ($status === 'paid') {
            $updateData['paid_at'] = now();
            $updateData['confirmed_at'] = now();
        }

        $booking->update($updateData);

        Log::info('Booking status updated', [
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'old_status' => $booking->getOriginal('status'),
            'new_status' => $status
        ]);
    }

    public function getTransactionStatus($orderId)
    {
        try {
            $status = \Midtrans\Transaction::status($orderId);
            return $status;
        } catch (\Exception $e) {
            Log::error('Failed to get Midtrans transaction status', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
