<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Booking - WIFA Sport Center</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Midtrans Snap.js -->
    @if($booking->payment_method === 'midtrans')
        <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif
</head>
<body class="bg-gradient-to-br from-amber-50 to-orange-100 min-h-screen">
    
    <!-- Include Navbar -->
    @include('components.navbar')

    <div class="container mx-auto px-4 pt-32 pb-16">
        
        <!-- Success Message -->
        <div class="text-center mb-12">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-lg animate-bounce">
                <i class="fas fa-check text-4xl text-white"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-green-700 mb-4">
                Booking Berhasil!
            </h1>
            <p class="text-xl text-gray-700 max-w-2xl mx-auto">
                Terima kasih! Booking Anda telah berhasil dibuat dan sedang menunggu konfirmasi.
            </p>
        </div>

        <!-- Booking Details -->
        <div class="max-w-4xl mx-auto">
            
            <!-- Booking Code -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border-2 border-green-100">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Kode Booking</h2>
                    <div class="inline-block bg-gradient-to-r from-amber-400 to-orange-500 text-white px-8 py-4 rounded-lg text-3xl font-bold tracking-wider shadow-lg">
                        {{ $booking->booking_code }}
                    </div>
                    <p class="text-gray-600 mt-4">Simpan kode ini untuk referensi booking Anda</p>
                </div>
            </div>

            <!-- Booking Information -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border-2 border-green-100">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>Detail Booking
                </h2>
                
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <span class="text-gray-600 font-medium">Olahraga:</span>
                            <span class="font-bold text-amber-700">{{ $booking->sport->name }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <span class="text-gray-600 font-medium">Lapangan:</span>
                            <span class="font-bold text-amber-700">{{ $booking->court->name }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <span class="text-gray-600 font-medium">Tanggal:</span>
                            <span class="font-bold text-amber-700">{{ $booking->booking_date->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <span class="text-gray-600 font-medium">Waktu:</span>
                            <span class="font-bold text-amber-700">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <span class="text-gray-600 font-medium">Nama Tim:</span>
                            <span class="font-bold text-amber-700">{{ $booking->team_name }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <span class="text-gray-600 font-medium">Metode Pembayaran:</span>
                            <span class="font-bold text-amber-700">
                                @if($booking->payment_method === 'cash')
                                    <i class="fas fa-money-bill-wave mr-2"></i>Tunai
                                @elseif($booking->payment_method === 'midtrans')
                                    <i class="fas fa-credit-card mr-2"></i>Midtrans (Card/E-wallet/Bank)
                                @else
                                    <i class="fas fa-university mr-2"></i>Transfer Bank
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <span class="text-gray-600 font-medium">Status:</span>
                            <span class="inline-block px-3 py-1 
                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                @elseif($booking->status === 'pending_payment') bg-yellow-100 text-yellow-800
                                @elseif($booking->status === 'paid') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif 
                                rounded-full text-sm font-semibold">
                                <i class="fas 
                                    @if($booking->status === 'confirmed') fa-check-circle
                                    @elseif($booking->status === 'pending_payment') fa-clock
                                    @elseif($booking->status === 'paid') fa-credit-card
                                    @else fa-question-circle @endif mr-1"></i>
                                @if($booking->status === 'confirmed') Dikonfirmasi
                                @elseif($booking->status === 'pending_payment') Menunggu Pembayaran
                                @elseif($booking->status === 'paid') Sudah Dibayar
                                @else {{ ucfirst($booking->status) }} @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-amber-100 rounded-lg">
                            <span class="text-amber-700 font-bold text-lg">Total Harga:</span>
                            <span class="font-bold text-2xl text-amber-800">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                @if($booking->notes)
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <h4 class="font-semibold text-gray-800 mb-2">Catatan:</h4>
                    <p class="text-gray-700">{{ $booking->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Payment Instructions -->
            @if($booking->payment_method === 'midtrans')
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border-2 border-purple-100">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-credit-card mr-2 text-purple-600"></i>Pembayaran Midtrans
                </h2>
                
                @if($booking->status === 'pending_payment')
                <div class="bg-purple-50 rounded-lg p-6 mb-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                        <span class="font-semibold text-gray-800">Selesaikan pembayaran sekarang</span>
                    </div>
                    <p class="text-gray-700 mb-4">Klik tombol di bawah untuk melanjutkan pembayaran melalui Midtrans. Anda dapat menggunakan kartu kredit, e-wallet, atau transfer bank.</p>
                    
                    <button id="pay-button" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-4 px-6 rounded-lg transition-all duration-300 text-lg">
                        <i class="fas fa-payment mr-2"></i>Bayar Sekarang - Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                    </button>
                </div>
                
                <div class="bg-blue-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 mb-2">Metode Pembayaran yang Tersedia:</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm text-gray-700">
                        <div><i class="fas fa-credit-card mr-1"></i> Kartu Kredit</div>
                        <div><i class="fas fa-mobile-alt mr-1"></i> GoPay</div>
                        <div><i class="fas fa-wallet mr-1"></i> OVO</div>
                        <div><i class="fas fa-university mr-1"></i> Transfer Bank</div>
                    </div>
                </div>
                @elseif($booking->status === 'paid')
                <div class="bg-green-50 rounded-lg p-6">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        <span class="font-semibold text-green-800">Pembayaran Berhasil!</span>
                    </div>
                    <p class="text-green-700">Pembayaran Anda telah berhasil diproses melalui Midtrans.</p>
                </div>
                @endif
            </div>
            @elseif($booking->payment_method === 'transfer')
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border-2 border-blue-100">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-university mr-2 text-blue-600"></i>Instruksi Pembayaran Transfer
                </h2>
                
                <div class="bg-blue-50 rounded-lg p-6 mb-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                        <span class="font-semibold text-gray-800">Penting: Lakukan pembayaran dalam 2 jam</span>
                    </div>
                    <p class="text-gray-700">Booking akan otomatis dibatalkan jika pembayaran tidak dilakukan dalam 2 jam.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="p-6 border border-gray-200 rounded-lg">
                        <h4 class="font-bold text-gray-800 mb-4">Bank BCA</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">No. Rekening:</span>
                                <span class="font-mono font-bold">1234567890</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Atas Nama:</span>
                                <span class="font-bold">WIFA Sport Center</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 border border-gray-200 rounded-lg">
                        <h4 class="font-bold text-gray-800 mb-4">Bank Mandiri</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">No. Rekening:</span>
                                <span class="font-mono font-bold">0987654321</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Atas Nama:</span>
                                <span class="font-bold">WIFA Sport Center</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                    <h4 class="font-semibold text-gray-800 mb-2">Cara Konfirmasi Pembayaran:</h4>
                    <ol class="list-decimal list-inside text-gray-700 space-y-1">
                        <li>Transfer sesuai jumlah yang tertera</li>
                        <li>Kirim bukti transfer via WhatsApp ke <a href="https://wa.me/6281234567890" class="text-blue-600 font-semibold">0812-3456-7890</a></li>
                        <li>Sertakan kode booking: <span class="font-mono font-bold">{{ $booking->booking_code }}</span></li>
                        <li>Tim kami akan mengkonfirmasi dalam 1x24 jam</li>
                    </ol>
                </div>
            </div>
            @endif

            <!-- Cash Payment Instructions -->
            @if($booking->payment_method === 'cash')
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border-2 border-green-100">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-money-bill-wave mr-2 text-green-600"></i>Instruksi Pembayaran Tunai
                </h2>
                
                <div class="bg-green-50 rounded-lg p-6">
                    <h4 class="font-semibold text-gray-800 mb-4">Pembayaran di Tempat</h4>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Datang 15 menit sebelum waktu booking</li>
                        <li>Bawa kode booking: <span class="font-mono font-bold bg-white px-2 py-1 rounded">{{ $booking->booking_code }}</span></li>
                        <li>Lakukan pembayaran di front desk</li>
                        <li>Dapatkan kunci/akses lapangan setelah pembayaran</li>
                    </ul>
                </div>
            </div>
            @endif

            <!-- Next Steps -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border-2 border-amber-100">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-tasks mr-2 text-amber-600"></i>Langkah Selanjutnya
                </h2>
                
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="text-center p-6 bg-amber-50 rounded-lg">
                        <div class="w-16 h-16 mx-auto mb-4 bg-amber-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-white text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-800 mb-2">Menunggu Konfirmasi</h4>
                        <p class="text-gray-600 text-sm">Tim kami akan mengkonfirmasi booking Anda dalam 1x24 jam</p>
                    </div>
                    
                    <div class="text-center p-6 bg-blue-50 rounded-lg">
                        <div class="w-16 h-16 mx-auto mb-4 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-bell text-white text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-800 mb-2">Notifikasi</h4>
                        <p class="text-gray-600 text-sm">Anda akan mendapat notifikasi via WhatsApp saat booking dikonfirmasi</p>
                    </div>
                    
                    <div class="text-center p-6 bg-green-50 rounded-lg">
                        <div class="w-16 h-16 mx-auto mb-4 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-play text-white text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-800 mb-2">Siap Bermain</h4>
                        <p class="text-gray-600 text-sm">Datang tepat waktu dan nikmati fasilitas terbaik kami</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('my-bookings') }}" 
                   class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-300 text-center">
                    <i class="fas fa-list mr-2"></i>Lihat Semua Booking
                </a>
                
                <a href="{{ route('booking.index') }}" 
                   class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-300 text-center">
                    <i class="fas fa-plus mr-2"></i>Booking Lagi
                </a>
                
                <a href="{{ route('home') }}" 
                   class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-300 text-center">
                    <i class="fas fa-home mr-2"></i>Kembali ke Home
                </a>
            </div>
        </div>
    </div>

    <script>
        @if($booking->payment_method === 'midtrans' && $booking->status === 'pending_payment' && ($booking->midtrans_snap_token || session('snap_token')))
        // Midtrans Snap Payment
        document.getElementById('pay-button').onclick = function() {
            var snapToken = '{{ $booking->midtrans_snap_token ?: session('snap_token') }}';
            console.log('Snap Token:', snapToken); // Debug log
            
            if (!snapToken) {
                alert('Snap token tidak ditemukan. Silakan refresh halaman atau coba lagi.');
                return;
            }
            
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    alert('Pembayaran berhasil!');
                    console.log('Payment Success:', result);
                    location.reload();
                },
                onPending: function(result) {
                    alert('Pembayaran pending, silakan selesaikan pembayaran.');
                    console.log('Payment Pending:', result);
                },
                onError: function(result) {
                    alert('Pembayaran gagal!');
                    console.log('Payment Error:', result);
                }
            });
        };
        @else
        // Debug: Show why snap payment is not available
        console.log('Midtrans Debug Info:', {
            payment_method: '{{ $booking->payment_method }}',
            status: '{{ $booking->status }}',
            snap_token_db: '{{ $booking->midtrans_snap_token }}',
            snap_token_session: '{{ session('snap_token') }}',
            client_key: '{{ config('midtrans.client_key') }}'
        });
        @endif

        // Show WhatsApp contact with booking code
        function showWhatsApp() {
            const bookingCode = '{{ $booking->booking_code }}';
            const message = `Halo WIFA Sport Center, saya ingin konfirmasi pembayaran untuk booking dengan kode: ${bookingCode}`;
            const encodedMessage = encodeURIComponent(message);
            window.open(`https://wa.me/6281234567890?text=${encodedMessage}`, '_blank');
        }

        // Auto-refresh page every 5 minutes to check for status updates
        setInterval(function() {
            location.reload();
        }, 300000); // 5 minutes
    </script>
</body>
</html>
