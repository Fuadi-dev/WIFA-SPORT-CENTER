<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} - WIFA Sport Center</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-amber-50 to-orange-100 min-h-screen">
    
    <!-- Include Navbar -->
    @include('components.navbar')

    <div class="container mx-auto px-4 pt-32 pb-16">
        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-600 mb-8">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-amber-600">Home</a>
                    <i class="fas fa-chevron-right mx-2"></i>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('events.index') }}" class="hover:text-amber-600">Event & Turnamen</a>
                    <i class="fas fa-chevron-right mx-2"></i>
                </li>
                <li class="text-amber-600 font-semibold">{{ $event->title }}</li>
            </ol>
        </nav>

        <div class="grid lg:grid-cols-12 gap-8">
            <!-- Poster Display -->
            <div class="lg:col-span-5">
                <!-- Event Poster Card - Large Format -->
                <div class="bg-white rounded-xl shadow-2xl overflow-hidden border-4 border-white hover:border-amber-200 transition-all duration-300 mb-8 sticky top-24 poster-card">
                    <!-- Poster Container - A3/A4 Portrait Ratio -->
                    <div class="relative poster-large bg-gradient-to-br from-amber-400 via-orange-500 to-red-500">
                        @if($event->poster)
                            <img src="{{ Storage::url($event->poster) }}" 
                                 alt="{{ $event->title }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <!-- Default Poster Design - Large Version -->
                            <div class="w-full h-full relative flex flex-col justify-between p-8 text-white overflow-hidden">
                                <!-- Background Pattern -->
                                <div class="absolute inset-0 opacity-10">
                                    <div class="floating-pattern w-full h-full bg-repeat" style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"80\" height=\"80\" viewBox=\"0 0 80 80\"><circle cx=\"40\" cy=\"40\" r=\"3\" fill=\"white\" opacity=\"0.4\"/><circle cx=\"20\" cy=\"20\" r=\"2\" fill=\"white\" opacity=\"0.3\"/><circle cx=\"60\" cy=\"60\" r=\"2.5\" fill=\"white\" opacity=\"0.2\"/></svg>')"></div>
                                </div>
                                
                                <!-- Decorative Elements -->
                                <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-20 translate-x-20"></div>
                                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full translate-y-16 -translate-x-16"></div>
                                
                                <!-- Header -->
                                <div class="relative z-10">
                                    <div class="text-center mb-6">
                                        <div class="bg-black/30 backdrop-blur-sm rounded-full px-4 py-2 inline-block mb-4">
                                            <span class="text-sm font-mono tracking-wider">{{ $event->event_code }}</span>
                                        </div>
                                        <div class="bg-white/20 backdrop-blur-sm rounded-full p-6 w-24 h-24 mx-auto flex items-center justify-center shadow-lg">
                                            <i class="{{ $event->sport->icon }} text-4xl"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Main Content -->
                                <div class="relative z-10 text-center flex-1 flex flex-col justify-center px-4">
                                    <h1 class="text-2xl md:text-3xl font-bold mb-4 leading-tight drop-shadow-lg">
                                        {{ $event->title }}
                                    </h1>
                                    <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-3 mb-6 inline-block mx-auto">
                                        <p class="text-lg font-semibold">
                                            {{ $event->sport->name }}
                                        </p>
                                    </div>
                                    <div class="bg-black/40 backdrop-blur-sm rounded-xl p-6 shadow-lg">
                                        <div class="text-4xl font-bold mb-2">
                                            {{ \Carbon\Carbon::parse($event->event_date)->format('d') }}
                                        </div>
                                        <div class="text-lg font-medium mb-1">
                                            {{ \Carbon\Carbon::parse($event->event_date)->format('M Y') }}
                                        </div>
                                        <div class="text-sm opacity-80">
                                            {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }} WIB
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Footer -->
                                <div class="relative z-10 text-center">
                                    <div class="text-sm opacity-80 mb-4 font-medium tracking-wide">WIFA SPORT CENTER</div>
                                    @if($event->registration_fee > 0)
                                        <div class="bg-amber-500/90 backdrop-blur-sm rounded-full px-6 py-3 text-lg font-bold shadow-lg">
                                            @if($event->registration_fee >= 1000000)
                                                Rp {{ number_format($event->registration_fee/1000000, 1) }}JT
                                            @elseif($event->registration_fee >= 1000)
                                                Rp {{ number_format($event->registration_fee/1000, 0) }}K
                                            @else
                                                Rp {{ number_format($event->registration_fee, 0) }}
                                            @endif
                                        </div>
                                    @else
                                        <div class="bg-green-500/90 backdrop-blur-sm rounded-full px-6 py-3 text-lg font-bold shadow-lg">
                                            GRATIS
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 right-4 z-20">
                            @if($event->status === 'open_registration')
                                <span class="bg-green-500/95 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg border border-green-400">
                                    <i class="fas fa-check-circle mr-2"></i>BUKA PENDAFTARAN
                                </span>
                            @elseif($event->status === 'registration_closed')
                                <span class="bg-yellow-500/95 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg border border-yellow-400">
                                    <i class="fas fa-clock mr-2"></i>PENDAFTARAN DITUTUP
                                </span>
                            @elseif($event->status === 'ongoing')
                                <span class="bg-blue-500/95 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg border border-blue-400 animate-pulse">
                                    <i class="fas fa-play mr-2"></i>SEDANG BERLANGSUNG
                                </span>
                            @elseif($event->status === 'completed')
                                <span class="bg-purple-500/95 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg border border-purple-400">
                                    <i class="fas fa-trophy mr-2"></i>SELESAI
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Poster Actions -->
                    <div class="bg-white p-4 border-t border-gray-100">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span class="flex items-center font-medium">
                                <i class="fas fa-eye mr-2 text-amber-600"></i>
                                Detail Poster
                            </span>
                            <span class="flex items-center font-medium">
                                <i class="fas fa-download mr-2 text-amber-600"></i>
                                Format A3/A4
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-7">
                <!-- Event Information Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 border-2 border-amber-100 mb-8">
                    <!-- Title & Sport -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 md:mb-0">
                            {{ $event->title }}
                        </h1>
                        <span class="inline-flex items-center bg-amber-100 text-amber-800 text-lg font-semibold px-4 py-2 rounded-full">
                            <i class="{{ $event->sport->icon }} mr-2"></i>
                            {{ $event->sport->name }}
                        </span>
                    </div>

                    <!-- Event Description -->
                    <div class="prose max-w-none text-gray-700 mb-6">
                        {!! nl2br(e($event->description)) !!}
                    </div>

                    <!-- Requirements -->
                    @if($event->requirements)
                        <div class="bg-blue-50 rounded-lg p-6 mb-6 border-l-4 border-blue-400">
                            <h3 class="text-lg font-semibold text-blue-800 mb-3">
                                <i class="fas fa-list-check mr-2"></i>Persyaratan
                            </h3>
                            <div class="text-blue-700">
                                {!! nl2br(e($event->requirements)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Prize Info -->
                    @if($event->prize_info)
                        <div class="bg-yellow-50 rounded-lg p-6 mb-6 border-l-4 border-yellow-400">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-3">
                                <i class="fas fa-trophy mr-2"></i>Informasi Hadiah
                            </h3>
                            <div class="text-yellow-700">
                                {!! nl2br(e($event->prize_info)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Event Details Grid -->
                    <div class="grid md:grid-cols-2 gap-6 mt-8">
                        <div class="space-y-4">
                            <!-- Date & Time -->
                            <div class="flex items-start bg-gray-50 rounded-lg p-4">
                                <i class="fas fa-calendar-alt w-5 h-5 text-amber-600 mt-1 mr-3"></i>
                                <div>
                                    <div class="font-semibold text-gray-800">Tanggal & Waktu</div>
                                    <div class="text-gray-600">{{ \Carbon\Carbon::parse($event->event_date)->format('l, d F Y') }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }} WIB
                                    </div>
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="flex items-start bg-gray-50 rounded-lg p-4">
                                <i class="fas fa-map-marker-alt w-5 h-5 text-amber-600 mt-1 mr-3"></i>
                                <div>
                                    <div class="font-semibold text-gray-800">Lokasi</div>
                                    <div class="text-gray-600">{{ $event->court->name }}</div>
                                    <div class="text-sm text-gray-500">WIFA Sport Center</div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <!-- Registration Fee -->
                            <div class="flex items-start bg-gray-50 rounded-lg p-4">
                                <i class="fas fa-money-bill w-5 h-5 text-amber-600 mt-1 mr-3"></i>
                                <div>
                                    <div class="font-semibold text-gray-800">Biaya Pendaftaran</div>
                                    <div class="text-gray-600">
                                        @if($event->registration_fee > 0)
                                            Rp {{ number_format($event->registration_fee, 0, ',', '.') }}
                                        @else
                                            <span class="text-green-600 font-semibold">Gratis</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Registration Deadline -->
                            <div class="flex items-start bg-gray-50 rounded-lg p-4">
                                <i class="fas fa-hourglass-end w-5 h-5 text-amber-600 mt-1 mr-3"></i>
                                <div>
                                    <div class="font-semibold text-gray-800">Batas Pendaftaran</div>
                                    <div class="text-gray-600">
                                        {{ \Carbon\Carbon::parse($event->registration_deadline)->format('d F Y H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Status -->
                @auth
                    @if($userRegistration)
                        <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-green-200 mb-8">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-green-800">Anda Sudah Terdaftar</h3>
                                        <p class="text-green-600">Kode: {{ $userRegistration->registration_code }}</p>
                                        <p class="text-sm text-gray-600">Tim: {{ $userRegistration->team_name }}</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-sm font-semibold
                                    {{ $userRegistration->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $userRegistration->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ !in_array($userRegistration->status, ['approved', 'pending']) ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($userRegistration->status) }}
                                </span>
                            </div>
                        </div>
                    @endif
                @endauth

                <!-- Action Buttons -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-amber-100 mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-bolt mr-2 text-amber-600"></i>Aksi
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <!-- Registration Button -->
                        <div>
                            @auth
                                @if($userRegistration)
                                    <button class="w-full bg-green-500 text-white font-semibold py-4 px-6 rounded-lg cursor-not-allowed opacity-75">
                                        <i class="fas fa-check mr-2"></i>Sudah Terdaftar
                                    </button>
                                @elseif($event->is_registration_open)
                                    <a href="{{ route('events.register', $event) }}" 
                                       class="block w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-300 hover:shadow-lg text-center transform hover:scale-105">
                                        <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                                    </a>
                                @else
                                    <button class="w-full bg-gray-400 text-white font-semibold py-4 px-6 rounded-lg cursor-not-allowed">
                                        <i class="fas fa-times mr-2"></i>Pendaftaran Ditutup
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" 
                                   class="block w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-300 hover:shadow-lg text-center transform hover:scale-105">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Login untuk Mendaftar
                                </a>
                            @endauth
                        </div>
                        
                        <!-- Share Button -->
                        <div>
                            <button onclick="shareEvent()" 
                                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-300 hover:shadow-lg transform hover:scale-105">
                                <i class="fas fa-share-alt mr-2"></i>Bagikan Event
                            </button>
                        </div>
                    </div>
                    
                    <!-- Event Stats -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-amber-600">{{ $event->registrations_count ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Tim Terdaftar</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-amber-600">{{ $event->max_teams }}</div>
                                <div class="text-sm text-gray-600">Kuota Maksimal</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-amber-600">{{ $event->max_teams - ($event->registrations_count ?? 0) }}</div>
                                <div class="text-sm text-gray-600">Slot Tersisa</div>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-amber-500 to-orange-500 h-3 rounded-full transition-all duration-500" 
                                     style="width: {{ ($event->registrations_count ?? 0) / $event->max_teams * 100 }}%"></div>
                            </div>
                            <div class="text-center text-sm text-gray-600 mt-2">
                                {{ number_format(($event->registrations_count ?? 0) / $event->max_teams * 100, 1) }}% Terisi
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                    <h3 class="text-lg font-bold mb-4">
                        <i class="fas fa-phone mr-2"></i>Butuh Bantuan?
                    </h3>
                    <p class="text-sm mb-4 opacity-90">
                        Hubungi admin WIFA Sport Center untuk informasi lebih lanjut tentang event ini.
                    </p>
                    <a href="https://wa.me/6285741182762" target="_blank"
                       class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg transition-all duration-300 shadow-lg text-sm">
                        <i class="fab fa-whatsapp mr-2"></i>
                        WhatsApp Admin
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- CSS Styles -->
    <style>
        /* Large Poster (24x36 inch equivalent) for detail page */
        .poster-large {
            aspect-ratio: 2 / 3; /* 24:36 ratio */
            min-height: 600px; /* Ensure substantial size */
        }
        
        /* Background Pattern Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .floating-pattern {
            animation: float 6s ease-in-out infinite;
        }
        
        /* Poster Gallery Grid */
        .poster-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        /* Enhanced Poster Card Effects */
        .poster-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .poster-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        }
        
        /* Featured Poster Animation */
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px rgba(245, 158, 11, 0.5); }
            50% { box-shadow: 0 0 30px rgba(245, 158, 11, 0.8); }
        }
        
        .featured-poster {
            animation: glow 3s ease-in-out infinite;
        }
        
        /* Backdrop Blur Support */
        .backdrop-blur-sm {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
        
        /* Responsive poster scaling */
        @media (max-width: 768px) {
            .poster-large {
                min-height: 400px;
            }
        }
    </style>

    <!-- JavaScript -->
    <script>
        function shareEvent() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $event->title }}',
                    text: 'Lihat event menarik ini di WIFA Sport Center!',
                    url: window.location.href,
                });
            } else {
                // Fallback to copy URL
                navigator.clipboard.writeText(window.location.href).then(function() {
                    alert('Link event berhasil disalin!');
                });
            }
        }
    </script>
</body>
</html>