<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Jadwal - WIFA Sport Center</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Custom scrollbar for date selection */
        .date-scroll::-webkit-scrollbar {
            height: 6px;
        }
        .date-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .date-scroll::-webkit-scrollbar-thumb {
            background: #f59e0b;
            border-radius: 10px;
        }
        .date-scroll::-webkit-scrollbar-thumb:hover {
            background: #d97706;
        }
        
        /* Scroll hint animation */
        @keyframes scroll-hint {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(10px); }
        }
        .scroll-hint {
            animation: scroll-hint 2s ease-in-out infinite;
        }
        
        /* Disabled time slot styles */
        .time-slot input:disabled + .time-card {
            background-color: #f8f9fa !important;
            border-color: #e9ecef !important;
            cursor: not-allowed !important;
            opacity: 0.6;
        }
        
        .time-slot input:disabled + .time-card:hover {
            background-color: #f8f9fa !important;
            border-color: #e9ecef !important;
        }
        
        /* Available time slot hover effect */
        .time-slot input:not(:disabled) + .time-card:hover {
            background-color: #fef3cd !important;
            border-color: #f59e0b !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        }
    </style>
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
                @if($court->physical_location)
                    @php
                        $sharedCourts = $court->getSharedCourts()->where('id', '!=', $court->id);
                    @endphp
                    @if($sharedCourts->count() > 0)
                        <br><span class="text-sm text-amber-600 font-semibold">
                            📍 Lapangan fisik yang sama dengan: 
                            {{ $sharedCourts->map(function($c) { return $c->sport->name . ' (' . $c->name . ')'; })->join(', ') }}
                        </span>
                    @endif
                @endif
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
                    <span class="text-sm font-normal text-gray-600 ml-2">(Geser untuk melihat tanggal lainnya)</span>
                </h2>
                
                <div class="overflow-x-auto date-scroll">
                    <div class="flex gap-4 pb-4" style="min-width: max-content;">
                        @foreach($dates as $date)
                        <label class="date-option cursor-pointer flex-shrink-0">
                            <input type="radio" name="date" value="{{ $date->format('Y-m-d') }}" class="hidden" required>
                            <div class="date-card bg-gray-50 hover:bg-amber-50 border-2 border-gray-200 hover:border-amber-300 rounded-lg p-4 text-center transition-all duration-300 w-20">
                                <div class="font-semibold text-gray-700 text-xs">{{ $date->locale('id')->isoFormat('ddd') }}</div>
                                <div class="text-2xl font-bold text-amber-600">{{ $date->format('d') }}</div>
                                <div class="text-sm text-gray-600">{{ $date->locale('id')->isoFormat('MMM') }}</div>
                                @if($date->isToday())
                                    <div class="text-xs text-amber-600 font-semibold mt-1">Hari Ini</div>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <!-- Scroll indicator -->
                    <div class="text-center mt-2">
                        <i class="fas fa-chevron-left scroll-hint text-amber-500 text-xs"></i>
                        <span class="text-xs text-gray-500 mx-2">Geser untuk melihat tanggal lainnya</span>
                        <i class="fas fa-chevron-right scroll-hint text-amber-500 text-xs"></i>
                    </div>
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
                        <option value="4">4 Jam</option>
                        <option value="5">5 Jam</option>
                        <option value="6">6 Jam</option>
                        <option value="7">7 Jam</option>
                        <option value="8">8 Jam</option>
                        <option value="9">9 Jam</option>
                        <option value="10">10 Jam</option>
                        <option value="11">11 Jam</option>
                        <option value="12">12 Jam</option>
                        <option value="13">13 Jam</option>
                        <option value="14">14 Jam</option>
                        <option value="15">15 Jam</option>
                        <option value="16">16 Jam</option>
                        <option value="17">17 Jam</option>
                        <option value="18">18 Jam (Full Day)</option>
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
        let availabilityCache = new Map(); // Cache untuk availability results
        let availabilityTimeout = null; // Timeout untuk debounce
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Date selection
        document.querySelectorAll('input[name="date"]').forEach(input => {
            input.addEventListener('change', function() {
                selectedDate = this.value;
                selectedTime = null; // Clear selected time when date changes
                
                console.log('Date selected:', selectedDate);
                
                // Clear any selected time radio buttons
                document.querySelectorAll('input[name="start_time"]').forEach(timeInput => {
                    timeInput.checked = false;
                });
                
                // Clear availability cache for the new date
                clearAvailabilityDisplay();
                updateSelectedDate();
                updateSelectedTime(); // This will show "-" since selectedTime is null
                checkAvailabilityWithDebounce();
                updateContinueButton();
            });
        });

        // Time selection
        document.querySelectorAll('input[name="start_time"]').forEach(input => {
            input.addEventListener('change', function() {
                const proposedStartTime = this.value;
                
                // Check if this selection would create an overlap
                if (selectedDate && selectedDuration) {
                    checkTimeSlotBeforeSelection(proposedStartTime, selectedDuration).then(isValid => {
                        if (isValid) {
                            selectedTime = proposedStartTime;
                            updateSelectedTime();
                            calculatePrice();
                            updateContinueButton();
                        } else {
                            // Revert selection
                            this.checked = false;
                            
                            // Show SweetAlert warning
                            Swal.fire({
                                icon: 'warning',
                                title: 'Waktu Tidak Tersedia',
                                text: `Tidak dapat memilih ${proposedStartTime} dengan durasi ${selectedDuration} jam karena bertabrakan dengan booking yang sudah ada.`,
                                showConfirmButton: true,
                                confirmButtonText: 'Mengerti',
                                confirmButtonColor: '#f59e0b'
                            });
                        }
                    });
                } else {
                    selectedTime = proposedStartTime;
                    updateSelectedTime();
                    calculatePrice();
                    updateContinueButton();
                }
            });
        });

        // Duration selection
        document.getElementById('duration').addEventListener('change', function() {
            const newDuration = parseInt(this.value);
            
            // If there's a selected time, check if new duration creates conflict
            if (selectedTime && selectedDate) {
                checkTimeSlotBeforeSelection(selectedTime, newDuration).then(isValid => {
                    if (isValid) {
                        selectedDuration = newDuration;
                        updateSelectedDuration();
                        updateSelectedTime();
                        calculatePrice();
                        if (selectedDate) {
                            clearAvailabilityDisplay();
                            checkAvailabilityWithDebounce();
                        }
                        updateContinueButton();
                    } else {
                        // Revert duration change
                        this.value = selectedDuration;
                        
                        // Show SweetAlert warning
                        Swal.fire({
                            icon: 'error',
                            title: 'Durasi Tidak Valid',
                            text: `Tidak dapat mengubah durasi menjadi ${newDuration} jam karena akan bertabrakan dengan booking yang sudah ada.`,
                            showConfirmButton: true,
                            confirmButtonText: 'Pilih Durasi Lain',
                            confirmButtonColor: '#f59e0b'
                        });
                    }
                });
            } else {
                selectedDuration = newDuration;
                selectedTime = null; // Clear selected time when duration changes
                
                // Clear any selected time radio buttons
                document.querySelectorAll('input[name="start_time"]').forEach(timeInput => {
                    timeInput.checked = false;
                });
                
                updateSelectedDuration();
                updateSelectedTime(); // This will show "-" since selectedTime is null
                calculatePrice();
                if (selectedDate) {
                    clearAvailabilityDisplay();
                    checkAvailabilityWithDebounce();
                }
                updateContinueButton();
            }
        });

        // Check if a specific time slot would be valid before allowing selection
        async function checkTimeSlotBeforeSelection(startTime, duration) {
            if (!selectedDate) return false;
            
            const startHour = parseInt(startTime.split(':')[0]);
            const endHour = startHour + duration;
            const endTime = Math.min(endHour, 24).toString().padStart(2, '0') + ':00';
            
            // Check if exceeds operating hours
            if (endHour > 24) {
                return false;
            }
            
            try {
                const response = await fetch('{{ route("booking.check-availability") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        court_id: {{ $court->id }},
                        date: selectedDate,
                        start_time: startTime,
                        end_time: endTime
                    })
                });
                
                const data = await response.json();
                return data.available;
            } catch (error) {
                console.error('Error checking availability:', error);
                return false;
            }
        }

        // Debounced availability check to prevent multiple rapid requests
        function checkAvailabilityWithDebounce() {
            if (availabilityTimeout) {
                clearTimeout(availabilityTimeout);
            }
            
            availabilityTimeout = setTimeout(() => {
                checkAvailability();
            }, 300); // 300ms delay
        }

        // Clear availability display and reset all time slots
        function clearAvailabilityDisplay() {
            document.querySelectorAll('.time-card').forEach(card => {
                const input = card.previousElementSibling;
                const status = card.querySelector('.availability-status');
                
                // Reset to loading state
                status.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengecek...';
                status.className = 'availability-status text-xs mt-1 text-gray-500';
                
                // Reset card styles
                card.classList.remove('bg-amber-100', 'border-amber-500', 'shadow-md', 'opacity-50', 'cursor-not-allowed');
                card.classList.add('bg-gray-50', 'border-gray-200');
                
                // Reset input state
                input.disabled = false;
                input.checked = false;
            });
            
            // Clear cache for current date and duration combination
            const cacheKey = `${selectedDate}-${selectedDuration}`;
            availabilityCache.delete(cacheKey);
        }

        // Initial load - set all time slots to "Pilih tanggal terlebih dahulu"
        function initializeTimeSlots() {
            document.querySelectorAll('.time-card').forEach(card => {
                const status = card.querySelector('.availability-status');
                status.innerHTML = '<i class="fas fa-calendar mr-1"></i>Pilih tanggal dulu';
                status.className = 'availability-status text-xs mt-1 text-gray-400';
                
                const input = card.previousElementSibling;
                input.disabled = true;
                card.classList.add('opacity-50');
                card.classList.remove('hover:bg-amber-50', 'hover:border-amber-300');
            });
        }

        function updateSelectedDate() {
            if (selectedDate) {
                const date = new Date(selectedDate);
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                document.getElementById('selectedDate').textContent = date.toLocaleDateString('id-ID', options);
                
                // Enable time slots
                document.querySelectorAll('.time-card').forEach(card => {
                    const input = card.previousElementSibling;
                    input.disabled = false;
                    card.classList.remove('opacity-50');
                    card.classList.add('hover:bg-amber-50', 'hover:border-amber-300');
                });
            } else {
                document.getElementById('selectedDate').textContent = '-';
            }
        }

        function updateSelectedTime() {
            if (selectedTime) {
                const startHour = parseInt(selectedTime.split(':')[0]);
                const endHour = startHour + selectedDuration;
                
                // Check if booking exceeds operating hours (24:00)
                if (endHour > 24) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Melebihi Jam Operasional',
                        text: 'Durasi booking melebihi jam operasional. Maksimal sampai jam 24:00 (12 malam).',
                        confirmButtonColor: '#f59e0b'
                    });
                    // Reset duration to maximum possible
                    const maxDuration = 24 - startHour;
                    document.getElementById('duration').value = maxDuration;
                    selectedDuration = maxDuration;
                }
                
                const finalEndHour = Math.min(startHour + selectedDuration, 24);
                const endTime = finalEndHour.toString().padStart(2, '0') + ':00';
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
                
                // Update duration selector options based on selected start time
                updateDurationOptions();
            } else {
                document.getElementById('selectedTime').textContent = '-';
                
                // Remove end_time input if no time selected
                const endTimeInput = document.querySelector('input[name="end_time"]');
                if (endTimeInput) {
                    endTimeInput.remove();
                }
            }
        }

        function updateDurationOptions() {
            if (selectedTime) {
                const startHour = parseInt(selectedTime.split(':')[0]);
                const maxDuration = 24 - startHour;
                const durationSelect = document.getElementById('duration');
                
                // Update duration options based on available hours
                for (let i = 1; i <= 18; i++) {
                    const option = durationSelect.querySelector(`option[value="${i}"]`);
                    if (option) {
                        if (i <= maxDuration) {
                            option.disabled = false;
                            option.style.display = 'block';
                        } else {
                            option.disabled = true;
                            option.style.display = 'none';
                        }
                    }
                }
                
                // If current selection exceeds max, adjust it
                if (selectedDuration > maxDuration) {
                    selectedDuration = maxDuration;
                    durationSelect.value = maxDuration;
                }
            }
        }

        function updateSelectedDuration() {
            // Update display if there's a selectedDuration display element
            const durationDisplay = document.getElementById('selectedDuration');
            if (durationDisplay) {
                durationDisplay.textContent = selectedDuration + ' jam';
            }
            updateSelectedTime(); // Update time display with new duration
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

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSelectedDuration();
            
            // Auto-select today's date if available
            autoSelectTodayDate();
            
            // Only initialize time slots as disabled if no date is selected
            if (!selectedDate) {
                initializeTimeSlots();
            }
            
            calculatePrice(); // This will show "Pilih waktu untuk melihat harga"
            updateContinueButton();
            
            console.log('Schedule page initialized');
        });

        // Form submission validation
        document.getElementById('scheduleForm').addEventListener('submit', function(e) {
            // Last-minute validation before form submission
            if (!selectedDate || !selectedTime) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Tidak Lengkap',
                    text: 'Silakan pilih tanggal dan waktu terlebih dahulu.',
                    confirmButtonColor: '#f59e0b'
                });
                return false;
            }
            
            // Check if selected time slot is still available
            const selectedTimeInput = document.querySelector(`input[name="start_time"][value="${selectedTime}"]`);
            if (!selectedTimeInput || selectedTimeInput.disabled) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Slot Tidak Tersedia',
                    text: 'Slot waktu yang dipilih sudah tidak tersedia. Silakan pilih waktu lain.',
                    confirmButtonColor: '#f59e0b'
                }).then(() => {
                    selectedTime = null;
                    updateSelectedTime();
                    updateContinueButton();
                });
                return false;
            }
            
            console.log('Form validation passed, submitting...');
            return true;
        });

        // Auto-select today's date
        function autoSelectTodayDate() {
            const today = new Date();
            const todayString = today.getFullYear() + '-' + 
                              String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                              String(today.getDate()).padStart(2, '0');
            
            const todayInput = document.querySelector(`input[name="date"][value="${todayString}"]`);
            if (todayInput) {
                console.log('Auto-selecting today:', todayString);
                todayInput.checked = true;
                selectedDate = todayString;
                
                // Apply active styling to today's date card
                const todayCard = todayInput.nextElementSibling;
                todayCard.classList.remove('bg-gray-50', 'border-gray-200');
                todayCard.classList.add('bg-amber-100', 'border-amber-500', 'shadow-md');
                
                // Update the selected date display
                updateSelectedDate();
                
                // Check availability for today
                checkAvailabilityWithDebounce();
                
                console.log('Today auto-selected and availability check initiated');
            } else {
                console.log('Today not found in available dates');
            }
        }

        // Add debugging
        window.debugAvailability = function() {
            console.log('Debug Info:', {
                selectedDate,
                selectedTime,
                selectedDuration,
                cacheSize: availabilityCache.size,
                cacheKeys: Array.from(availabilityCache.keys())
            });
        };

        function updateSelectedDuration() {
            document.getElementById('selectedDuration').textContent = selectedDuration + ' jam';
            updateSelectedTime(); // Update time display with new duration
        }

        function calculatePrice() {
            if (!selectedTime) {
                // Clear price display if no time selected
                document.getElementById('totalPrice').textContent = 'Rp 0';
                document.getElementById('priceBreakdown').innerHTML = '<div class="text-gray-500 text-sm">Pilih waktu untuk melihat harga</div>';
                return;
            }
            
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
                document.getElementById('totalPrice').textContent = 'Error';
                document.getElementById('priceBreakdown').innerHTML = '<div class="text-red-500 text-sm">Error menghitung harga</div>';
            });
        }

        function updateContinueButton() {
            const continueBtn = document.getElementById('continueBtn');
            
            // Basic requirements: date and time must be selected
            if (!selectedDate || !selectedTime) {
                continueBtn.disabled = true;
                return;
            }
            
            // Check if selected time slot is actually available (not disabled)
            const selectedTimeInput = document.querySelector(`input[name="start_time"][value="${selectedTime}"]`);
            if (!selectedTimeInput || selectedTimeInput.disabled) {
                console.log('Continue button disabled: selected time slot is not available');
                continueBtn.disabled = true;
                return;
            }
            
            // Enable button only if everything is valid
            console.log('Continue button enabled: all validations passed');
            continueBtn.disabled = false;
        }

        function checkAvailability() {
            if (!selectedDate) return;
            
            console.log(`Checking availability for ${selectedDate} with duration ${selectedDuration}`);
            
            // Create a cache key for this specific date-duration combination
            const cacheKey = `${selectedDate}-${selectedDuration}`;
            
            // Check if we have cached results
            if (availabilityCache.has(cacheKey)) {
                console.log('Using cached results');
                const cachedResults = availabilityCache.get(cacheKey);
                applyCachedResults(cachedResults);
                return;
            }
            
            // Show loading state for all slots
            document.querySelectorAll('.time-card').forEach(card => {
                const status = card.querySelector('.availability-status');
                status.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengecek...';
                status.className = 'availability-status text-xs mt-1 text-gray-500';
            });

            const availabilityResults = new Map();
            let pendingRequests = 0;
            
            // Check availability for each time slot based on current duration
            document.querySelectorAll('input[name="start_time"]').forEach(input => {
                const startTime = input.value;
                const startHour = parseInt(startTime.split(':')[0]);
                const endHour = startHour + selectedDuration;
                const endTime = Math.min(endHour, 24).toString().padStart(2, '0') + ':00';
                
                // Skip slots that would exceed operating hours
                if (endHour > 24) {
                    const result = {
                        available: false,
                        reason: 'exceeds_hours',
                        message: 'Melebihi jam operasional'
                    };
                    availabilityResults.set(startTime, result);
                    updateTimeSlotDisplay(input, result);
                    return;
                }

                pendingRequests++;
                
                fetch('{{ route("booking.check-availability") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        court_id: {{ $court->id }},
                        date: selectedDate,
                        start_time: startTime,
                        end_time: endTime
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const result = {
                        available: data.available,
                        reason: data.available ? 'available' : 'booked',
                        message: data.available ? 'Tersedia' : 'Sudah Dibooking'
                    };
                    
                    availabilityResults.set(startTime, result);
                    updateTimeSlotDisplay(input, result);
                    
                    pendingRequests--;
                    
                    // If all requests are done, cache the results
                    if (pendingRequests === 0) {
                        availabilityCache.set(cacheKey, availabilityResults);
                        console.log(`Cached results for ${cacheKey}`);
                    }
                })
                .catch(error => {
                    console.error('Error checking availability:', error);
                    const result = {
                        available: false,
                        reason: 'error',
                        message: 'Error'
                    };
                    availabilityResults.set(startTime, result);
                    updateTimeSlotDisplay(input, result);
                    
                    pendingRequests--;
                });
            });
            
            // If selectedTime is set, recalculate price
            if (selectedTime) {
                calculatePrice();
            }
        }

        function applyCachedResults(cachedResults) {
            document.querySelectorAll('input[name="start_time"]').forEach(input => {
                const startTime = input.value;
                if (cachedResults.has(startTime)) {
                    const result = cachedResults.get(startTime);
                    updateTimeSlotDisplay(input, result);
                }
            });
        }

        function updateTimeSlotDisplay(input, result) {
            const card = input.nextElementSibling;
            const status = card.querySelector('.availability-status');
            
            // Clear previous states
            card.classList.remove('opacity-50', 'cursor-not-allowed', 'hover:bg-amber-50', 'hover:border-amber-300');
            
            if (result.available) {
                status.innerHTML = '<i class="fas fa-check-circle mr-1"></i>' + result.message;
                status.className = 'availability-status text-xs mt-1 text-green-600';
                input.disabled = false;
                card.classList.add('hover:bg-amber-50', 'hover:border-amber-300');
                
                // Update continue button in case this affects the selected time
                updateContinueButton();
            } else {
                let iconClass = 'fas fa-times-circle';
                let statusClass = 'availability-status text-xs mt-1 text-red-500';
                
                if (result.reason === 'exceeds_hours') {
                    iconClass = 'fas fa-clock';
                    statusClass = 'availability-status text-xs mt-1 text-orange-500';
                } else if (result.reason === 'error') {
                    iconClass = 'fas fa-exclamation-triangle';
                    statusClass = 'availability-status text-xs mt-1 text-yellow-600';
                }
                
                status.innerHTML = `<i class="${iconClass} mr-1"></i>${result.message}`;
                status.className = statusClass;
                input.disabled = true;
                card.classList.add('opacity-50', 'cursor-not-allowed');
                
                // If this was the selected time and now it's not available, clear selection
                if (input.checked) {
                    input.checked = false;
                    selectedTime = null;
                    updateSelectedTime();
                    updateContinueButton();
                }
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
        initializeTimeSlots();
    </script>
</body>
</html>
