<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - WIFA Sport Center</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-amber-50 to-orange-100 min-h-screen">
    
    <!-- Include Navbar -->
    @include('components.navbar')

    <div class="container mx-auto px-4 pt-32 pb-16">
        
        <!-- Header -->
        <div class="text-center mb-8 sm:mb-12">
            <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-3 sm:mb-4 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                <i class="fas fa-calendar-check text-2xl sm:text-3xl text-white"></i>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-amber-800 mb-3 sm:mb-4">
                My Bookings
            </h1>
            <p class="text-base sm:text-xl text-gray-700 max-w-2xl mx-auto px-4">
                Kelola dan pantau semua booking Anda
            </p>
        </div>

        <div class="max-w-6xl mx-auto">
            
            <!-- Action Bar -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 sm:mb-8 gap-3 sm:gap-0">
                <div class="w-full sm:w-auto text-center sm:text-left">
                    <p class="text-gray-600 text-sm sm:text-base">
                        Total: <span class="font-semibold text-amber-700">{{ $bookings->total() }}</span> booking
                    </p>
                </div>
                
                <a href="{{ route('booking.index') }}" 
                   class="w-full sm:w-auto bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-2 sm:py-3 px-5 sm:px-6 rounded-lg transition-all duration-300 text-center text-sm sm:text-base">
                    <i class="fas fa-plus mr-2"></i>Booking Baru
                </a>
            </div>

            @if($bookings->isEmpty())
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-lg p-8 sm:p-12 text-center border-2 border-gray-100">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-4 sm:mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-times text-3xl sm:text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-700 mb-3 sm:mb-4">Belum Ada Booking</h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8 max-w-md mx-auto px-4">
                        Anda belum memiliki booking apapun. Mulai booking lapangan favorit Anda sekarang!
                    </p>
                    <a href="{{ route('booking.index') }}" 
                       class="inline-block bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-2 sm:py-3 px-6 sm:px-8 rounded-lg transition-all duration-300 text-sm sm:text-base">
                        <i class="fas fa-plus mr-2"></i>Mulai Booking
                    </a>
                </div>
            @else
                <!-- Bookings List -->
                <div class="space-y-6">
                    @foreach($bookings as $booking)
                    <div class="bg-white rounded-xl shadow-lg border-2 border-gray-100 overflow-hidden">
                        <div class="p-4 sm:p-6">
                            <!-- Header -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6">
                                <div class="mb-3 sm:mb-0">
                                    <div class="flex items-center mb-2">
                                        <!-- Hide icon box on mobile, show on desktop -->
                                        <div class="hidden sm:flex w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg items-center justify-center mr-4">
                                            <i class="{{ $booking->sport->icon }} text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center">
                                                <!-- Show icon inline on mobile -->
                                                <i class="{{ $booking->sport->icon }} text-amber-600 mr-2 sm:hidden"></i>
                                                <h3 class="text-lg sm:text-xl font-bold text-gray-800">{{ $booking->sport->name }}</h3>
                                            </div>
                                            <p class="text-amber-600 font-semibold text-sm sm:text-base">{{ $booking->booking_code }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="w-full sm:w-auto">
                                    <!-- Status Badge -->
                                    @php
                                        $statusConfig = [
                                            'pending_payment' => ['bg-yellow-100', 'text-yellow-800', 'fas fa-clock', 'Menunggu Pembayaran'],
                                            'pending_confirmation' => ['bg-yellow-100', 'text-yellow-800', 'fas fa-clock', 'Menunggu Konfirmasi'],
                                            'confirmed' => ['bg-blue-100', 'text-blue-800', 'fas fa-check-circle', 'Dikonfirmasi'],
                                            'paid' => ['bg-green-100', 'text-green-800', 'fas fa-credit-card', 'Sudah Bayar'],
                                            'cancelled' => ['bg-red-100', 'text-red-800', 'fas fa-times-circle', 'Dibatalkan'],
                                            'completed' => ['bg-gray-100', 'text-gray-800', 'fas fa-flag-checkered', 'Selesai']
                                        ];
                                        $config = $statusConfig[$booking->status] ?? $statusConfig['pending'];
                                    @endphp
                                    
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-semibold {{ $config[0] }} {{ $config[1] }} w-full sm:w-auto justify-center sm:justify-start">
                                        <i class="{{ $config[2] }} mr-1"></i>{{ $config[3] }}
                                    </span>
                                </div>
                            </div>

                            <!-- Booking Details -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-4 sm:mb-6">
                                <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                                    <div class="text-xs sm:text-sm text-gray-600 mb-1">Lapangan</div>
                                    <div class="font-semibold text-gray-800 text-sm sm:text-base">{{ $booking->court->name }}</div>
                                </div>
                                
                                <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                                    <div class="text-xs sm:text-sm text-gray-600 mb-1">Tanggal</div>
                                    <div class="font-semibold text-gray-800 text-sm sm:text-base">{{ $booking->booking_date->format('d M Y') }}</div>
                                </div>
                                
                                <div class="bg-gray-50 rounded-lg p-3 sm:p-4 col-span-2 sm:col-span-1">
                                    <div class="text-xs sm:text-sm text-gray-600 mb-1">Waktu</div>
                                    <div class="font-semibold text-gray-800 text-sm sm:text-base">
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                    </div>
                                </div>
                                
                                <!-- Hide Team Name on mobile -->
                                <div class="bg-gray-50 rounded-lg p-3 sm:p-4 hidden sm:block">
                                    <div class="text-xs sm:text-sm text-gray-600 mb-1">Tim</div>
                                    <div class="font-semibold text-gray-800 text-sm sm:text-base">{{ $booking->team_name }}</div>
                                </div>
                                
                                <div class="bg-amber-50 rounded-lg p-3 sm:p-4 col-span-2 sm:col-span-1">
                                    <div class="text-xs sm:text-sm text-amber-600 mb-1">Total Bayar</div>
                                    <div class="font-bold text-amber-800 text-sm sm:text-base">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                                    @if($booking->discount_amount > 0)
                                        <div class="text-xs text-green-600 mt-1">
                                            <i class="fas fa-tag mr-1"></i>Hemat Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Promo Badge -->
                            @if($booking->discount_amount > 0)
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-3 mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-gift text-green-600 mr-2 text-sm"></i>
                                    <div class="flex-1">
                                        @if($booking->promoCode)
                                            <span class="text-xs sm:text-sm font-semibold text-green-800">Kode Promo: {{ $booking->promoCode->code }}</span>
                                        @elseif($booking->autoPromo)
                                            <span class="text-xs sm:text-sm font-semibold text-green-800">{{ $booking->autoPromo->name }}</span>
                                        @endif
                                        <span class="text-xs text-green-600 ml-2">- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Payment Method - Hide creation time on mobile -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <span class="text-gray-600 mr-2 text-xs sm:text-sm">Pembayaran:</span>
                                    @if($booking->payment_method === 'cash')
                                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded text-xs sm:text-sm font-semibold">
                                            <i class="fas fa-money-bill-wave mr-1"></i>Tunai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs sm:text-sm font-semibold">
                                            <i class="fas fa-university mr-1"></i>Transfer
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Hide on mobile -->
                                <div class="text-xs sm:text-sm text-gray-500 hidden sm:block">
                                    Dibuat: {{ $booking->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>

                            <!-- Hide notes on mobile EXCEPT for cancelled status -->
                            @if($booking->notes)
                            <div class="bg-blue-50 rounded-lg p-3 sm:p-4 mb-4 {{ $booking->status === 'cancelled' ? '' : 'hidden sm:block' }}">
                                <div class="text-sm text-blue-600 mb-1">
                                    @if($booking->status === 'cancelled')
                                        <i class="fas fa-info-circle mr-1"></i>Alasan Pembatalan
                                    @else
                                        Catatan
                                    @endif
                                </div>
                                <div class="text-blue-800 text-sm">{{ $booking->notes }}</div>
                            </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-2 sm:gap-3">
                                <a href="{{ route('booking.confirmation', $booking->slug) }}" 
                                   class="flex items-center justify-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300 text-sm sm:text-base">
                                    <i class="fas fa-eye mr-2"></i>Detail
                                </a>
                                
                                @if($booking->status === 'pending')
                                    <button onclick="cancelBooking({{ $booking->id }})" 
                                            class="flex items-center justify-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-300 text-sm sm:text-base">
                                        <i class="fas fa-times mr-2"></i>Batalkan
                                    </button>
                                @endif
                                
                                <!-- WhatsApp Button for Cash Payment Only -->
                                @if($booking->payment_method === 'cash' && in_array($booking->status, ['pending_confirmation', 'confirmed']))
                                    @php
                                        $whatsappService = new \App\Services\WhatsAppService();
                                        $whatsappUrl = $whatsappService->generateBookingConfirmationUrl($booking);
                                    @endphp
                                    <a href="{{ $whatsappUrl }}" 
                                       target="_blank"
                                       onclick="markWhatsAppClickedList('{{ $booking->booking_code }}')"
                                       id="wa-btn-{{ $booking->booking_code }}"
                                       class="flex items-center justify-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-300 text-sm sm:text-base">
                                        <i class="fab fa-whatsapp mr-2"></i>Hubungi Admin
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function cancelBooking(bookingId) {
            if (confirm('Apakah Anda yakin ingin membatalkan booking ini?')) {
                // Implementation for cancel booking
                fetch(`/booking/${bookingId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Gagal membatalkan booking');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });
            }
        }

        // Mark WhatsApp as clicked for cash payment in listing
        function markWhatsAppClickedList(bookingCode) {
            // Save to localStorage
            localStorage.setItem('whatsapp_clicked_' + bookingCode, 'true');
            
            // Hide button with animation
            setTimeout(function() {
                const button = document.getElementById('wa-btn-' + bookingCode);
                if (button) {
                    button.style.opacity = '0';
                    button.style.transition = 'opacity 0.3s';
                    setTimeout(function() {
                        button.style.display = 'none';
                    }, 300);
                }
            }, 500); // Small delay to ensure WhatsApp opens
        }

        // Check if WhatsApp was already clicked for each booking
        document.addEventListener('DOMContentLoaded', function() {
            // Get all booking codes from buttons
            const waButtons = document.querySelectorAll('[id^="wa-btn-"]');
            
            waButtons.forEach(function(button) {
                const bookingCode = button.id.replace('wa-btn-', '');
                const whatsappClicked = localStorage.getItem('whatsapp_clicked_' + bookingCode);
                
                if (whatsappClicked === 'true') {
                    button.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
