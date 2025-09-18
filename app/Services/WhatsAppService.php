<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use Carbon\Carbon;

class WhatsAppService
{
    private $accessToken;
    private $phoneNumberId;
    private $baseUrl;

    public function __construct()
    {
        $this->accessToken = env('WHATSAPP_ACCESS_TOKEN');
        $this->phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
        $this->baseUrl = 'https://graph.facebook.com/v17.0';
    }

    public function sendOTP($phoneNumber, $otpCode, $name)
    {
        try {
            // Format phone number (remove leading 0 and add country code)
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            
            $message = "Halo {$name}! 👋\n\n";
            $message .= "Kode OTP untuk registrasi WIFA Sport Center:\n\n";
            $message .= "🔐 *{$otpCode}*\n\n";
            $message .= "Kode ini berlaku selama 5 menit.\n";
            $message .= "Jangan bagikan kode ini kepada siapapun.\n\n";
            $message .= "Terima kasih telah bergabung dengan WIFA Sport Center! 💪";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/' . $this->phoneNumberId . '/messages', [
                'messaging_product' => 'whatsapp',
                'to' => $formattedPhone,
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp OTP sent successfully', [
                    'phone' => $formattedPhone,
                    'name' => $name,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('Failed to send WhatsApp OTP', [
                    'phone' => $formattedPhone,
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending WhatsApp OTP', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If starts with 0, replace with 62 (Indonesia country code)
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // If doesn't start with 62, add it
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    public function sendWelcomeMessage($phoneNumber, $name)
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            
            $message = "Selamat datang di WIFA Sport Center, {$name}! 🎉\n\n";
            $message .= "Akun Anda telah berhasil dibuat. Anda sekarang dapat:\n\n";
            $message .= "✅ Mengakses semua fasilitas gym\n";
            $message .= "✅ Booking kelas fitness\n";
            $message .= "✅ Konsultasi dengan trainer\n";
            $message .= "✅ Tracking progress latihan\n\n";
            $message .= "Mari mulai perjalanan fitness Anda bersama kami! 💪\n\n";
            $message .= "Tim WIFA Sport Center";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/' . $this->phoneNumberId . '/messages', [
                'messaging_product' => 'whatsapp',
                'to' => $formattedPhone,
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Exception while sending welcome message', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Generate WhatsApp URL for booking confirmation
     */
    public function generateBookingConfirmationUrl(Booking $booking): string
    {
        $message = $this->generateBookingMessage($booking);
        $encodedMessage = urlencode($message);
        
        return "https://wa.me/6282328605554?text={$encodedMessage}";
    }
    
    /**
     * Generate booking confirmation message
     */
    private function generateBookingMessage(Booking $booking): string
    {
        $date = Carbon::parse($booking->booking_date)->format('d M Y');
        $day = Carbon::parse($booking->booking_date)->locale('id')->dayName;
        $time = Carbon::parse($booking->start_time)->format('H:i') . ' - ' . Carbon::parse($booking->end_time)->format('H:i');
        
        // Calculate duration
        $start = Carbon::parse($booking->start_time);
        $end = Carbon::parse($booking->end_time);
        $duration = $start->diffInHours($end);
        
        $message = "🏟️ *KONFIRMASI BOOKING WIFA SPORT CENTER*\n\n";
        $message .= "Assalamualaikum, saya ingin konfirmasi booking dengan detail sebagai berikut:\n\n";
        $message .= "📋 *Detail Booking:*\n";
        $message .= "• Olahraga: {$booking->sport->name}\n";
        $message .= "• Lapangan: {$booking->court->name}\n";
        $message .= "• Tanggal: {$day}, {$date}\n";
        $message .= "• Waktu: {$time} WIB\n";
        $message .= "• Durasi: {$duration} jam\n";
        $message .= "• Total Harga: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n";
        $message .= "• Nama Tim: {$booking->team_name}\n";
        $message .= "• Nama Pemesan: {$booking->user->name}\n";
        
        if ($booking->notes) {
            $message .= "• Catatan: {$booking->notes}\n";
        }
        
        $message .= "\n� *Metode Pembayaran: TUNAI*\n";
        $message .= "Saya akan melakukan pembayaran tunai langsung di tempat pada hari H.\n\n";
        
        $message .= "🙏 Mohon konfirmasi ketersediaan jadwal dan booking ini.\n";
        $message .= "Terima kasih atas pelayanannya.\n\n";
        $message .= "Wassalamualaikum\n\n";
        $message .= "---\n";
        $message .= "Booking ID: {$booking->booking_code}";
        
        return $message;
    }
    
    /**
     * Generate simple WhatsApp URL with custom message
     */
    public function generateCustomUrl(string $message): string
    {
        $encodedMessage = urlencode($message);
        return "https://wa.me/6282328605554?text={$encodedMessage}";
    }
}
