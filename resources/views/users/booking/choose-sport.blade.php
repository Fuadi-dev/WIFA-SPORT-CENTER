<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Olahraga - WIFA Sport Center</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-amber-50 to-orange-100 min-h-screen">
    
    <!-- Include Navbar -->
    @include('components.navbar')

    <div class="container mx-auto px-4 pt-32 pb-16">
        <!-- Breadcrumb - Hide on mobile -->
        <nav class="text-xs sm:text-sm text-gray-600 mb-6 sm:mb-8 hidden sm:block">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-amber-600">Home</a>
                    <i class="fas fa-chevron-right mx-2"></i>
                </li>
                <li class="text-amber-600 font-semibold">Pilih Olahraga</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="text-center mb-8 sm:mb-12">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-amber-800 mb-3 sm:mb-4">
                <i class="fas fa-dumbbell mr-2 sm:mr-3"></i>Pilih Olahraga
            </h1>
            <p class="text-base sm:text-xl text-gray-700 max-w-2xl mx-auto px-4">
                Pilih jenis olahraga yang ingin Anda booking
            </p>
        </div>

        <!-- Sports Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8 max-w-6xl mx-auto">
            @foreach($sports as $sport)
            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border-2 border-amber-100 hover:border-amber-300">
                <div class="p-6 sm:p-8 text-center">
                    <!-- Sport Icon -->
                    <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-4 sm:mb-6 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                        <i class="{{ $sport->icon }} text-3xl sm:text-4xl text-white"></i>
                    </div>
                    
                    <!-- Sport Name -->
                    <h3 class="text-xl sm:text-2xl font-bold text-amber-800 mb-2 sm:mb-3">{{ $sport->name }}</h3>
                    
                    <!-- Description - Hide on mobile -->
                    <p class="text-gray-600 mb-4 text-sm hidden sm:block">{{ $sport->description }}</p>
                    
                    
                    <!-- Select Button -->
                    <a href="{{ route('booking.court', $sport->slug) }}" 
                       class="w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-2 sm:py-3 px-4 sm:px-6 rounded-lg transition-all duration-300 hover:shadow-lg transform hover:scale-105 inline-block text-sm sm:text-base">
                        <i class="fas fa-arrow-right mr-2"></i>Pilih {{ $sport->name }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Info Section -->
        <div class="mt-12 sm:mt-16 bg-white rounded-xl shadow-lg p-6 sm:p-8 max-w-4xl mx-auto border-2 border-amber-100">
            <div class="text-center mb-4 sm:mb-6">
                <h2 class="text-xl sm:text-2xl font-bold text-amber-800 mb-2 sm:mb-4">
                    <i class="fas fa-info-circle mr-2"></i>Informasi Booking
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                <div>
                    <h3 class="font-semibold text-amber-700 mb-3 text-sm sm:text-base">📅 Jadwal Operasional</h3>
                    <ul class="text-gray-600 space-y-1 text-sm sm:text-base">
                        <li>Senin - Minggu: 08:00 - 24:00</li>
                        <li>Booking minimal 1 jam</li>
                        <li>Maksimal booking 3 jam berturut-turut</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-semibold text-amber-700 mb-3 text-sm sm:text-base">💳 Metode Pembayaran</h3>
                    <ul class="text-gray-600 space-y-1 text-sm sm:text-base">
                        <li>💰 Bayar di tempat (Cash)</li>
                        <li>🏦 Transfer bank</li>
                        <li>📱 Konfirmasi otomatis via WhatsApp</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <style>
        .text-glow {
            text-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
        }
    </style>
</body>
</html>
