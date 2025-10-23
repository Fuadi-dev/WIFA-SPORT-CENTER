<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemesanan - WIFA Sport Center</title>
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
                    <a href="{{ route('home') }}" class="hover:text-amber-600">Beranda</a>
                    <i class="fas fa-chevron-right mx-2"></i>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('booking.index') }}" class="hover:text-amber-600">Pilih Olahraga</a>
                    <i class="fas fa-chevron-right mx-2"></i>
                </li>
                <li class="flex items-center">
                    <a href="#" class="hover:text-amber-600">Pilih Jadwal</a>
                    <i class="fas fa-chevron-right mx-2"></i>
                </li>
                <li class="text-amber-600 font-semibold">Form Pemesanan</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="text-center mb-12">
            <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                <i class="fas fa-edit text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-amber-800 mb-4">
                Form Booking
            </h1>
            <p class="text-xl text-gray-700 max-w-2xl mx-auto">
                Isi detail booking Anda untuk menyelesaikan reservasi
            </p>
        </div>

        <form action="{{ route('booking.store') }}" method="POST" class="max-w-4xl mx-auto">
            @csrf
            
            <!-- Hidden fields for booking details -->
            <input type="hidden" name="sport_id" value="{{ request('sport_id') }}">
            <input type="hidden" name="court_id" value="{{ request('court_id') }}">
            <input type="hidden" name="date" value="{{ request('date') }}">
            <input type="hidden" name="start_time" value="{{ request('start_time') }}">
            <input type="hidden" name="end_time" value="{{ request('end_time') }}">

            <!-- Booking Summary -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border-2 border-amber-100">
                <h2 class="text-2xl font-bold text-amber-800 mb-6">
                    <i class="fas fa-info-circle mr-2"></i>Ringkasan Booking
                </h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Olahraga:</span>
                            <span class="font-semibold text-amber-700">{{ $sport->name }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Lapangan:</span>
                            <span class="font-semibold text-amber-700">{{ $court->name }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Tanggal:</span>
                            <span class="font-semibold text-amber-700">{{ \Carbon\Carbon::parse(request('date'))->format('d M Y') }}</span>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Waktu:</span>
                            <span class="font-semibold text-amber-700">{{ request('start_time') }} - {{ request('end_time') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Durasi:</span>
                            <span class="font-semibold text-amber-700">{{ $duration }} jam</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-amber-100 rounded-lg">
                            <span class="text-amber-700 font-semibold">Total Harga:</span>
                            <span class="font-bold text-xl text-amber-800" id="originalPrice">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Promo Section -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border-2 border-amber-100">
                <h2 class="text-2xl font-bold text-amber-800 mb-6">
                    <i class="fas fa-tags mr-2"></i>Kode Promo
                </h2>
                
                <!-- Auto Promo Alert (if applicable) -->
                @if(isset($autoPromo))
                <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-gift text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-semibold text-green-800 mb-1">
                                <i class="fas fa-check-circle mr-1"></i>Promo Otomatis Terdeteksi!
                            </h3>
                            <p class="text-green-700 mb-2">
                                <strong>{{ $autoPromo->name }}</strong>
                            </p>
                            @if($autoPromo->description)
                            <p class="text-sm text-green-600 mb-2">{{ $autoPromo->description }}</p>
                            @endif
                            <div class="flex items-center space-x-4 text-sm">
                                @if($autoPromo->discount_type === 'percentage')
                                    <span class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-800 rounded-lg font-semibold">
                                        <i class="fas fa-percentage mr-1"></i>
                                        Diskon {{ rtrim(rtrim(number_format($autoPromo->discount_value, 2, ',', '.'), '0'), ',') }}%
                                    </span>
                                    @if($autoPromo->max_discount)
                                    <span class="text-green-700 text-xs">
                                        (Max: Rp {{ number_format($autoPromo->max_discount, 0, ',', '.') }})
                                    </span>
                                    @endif
                                    <span class="text-green-700 font-semibold">
                                        Hemat: Rp {{ number_format($totalPrice * ($autoPromo->discount_value / 100) > ($autoPromo->max_discount ?? PHP_INT_MAX) ? $autoPromo->max_discount : $totalPrice * ($autoPromo->discount_value / 100), 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-800 rounded-lg font-semibold">
                                        <i class="fas fa-tag mr-1"></i>
                                        Potongan Rp {{ number_format($autoPromo->discount_value, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                            <input type="hidden" name="auto_promo_detected" value="1">
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Promo Code Input -->
                <div>
                    <label for="promo_code" class="block text-sm font-semibold text-gray-700 mb-2">
                        Punya Kode Promo? <span class="text-gray-500 font-normal">(Opsional)</span>
                    </label>
                    <div class="flex gap-3">
                        <input type="text" 
                               id="promo_code" 
                               name="promo_code" 
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent uppercase" 
                               placeholder="Masukkan kode promo (contoh: WIFA2024)"
                               value="{{ old('promo_code') }}">
                        <button type="button" 
                                onclick="validatePromoCode()" 
                                class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg transition-all duration-300">
                            <i class="fas fa-check mr-1"></i>Validasi
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>Masukkan kode promo untuk mendapatkan diskon tambahan
                    </p>
                </div>
                
                <!-- Promo Code Result -->
                <div id="promoResult" class="mt-4 hidden"></div>
                
                <!-- Discount Summary -->
                <div id="discountSummary" class="mt-6 hidden">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Harga Asli:</span>
                                <span class="text-gray-700 line-through" id="priceBeforeDiscount">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-green-700 font-semibold">
                                    <i class="fas fa-tag mr-1"></i>Diskon:
                                </span>
                                <span class="text-green-700 font-semibold" id="discountAmount">- Rp 0</span>
                            </div>
                            <div class="border-t-2 border-green-300 pt-3 flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-800">Total Bayar:</span>
                                <span class="text-2xl font-bold text-green-600" id="finalPrice">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="validated_promo_code" id="validatedPromoCode" value="">
                <input type="hidden" name="discount_amount" id="discountAmountInput" value="0">
            </div>

            <!-- Team Details -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border-2 border-amber-100">
                <h2 class="text-2xl font-bold text-amber-800 mb-6">
                    <i class="fas fa-users mr-2"></i>Detail Tim/Penyewa
                </h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="team_name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Tim/Instansi/Individu <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="team_name" 
                               name="team_name" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent" 
                               placeholder="Masukkan nama tim, instansi, atau nama individu"
                               value="{{ old('team_name') }}"
                               required>
                        @error('team_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="contact_person" class="block text-sm font-semibold text-gray-700 mb-2">
                            Penanggung Jawab
                        </label>
                        <input type="text" 
                               id="contact_person" 
                               name="contact_person" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent" 
                               placeholder="Nama penanggung jawab"
                               value="{{ old('contact_person', auth()->user()->name) }}">
                    </div>
                </div>

                <div class="mt-6">
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                        Catatan Tambahan
                    </label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent" 
                              placeholder="Catatan khusus, permintaan, atau informasi tambahan...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border-2 border-amber-100">
                <h2 class="text-2xl font-bold text-amber-800 mb-6">
                    <i class="fas fa-credit-card mr-2"></i>Metode Pembayaran
                </h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <label class="payment-option cursor-pointer">
                        <input type="radio" name="payment_method" value="cash" class="hidden" required>
                        <div class="payment-card border-2 border-gray-200 rounded-lg p-6 text-center transition-all duration-300 hover:border-amber-300 hover:bg-amber-50">
                            <div class="text-4xl text-green-600 mb-3">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Bayar Tunai</h3>
                            <p class="text-gray-600 text-sm mb-3">Bayar langsung di tempat saat hari H</p>
                            <div class="mt-3 text-green-600 font-semibold text-sm">
                                <i class="fas fa-check-circle mr-1"></i>Langsung Dikonfirmasi
                            </div>
                            <div class="text-xs text-green-600 mt-1">
                                <i class="fab fa-whatsapp mr-1"></i>Konfirmasi otomatis via WhatsApp
                            </div>
                            <div class="text-xs text-green-600 mt-1">
                                Status: Menunggu Konfirmasi
                            </div>
                        </div>
                    </label>
                    
                    <label class="payment-option cursor-pointer">
                        <input type="radio" name="payment_method" value="midtrans" class="hidden" required>
                        <div class="payment-card border-2 border-gray-200 rounded-lg p-6 text-center transition-all duration-300 hover:border-amber-300 hover:bg-amber-50">
                            <div class="text-4xl text-blue-600 mb-3">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Payment Gateway</h3>
                            <p class="text-gray-600 text-sm mb-3">Kartu Kredit, Debit, E-Wallet, Bank Transfer</p>
                            <div class="mt-3 text-blue-600 font-semibold text-sm">
                                <i class="fas fa-shield-alt mr-1"></i>Powered by Midtrans
                            </div>
                            <div class="text-xs text-yellow-600 mt-1">
                                Status: Pending Payment
                            </div>
                        </div>
                    </label>
                </div>
                
                @error('payment_method')
                    <p class="text-red-500 text-sm mt-4">{{ $message }}</p>
                @enderror
            </div>

            <!-- Terms & Conditions -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border-2 border-amber-100">
                <h2 class="text-2xl font-bold text-amber-800 mb-6">
                    <i class="fas fa-file-contract mr-2"></i>Syarat & Ketentuan
                </h2>
                
                <div class="bg-amber-50 rounded-lg p-6 mb-6">
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Booking berlaku setelah dikonfirmasi oleh admin</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Pembatalan maksimal 24 jam sebelum jadwal booking</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Keterlambatan lebih dari 30 menit dianggap hangus</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Pembayaran transfer harus dilakukan maksimal 2 jam setelah booking</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-600 mr-3 mt-1"></i>
                            <span>Wajib menjaga kebersihan dan ketertiban fasilitas</span>
                        </li>
                    </ul>
                </div>
                
                <label class="flex items-start cursor-pointer">
                    <input type="checkbox" name="agree_terms" class="mt-1 mr-3" required>
                    <span class="text-gray-700">
                        Saya telah membaca dan menyetujui 
                        <a href="#" class="text-amber-600 hover:text-amber-700 font-semibold">syarat dan ketentuan</a> 
                        yang berlaku di WIFA Sport Center
                    </span>
                </label>
                
                @error('agree_terms')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('booking.schedule', ['sport' => request('sport_id'), 'court' => request('court_id')]) }}" 
                   class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-300 text-center">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Jadwal
                </a>
                
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-300">
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Booking
                </button>
            </div>
        </form>
    </div>

    <script>
        const originalPrice = {{ $totalPrice }};
        let appliedDiscount = 0;
        
        // Validate Promo Code
        function validatePromoCode() {
            const promoCode = document.getElementById('promo_code').value.trim().toUpperCase();
            const promoResult = document.getElementById('promoResult');
            
            if (!promoCode) {
                showPromoError('Masukkan kode promo terlebih dahulu');
                return;
            }
            
            // Show loading
            promoResult.innerHTML = `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-spinner fa-spin text-blue-600 mr-2"></i>
                        <span class="text-blue-700">Memvalidasi kode promo...</span>
                    </div>
                </div>
            `;
            promoResult.classList.remove('hidden');
            
            // AJAX request to validate promo code
            fetch('/api/validate-promo-code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    code: promoCode,
                    booking_date: '{{ request("date") }}',
                    start_time: '{{ request("start_time") }}',
                    total_amount: originalPrice
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showPromoSuccess(data.promo, data.discount);
                    applyDiscount(data.discount);
                    document.getElementById('validatedPromoCode').value = promoCode;
                } else {
                    showPromoError(data.message);
                    document.getElementById('validatedPromoCode').value = '';
                }
            })
            .catch(error => {
                showPromoError('Terjadi kesalahan saat validasi');
                console.error('Error:', error);
            });
        }
        
        function showPromoSuccess(promo, discount) {
            const promoResult = document.getElementById('promoResult');
            
            // Format discount display
            let discountText = '';
            if (promo.type === 'percentage') {
                // Remove unnecessary decimal zeros (10.00 -> 10)
                const percentage = parseFloat(promo.value).toString();
                discountText = `Diskon ${percentage}%`;
            } else {
                discountText = 'Potongan Rp ' + new Intl.NumberFormat('id-ID').format(promo.value);
            }
            
            promoResult.innerHTML = `
                <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-2xl text-green-600 mr-3"></i>
                        <div class="flex-1">
                            <h4 class="font-semibold text-green-800 mb-1">
                                <i class="fas fa-gift mr-1"></i>Kode Promo Valid!
                            </h4>
                            <p class="text-sm text-green-700 font-semibold mb-1">
                                ${discountText}
                            </p>
                            <p class="text-sm text-green-600">
                                💰 Anda hemat: Rp ${new Intl.NumberFormat('id-ID').format(discount)}
                            </p>
                        </div>
                        <button onclick="removePromo()" class="text-red-500 hover:text-red-700 text-xl">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                </div>
            `;
            promoResult.classList.remove('hidden');
        }
        
        function showPromoError(message) {
            const promoResult = document.getElementById('promoResult');
            promoResult.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                        <span class="text-red-700">${message}</span>
                    </div>
                </div>
            `;
            promoResult.classList.remove('hidden');
            
            // Hide after 5 seconds
            setTimeout(() => {
                promoResult.classList.add('hidden');
            }, 5000);
        }
        
        function applyDiscount(discount) {
            appliedDiscount = discount;
            const finalPrice = originalPrice - discount;
            
            document.getElementById('discountAmountInput').value = discount;
            document.getElementById('priceBeforeDiscount').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(originalPrice);
            document.getElementById('discountAmount').textContent = '- Rp ' + new Intl.NumberFormat('id-ID').format(discount);
            document.getElementById('finalPrice').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(finalPrice);
            document.getElementById('discountSummary').classList.remove('hidden');
        }
        
        function removePromo() {
            document.getElementById('promo_code').value = '';
            document.getElementById('validatedPromoCode').value = '';
            document.getElementById('discountAmountInput').value = '0';
            document.getElementById('promoResult').classList.add('hidden');
            document.getElementById('discountSummary').classList.add('hidden');
            appliedDiscount = 0;
        }
        
        // Payment method selection
        document.addEventListener('change', function(e) {
            if (e.target.name === 'payment_method') {
                document.querySelectorAll('.payment-card').forEach(card => {
                    card.classList.remove('border-amber-500', 'bg-amber-100', 'shadow-md');
                    card.classList.add('border-gray-200', 'bg-white');
                });
                
                e.target.nextElementSibling.classList.remove('border-gray-200', 'bg-white');
                e.target.nextElementSibling.classList.add('border-amber-500', 'bg-amber-100', 'shadow-md');
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const teamName = document.getElementById('team_name').value.trim();
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            const agreeTerms = document.querySelector('input[name="agree_terms"]').checked;

            if (!teamName) {
                alert('Nama tim/instansi/individu harus diisi');
                e.preventDefault();
                return;
            }

            if (!paymentMethod) {
                alert('Pilih metode pembayaran');
                e.preventDefault();
                return;
            }

            if (!agreeTerms) {
                alert('Anda harus menyetujui syarat dan ketentuan');
                e.preventDefault();
                return;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
