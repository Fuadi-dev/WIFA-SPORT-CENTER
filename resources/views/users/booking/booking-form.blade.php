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
                            <span class="font-bold text-xl text-amber-800">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
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
                                Status: Confirmed
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
