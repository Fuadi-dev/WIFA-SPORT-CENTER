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
        <div class="text-center mb-12">
            <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                <i class="fas fa-calendar-check text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-amber-800 mb-4">
                My Bookings
            </h1>
            <p class="text-xl text-gray-700 max-w-2xl mx-auto">
                Kelola dan pantau semua booking Anda di WIFA Sport Center
            </p>
        </div>

        <div class="max-w-6xl mx-auto">
            
            <!-- Action Bar -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
                <div class="mb-4 sm:mb-0">
                    <p class="text-gray-600">
                        Total: <span class="font-semibold text-amber-700">{{ $bookings->total() }}</span> booking(s)
                    </p>
                </div>
                
                <a href="{{ route('booking.index') }}" 
                   class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300">
                    <i class="fas fa-plus mr-2"></i>Booking Baru
                </a>
            </div>

            @if($bookings->isEmpty())
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-lg p-12 text-center border-2 border-gray-100">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-700 mb-4">Belum Ada Booking</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        Anda belum memiliki booking apapun. Mulai booking lapangan favorit Anda sekarang!
                    </p>
                    <a href="{{ route('booking.index') }}" 
                       class="inline-block bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-3 px-8 rounded-lg transition-all duration-300">
                        <i class="fas fa-plus mr-2"></i>Mulai Booking
                    </a>
                </div>
            @else
                <!-- Bookings List -->
                <div class="space-y-6">
                    @foreach($bookings as $booking)
                    <div class="bg-white rounded-xl shadow-lg border-2 border-gray-100 overflow-hidden">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                                <div>
                                    <div class="flex items-center mb-2">
                                        <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center mr-4">
                                            <i class="{{ $booking->sport->icon }} text-white text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-800">{{ $booking->sport->name }}</h3>
                                            <p class="text-amber-600 font-semibold">{{ $booking->booking_code }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-3">
                                    <!-- Status Badge -->
                                    @php
                                        $statusConfig = [
                                            'pending' => ['bg-yellow-100', 'text-yellow-800', 'fas fa-clock', 'Menunggu Konfirmasi'],
                                            'confirmed' => ['bg-blue-100', 'text-blue-800', 'fas fa-check-circle', 'Dikonfirmasi'],
                                            'paid' => ['bg-green-100', 'text-green-800', 'fas fa-credit-card', 'Sudah Bayar'],
                                            'cancelled' => ['bg-red-100', 'text-red-800', 'fas fa-times-circle', 'Dibatalkan'],
                                            'completed' => ['bg-gray-100', 'text-gray-800', 'fas fa-flag-checkered', 'Selesai']
                                        ];
                                        $config = $statusConfig[$booking->status] ?? $statusConfig['pending'];
                                    @endphp
                                    
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $config[0] }} {{ $config[1] }}">
                                        <i class="{{ $config[2] }} mr-1"></i>{{ $config[3] }}
                                    </span>
                                </div>
                            </div>

                            <!-- Booking Details -->
                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-sm text-gray-600 mb-1">Lapangan</div>
                                    <div class="font-semibold text-gray-800">{{ $booking->court->name }}</div>
                                </div>
                                
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-sm text-gray-600 mb-1">Tanggal</div>
                                    <div class="font-semibold text-gray-800">{{ $booking->booking_date->format('d M Y') }}</div>
                                </div>
                                
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-sm text-gray-600 mb-1">Waktu</div>
                                    <div class="font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-sm text-gray-600 mb-1">Tim</div>
                                    <div class="font-semibold text-gray-800">{{ $booking->team_name }}</div>
                                </div>
                                
                                <div class="bg-amber-50 rounded-lg p-4">
                                    <div class="text-sm text-amber-600 mb-1">Total Harga</div>
                                    <div class="font-bold text-amber-800">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <span class="text-gray-600 mr-2">Pembayaran:</span>
                                    @if($booking->payment_method === 'cash')
                                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded text-sm font-semibold">
                                            <i class="fas fa-money-bill-wave mr-1"></i>Tunai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm font-semibold">
                                            <i class="fas fa-university mr-1"></i>Transfer
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="text-sm text-gray-500">
                                    Dibuat: {{ $booking->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>

                            @if($booking->notes)
                            <div class="bg-blue-50 rounded-lg p-4 mb-4">
                                <div class="text-sm text-blue-600 mb-1">Catatan</div>
                                <div class="text-blue-800">{{ $booking->notes }}</div>
                            </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('booking.confirmation', $booking->id) }}" 
                                   class="flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-300">
                                    <i class="fas fa-eye mr-2"></i>Detail
                                </a>
                                
                                @if($booking->status === 'pending')
                                    <button onclick="cancelBooking({{ $booking->id }})" 
                                            class="flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-300">
                                        <i class="fas fa-times mr-2"></i>Batalkan
                                    </button>
                                @endif
                                
                                @if($booking->payment_method === 'transfer' && in_array($booking->status, ['pending', 'confirmed']))
                                    <a href="https://wa.me/6281234567890?text=Halo,%20saya%20ingin%20konfirmasi%20pembayaran%20booking%20{{ $booking->booking_code }}" 
                                       target="_blank"
                                       class="flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-300">
                                        <i class="fab fa-whatsapp mr-2"></i>Konfirmasi Bayar
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
    </script>
</body>
</html>
