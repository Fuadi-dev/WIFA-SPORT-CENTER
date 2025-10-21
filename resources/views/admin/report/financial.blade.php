@extends('admin.layouts.admin')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="px-6 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Laporan Keuangan</h1>
                    <p class="mt-2 text-gray-600">Analisis pendapatan dari booking lapangan dan event</p>
                </div>
                
                <div class="mt-4 md:mt-0 flex space-x-3">
                    <button onclick="printReport()" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
                        <i class="fas fa-print mr-2"></i>
                        Cetak
                    </button>
                    <button onclick="exportReport()" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-lg">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="px-6 py-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Pilih Periode</h3>
            <form method="GET" action="{{ route('admin.reports.financial') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>Terapkan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Revenue -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Total Pendapatan</p>
                        <p class="text-3xl font-bold mt-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                </div>
                <div class="flex items-center text-green-100 text-sm">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
                </div>
            </div>

            <!-- Booking Revenue -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Pendapatan Booking</p>
                        <p class="text-3xl font-bold mt-1">Rp {{ number_format($stats['booking_revenue'], 0, ',', '.') }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-check text-2xl"></i>
                    </div>
                </div>
                <div class="flex items-center text-blue-100 text-sm">
                    <i class="fas fa-list-ol mr-2"></i>
                    {{ number_format($stats['booking_count']) }} booking
                </div>
            </div>

            <!-- Event Revenue -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Pendapatan Event</p>
                        <p class="text-3xl font-bold mt-1">Rp {{ number_format($stats['event_revenue'], 0, ',', '.') }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-trophy text-2xl"></i>
                    </div>
                </div>
                <div class="flex items-center text-purple-100 text-sm">
                    <i class="fas fa-users mr-2"></i>
                    {{ number_format($stats['event_registrations']) }} pendaftar
                </div>
            </div>
        </div>

        <!-- Revenue Comparison Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Perbandingan Pendapatan Bulanan ({{ now()->year }})</h3>
            <div class="chart-container" style="position: relative; height: 400px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Revenue by Sport -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Booking by Sport -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pendapatan Booking per Olahraga</h3>
                @if($bookingBySport->count() > 0)
                    <div class="space-y-3">
                        @foreach($bookingBySport as $sport)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <i class="fas fa-futbol text-blue-500 mr-3"></i>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $sport->name }}</p>
                                            <p class="text-sm text-gray-500">{{ number_format($sport->count) }} booking</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600">Rp {{ number_format($sport->revenue, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format(($sport->revenue / $stats['booking_revenue']) * 100, 1) }}%</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Tidak ada data</p>
                    </div>
                @endif
            </div>

            <!-- Event by Sport -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pendapatan Event per Olahraga</h3>
                @if($eventBySport->count() > 0)
                    <div class="space-y-3">
                        @foreach($eventBySport as $sport)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <i class="fas fa-trophy text-purple-500 mr-3"></i>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $sport->name }}</p>
                                            <p class="text-sm text-gray-500">{{ number_format($sport->count) }} pendaftar</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600">Rp {{ number_format($sport->revenue, 0, ',', '.') }}</p>
                                    @if($stats['event_revenue'] > 0)
                                        <p class="text-xs text-gray-500">{{ number_format(($sport->revenue / $stats['event_revenue']) * 100, 1) }}%</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Tidak ada data</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Top Performers -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Top Courts -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-star text-amber-500 mr-2"></i>
                    Top 5 Lapangan Terlaris
                </h3>
                @if($topCourts->count() > 0)
                    <div class="space-y-3">
                        @foreach($topCourts as $index => $court)
                            <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg border border-amber-100">
                                <div class="flex-shrink-0 w-10 h-10 bg-amber-500 text-white rounded-full flex items-center justify-center font-bold">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $court->name }}</p>
                                    <p class="text-sm text-gray-500">{{ number_format($court->booking_count) }} booking</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600">Rp {{ number_format($court->revenue, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Tidak ada data</p>
                    </div>
                @endif
            </div>

            <!-- Top Events -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-medal text-purple-500 mr-2"></i>
                    Top 5 Event Terlaris
                </h3>
                @if($topEvents->count() > 0)
                    <div class="space-y-3">
                        @foreach($topEvents as $index => $event)
                            <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-100">
                                <div class="flex-shrink-0 w-10 h-10 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 truncate">{{ $event->title }}</p>
                                    <p class="text-sm text-gray-500">{{ number_format($event->registration_count) }} pendaftar</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600">Rp {{ number_format($event->revenue, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Tidak ada data</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Revenue Distribution Pie Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Pendapatan</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="distributionChart"></canvas>
                </div>
                <div class="flex flex-col justify-center space-y-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-blue-500 rounded-full mr-3"></div>
                            <span class="text-gray-700 font-medium">Booking Lapangan</span>
                        </div>
                        <span class="font-bold text-blue-600">
                            {{ $stats['total_revenue'] > 0 ? number_format(($stats['booking_revenue'] / $stats['total_revenue']) * 100, 1) : 0 }}%
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-purple-500 rounded-full mr-3"></div>
                            <span class="text-gray-700 font-medium">Event Tournament</span>
                        </div>
                        <span class="font-bold text-purple-600">
                            {{ $stats['total_revenue'] > 0 ? number_format(($stats['event_revenue'] / $stats['total_revenue']) * 100, 1) : 0 }}%
                        </span>
                    </div>
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700 font-semibold">Total Pendapatan</span>
                            <span class="font-bold text-green-600 text-lg">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Export Report Function
    function exportReport() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = "{{ route('admin.reports.financial.export') }}?" + params.toString();
    }
    
    // Print Report Function
    function printReport() {
        window.print();
    }
    
    // Initialize charts
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Revenue Chart
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            const monthlyData = @json($monthlyData);
            
            new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: monthlyData.map(d => d.month_name),
                    datasets: [
                        {
                            label: 'Booking',
                            data: monthlyData.map(d => d.booking_revenue),
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1
                        },
                        {
                            label: 'Event',
                            data: monthlyData.map(d => d.event_revenue),
                            backgroundColor: 'rgba(168, 85, 247, 0.8)',
                            borderColor: 'rgb(168, 85, 247)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: false,
                        },
                        y: {
                            stacked: false,
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID', {notation: 'compact'}).format(value);
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Distribution Pie Chart
        const distributionCtx = document.getElementById('distributionChart');
        if (distributionCtx) {
            new Chart(distributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Booking Lapangan', 'Event Tournament'],
                    datasets: [{
                        data: [
                            {{ $stats['booking_revenue'] }},
                            {{ $stats['event_revenue'] }}
                        ],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(168, 85, 247, 0.8)'
                        ],
                        borderColor: [
                            'rgb(59, 130, 246)',
                            'rgb(168, 85, 247)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed);
                                    
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    label += ' (' + percentage + '%)';
                                    
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>

<style>
    @media print {
        .admin-sidebar,
        button,
        nav,
        form {
            display: none !important;
        }
        
        .main-content {
            margin-left: 0 !important;
        }
        
        body {
            background: white !important;
        }
        
        .bg-gray-50 {
            background: white !important;
        }
    }
</style>
@endpush
