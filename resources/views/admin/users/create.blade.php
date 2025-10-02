@extends('admin.app')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah User Baru</h1>
            <p class="text-gray-600 mt-1">Buat akun user baru untuk sistem</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="max-w-2xl mx-auto">
        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-user-plus mr-2 text-amber-600"></i>
                    Informasi User
                </h3>
            </div>
            
            <form method="POST" action="{{ route('admin.users.store') }}" class="p-6">
                @csrf
                
                <!-- Name Field -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('name') border-red-500 @enderror"
                           placeholder="Masukkan nama lengkap"
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('email') border-red-500 @enderror"
                           placeholder="contoh@email.com"
                           required>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('password') border-red-500 @enderror pr-10"
                               placeholder="Minimal 8 karakter"
                               required>
                        <button type="button" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                onclick="togglePassword('password')">
                            <i class="fas fa-eye text-gray-400" id="password-icon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation Field -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 pr-10"
                               placeholder="Konfirmasi password"
                               required>
                        <button type="button" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                onclick="togglePassword('password_confirmation')">
                            <i class="fas fa-eye text-gray-400" id="password_confirmation-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Role Selection -->
                <div class="mb-6">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select id="role" 
                            name="role" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('role') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Role</option>
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    
                    <!-- Role Descriptions -->
                    <div class="mt-2 text-sm text-gray-600 space-y-1">
                        <div class="flex items-center">
                            <span class="w-12 text-gray-500">User:</span>
                            <span>Dapat melakukan booking dan mendaftar event</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-12 text-gray-500">Admin:</span>
                            <span>Dapat mengelola booking, event, dan user</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-12 text-gray-500">Owner:</span>
                            <span>Akses penuh ke sistem dan laporan</span>
                        </div>
                    </div>
                </div>

                <!-- Status Selection -->
                <div class="mb-8">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" 
                            name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('status') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Status</option>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="non-active" {{ old('status') === 'non-active' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-600">User dengan status non-aktif tidak dapat login ke sistem</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.users.index') }}" 
                       class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan User
                    </button>
                </div>
            </form>
        </div>

        <!-- Information Card -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-blue-800 mb-2">Informasi Penting</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Password akan di-hash secara otomatis untuk keamanan</li>
                        <li>• User akan menerima notifikasi login pertama kali</li>
                        <li>• Admin dapat mengubah status user kapan saja</li>
                        <li>• Email harus unik dan belum terdaftar di sistem</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50" id="success-message">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50" id="error-message">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    </div>
@endif

<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Auto-hide messages after 5 seconds
    setTimeout(function() {
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        if (successMessage) successMessage.remove();
        if (errorMessage) errorMessage.remove();
    }, 5000);
</script>
@endsection