<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Lapangan - WIFA Sport Center</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom styles for schedule table */
        .schedule-table {
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .status-available {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }
        
        .status-booked {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }
        
        .status-libur {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }
        
        .court-tab {
            transition: all 0.3s ease;
        }
        
        .court-tab:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        
        .today-row {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 50%, #fcf0c1 100%);
            border-left: 4px solid #f59e0b;
        }
        
        .table-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        /* Animation for loading */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Responsive table */
        @media (max-width: 768px) {
            .schedule-table {
                font-size: 0.875rem;
            }
            
            .court-tab {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-50 to-orange-100 min-h-screen">
    
    <!-- Include Navbar -->
    @include('components.navbar')

    <div class="container mx-auto px-4 pt-32 pb-16">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                <i class="fas fa-calendar-alt text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-amber-800 mb-4">
                Jadwal Lapangan
            </h1>
            <p class="text-xl text-gray-700 max-w-2xl mx-auto">
                Lihat jadwal booking semua lapangan olahraga di WIFA Sport Center
            </p>
        </div>

        <!-- Court Selection Tabs -->
        <div class="mb-8 fade-in">
            <div class="flex flex-wrap justify-center gap-3 mb-6">
                @foreach($courts as $court)
                    <a href="?court={{ $court->id }}&date={{ $selectedDate }}" 
                       class="court-tab px-6 py-3 rounded-xl font-semibold transition-all duration-300 inline-flex items-center space-x-2
                              {{ $selectedCourt->id == $court->id ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-lg scale-105' : 'bg-white text-amber-800 hover:bg-amber-50 shadow-md hover:shadow-lg' }}">
                        <i class="{{ $court->sport->icon }} text-lg"></i>
                        <span>{{ $court->name }}</span>
                    </a>
                @endforeach
            </div>
            
            <!-- Selected Court Info -->
            <div class="text-center bg-white rounded-xl p-4 shadow-md max-w-md mx-auto">
                <h3 class="text-lg font-semibold text-amber-800">
                    <i class="{{ $selectedCourt->sport->icon }} mr-2"></i>
                    {{ $selectedCourt->sport->name }} - {{ $selectedCourt->name }}
                </h3>
                @if($selectedCourt->physical_location)
                    @php
                        $sharedCourts = $selectedCourt->getSharedCourts();
                    @endphp
                    @if($sharedCourts->count() > 0)
                        <p class="text-sm text-amber-600 mt-1">
                            📍 Berbagi lapangan dengan: {{ $sharedCourts->map(function($c) { return $c->sport->name; })->join(', ') }}
                        </p>
                    @endif
                @endif
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 fade-in">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Date Filter -->
                <div class="flex items-center space-x-2">
                    <label class="text-gray-700 font-semibold">
                        <i class="fas fa-calendar mr-1"></i>Pilih Tanggal:
                    </label>
                    <input type="date" 
                           id="filterDate" 
                           value="{{ $selectedDate }}"
                           class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                
                <!-- Search -->
                <div class="flex items-center space-x-2">
                    <label class="text-gray-700 font-semibold">
                        <i class="fas fa-search mr-1"></i>Cari Tim:
                    </label>
                    <input type="text" 
                           id="searchTeam" 
                           placeholder="Nama tim atau pemesan..."
                           class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500 w-full md:w-64">
                </div>
                
                <!-- Legend -->
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <span class="inline-flex items-center px-2 py-1 rounded status-available">
                        <i class="fas fa-circle text-xs mr-1"></i>Tersedia
                    </span>
                    <span class="inline-flex items-center px-2 py-1 rounded status-booked">
                        <i class="fas fa-circle text-xs mr-1"></i>Dibooking
                    </span>
                    <span class="inline-flex items-center px-2 py-1 rounded status-libur">
                        <i class="fas fa-circle text-xs mr-1"></i>Libur
                    </span>
                </div>
            </div>
        </div>

        <!-- Schedule Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden fade-in schedule-table">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="table-header text-white">
                            <th class="py-4 px-6 text-left font-bold">
                                <i class="fas fa-calendar-day mr-2"></i>Tanggal
                            </th>
                            <th class="py-4 px-6 text-left font-bold">
                                <i class="fas fa-clock mr-2"></i>Jam
                            </th>
                            <th class="py-4 px-6 text-left font-bold">
                                <i class="fas fa-users mr-2"></i>Tim/Pemesan
                            </th>
                            <th class="py-4 px-6 text-left font-bold">
                                <i class="fas fa-info-circle mr-2"></i>Status
                            </th>
                            <th class="py-4 px-6 text-left font-bold">
                                <i class="fas fa-tag mr-2"></i>Kategori Harga
                            </th>
                        </tr>
                    </thead>
                    <tbody id="scheduleTableBody">
                        @forelse($schedules as $index => $schedule)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200 schedule-row
                                       {{ $schedule->is_today ? 'today-row' : '' }}"
                                data-team="{{ strtolower($schedule->team_name ?? '') }}"
                                data-date="{{ $schedule->date->format('Y-m-d') }}">
                                
                                <!-- Date -->
                                <td class="py-4 px-6">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-gray-800">
                                            {{ $schedule->date->locale('id')->isoFormat('DD MMM YYYY') }}
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            {{ $schedule->date->locale('id')->isoFormat('dddd') }}
                                        </span>
                                        @if($schedule->is_today)
                                            <span class="text-xs bg-amber-200 text-amber-800 px-2 py-1 rounded-full mt-1 inline-block w-fit">
                                                Hari Ini
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Time -->
                                <td class="py-4 px-6">
                                    <span class="font-mono text-lg font-semibold text-gray-800">
                                        {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                    </span>
                                </td>
                                
                                <!-- Team/Booker -->
                                <td class="py-4 px-6">
                                    @if($schedule->status === 'booked' && $schedule->booking_details)
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-gray-800">{{ $schedule->team_name }}</span>
                                            <span class="text-sm text-gray-600">
                                                {{ $schedule->booking_details->user->name ?? 'N/A' }}
                                            </span>
                                            @if($schedule->booking_details->notes)
                                                <span class="text-xs text-gray-500 mt-1">
                                                    <i class="fas fa-sticky-note mr-1"></i>{{ $schedule->booking_details->notes }}
                                                </span>
                                            @endif
                                        </div>
                                    @elseif($schedule->status === 'libur')
                                        <span class="font-semibold text-red-600">{{ $schedule->team_name }}</span>
                                    @else
                                        <span class="text-gray-400 italic">Belum ada booking</span>
                                    @endif
                                </td>
                                
                                <!-- Status -->
                                <td class="py-4 px-6">
                                    @if($schedule->status === 'available')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold status-available">
                                            <i class="fas fa-check-circle mr-2"></i>Tersedia
                                        </span>
                                    @elseif($schedule->status === 'booked')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold status-booked">
                                            <i class="fas fa-calendar-check mr-2"></i>Sudah Dibooking
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold status-libur">
                                            <i class="fas fa-times-circle mr-2"></i>Libur
                                        </span>
                                    @endif
                                </td>
                                
                                <!-- Price Category -->
                                <td class="py-4 px-6">
                                    @if($schedule->price_category === 'morning')
                                        <span class="inline-flex items-center px-2 py-1 rounded text-sm font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-sun mr-1"></i>Pagi (Rp 60.000)
                                        </span>
                                    @elseif($schedule->price_category === 'afternoon')
                                        <span class="inline-flex items-center px-2 py-1 rounded text-sm font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-cloud-sun mr-1"></i>Siang (Rp 80.000)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded text-sm font-medium bg-purple-100 text-purple-800">
                                            <i class="fas fa-moon mr-1"></i>Malam (Rp 100.000)
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-500">
                                        <i class="fas fa-calendar-times text-4xl mb-4"></i>
                                        <p class="text-lg font-semibold">Tidak ada jadwal untuk lapangan ini</p>
                                        <p class="text-sm">Pilih lapangan lain atau ubah tanggal</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 text-center">
            <a href="{{ route('booking.index') }}" 
               class="inline-flex items-center bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold px-8 py-3 rounded-xl transition-all duration-300 hover:shadow-lg hover:scale-105">
                <i class="fas fa-plus-circle mr-2"></i>
                Booking Sekarang
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterDate = document.getElementById('filterDate');
            const searchTeam = document.getElementById('searchTeam');
            const tableRows = document.querySelectorAll('.schedule-row');
            
            // Date filter
            filterDate.addEventListener('change', function() {
                const selectedCourt = {{ $selectedCourt->id }};
                window.location.href = `?court=${selectedCourt}&date=${this.value}`;
            });
            
            // Search functionality
            searchTeam.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                tableRows.forEach(row => {
                    const teamName = row.getAttribute('data-team');
                    if (teamName.includes(searchTerm) || searchTerm === '') {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Show "no results" message if needed
                const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
                const noResultsRow = document.getElementById('noResultsRow');
                
                if (visibleRows.length === 0 && searchTerm !== '') {
                    if (!noResultsRow) {
                        const tbody = document.getElementById('scheduleTableBody');
                        const newRow = document.createElement('tr');
                        newRow.id = 'noResultsRow';
                        newRow.innerHTML = `
                            <td colspan="5" class="py-8 text-center text-gray-500">
                                <i class="fas fa-search text-2xl mb-2"></i>
                                <p>Tidak ditemukan hasil untuk "${searchTerm}"</p>
                            </td>
                        `;
                        tbody.appendChild(newRow);
                    }
                } else if (noResultsRow) {
                    noResultsRow.remove();
                }
            });
            
            // Add fade-in animation to table rows
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });
    </script>
</body>
</html>
