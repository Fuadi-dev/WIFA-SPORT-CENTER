<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Jadwal - WIFA Sport Center</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <li class="text-amber-600 font-semibold">Pilih Jadwal</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="text-center mb-12">
            <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                <i class="{{ $sport->icon }} text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-amber-800 mb-4">
                Pilih Jadwal Booking
            </h1>
            <p class="text-xl text-gray-700 max-w-2xl mx-auto">
                {{ $sport->name }} - {{ $court->name }}
            </p>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 max-w-2xl mx-auto">
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg text-center">
                    <i class="fas fa-sun mr-2"></i>
                    <div class="font-semibold">06:00 - 12:00</div>
                    <div class="text-sm">Rp 60.000/jam</div>
                </div>
                <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg text-center">
                    <i class="fas fa-cloud-sun mr-2"></i>
                    <div class="font-semibold">12:00 - 18:00</div>
                    <div class="text-sm">Rp 80.000/jam</div>
                </div>
                <div class="bg-purple-100 text-purple-800 px-4 py-2 rounded-lg text-center">
                    <i class="fas fa-moon mr-2"></i>
                    <div class="font-semibold">18:00 - 24:00</div>
                    <div class="text-sm">Rp 100.000/jam</div>
                </div>
            </div>
        </div>

        <!-- Booking Form -->
        <form id="scheduleForm" action="{{ route('booking.form') }}" method="GET" class="max-w-4xl mx-auto">
            <input type="hidden" name="sport_id" value="{{ $sport->id }}">
            <input type="hidden" name="court_id" value="{{ $court->id }}">
            
            <!-- Date Selection -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border-2 border-amber-100">
                <h2 class="text-2xl font-bold text-amber-800 mb-6">
                    <i class="fas fa-calendar-alt mr-2"></i>Pilih Tanggal
                </h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                    @foreach($dates as $date)
                    <label class="date-option cursor-pointer">
                        <input type="radio" name="date" value="{{ $date->format('Y-m-d') }}" class="hidden" required>
                        <div class="date-card bg-gray-50 hover:bg-amber-50 border-2 border-gray-200 hover:border-amber-300 rounded-lg p-4 text-center transition-all duration-300">
                            <div class="font-semibold text-gray-700">{{ $date->format('D') }}</div>
                            <div class="text-2xl font-bold text-amber-600">{{ $date->format('d') }}</div>
                            <div class="text-sm text-gray-600">{{ $date->format('M') }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Time Selection -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border-2 border-amber-100">
                <h2 class="text-2xl font-bold text-amber-800 mb-6">
                    <i class="fas fa-clock mr-2"></i>Pilih Waktu
                </h2>
                
                <!-- Time Duration Selector -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Durasi Booking</label>
                    <select id="duration" class="w-full max-w-xs px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="1">1 Jam</option>
                        <option value="2">2 Jam</option>
                        <option value="3">3 Jam</option>
                    </select>
                </div>
                
                <!-- Time Slots Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3" id="timeSlots">
                    @foreach($timeSlots as $slot)
                    <label class="time-slot cursor-pointer">
                        <input type="radio" name="start_time" value="{{ $slot['start'] }}" data-price="{{ $slot['price'] }}" data-category="{{ $slot['price_category'] }}" class="hidden" required>
                        <div class="time-card bg-gray-50 hover:bg-amber-50 border-2 border-gray-200 hover:border-amber-300 rounded-lg p-3 text-center transition-all duration-300">
                            <div class="font-semibold text-gray-700">{{ $slot['display'] }}</div>
                            <div class="text-xs mt-1 
                                @if($slot['price_category'] == 'morning') text-green-600
                                @elseif($slot['price_category'] == 'afternoon') text-yellow-600
                                @else text-purple-600 @endif">
                                Rp {{ number_format($slot['price'], 0, ',', '.') }}
                            </div>
                            <div class="availability-status text-xs mt-1 text-gray-500">
                                <i class="fas fa-check-circle mr-1"></i>Tersedia
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Summary & Continue -->
            <div class="bg-white rounded-xl shadow-lg p-8 border-2 border-amber-100">
                <h2 class="text-2xl font-bold text-amber-800 mb-6">
                    <i class="fas fa-receipt mr-2"></i>Ringkasan Booking
                </h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Olahraga:</span>
                                <span class="font-semibold">{{ $sport->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Lapangan:</span>
                                <span class="font-semibold">{{ $court->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal:</span>
                                <span class="font-semibold" id="selectedDate">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Waktu:</span>
                                <span class="font-semibold" id="selectedTime">-</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="bg-amber-50 rounded-lg p-4">
                            <div class="space-y-2">
                                <div id="priceBreakdown" class="text-sm text-gray-600 mb-3"></div>
                                <hr class="border-amber-200">
                                <div class="flex justify-between font-bold text-lg">
                                    <span>Total:</span>
                                    <span class="text-amber-600" id="totalPrice">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 mt-8">
                    <a href="{{ route('booking.index') }}" 
                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 text-center">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                            id="continueBtn" disabled>
                        <i class="fas fa-arrow-right mr-2"></i>Lanjutkan ke Form Booking
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        let selectedDate = null;
        let selectedTime = null;
        let selectedDuration = 1;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Date selection
        document.querySelectorAll('input[name="date"]').forEach(input => {
            input.addEventListener('change', function() {
                selectedDate = this.value;
                updateSelectedDate();
                checkAvailability();
                updateContinueButton();
            });
        });

        // Time selection
        document.querySelectorAll('input[name="start_time"]').forEach(input => {
            input.addEventListener('change', function() {
                selectedTime = this.value;
                updateSelectedTime();
                calculatePrice();
                updateContinueButton();
            });
        });

        // Duration selection
        document.getElementById('duration').addEventListener('change', function() {
            selectedDuration = parseInt(this.value);
            updateSelectedDuration();
            calculatePrice();
            checkAvailability();
        });

        function updateSelectedDate() {
            if (selectedDate) {
                const date = new Date(selectedDate);
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                document.getElementById('selectedDate').textContent = date.toLocaleDateString('id-ID', options);
            }
        }

        function updateSelectedTime() {
            if (selectedTime) {
                const startHour = parseInt(selectedTime.split(':')[0]);
                const endHour = startHour + selectedDuration;
                const endTime = endHour.toString().padStart(2, '0') + ':00';
                document.getElementById('selectedTime').textContent = selectedTime + ' - ' + endTime;
                
                // Add hidden input for end_time
                let endTimeInput = document.querySelector('input[name="end_time"]');
                if (!endTimeInput) {
                    endTimeInput = document.createElement('input');
                    endTimeInput.type = 'hidden';
                    endTimeInput.name = 'end_time';
                    document.getElementById('scheduleForm').appendChild(endTimeInput);
                }
                endTimeInput.value = endTime;
            }
        }

        function updateSelectedDuration() {
            document.getElementById('selectedDuration').textContent = selectedDuration + ' jam';
            updateSelectedTime(); // Update time display with new duration
        }

        function calculatePrice() {
            if (selectedTime) {
                fetch('{{ route("booking.get-price") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        start_time: selectedTime,
                        duration: selectedDuration
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Update total price
                    document.getElementById('totalPrice').textContent = 'Rp ' + data.total_price.toLocaleString('id-ID');
                    
                    // Update price breakdown
                    let breakdownHtml = '<div class="font-semibold mb-2">Rincian Harga:</div>';
                    data.price_breakdown.forEach(item => {
                        let colorClass = '';
                        if (item.category === 'morning') colorClass = 'text-green-600';
                        else if (item.category === 'afternoon') colorClass = 'text-yellow-600';
                        else colorClass = 'text-purple-600';
                        
                        breakdownHtml += `<div class="flex justify-between ${colorClass}">
                            <span>${item.hour}</span>
                            <span>Rp ${item.price.toLocaleString('id-ID')}</span>
                        </div>`;
                    });
                    
                    document.getElementById('priceBreakdown').innerHTML = breakdownHtml;
                    
                    // Update end time input
                    let endTimeInput = document.querySelector('input[name="end_time"]');
                    if (endTimeInput) {
                        endTimeInput.value = data.end_time;
                    }
                })
                .catch(error => {
                    console.error('Error calculating price:', error);
                });
            }
        }

        function updateContinueButton() {
            const continueBtn = document.getElementById('continueBtn');
            continueBtn.disabled = !(selectedDate && selectedTime);
        }

        function checkAvailability() {
            if (selectedDate && selectedTime) {
                calculatePrice();
            }
        }

        // Style active selections
        document.addEventListener('change', function(e) {
            if (e.target.name === 'date') {
                document.querySelectorAll('.date-card').forEach(card => {
                    card.classList.remove('bg-amber-100', 'border-amber-500', 'shadow-md');
                    card.classList.add('bg-gray-50', 'border-gray-200');
                });
                e.target.nextElementSibling.classList.remove('bg-gray-50', 'border-gray-200');
                e.target.nextElementSibling.classList.add('bg-amber-100', 'border-amber-500', 'shadow-md');
            }
            
            if (e.target.name === 'start_time') {
                document.querySelectorAll('.time-card').forEach(card => {
                    card.classList.remove('bg-amber-100', 'border-amber-500', 'shadow-md');
                    card.classList.add('bg-gray-50', 'border-gray-200');
                });
                e.target.nextElementSibling.classList.remove('bg-gray-50', 'border-gray-200');
                e.target.nextElementSibling.classList.add('bg-amber-100', 'border-amber-500', 'shadow-md');
            }
        });

        // Initialize
        updateSelectedDuration();
    </script>
</body>
</html>
