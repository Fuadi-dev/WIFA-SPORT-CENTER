<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register - WIFA Sport Center</title>
    @vite('resources/css/app.css')
    <style>
        html {
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .register-bg {
            background: linear-gradient(135deg, rgba(120,53,15,0.95) 0%, rgba(146,64,14,0.9) 50%, rgba(180,83,9,0.95) 100%);
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
                    Buat Akun Baru
                </h2>
                <p class="text-amber-200 text-lg">
                    Bergabung dengan <span class="font-semibold text-amber-300">WIFA Sport Center</span>
                </p>
            </div>

            <!-- Register Form -->
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
                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span id="error-text" class="whitespace-pre-line flex-1"></span>
                        </div>
                    </div>
                </div>

                <form class="space-y-6" id="registerForm">
                    @csrf
                    
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-amber-100 mb-2">
                            Nama Lengkap
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input id="name" name="name" type="text" required 
                                   class="block w-full pl-10 pr-3 py-3 border border-amber-400/30 rounded-lg bg-white/10 backdrop-blur-sm text-white placeholder-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300"
                                   placeholder="Masukan nama lengkap">
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-amber-100 mb-2">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" required 
                                   class="block w-full pl-10 pr-3 py-3 border border-amber-400/30 rounded-lg bg-white/10 backdrop-blur-sm text-white placeholder-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300"
                                   placeholder="Masukan alamat email">
                        </div>
                    </div>

                    <!-- WhatsApp Field -->
                    <div>
                        <label for="phone_number" class="block text-sm font-semibold text-amber-100 mb-2">
                            Nomor WhatsApp
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.525 3.488"/>
                                </svg>
                            </div>
                            <input id="phone_number" name="phone_number" type="tel" required 
                                   class="block w-full pl-10 pr-3 py-3 border border-amber-400/30 rounded-lg bg-white/10 backdrop-blur-sm text-white placeholder-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300"
                                   placeholder="Contoh: 08123456789">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-amber-100 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" required 
                                   class="block w-full pl-10 pr-12 py-3 border border-amber-400/30 rounded-lg bg-white/10 backdrop-blur-sm text-white placeholder-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300"
                                   placeholder="Masukan password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center z-10">
                                <button type="button" class="text-amber-400 hover:text-amber-300 transition-colors duration-200 focus:outline-none" onclick="togglePassword()">
                                    <svg id="eye-icon" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Password Requirements -->
                        <div class="mt-3 space-y-2">
                            <p class="text-xs text-amber-200 mb-2">Password harus memenuhi:</p>
                            <div class="space-y-1 text-xs">
                                <div id="length-check" class="flex items-center text-red-400">
                                    <svg class="h-3 w-3 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Minimal 8 karakter
                                </div>
                                <div id="number-check" class="flex items-center text-red-400">
                                    <svg class="h-3 w-3 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Mengandung angka
                                </div>
                                <div id="letter-check" class="flex items-center text-red-400">
                                    <svg class="h-3 w-3 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    Mengandung huruf
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-amber-100 mb-2">
                            Konfirmasi Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" required 
                                   class="block w-full pl-10 pr-3 py-3 border border-amber-400/30 rounded-lg bg-white/10 backdrop-blur-sm text-white placeholder-amber-200 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all duration-300"
                                   placeholder="Ulangi password">
                        </div>
                        <div id="password-match" class="mt-2 text-xs text-red-400 hidden">
                            Password tidak cocok
                        </div>
                    </div>

                    <!-- Register Button -->
                    <div>
                        <button type="submit" id="registerBtn" disabled
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </span>
                            Daftar
                        </button>
                    </div>

                    <!-- Alternative Login -->
                    <div class="mt-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-amber-400/30"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-transparent text-amber-200">Atau daftar dengan</span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="{{ route('auth.google') }}" 
                                    class="w-full inline-flex justify-center py-3 px-4 border border-amber-400/30 rounded-lg shadow-sm bg-white/5 text-sm font-medium text-amber-200 hover:bg-white/10 hover:text-amber-100 transition-all duration-200 group">
                                <svg class="h-5 w-5 mr-3" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                </svg>
                                <span class="font-medium">Daftar dengan Google</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer Links -->
            <div class="text-center">
                <p class="text-amber-200 mb-4">
                    Sudah punya akun? 
                    <a href="{{ url('/login') }}" class="font-medium text-amber-300 hover:text-amber-200 transition-colors duration-200">
                        Masuk di sini
                    </a>
                </p>
                <div class="mt-4">
                    <a href="/" class="inline-flex items-center text-amber-300 hover:text-amber-200 transition-colors duration-200">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.innerHTML = `
                    <path d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414L15 13.586A8 8 0 018.707 6.293L3.707 2.293zM6.586 11L9 13.414C9.63 13.79 10.297 14 11 14a3 3 0 003-3 2.97 2.97 0 00-.414-1.5L6.586 11z"></path>
                    <path d="M11 5a4 4 0 013.446 6.032l-2.261-2.26A1.993 1.993 0 0011 8c-.63 0-1.202.29-1.569.738L6.155 5.462A7.988 7.988 0 0111 5zM4.636 3.464L1.414 6.707a1 1 0 000 1.414L4.636 11.343a8 8 0 0110.728 0l3.222-3.222a1 1 0 000-1.414L15.364 3.464a8 8 0 00-10.728 0z"></path>
                `;
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML = `
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                `;
            }
        }

        // Password validation and form control
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const registerBtn = document.getElementById('registerBtn');
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone_number');
            const registerForm = document.getElementById('registerForm');
            
            const lengthCheck = document.getElementById('length-check');
            const numberCheck = document.getElementById('number-check');
            const letterCheck = document.getElementById('letter-check');
            const passwordMatch = document.getElementById('password-match');

            function validatePassword() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                
                // Check length
                const hasLength = password.length >= 8;
                updateCheckStatus(lengthCheck, hasLength);
                
                // Check for numbers
                const hasNumber = /\d/.test(password);
                updateCheckStatus(numberCheck, hasNumber);
                
                // Check for letters
                const hasLetter = /[a-zA-Z]/.test(password);
                updateCheckStatus(letterCheck, hasLetter);
                
                // Check password match
                const passwordsMatch = password === confirmPassword && confirmPassword !== '';
                if (confirmPassword !== '') {
                    if (passwordsMatch) {
                        passwordMatch.classList.add('hidden');
                        passwordMatch.classList.remove('text-red-400');
                        passwordMatch.classList.add('text-green-400');
                    } else {
                        passwordMatch.classList.remove('hidden');
                        passwordMatch.classList.add('text-red-400');
                        passwordMatch.classList.remove('text-green-400');
                    }
                }
                
                // Check if all validations pass
                const allValid = hasLength && hasNumber && hasLetter && passwordsMatch && 
                                nameInput.value.trim() !== '' && 
                                emailInput.value.trim() !== '' && 
                                phoneInput.value.trim() !== '';
                
                // Enable/disable register button
                if (allValid) {
                    registerBtn.disabled = false;
                    registerBtn.classList.remove('bg-gray-500', 'cursor-not-allowed');
                    registerBtn.classList.add('bg-gradient-to-r', 'from-amber-500', 'to-orange-600', 'hover:from-amber-600', 'hover:to-orange-700', 'transform', 'hover:scale-105');
                    registerBtn.querySelector('svg').classList.remove('text-gray-300');
                    registerBtn.querySelector('svg').classList.add('text-amber-200', 'group-hover:text-amber-100');
                } else {
                    registerBtn.disabled = true;
                    registerBtn.classList.add('bg-gray-500', 'cursor-not-allowed');
                    registerBtn.classList.remove('bg-gradient-to-r', 'from-amber-500', 'to-orange-600', 'hover:from-amber-600', 'hover:to-orange-700', 'transform', 'hover:scale-105');
                    registerBtn.querySelector('svg').classList.add('text-gray-300');
                    registerBtn.querySelector('svg').classList.remove('text-amber-200', 'group-hover:text-amber-100');
                }
            }

            function updateCheckStatus(element, isValid) {
                const icon = element.querySelector('svg');
                if (isValid) {
                    element.classList.remove('text-red-400');
                    element.classList.add('text-green-400');
                    icon.innerHTML = `<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>`;
                } else {
                    element.classList.add('text-red-400');
                    element.classList.remove('text-green-400');
                    icon.innerHTML = `<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>`;
                }
            }

            // Form submission
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (registerBtn.disabled) return;
                
                // Show loading state
                const originalText = registerBtn.textContent;
                registerBtn.textContent = 'Mendaftar...';
                registerBtn.disabled = true;
                
                // Prepare form data
                const formData = {
                    name: nameInput.value.trim(),
                    email: emailInput.value.trim(),
                    phone_number: phoneInput.value.trim(),
                    password: passwordInput.value,
                    password_confirmation: confirmPasswordInput.value
                };
                
                // Send registration request
                fetch('{{ route("register.post") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => {
                    // Clone response to read it multiple times
                    return response.json().then(data => ({ 
                        ok: response.ok, 
                        status: response.status,
                        data: data 
                    }));
                })
                .then(({ ok, status, data }) => {
                    if (data.success) {
                        showMessage('success', data.message);
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 1500);
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
                            showMessage('error', data.message || 'Registrasi gagal. Silakan periksa data Anda.');
                        }
                    }
                })
                .catch(error => {
                    console.error('Registration error:', error);
                    showMessage('error', 'Tidak dapat terhubung ke server. Pastikan koneksi internet Anda aktif.');
                })
                .finally(() => {
                    registerBtn.textContent = originalText;
                    validatePassword(); // Re-enable button if form is valid
                });
            });

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
                validatePassword(); // Trigger validation after formatting
            });

            // Add event listeners
            passwordInput.addEventListener('input', validatePassword);
            confirmPasswordInput.addEventListener('input', validatePassword);
            nameInput.addEventListener('input', validatePassword);
            emailInput.addEventListener('input', validatePassword);

            // Add focus animations
            const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="password"]');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('scale-105');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('scale-105');
                });
            });
            
            // Message display function
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
                
                // Auto hide after delay (longer for errors with multiple lines)
                const hideDelay = type === 'error' && message.includes('\n') ? 8000 : 5000;
                setTimeout(() => {
                    messageContainer.classList.add('hidden');
                }, hideDelay);
            }
        });
    </script>
</body>
</html>