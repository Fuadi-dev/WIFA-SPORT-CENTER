<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Lapangan - WIFA Sport Center</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .time-slot {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .time-slot:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .time-slot.available {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 2px solid #10b981;
            color: #065f46;
        }
        
        .time-slot.booked {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 2px solid #ef4444;
            color: #991b1b;
            cursor: not-allowed;
        }
        
        .time-slot.booked:hover {
            transform: none;
        }
        
        .court-tab {
            transition: all 0.3s ease;
        }
        
        .court-tab:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        
        /* Date Cards Styles */
        .date-card {
            min-width: 120px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .date-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .date-card.active {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border-color: #f59e0b;
            transform: scale(1.05);
        }
        
        .date-card.inactive {
            background: white;
            color: #374151;
            border-color: #e5e7eb;
        }
        
        .date-card.weekend {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-color: #f59e0b;
        }
        
        .date-card.weekend.active {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        /* Scrollbar Hide */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #f59e0b;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
                Jadwal Lapangan Real-Time
            </h1>
            <p class="text-xl text-gray-700 max-w-2xl mx-auto">
                Lihat jadwal booking lapangan Futsal, Voli, dan Badminton secara real-time
            </p>
        </div>

        <!-- Court Selection and Date Filter -->
        <div class="mb-8 fade-in">
            <!-- Court Selection Tabs -->
            <div class="flex flex-wrap justify-center gap-3 mb-6">
                @foreach($courts as $court)
                    <button 
                        onclick="selectCourt({{ $court->id }})"
                        class="court-tab px-6 py-3 rounded-xl font-semibold transition-all duration-300 inline-flex items-center space-x-2
                               {{ $selectedCourt->id == $court->id ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-lg scale-105' : 'bg-white text-amber-800 hover:bg-amber-50 shadow-md hover:shadow-lg' }}"
                        id="court-tab-{{ $court->id }}">
                        <i class="{{ $court->sport->icon }} text-lg"></i>
                        <span>{{ $court->name }}</span>
                    </button>
                @endforeach
            </div>
            
            <!-- Selected Court Info -->
            <div class="text-center bg-white rounded-xl p-4 shadow-md max-w-md mx-auto mt-4 mb-5">
                <h3 class="text-lg font-semibold text-amber-800" id="selected-court-info">
                    <i class="{{ $selectedCourt->sport->icon }} mr-2"></i>
                    {{ $selectedCourt->sport->name }} - {{ $selectedCourt->name }}
                </h3>
                <p class="text-sm text-gray-600 mt-1" id="selected-date-info">
                    {{ Carbon\Carbon::parse($selectedDate)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}
                </p>
            </div>

            <!-- Date Filter Cards -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">
                    <i class="fas fa-calendar mr-2"></i>Pilih Tanggal
                </h3>
                <div class="relative">
                    <div class="flex overflow-x-auto scrollbar-hide space-x-3 py-2 px-2" id="dateCardsContainer">
                        <!-- Date cards will be generated by JavaScript -->
                    </div>
                    <!-- Scroll buttons -->
                    <button id="scrollLeft" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-white shadow-lg rounded-full p-2 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chevron-left text-gray-600"></i>
                    </button>
                    <button id="scrollRight" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-white shadow-lg rounded-full p-2 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chevron-right text-gray-600"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Time Slots Grid -->
        <div class="bg-white rounded-xl shadow-lg p-6 fade-in">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-clock mr-2"></i>Slot Waktu Tersedia
                </h3>
                <div class="flex items-center space-x-4 text-sm">
                    <span class="inline-flex items-center">
                        <div class="w-3 h-3 bg-green-400 rounded-full mr-2"></div>
                        Tersedia
                    </span>
                    <span class="inline-flex items-center">
                        <div class="w-3 h-3 bg-red-400 rounded-full mr-2"></div>
                        Dibooking
                    </span>
                </div>
            </div>
            
            <!-- Loading State -->
            <div id="loading-state" class="text-center py-8 hidden">
                <div class="loading-spinner mx-auto mb-3"></div>
                <p class="text-gray-600">Memuat jadwal...</p>
            </div>
            
            <!-- Time Slots Container -->
            <div id="time-slots-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                @foreach($timeSlots as $slot)
                    <div class="time-slot {{ $slot['is_available'] ? 'available' : 'booked' }} p-4 rounded-xl text-center"
                         onclick="{{ $slot['is_available'] ? 'showSlotDetails(' . json_encode($slot) . ')' : 'showBookingDetails(' . json_encode($slot) . ')' }}">
                        <div class="font-bold text-lg mb-1">{{ $slot['start_time'] }}</div>
                        <div class="text-sm opacity-80 mb-2">{{ $slot['price_label'] }}</div>
                        <div class="text-xs font-semibold">
                            @if($slot['is_available'])
                                Rp {{ number_format($slot['price'], 0, ',', '.') }}
                            @else
                                {{ $slot['booking_info']['team_name'] ?? 'Dibooking' }}
                            @endif
                        </div>
                        @if(!$slot['is_available'])
                            <div class="text-xs mt-1 opacity-60">
                                <i class="fas fa-user mr-1"></i>{{ $slot['booking_info']['user_name'] ?? 'N/A' }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <!-- No Slots Message -->
            <div id="no-slots-message" class="text-center py-12 hidden">
                <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                <p class="text-lg font-semibold text-gray-600">Tidak ada slot waktu tersedia</p>
                <p class="text-sm text-gray-500">Pilih tanggal atau lapangan lain</p>
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
        let selectedCourtId = {{ $selectedCourt->id }};
        let selectedDate = '{{ $selectedDate }}';
        
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure selectedDate is current if not set properly
            if (!selectedDate || selectedDate === '') {
                const today = new Date();
                selectedDate = today.getFullYear() + '-' + 
                              String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                              String(today.getDate()).padStart(2, '0');
            }
            
            generateDateCards();
            setupScrollButtons();
        });
        
        function generateDateCards() {
            const container = document.getElementById('dateCardsContainer');
            const today = new Date();
            
            // Fix timezone issue - get today's date in local timezone
            const todayStr = today.getFullYear() + '-' + 
                           String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                           String(today.getDate()).padStart(2, '0');
            
            const dates = [];
            
            // Generate 30 days starting from today (1 month ahead)
            for (let i = 0; i < 30; i++) {
                const date = new Date(today);
                date.setDate(today.getDate() + i);
                dates.push(date);
            }
            
            container.innerHTML = '';
            
            dates.forEach(date => {
                const dateStr = date.getFullYear() + '-' + 
                              String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                              String(date.getDate()).padStart(2, '0');
                              
                const isSelected = dateStr === selectedDate;
                const isWeekend = date.getDay() === 0 || date.getDay() === 6;
                const isToday = dateStr === todayStr; // Use corrected today comparison
                
                const card = document.createElement('div');
                card.className = `date-card p-4 rounded-xl text-center flex-shrink-0 ${
                    isSelected ? 'active' : 
                    isWeekend ? 'weekend inactive' : 'inactive'
                }`;
                card.setAttribute('data-date', dateStr); // Store date as data attribute
                card.onclick = () => selectDate(dateStr);
                
                const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                
                card.innerHTML = `
                    <div class="text-xs font-medium opacity-75 mb-1">
                        ${dayNames[date.getDay()]}
                    </div>
                    <div class="text-2xl font-bold mb-1">
                        ${date.getDate()}
                    </div>
                    <div class="text-xs opacity-75">
                        ${monthNames[date.getMonth()]}
                    </div>
                    ${isToday ? '<div class="text-xs mt-1 font-semibold">Hari Ini</div>' : ''}
                    ${isWeekend ? '<div class="text-xs mt-1"><i class="fas fa-star"></i> Weekend</div>' : ''}
                `;
                
                container.appendChild(card);
            });
            
            // Scroll to selected date
            scrollToSelectedDate();
        }
        
        function selectDate(dateStr) {
            selectedDate = dateStr;
            updateDateCards();
            updateSchedule();
            updateDateInfo();
        }
        
        function updateDateCards() {
            const cards = document.querySelectorAll('.date-card');
            cards.forEach(card => {
                const cardDate = card.getAttribute('data-date');
                const isWeekend = card.classList.contains('weekend');
                
                // Remove all state classes
                card.classList.remove('active', 'inactive');
                
                // Add appropriate state class
                if (cardDate === selectedDate) {
                    card.classList.add('active');
                } else {
                    card.classList.add('inactive');
                }
                
                // Ensure weekend class is preserved
                if (isWeekend && !card.classList.contains('weekend')) {
                    card.classList.add('weekend');
                }
            });
        }
        
        function setupScrollButtons() {
            const container = document.getElementById('dateCardsContainer');
            const scrollLeft = document.getElementById('scrollLeft');
            const scrollRight = document.getElementById('scrollRight');
            
            scrollLeft.addEventListener('click', () => {
                container.scrollBy({ left: -300, behavior: 'smooth' });
            });
            
            scrollRight.addEventListener('click', () => {
                container.scrollBy({ left: 300, behavior: 'smooth' });
            });
        }
        
        function scrollToSelectedDate() {
            const container = document.getElementById('dateCardsContainer');
            const activeCard = container.querySelector('.date-card.active');
            if (activeCard) {
                const containerRect = container.getBoundingClientRect();
                const cardRect = activeCard.getBoundingClientRect();
                const scrollLeft = activeCard.offsetLeft - (containerRect.width / 2) + (cardRect.width / 2);
                container.scrollTo({ left: scrollLeft, behavior: 'smooth' });
            }
        }
        
        function selectCourt(courtId) {
            selectedCourtId = courtId;
            
            // Update active tab
            document.querySelectorAll('.court-tab').forEach(tab => {
                tab.classList.remove('bg-gradient-to-r', 'from-amber-500', 'to-orange-500', 'text-white', 'shadow-lg', 'scale-105');
                tab.classList.add('bg-white', 'text-amber-800', 'hover:bg-amber-50', 'shadow-md', 'hover:shadow-lg');
            });
            
            const activeTab = document.getElementById(`court-tab-${courtId}`);
            activeTab.classList.remove('bg-white', 'text-amber-800', 'hover:bg-amber-50', 'shadow-md', 'hover:shadow-lg');
            activeTab.classList.add('bg-gradient-to-r', 'from-amber-500', 'to-orange-500', 'text-white', 'shadow-lg', 'scale-105');
            
            updateSchedule();
        }
        
        function updateSchedule() {
            const loadingState = document.getElementById('loading-state');
            const timeSlotsContainer = document.getElementById('time-slots-container');
            const noSlotsMessage = document.getElementById('no-slots-message');
            
            // Show loading
            loadingState.classList.remove('hidden');
            timeSlotsContainer.classList.add('hidden');
            noSlotsMessage.classList.add('hidden');
            
            // Make AJAX request
            fetch(`{{ route('jadwal') }}?ajax=1&court_id=${selectedCourtId}&date=${selectedDate}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                loadingState.classList.add('hidden');
                
                if (data.success && data.timeSlots.length > 0) {
                    renderTimeSlots(data.timeSlots);
                    updateCourtInfo(data.court);
                    timeSlotsContainer.classList.remove('hidden');
                } else {
                    noSlotsMessage.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadingState.classList.add('hidden');
                noSlotsMessage.classList.remove('hidden');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat jadwal. Silakan coba lagi.',
                    confirmButtonColor: '#f59e0b'
                });
            });
        }
        
        function renderTimeSlots(timeSlots) {
            const container = document.getElementById('time-slots-container');
            container.innerHTML = '';
            
            timeSlots.forEach(slot => {
                const slotElement = document.createElement('div');
                slotElement.className = `time-slot ${slot.is_available ? 'available' : 'booked'} p-4 rounded-xl text-center`;
                slotElement.onclick = slot.is_available ? 
                    () => showSlotDetails(slot) : 
                    () => showBookingDetails(slot);
                
                slotElement.innerHTML = `
                    <div class="font-bold text-lg mb-1">${slot.start_time}</div>
                    <div class="text-sm opacity-80 mb-2">${slot.price_label}</div>
                    <div class="text-xs font-semibold">
                        ${slot.is_available ? 
                            'Rp ' + new Intl.NumberFormat('id-ID').format(slot.price) : 
                            (slot.booking_info?.team_name || 'Dibooking')
                        }
                    </div>
                    ${!slot.is_available && slot.booking_info ? 
                        `<div class="text-xs mt-1 opacity-60">
                            <i class="fas fa-user mr-1"></i>${slot.booking_info.user_name || 'N/A'}
                        </div>` : ''
                    }
                `;
                
                container.appendChild(slotElement);
            });
        }
        
        function updateCourtInfo(court) {
            document.getElementById('selected-court-info').innerHTML = `
                <i class="${court.sport.icon} mr-2"></i>
                ${court.sport.name} - ${court.name}
            `;
        }
        
        function updateDateInfo() {
            const date = new Date(selectedDate);
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                locale: 'id-ID'
            };
            
            document.getElementById('selected-date-info').textContent = 
                date.toLocaleDateString('id-ID', options);
        }
        
        function showSlotDetails(slot) {
            Swal.fire({
                title: 'Detail Slot Waktu',
                html: `
                    <div class="text-left">
                        <p><strong>Waktu:</strong> ${slot.start_time} - ${slot.end_time}</p>
                        <p><strong>Kategori:</strong> ${slot.price_label}</p>
                        <p><strong>Harga:</strong> Rp ${new Intl.NumberFormat('id-ID').format(slot.price)}</p>
                        <p><strong>Status:</strong> <span class="text-green-600 font-semibold">Tersedia</span></p>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Booking Sekarang',
                cancelButtonText: 'Tutup',
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to booking page with pre-selected values
                    window.location.href = `{{ route('booking.index') }}?court=${selectedCourtId}&date=${selectedDate}&time=${slot.start_time}`;
                }
            });
        }
        
        function showBookingDetails(slot) {
            if (!slot.booking_info) {
                Swal.fire({
                    icon: 'info',
                    title: 'Slot Tidak Tersedia',
                    text: 'Slot waktu ini sudah dibooking.',
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }
            
            Swal.fire({
                title: 'Detail Booking',
                html: `
                    <div class="text-left">
                        <p><strong>Waktu:</strong> ${slot.start_time} - ${slot.end_time}</p>
                        <p><strong>Tim:</strong> ${slot.booking_info.team_name}</p>
                        <p><strong>Pemesan:</strong> ${slot.booking_info.user_name}</p>
                        ${slot.booking_info.court_name && slot.booking_info.sport_name ? 
                            `<p><strong>Lapangan Terbooking:</strong> ${slot.booking_info.sport_name} - ${slot.booking_info.court_name}</p>` : ''
                        }
                        <p><strong>Status:</strong> <span class="text-blue-600 font-semibold">${slot.booking_info.status}</span></p>
                        ${slot.booking_info.total_amount ? 
                            `<p><strong>Total:</strong> Rp ${new Intl.NumberFormat('id-ID').format(slot.booking_info.total_amount)}</p>` : ''
                        }
                        ${slot.booking_info.notes ? 
                            `<p><strong>Catatan:</strong> ${slot.booking_info.notes}</p>` : ''
                        }
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#f59e0b'
            });
        }
    </script>
</body>
</html>
