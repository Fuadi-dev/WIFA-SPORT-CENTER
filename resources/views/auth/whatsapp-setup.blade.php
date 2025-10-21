<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Setup WhatsApp - WIFA Sport Center</title>
    @vite('resources/css/app.css')
    <style>
        html {
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-amber-900 via-amber-800 to-orange-900 relative overflow-hidden">
    
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="2"/></g></svg>'); background-size: 60px 60px;"></div>
    </div>

    <!-- Floating Elements -->
    <div class="absolute top-10 left-10 w-32 h-32 bg-amber-400/10 rounded-full blur-xl animate-pulse"></div>
    <div class="absolute bottom-20 right-20 w-24 h-24 bg-orange-500/15 rounded-full blur-lg animate-pulse delay-1000"></div>

    <!-- Main Container -->
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-md w-full space-y-8">
            
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
                    Satu Langkah Lagi!
                </h2>
                <p class="text-amber-200 text-lg">
                    Kami perlu nomor WhatsApp Anda untuk notifikasi booking
                </p>
            </div>

            <!-- WhatsApp Form -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-8 border border-amber-400/20">
                
                <!-- Info Box -->
                <div class="bg-blue-500/20 border border-blue-400/30 text-blue-200 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm">
                            <p class="font-semibold mb-1">Mengapa kami memerlukan nomor WhatsApp?</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Konfirmasi booking otomatis</li>
                                <li>Reminder jadwal bermain</li>
                                <li>Promo & penawaran khusus</li>
                            </ul>
                        </div>
                    </div>
                </div>

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
                    <div id="error-message" class="bg-red-500/20 border border-red-400/30 text-red-200 px-4 py-3 rounded-lg hidden">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span id="error-text" class="whitespace-pre-line"></span>
                        </div>
                    </div>
                </div>

                <form class="space-y-6" id="whatsappForm">
                    @csrf
                    
                    <!-- WhatsApp Field -->
                    <div>
                        <label for="phone_number" class="block text-sm font-semibold text-amber-100 mb-2">
                            Nomor WhatsApp <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                            </div>
                            <input id="phone_number" name="phone_number" type="tel" required 
                                   class="block w-full pl-10 pr-3 py-3 border border-amber-400/30 rounded-lg bg-white/10 backdrop-blur-sm text-white placeholder-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300"
                                   placeholder="08123456789">
                        </div>
                        <p class="mt-2 text-xs text-amber-200">
                            Format: 08xxxxxxxxxx
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="space-y-3">
                        <button type="submit" id="submitBtn"
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-amber-200 group-hover:text-amber-100 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            <span id="submit-text">Simpan & Lanjutkan</span>
                        </button>

                        {{-- <button type="button" onclick="skipWhatsApp()"
                                class="w-full flex justify-center py-3 px-4 border border-amber-400/30 text-sm font-medium rounded-lg text-amber-200 bg-white/5 hover:bg-white/10 transition-all duration-200">
                            Lewati untuk saat ini
                        </button> --}}
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-xs text-amber-200">
                    Nomor WhatsApp Anda aman dan hanya digunakan untuk keperluan layanan WIFA Sport Center
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const whatsappForm = document.getElementById('whatsappForm');
            const submitBtn = document.getElementById('submitBtn');
            const phoneInput = document.getElementById('phone_number');

            // Phone number formatting
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                
                // Auto-format: if starts with 0, keep it; if starts with 62, keep it
                if (value.startsWith('0')) {
                    e.target.value = value;
                } else if (value.startsWith('62')) {
                    e.target.value = value;
                } else if (value.length > 0) {
                    e.target.value = '0' + value;
                } else {
                    e.target.value = value;
                }
            });

            // Form submission
            whatsappForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const phone_number = phoneInput.value.trim();
                
                // Validation
                if (!phone_number) {
                    showMessage('error', 'Nomor WhatsApp wajib diisi');
                    return;
                }

                // Validate phone format
                if (!phone_number.match(/^(0|62)\d{9,13}$/)) {
                    showMessage('error', 'Format nomor WhatsApp tidak valid. Gunakan format: 08xxx atau 62xxx');
                    return;
                }
                
                // Show loading state
                const originalText = submitBtn.querySelector('#submit-text').textContent;
                submitBtn.querySelector('#submit-text').textContent = 'Menyimpan...';
                submitBtn.disabled = true;
                
                // Send request
                fetch('{{ route("whatsapp.setup.post") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ phone_number: phone_number })
                })
                .then(response => {
                    return response.json().then(data => ({ 
                        ok: response.ok, 
                        status: response.status,
                        data: data 
                    }));
                })
                .then(({ ok, status, data }) => {
                    if (data.success) {
                        // Show success message briefly before redirect
                        showMessage('success', data.message);
                        setTimeout(() => {
                            window.location.href = data.redirect_url || '/';
                        }, 1000);
                    } else {
                        // Handle validation errors
                        if (data.errors) {
                            let errorText = [];
                            Object.keys(data.errors).forEach(field => {
                                data.errors[field].forEach(error => {
                                    errorText.push(error);
                                });
                            });
                            showMessage('error', errorText.join('\n'));
                        } else {
                            // Show specific error message from server
                            showMessage('error', data.message || 'Gagal menyimpan nomor WhatsApp.');
                        }
                        submitBtn.querySelector('#submit-text').textContent = originalText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('WhatsApp setup error:', error);
                    showMessage('error', 'Tidak dapat terhubung ke server. Pastikan koneksi internet Anda aktif.');
                    submitBtn.querySelector('#submit-text').textContent = originalText;
                    submitBtn.disabled = false;
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
                
                // Auto hide after 5 seconds (longer for errors with multiple lines)
                const hideDelay = type === 'error' && message.includes('\n') ? 8000 : 5000;
                setTimeout(() => {
                    messageContainer.classList.add('hidden');
                }, hideDelay);
            }

            // Make showMessage available globally
            window.showMessage = showMessage;
        });

        // function skipWhatsApp() {
        //     if (confirm('Anda yakin ingin melewati setup WhatsApp? Anda tidak akan menerima notifikasi booking.')) {
        //         window.location.href = '/';
        //     }
        // }
    </script>
</body>
</html>
