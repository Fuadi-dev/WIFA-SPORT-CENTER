@extends('admin.layouts.admin')

@section('title', 'Laporan Booking')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="px-6 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Laporan Booking</h1>
                    <p class="mt-2 text-gray-600">Analisis dan statistik booking lapangan</p>
                </div>
                
                <div class="mt-4 md:mt-0 flex space-x-3">
                    <button onclick="printReport()" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-print mr-2"></i>
                        Cetak
                    </button>
                    <button onclick="exportReport()" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="px-6 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">

            <!-- Total Revenue -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Completed Bookings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Booking Selesai</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2">{{ number_format($stats['completed_bookings']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Paid Bookings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Booking Dibayar</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($stats['paid_bookings']) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-check-alt text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Laporan</h3>
            <form method="GET" action="{{ route('admin.reports.bookings') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    
                    <!-- Sport Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Olahraga</label>
                        <select name="sport_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">Semua Olahraga</option>
                            @foreach($sports as $sport)
                                <option value="{{ $sport->id }}" {{ request('sport_id') == $sport->id ? 'selected' : '' }}>
                                    {{ $sport->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Court Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lapangan</label>
                        <select name="court_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">Semua Lapangan</option>
                            @foreach($courts as $court)
                                <option value="{{ $court->id }}" {{ request('court_id') == $court->id ? 'selected' : '' }}>
                                    {{ $court->name }} ({{ $court->sport->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Booking</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">Semua Status</option>
                            <option value="pending_payment" {{ request('status') === 'pending_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Dibayar</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <a href="{{ route('admin.reports.bookings') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        <i class="fas fa-times mr-1"></i>Reset
                    </a>
                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        <i class="fas fa-filter mr-1"></i>Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Monthly Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking per Bulan ({{ now()->year }})</h3>
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
            
            <!-- Sport Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking per Olahraga</h3>
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="sportChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Booking</h3>
            </div>
            
            @if($bookings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lapangan</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($bookings as $booking)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->booking_code }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $booking->booking_date->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $booking->court->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $booking->court->sport->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($booking->status === 'paid') bg-green-100 text-green-800
                                            @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800
                                            @elseif($booking->status === 'completed') bg-purple-100 text-purple-800
                                            @elseif($booking->status === 'pending_payment') bg-yellow-100 text-yellow-800
                                            @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            @if($booking->status === 'paid') Dibayar
                                            @elseif($booking->status === 'confirmed') Dikonfirmasi
                                            @elseif($booking->status === 'completed') Selesai
                                            @elseif($booking->status === 'pending_payment') Menunggu Pembayaran
                                            @elseif($booking->status === 'cancelled') Dibatalkan
                                            @else {{ ucfirst($booking->status) }}
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-bar text-white text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Data</h3>
                    <p class="text-gray-500 mb-4">Tidak ada data booking yang sesuai dengan filter Anda</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart instances
    let monthlyChart = null;
    let sportChart = null;
    
    // Export Report Function
    function exportReport() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = "{{ route('admin.reports.bookings.export') }}?" + params.toString();
    }
    
    // Print Report Function
    function printReport() {
        window.print();
    }
    
    // Initialize charts when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Bookings Chart
        const monthlyCanvas = document.getElementById('monthlyChart');
        if (monthlyCanvas) {
            fetch("{{ route('admin.reports.bookings.by-month') }}")
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Destroy existing chart if exists
                    if (monthlyChart) {
                        monthlyChart.destroy();
                    }
                    
                    const ctx = monthlyCanvas.getContext('2d');
                    monthlyChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.map(item => item.month_name),
                            datasets: [{
                                label: 'Jumlah Booking',
                                data: data.map(item => item.bookings),
                                borderColor: 'rgb(245, 158, 11)',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
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
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading monthly chart:', error);
                    monthlyCanvas.parentElement.innerHTML = '<p class="text-center text-gray-500 py-8">Gagal memuat data chart</p>';
                });
        }
        
        // Sport Bookings Chart
        const sportCanvas = document.getElementById('sportChart');
        if (sportCanvas) {
            fetch("{{ route('admin.reports.bookings.by-sport') }}")
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Destroy existing chart if exists
                    if (sportChart) {
                        sportChart.destroy();
                    }
                    
                    const ctx = sportCanvas.getContext('2d');
                    sportChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: data.map(item => item.name),
                            datasets: [{
                                data: data.map(item => item.count),
                                backgroundColor: [
                                    'rgba(245, 158, 11, 0.8)',
                                    'rgba(59, 130, 246, 0.8)',
                                    'rgba(16, 185, 129, 0.8)',
                                    'rgba(139, 92, 246, 0.8)',
                                    'rgba(239, 68, 68, 0.8)',
                                ],
                                borderWidth: 2,
                                borderColor: '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading sport chart:', error);
                    sportCanvas.parentElement.innerHTML = '<p class="text-center text-gray-500 py-8">Gagal memuat data chart</p>';
                });
        }
    });
</script>

<style>
    @media print {
        .admin-sidebar,
        button,
        .pagination,
        nav {
            display: none !important;
        }
        
        .main-content {
            margin-left: 0 !important;
        }
        
        body {
            background: white !important;
        }
    }
</style>
@endpush
