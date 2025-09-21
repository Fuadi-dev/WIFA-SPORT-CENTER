<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Court - WIFA Sport Center</title>
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
                    <a href="{{ route('booking.index') }}" class="hover:text-amber-600">Pilih Olahraga</a>
                    <i class="fas fa-chevron-right mx-2"></i>
                </li>
                <li class="text-amber-600 font-semibold">Pilih Court {{ $sport->name }}</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="text-center mb-12">
            <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                <i class="{{ $sport->icon }} text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-amber-800 mb-4">
                Pilih Court {{ $sport->name }}
            </h1>
            <p class="text-xl text-gray-700 max-w-2xl mx-auto">
                Pilih court {{ $sport->name }} yang ingin Anda booking
            </p>
        </div>

        <!-- Courts Grid -->
        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            @foreach($courts as $court)
            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border-2 border-amber-100 hover:border-amber-300">
                <div class="p-8 text-center">
                    <!-- Court Illustration -->
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-green-400 to-green-600 rounded-lg flex items-center justify-center shadow-lg">
                        <i class="fas fa-square text-4xl text-white"></i>
                    </div>
                    
                    <!-- Court Name -->
                    <h3 class="text-2xl font-bold text-amber-800 mb-3">{{ $court->name }}</h3>
                    
                    <!-- Court Type Badge -->
                    @if($court->type)
                    <span class="inline-block bg-amber-100 text-amber-800 text-sm font-semibold px-3 py-1 rounded-full mb-4">
                        Court {{ $court->type }}
                    </span>
                    @endif
                    
                    <!-- Description -->
                    <p class="text-gray-600 mb-6 text-sm">{{ $court->description }}</p>
                    
                    <!-- Select Button -->
                    <a href="{{ route('booking.schedule', ['sport' => $sport->slug, 'court' => $court->slug]) }}" 
                       class="w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 hover:shadow-lg transform hover:scale-105 inline-block">
                        <i class="fas fa-calendar-alt mr-2"></i>Pilih Jadwal
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Back Button -->
        <div class="text-center mt-12">
            <a href="{{ route('booking.index') }}" 
               class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Pilih Olahraga
            </a>
        </div>

        <!-- Court Layout Info -->
        <div class="mt-16 bg-white rounded-xl shadow-lg p-8 max-w-4xl mx-auto border-2 border-amber-100">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-amber-800 mb-4">
                    <i class="fas fa-map mr-2"></i>Layout Lapangan {{ $sport->name }}
                </h2>
            </div>
            
            <div class="text-center text-gray-600">
                <p class="mb-4">1 Lapangan Voli = 2 Court Badminton (A & B)</p>
                <div class="bg-amber-50 rounded-lg p-6 max-w-md mx-auto">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-green-200 rounded p-4 text-center">
                            <i class="fas fa-square mb-2"></i>
                            <div class="font-semibold">Court A</div>
                        </div>
                        <div class="bg-green-200 rounded p-4 text-center">
                            <i class="fas fa-square mb-2"></i>
                            <div class="font-semibold">Court B</div>
                        </div>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        Lapangan Voli/Badminton
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
