@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Ringkasan keseluruhan WIFA Sport Center')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Bookings -->
    <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">Total Booking</p>
                <p class="text-3xl font-bold">{{ number_format($stats['total_bookings']) }}</p>
                <p class="text-blue-100 text-xs mt-1">
                    <i class="fas fa-arrow-up mr-1"></i>
                    {{ $todayStats['bookings'] }} hari ini
                </p>
            </div>
            <div class="bg-blue-400/30 p-3 rounded-lg">
                <i class="fas fa-calendar-check text-2xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Revenue -->
    <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Total Pendapatan</p>
                <p class="text-3xl font-bold">Rp {{ number_format($monthStats['revenue'] ?? 0, 0, ',', '.') }}</p>
                <p class="text-green-100 text-xs mt-1">
                    <i class="fas fa-arrow-up mr-1"></i>
                    Bulan ini
                </p>
            </div>
            <div class="bg-green-400/30 p-3 rounded-lg">
                <i class="fas fa-money-bill-wave text-2xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Users -->
    <div class="stat-card bg-gradient-to-br from-amber-500 to-orange-500 text-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-amber-100 text-sm font-medium">Total User</p>
                <p class="text-3xl font-bold">{{ number_format($stats['total_users']) }}</p>
                <p class="text-amber-100 text-xs mt-1">
                    <i class="fas fa-arrow-up mr-1"></i>
                    {{ $todayStats['new_users'] }} baru hari ini
                </p>
            </div>
            <div class="bg-amber-400/30 p-3 rounded-lg">
                <i class="fas fa-users text-2xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Pending Bookings -->
    <div class="stat-card bg-gradient-to-br from-red-500 to-red-600 text-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm font-medium">Booking Pending</p>
                <p class="text-3xl font-bold">{{ number_format($stats['pending_bookings']) }}</p>
                <p class="text-red-100 text-xs mt-1">
                    <i class="fas fa-clock mr-1"></i>
                    Butuh konfirmasi
                </p>
            </div>
            <div class="bg-red-400/30 p-3 rounded-lg">
                <i class="fas fa-hourglass-half text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Revenue Chart -->
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-chart-line mr-2 text-green-600"></i>
                Pendapatan 6 Bulan Terakhir
            </h3>
        </div>
        <div class="chart-container" style="position: relative; height: 220px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    
    <!-- Booking Trends -->
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                Trend Booking 7 Hari Terakhir
            </h3>
        </div>
        <div class="chart-container" style="position: relative; height: 220px;">
            <canvas id="bookingChart"></canvas>
        </div>
    </div>
</div>

<!-- Tables Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Recent Bookings -->
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-clock mr-2 text-amber-600"></i>
                Booking Terbaru
            </h3>
            <a href="#" class="text-amber-600 hover:text-amber-700 text-sm font-medium">Lihat Semua</a>
        </div>
        
        <div class="space-y-4">
            @forelse($recentBookings as $booking)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-semibold text-sm">
                            {{ strtoupper(substr($booking->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">{{ $booking->user->name ?? 'Unknown User' }}</p>
                            <p class="text-gray-600 text-xs">{{ $booking->court->name ?? 'Unknown Court' }} - {{ $booking->sport->name ?? 'Unknown Sport' }}</p>
                            <p class="text-gray-500 text-xs">{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }} {{ $booking->start_time }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($booking->status === 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status === 'completed') bg-blue-100 text-blue-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                        <p class="text-gray-600 text-xs mt-1">Rp {{ number_format($booking->total_price ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-calendar-times text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-500">Belum ada booking terbaru</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Popular Sports -->
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-trophy mr-2 text-amber-600"></i>
                Olahraga Populer
            </h3>
        </div>
        
        <div class="space-y-4">
            @forelse($popularSports as $sport)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white">
                            @if($sport->name === 'Futsal')
                                <i class="fas fa-futbol"></i>
                            @elseif($sport->name === 'Voli')
                                <i class="fas fa-volleyball-ball"></i>
                            @elseif($sport->name === 'Badminton')
                                <i class="fas fa-baseball-ball"></i>
                            @else
                                <i class="fas fa-running"></i>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $sport->name }}</p>
                            <p class="text-gray-600 text-sm">{{ $sport->booking_count }} booking</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-amber-400 to-orange-500 h-2 rounded-full" 
                                 style="width: {{ ($sport->booking_count / $popularSports->max('booking_count')) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-chart-bar text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-500">Belum ada data olahraga</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white p-6 rounded-xl shadow-lg">
    <h3 class="text-lg font-bold text-gray-800 mb-6">
        <i class="fas fa-bolt mr-2 text-amber-600"></i>
        Aksi Cepat
    </h3>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="#" class="flex flex-col items-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg hover:from-blue-100 hover:to-blue-200 transition-all duration-300 group">
            <div class="bg-blue-500 text-white p-3 rounded-full mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-plus"></i>
            </div>
            <span class="text-sm font-medium text-blue-700">Tambah Booking</span>
        </a>
        
        <a href="#" class="flex flex-col items-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg hover:from-green-100 hover:to-green-200 transition-all duration-300 group">
            <div class="bg-green-500 text-white p-3 rounded-full mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-check"></i>
            </div>
            <span class="text-sm font-medium text-green-700">Konfirmasi Booking</span>
        </a>
        
        <a href="#" class="flex flex-col items-center p-4 bg-gradient-to-br from-amber-50 to-orange-100 rounded-lg hover:from-amber-100 hover:to-orange-200 transition-all duration-300 group">
            <div class="bg-amber-500 text-white p-3 rounded-full mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <span class="text-sm font-medium text-amber-700">Kelola Lapangan</span>
        </a>
        
        <a href="#" class="flex flex-col items-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg hover:from-purple-100 hover:to-purple-200 transition-all duration-300 group">
            <div class="bg-purple-500 text-white p-3 rounded-full mb-3 group-hover:scale-110 transition-transform">
                <i class="fas fa-chart-line"></i>
            </div>
            <span class="text-sm font-medium text-purple-700">Lihat Laporan</span>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($revenueData, 'month')) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode(array_column($revenueData, 'revenue')) !!},
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // Booking Trends Chart
    const bookingCtx = document.getElementById('bookingChart').getContext('2d');
    const bookingChart = new Chart(bookingCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($bookingTrends, 'date')) !!},
            datasets: [{
                label: 'Jumlah Booking',
                data: {!! json_encode(array_column($bookingTrends, 'count')) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush