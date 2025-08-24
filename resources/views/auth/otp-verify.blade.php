<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verifikasi OTP - WIFA Sport Center</title>
    @vite('resources/css/app.css')
    <style>
        html {
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            border: 2px solid rgba(245, 158, 11, 0.3);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: white;
            transition: all 0.3s ease;
        }
        .otp-input:focus {
            outline: none;
            border-color: rgba(245, 158, 11, 0.8);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
            transform: scale(1.05);
        }
        .countdown {
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-amber-900 via-amber-800 to-orange-900 relative">
    
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="2"/></g></svg>'); background-size: 60px 60px;"></div>
    </div>

    <!-- Floating Elements -->
    <div class="absolute top-10 left-10 w-32 h-32 bg-amber-400/10 rounded-full blur-xl animate-pulse"></div>
    <div class="absolute bottom-20 right-20 w-24 h-24 bg-orange-500/15 rounded-full blur-lg animate-pulse delay-1000"></div>
    <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-amber-300/10 rounded-full blur-md animate-pulse delay-500"></div>

    <!-- Main Container -->
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 relative z-10 py-12">
        <div class="max-w-md w-full space-y-6">
            
            <!-- Header Section -->
            <div class="text-center">
                <!-- Logo -->
                <div class="flex justify-center mb-6">
                    <div class="relative">
                        <img src="{{ asset('asset/wifa.jpeg') }}" alt="WIFA SPORT CENTER Logo" 
                             class="h-20 w-20 md:h-24 md:w-24 rounded-full object-cover border-4 border-amber-400/50 shadow-2xl">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-amber-400/20 to-orange-500/20"></div>
                    </div>
                </div>
                
                <!-- Title -->
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-2">
                    Verifikasi OTP
                </h2>
                <p class="text-amber-200 text-lg mb-2">
                    Masukkan kode 6 digit yang dikirim ke WhatsApp
                </p>
                <p class="text-amber-300 font-semibold">
                    {{ $otpVerification->whatsapp }}
                </p>
            </div>

            <!-- OTP Form -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-8 border border-amber-400/20">
                
                <!-- Success/Error Messages -->
                <div id="message-container" class="mb-6 hidden">
                    <div id="success-message" class="bg-green-500/20 border border-green-400/30 text-green-200 px-4 py-3 rounded-lg mb-4 hidden">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span id="success-text"></span>
                        </div>
                    </div>
                    <div id="error-message" class="bg-red-500/20 border border-red-400/30 text-red-200 px-4 py-3 rounded-lg mb-4 hidden">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span id="error-text"></span>
                        </div>
                    </div>
                </div>

                <form id="otpForm" class="space-y-6">
                    @csrf
                    
                    <!-- OTP Input Fields -->
                    <div>
                        <label class="block text-sm font-semibold text-amber-100 mb-4 text-center">
                            Masukkan Kode OTP
                        </label>
                        <div class="flex justify-center space-x-3">
                            <input type="text" maxlength="1" class="otp-input" id="otp1" name="otp1" autocomplete="off">
                            <input type="text" maxlength="1" class="otp-input" id="otp2" name="otp2" autocomplete="off">
                            <input type="text" maxlength="1" class="otp-input" id="otp3" name="otp3" autocomplete="off">
                            <input type="text" maxlength="1" class="otp-input" id="otp4" name="otp4" autocomplete="off">
                            <input type="text" maxlength="1" class="otp-input" id="otp5" name="otp5" autocomplete="off">
                            <input type="text" maxlength="1" class="otp-input" id="otp6" name="otp6" autocomplete="off">
                        </div>
                    </div>

                    <!-- Countdown Timer -->
                    <div class="text-center">
                        <p class="text-amber-200 text-sm mb-2">Kode berakhir dalam:</p>
                        <div class="countdown text-2xl font-bold text-amber-300" id="countdown">05:00</div>
                    </div>

                    <!-- Verify Button -->
                    <div>
                        <button type="submit" id="verifyBtn" disabled
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-gray-500 cursor-not-allowed transition-all duration-300">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <span id="verify-text">Verifikasi OTP</span>
                        </button>
                    </div>

                    <!-- Resend OTP -->
                    <div class="text-center">
                        <p class="text-amber-200 text-sm mb-2">
                            Tidak menerima kode?
                        </p>
                        <button type="button" id="resendBtn" disabled
                                class="font-medium text-gray-400 cursor-not-allowed transition-colors duration-200">
                            <span id="resend-text">Kirim Ulang OTP</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer Links -->
            <div class="text-center">
                <div class="mt-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center text-amber-300 hover:text-amber-200 transition-colors duration-200">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke registrasi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // OTP Input Management
        const otpInputs = document.querySelectorAll('.otp-input');
        const verifyBtn = document.getElementById('verifyBtn');
        const resendBtn = document.getElementById('resendBtn');
        const otpForm = document.getElementById('otpForm');
        
        // Auto-focus and navigation
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                const value = e.target.value;
                
                // Only allow numbers
                if (!/^\d$/.test(value)) {
                    e.target.value = '';
                    return;
                }
                
                // Move to next input
                if (value && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
                
                checkOtpComplete();
            });
            
            input.addEventListener('keydown', function(e) {
                // Move to previous input on backspace
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });
            
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = e.clipboardData.getData('text');
                const numbers = paste.replace(/\D/g, '').slice(0, 6);
                
                numbers.split('').forEach((num, i) => {
                    if (i < otpInputs.length) {
                        otpInputs[i].value = num;
                    }
                });
                
                checkOtpComplete();
            });
        });
        
        function checkOtpComplete() {
            const otp = Array.from(otpInputs).map(input => input.value).join('');
            
            if (otp.length === 6) {
                verifyBtn.disabled = false;
                verifyBtn.classList.remove('bg-gray-500', 'cursor-not-allowed');
                verifyBtn.classList.add('bg-gradient-to-r', 'from-amber-500', 'to-orange-600', 'hover:from-amber-600', 'hover:to-orange-700', 'transform', 'hover:scale-105');
                verifyBtn.querySelector('svg').classList.remove('text-gray-300');
                verifyBtn.querySelector('svg').classList.add('text-amber-200');
            } else {
                verifyBtn.disabled = true;
                verifyBtn.classList.add('bg-gray-500', 'cursor-not-allowed');
                verifyBtn.classList.remove('bg-gradient-to-r', 'from-amber-500', 'to-orange-600', 'hover:from-amber-600', 'hover:to-orange-700', 'transform', 'hover:scale-105');
                verifyBtn.querySelector('svg').classList.add('text-gray-300');
                verifyBtn.querySelector('svg').classList.remove('text-amber-200');
            }
        }
        
        // Countdown Timer
        let timeLeft = 300; // 5 minutes in seconds
        
        function updateCountdown() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.getElementById('countdown').textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                // Enable resend button when timer expires
                resendBtn.disabled = false;
                resendBtn.classList.remove('text-gray-400', 'cursor-not-allowed');
                resendBtn.classList.add('text-amber-300', 'hover:text-amber-200', 'cursor-pointer');
                document.getElementById('countdown').textContent = '00:00';
                document.getElementById('countdown').classList.add('text-red-400');
                return;
            }
            
            timeLeft--;
            setTimeout(updateCountdown, 1000);
        }
        
        updateCountdown();
        
        // Form submission
        otpForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const otp = Array.from(otpInputs).map(input => input.value).join('');
            
            if (otp.length !== 6) {
                showMessage('error', 'Silakan masukkan kode OTP lengkap');
                return;
            }
            
            // Show loading state
            const verifyText = document.getElementById('verify-text');
            const originalText = verifyText.textContent;
            verifyText.textContent = 'Memverifikasi...';
            verifyBtn.disabled = true;
            
            // Send verification request
            fetch(`{{ route('otp.verify', $otpVerification->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    otp_code: otp
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('success', data.message);
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1500);
                } else {
                    showMessage('error', data.message);
                    // Clear OTP inputs
                    otpInputs.forEach(input => input.value = '');
                    otpInputs[0].focus();
                }
            })
            .catch(error => {
                showMessage('error', 'Terjadi kesalahan. Silakan coba lagi.');
            })
            .finally(() => {
                verifyText.textContent = originalText;
                checkOtpComplete();
            });
        });
        
        // Resend OTP
        resendBtn.addEventListener('click', function() {
            if (resendBtn.disabled) return;
            
            const resendText = document.getElementById('resend-text');
            const originalText = resendText.textContent;
            resendText.textContent = 'Mengirim...';
            resendBtn.disabled = true;
            
            fetch(`{{ route('otp.resend', $otpVerification->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('success', data.message);
                    // Reset countdown
                    timeLeft = 300;
                    document.getElementById('countdown').classList.remove('text-red-400');
                    updateCountdown();
                } else {
                    showMessage('error', data.message);
                    resendBtn.disabled = false;
                    resendBtn.classList.remove('text-gray-400', 'cursor-not-allowed');
                    resendBtn.classList.add('text-amber-300', 'hover:text-amber-200', 'cursor-pointer');
                }
            })
            .catch(error => {
                showMessage('error', 'Terjadi kesalahan. Silakan coba lagi.');
                resendBtn.disabled = false;
                resendBtn.classList.remove('text-gray-400', 'cursor-not-allowed');
                resendBtn.classList.add('text-amber-300', 'hover:text-amber-200', 'cursor-pointer');
            })
            .finally(() => {
                resendText.textContent = originalText;
            });
        });
        
        function showMessage(type, message) {
            const messageContainer = document.getElementById('message-container');
            const successMessage = document.getElementById('success-message');
            const errorMessage = document.getElementById('error-message');
            
            // Hide both messages first
            successMessage.classList.add('hidden');
            errorMessage.classList.add('hidden');
            
            if (type === 'success') {
                document.getElementById('success-text').textContent = message;
                successMessage.classList.remove('hidden');
            } else {
                document.getElementById('error-text').textContent = message;
                errorMessage.classList.remove('hidden');
            }
            
            messageContainer.classList.remove('hidden');
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                messageContainer.classList.add('hidden');
            }, 5000);
        }
        
        // Focus first input on load
        otpInputs[0].focus();
    </script>
</body>
</html>