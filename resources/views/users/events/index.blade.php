<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event & Turnamen - WIFA Sport Center</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-amber-50 to-orange-100 min-h-screen">
    
    <!-- Include Navbar -->
    @include('components.navbar')

    <div class="container mx-auto px-4 pt-32 pb-16">
        <!-- Breadcrumb -->
        <nav class="hidden sm:block text-sm text-gray-600 mb-8">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-amber-600">Home</a>
                    <i class="fas fa-chevron-right mx-2"></i>
                </li>
                <li class="text-amber-600 font-semibold">Event & Turnamen</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="text-center mb-8 sm:mb-12">
            <h1 class="text-2xl sm:text-4xl md:text-5xl font-bold text-amber-800 mb-3 sm:mb-4">
                <i class="fas fa-images mr-2 sm:mr-3"></i>Gallery Event
            </h1>
            <p class="text-base sm:text-xl text-gray-700 max-w-3xl mx-auto mb-4 sm:mb-6 px-4">
                Koleksi poster event dan turnamen olahraga terbaru di WIFA Sport Center
            </p>
            <div class="hidden sm:flex items-center justify-center space-x-6 text-sm text-gray-600">
                <div class="flex items-center">
                    <i class="fas fa-image mr-2 text-amber-600"></i>
                    <span>Format Poster Professional</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-eye mr-2 text-amber-600"></i>
                    <span>Klik untuk Detail Lengkap</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-download mr-2 text-amber-600"></i>
                    <span>Ukuran A3 & A4</span>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 sm:mb-8 border-2 border-amber-100">
            <form method="GET" action="{{ route('events.index') }}" class="space-y-3 sm:space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
                <!-- Search -->
                <div class="flex-1">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Cari Event</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Nama event..." 
                           class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>
                
                <!-- Sport Filter -->
                <div class="flex-1">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Olahraga</label>
                    <select name="sport_id" class="w-full px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Semua Olahraga</option>
                        @foreach($sports as $sport)
                            <option value="{{ $sport->id }}" {{ request('sport_id') == $sport->id ? 'selected' : '' }}>
                                {{ $sport->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 sm:flex-none px-4 sm:px-6 py-2 text-sm bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        <i class="fas fa-search mr-2"></i><span class="hidden sm:inline">Filter</span><span class="sm:hidden">Cari</span>
                    </button>
                    <a href="{{ route('events.index') }}" class="flex-1 sm:flex-none px-4 sm:px-6 py-2 text-sm bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-center">
                        <i class="fas fa-times mr-2"></i><span class="hidden sm:inline">Reset</span><span class="sm:hidden">Clear</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- Events Grid - Poster Gallery Style -->
        @if($events->count() > 0)
            <div class="poster-gallery mb-12">
                @foreach($events as $event)
                    <!-- Event Poster Card - A3/A4 Portrait Ratio (1:1.414) -->
                    <div class="group relative poster-card {{ $loop->first ? 'featured-poster' : '' }}">
                        <a href="{{ route('events.show', $event) }}" class="block">
                            <!-- Featured Badge for First Event -->
                            @if($loop->first)
                                <div class="absolute -top-2 -right-2 z-30">
                                    <div class="bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold shadow-lg animate-pulse">
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="bg-white rounded-lg shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden border-4 border-white hover:border-amber-200 {{ $loop->first ? 'ring-2 ring-amber-300' : '' }}">
                                <!-- Poster Container - A3/A4 Aspect Ratio -->
                                <div class="relative poster-medium bg-gradient-to-br from-amber-400 via-orange-500 to-red-500">
                                    @if($event->poster)
                                        <img src="{{ Storage::url($event->poster) }}" 
                                             alt="{{ $event->title }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <!-- Default Poster Design -->
                                        <div class="w-full h-full relative flex flex-col justify-between p-6 text-white overflow-hidden">
                                            <!-- Background Pattern -->
                                            <div class="absolute inset-0 opacity-10">
                                                <div class="floating-pattern w-full h-full bg-repeat" style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"60\" height=\"60\" viewBox=\"0 0 60 60\"><circle cx=\"30\" cy=\"30\" r=\"2\" fill=\"white\" opacity=\"0.4\"/><circle cx=\"15\" cy=\"15\" r=\"1\" fill=\"white\" opacity=\"0.3\"/><circle cx=\"45\" cy=\"45\" r=\"1.5\" fill=\"white\" opacity=\"0.2\"/></svg>')"></div>
                                            </div>
                                            
                                            <!-- Decorative Elements -->
                                            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-16 translate-x-16"></div>
                                            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
                                            
                                            <!-- Header -->
                                            <div class="relative z-10">
                                                <div class="text-center mb-4">
                                                    <div class="bg-black/30 backdrop-blur-sm rounded-full px-3 py-1 inline-block mb-3">
                                                        <span class="text-xs font-mono tracking-wider">{{ $event->event_code }}</span>
                                                    </div>
                                                    <div class="bg-white/20 backdrop-blur-sm rounded-full p-4 w-16 h-16 mx-auto flex items-center justify-center shadow-lg">
                                                        <i class="{{ $event->sport->icon }} text-2xl"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Main Content -->
                                            <div class="relative z-10 text-center flex-1 flex flex-col justify-center px-2">
                                                <h3 class="text-lg font-bold mb-3 leading-tight drop-shadow-lg">
                                                    {{ Str::limit($event->title, 45) }}
                                                </h3>
                                                <div class="bg-white/20 backdrop-blur-sm rounded-lg px-3 py-2 mb-4 inline-block mx-auto">
                                                    <p class="text-sm font-semibold">
                                                        {{ $event->sport->name }}
                                                    </p>
                                                </div>
                                                <div class="bg-black/40 backdrop-blur-sm rounded-lg p-4 shadow-lg">
                                                    <div class="text-2xl font-bold mb-1">
                                                        {{ \Carbon\Carbon::parse($event->event_date)->format('d') }}
                                                    </div>
                                                    <div class="text-sm font-medium">
                                                        {{ \Carbon\Carbon::parse($event->event_date)->format('M Y') }}
                                                    </div>
                                                    <div class="text-xs opacity-80 mt-1">
                                                        {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} WIB
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Footer -->
                                            <div class="relative z-10 text-center">
                                                <div class="text-xs opacity-80 mb-3 font-medium tracking-wide">WIFA SPORT CENTER</div>
                                                @if($event->registration_fee > 0)
                                                    <div class="bg-amber-500/90 backdrop-blur-sm rounded-full px-4 py-2 text-sm font-bold shadow-lg">
                                                        @if($event->registration_fee >= 1000000)
                                                            Rp {{ number_format($event->registration_fee/1000000, 1) }}JT
                                                        @elseif($event->registration_fee >= 1000)
                                                            Rp {{ number_format($event->registration_fee/1000, 0) }}K
                                                        @else
                                                            Rp {{ number_format($event->registration_fee, 0) }}
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="bg-green-500/90 backdrop-blur-sm rounded-full px-4 py-2 text-sm font-bold shadow-lg">
                                                        GRATIS
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Status Badge -->
                                    <div class="absolute top-3 right-3 z-20">
                                        @if($event->status === 'open_registration')
                                            <span class="bg-green-500/95 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg border border-green-400">
                                                <i class="fas fa-check-circle mr-1"></i>BUKA
                                            </span>
                                        @elseif($event->status === 'registration_closed')
                                            <span class="bg-yellow-500/95 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg border border-yellow-400">
                                                <i class="fas fa-clock mr-1"></i>TUTUP
                                            </span>
                                        @elseif($event->status === 'ongoing')
                                            <span class="bg-blue-500/95 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg border border-blue-400 animate-pulse">
                                                <i class="fas fa-play mr-1"></i>LIVE
                                            </span>
                                        @elseif($event->status === 'completed')
                                            <span class="bg-purple-500/95 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg border border-purple-400">
                                                <i class="fas fa-trophy mr-1"></i>SELESAI
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Hover Overlay -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex items-end justify-center pb-8 z-10">
                                        <div class="transform translate-y-8 group-hover:translate-y-0 transition-transform duration-500">
                                            <span class="bg-white/95 backdrop-blur-sm text-gray-800 px-6 py-3 rounded-full font-bold shadow-xl border border-white/50 hover:bg-amber-50 transition-colors">
                                                <i class="fas fa-eye mr-2 text-amber-600"></i>Lihat Detail
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Quick Info Strip -->
                                <div class="bg-white p-3 border-t border-gray-100">
                                    <div class="flex items-center justify-between text-xs text-gray-600">
                                        <span class="flex items-center font-medium">
                                            <i class="fas fa-users mr-1 text-amber-600"></i>
                                            {{ $event->registrations_count ?? 0 }}/{{ $event->max_teams }}
                                        </span>
                                        <span class="flex items-center font-medium">
                                            <i class="fas fa-calendar mr-1 text-amber-600"></i>
                                            {{ \Carbon\Carbon::parse($event->event_date)->format('d M') }}
                                        </span>
                                        <span class="flex items-center font-medium">
                                            <i class="fas fa-clock mr-1 text-amber-600"></i>
                                            {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $events->links() }}
            </div>
        @else
            <!-- No Events -->
            <div class="text-center py-12 sm:py-16">
                <div class="bg-white rounded-xl shadow-lg p-8 sm:p-12 max-w-md mx-auto border-2 border-amber-100">
                    <div class="poster-medium bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg mb-4 sm:mb-6 mx-auto max-w-xs flex items-center justify-center">
                        <i class="fas fa-images text-gray-400 text-4xl sm:text-6xl"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-3 sm:mb-4">Gallery Kosong</h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">
                        Belum ada poster event atau turnamen yang tersedia saat ini. 
                        Silakan periksa kembali nanti!
                    </p>
                    <a href="{{ route('booking.index') }}" 
                       class="inline-flex items-center bg-amber-600 hover:bg-amber-700 text-white font-semibold px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg transition-all duration-300 text-sm sm:text-base">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Booking Lapangan
                    </a>
                </div>
            </div>
        @endif

        <!-- Call to Action -->
        <div class="mt-12 sm:mt-16 bg-gradient-to-r from-amber-600 to-orange-600 rounded-xl shadow-lg p-6 sm:p-8 text-center text-white relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="w-full h-full bg-repeat floating-pattern" style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"80\" height=\"80\" viewBox=\"0 0 80 80\"><rect x=\"10\" y=\"10\" width=\"20\" height=\"28\" fill=\"white\" opacity=\"0.3\" rx=\"2\"/><rect x=\"50\" y=\"25\" width=\"15\" height=\"21\" fill=\"white\" opacity=\"0.2\" rx=\"1\"/></svg>')"></div>
            </div>
            
            <div class="relative z-10">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-bold mb-3 sm:mb-4">
                    <i class="fas fa-palette mr-2 sm:mr-3"></i>Ingin Membuat Poster Event?
                </h2>
                <p class="text-sm sm:text-lg mb-4 sm:mb-6 opacity-90">
                    Hubungi kami untuk konsultasi desain poster dan penyelenggaraan event di WIFA Sport Center
                </p>
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center items-center">
                    <a href="https://wa.me/6285741182762" target="_blank"
                       class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-semibold px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg transition-all duration-300 shadow-lg text-sm sm:text-base">
                        <i class="fab fa-whatsapp mr-2"></i>
                        Konsultasi Gratis
                    </a>
                    <span class="text-xs sm:text-sm opacity-75">atau</span>
                    <span class="font-semibold text-sm sm:text-base">
                        <i class="fas fa-phone mr-2"></i>
                        +62 857 4118 2762
                    </span>
                </div>
                <div class="mt-3 sm:mt-4 text-xs sm:text-sm opacity-80">
                    <i class="fas fa-star mr-1"></i>
                    <span class="hidden sm:inline">Desain poster professional • Format A3/A4 • Siap cetak</span>
                    <span class="sm:hidden">Poster professional • A3/A4</span>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* A3/A4 Poster Aspect Ratio */
        .aspect-\[1\/1\.414\] {
            aspect-ratio: 1 / 1.414;
        }
        
        /* Poster Gallery Grid */
        @media (min-width: 640px) {
            .grid-cols-poster-sm {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 2rem;
            }
        }
        
        @media (min-width: 1024px) {
            .grid-cols-poster-lg {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 2.5rem;
            }
        }
        
        @media (min-width: 1280px) {
            .grid-cols-poster-xl {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 3rem;
            }
        }
        
        /* Poster Card Hover Effects */
        .poster-card {
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .poster-card:hover {
            transform: translateY(-12px) rotate(1deg);
        }
        
        /* Featured Poster Special Effects */
        .featured-poster {
            position: relative;
        }
        
        .featured-poster::before {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            background: linear-gradient(45deg, #f59e0b, #f97316, #ef4444, #f59e0b);
            background-size: 400% 400%;
            border-radius: 12px;
            z-index: -1;
            opacity: 0.6;
            animation: gradientShift 3s ease infinite;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .featured-poster:hover {
            transform: translateY(-16px) rotate(2deg) scale(1.02);
        }
        
        /* Background Pattern Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .floating-pattern {
            animation: float 6s ease-in-out infinite;
        }
        
        /* Shimmer Effect for Loading */
        .poster-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* Large Poster (24x36 inch equivalent) for featured events */
        .poster-large {
            aspect-ratio: 2 / 3; /* 24:36 ratio */
        }
        
        /* Medium Poster (A2 size equivalent) */
        .poster-medium {
            aspect-ratio: 1 / 1.414; /* A2/A3/A4 ratio */
        }
        
        /* Gallery responsive breakpoints */
        .poster-gallery {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }
        
        @media (min-width: 640px) {
            .poster-gallery {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }
        }
        
        @media (min-width: 1024px) {
            .poster-gallery {
                grid-template-columns: repeat(3, 1fr);
                gap: 2rem;
            }
        }
        
        @media (min-width: 1280px) {
            .poster-gallery {
                grid-template-columns: repeat(4, 1fr);
                gap: 2.5rem;
            }
        }
    </style>
</body>
</html>