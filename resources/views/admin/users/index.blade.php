@extends('admin.layouts.admin')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola User</h1>
            <p class="text-gray-600 mt-1">Kelola data user yang terdaftar di sistem</p>
        </div>
        <button onclick="openAddUserModal()" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>Tambah User
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total User</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $users->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">User Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $users->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-user-times text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">User Non-Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $users->where('status', 'non-active')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-user-shield text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Admin</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $users->where('role', 'admin')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari User</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Nama atau email..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            </div>
            
            <!-- Role Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="all" {{ request('role') === 'all' ? 'selected' : '' }}>Semua Role</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                </select>
            </div>
            
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="non-active" {{ request('status') === 'non-active' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
            
            <!-- Actions -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">User</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Email</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Role</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Provider</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Terdaftar</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="flex items-center">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="h-8 w-8 rounded-full object-cover mr-3">
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-semibold text-sm mr-3">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        @if($user->id === auth()->user()->id)
                                            <span class="text-xs text-blue-600 font-semibold">(Anda)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-gray-600">{{ $user->email }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $user->role === 'owner' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $user->role === 'user' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->status === 'active' ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-600">
                                @if($user->provider === 'google')
                                    <span class="inline-flex items-center text-sm">
                                        <i class="fab fa-google mr-1 text-red-500"></i>
                                        Google
                                    </span>
                                @else
                                    <span class="text-gray-500">Manual</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-600">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- View Button -->
                                    <button onclick="viewUserDetail({{ $user->id }})" 
                                       class="text-blue-600 hover:text-blue-800 transition-colors"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    @if($user->id !== auth()->user()->id)
                                        <!-- Toggle Status Button -->
                                        <form method="POST" action="{{ route('admin.users.toggleStatus', $user) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="text-yellow-600 hover:text-yellow-800 transition-colors"
                                                    title="{{ $user->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                    onclick="return confirm('Yakin ingin mengubah status user ini?')">
                                                <i class="fas fa-toggle-{{ $user->status === 'active' ? 'on' : 'off' }}"></i>
                                            </button>
                                        </form>
                                        
                                        <!-- Delete Button -->
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 transition-colors"
                                                    title="Hapus User"
                                                    onclick="return confirm('Yakin ingin menghapus user {{ $user->name }}? Tindakan ini tidak dapat dibatalkan.')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400" title="Tidak dapat menghapus akun sendiri">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 px-4 text-center text-gray-500">
                                <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium">Tidak ada user ditemukan</p>
                                <p class="text-sm">Coba ubah filter atau kata kunci pencarian</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
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

<!-- Add User Modal -->
<div id="addUserModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-6 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-white">
                        <i class="fas fa-user-plus mr-2"></i>Tambah User Baru
                    </h3>
                    <button onclick="closeAddUserModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <form id="addUserForm" class="p-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-amber-600 mr-1"></i>Nama Lengkap *
                        </label>
                        <input type="text" name="name" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                               placeholder="Masukkan nama lengkap">
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope text-amber-600 mr-1"></i>Email *
                        </label>
                        <input type="email" name="email" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                               placeholder="email@example.com">
                    </div>
                    
                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone text-amber-600 mr-1"></i>Nomor Telepon
                        </label>
                        <input type="tel" name="phone_number" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                               placeholder="08xxxxxxxxxx">
                    </div>
                    
                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-amber-600 mr-1"></i>Password *
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required 
                                   class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                   placeholder="Minimal 8 karakter">
                            <button type="button" onclick="togglePasswordVisibility('password')" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Password Confirmation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-amber-600 mr-1"></i>Konfirmasi Password *
                        </label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required 
                                   class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                   placeholder="Ulangi password">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation')" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-tag text-amber-600 mr-1"></i>Role *
                        </label>
                        <select name="role" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">Pilih role...</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="owner">Owner</option>
                        </select>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-toggle-on text-amber-600 mr-1"></i>Status *
                        </label>
                        <select name="status" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="active">Aktif</option>
                            <option value="non-active">Non-Aktif</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeAddUserModal()" 
                            class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" id="submitUserBtn" 
                            class="px-6 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View User Modal -->
<div id="viewUserModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full">
            <div class="bg-gradient-to-r from-blue-500 to-purple-500 p-6 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-white">
                        <i class="fas fa-user-circle mr-2"></i>Detail User
                    </h3>
                    <button onclick="closeViewUserModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <div id="userDetailContent" class="p-6">
                <!-- User detail will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-hide messages after 5 seconds
    setTimeout(function() {
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        if (successMessage) successMessage.remove();
        if (errorMessage) errorMessage.remove();
    }, 5000);

    // Open Add User Modal
    function openAddUserModal() {
        document.getElementById('addUserModal').classList.remove('hidden');
    }

    // Close Add User Modal
    function closeAddUserModal() {
        document.getElementById('addUserModal').classList.add('hidden');
        document.getElementById('addUserForm').reset();
    }

    // View User Detail
    function viewUserDetail(userId) {
        // Show loading
        const modalContent = document.getElementById('userDetailContent');
        modalContent.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-amber-600"></i><p class="mt-2 text-gray-600">Loading...</p></div>';
        
        document.getElementById('viewUserModal').classList.remove('hidden');
        
        // Fetch user detail
        fetch(`/admin/users/${userId}/detail`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayUserDetail(data.user);
                } else {
                    modalContent.innerHTML = '<div class="text-center py-8 text-red-600"><i class="fas fa-exclamation-circle text-3xl"></i><p class="mt-2">Gagal memuat data user</p></div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                modalContent.innerHTML = '<div class="text-center py-8 text-red-600"><i class="fas fa-exclamation-circle text-3xl"></i><p class="mt-2">Terjadi kesalahan</p></div>';
            });
    }

    // Display User Detail
    function displayUserDetail(user) {
        const initial = user.name.charAt(0).toUpperCase();
        const providerIcon = user.provider === 'google' ? '<i class="fab fa-google text-red-500 mr-1"></i> Google' : '<i class="fas fa-envelope text-gray-500 mr-1"></i> Manual';
        
        const html = `
            <div class="flex flex-col items-center mb-6">
                ${user.avatar ? 
                    `<img src="${user.avatar}" alt="${user.name}" class="h-24 w-24 rounded-full object-cover mb-4 border-4 border-amber-200">` :
                    `<div class="h-24 w-24 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold text-3xl mb-4 border-4 border-amber-200">${initial}</div>`
                }
                <h3 class="text-2xl font-bold text-gray-900">${user.name}</h3>
                <p class="text-gray-600">${user.email}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Role</p>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        ${user.role === 'owner' ? 'bg-purple-100 text-purple-800' : ''}
                        ${user.role === 'admin' ? 'bg-blue-100 text-blue-800' : ''}
                        ${user.role === 'user' ? 'bg-gray-100 text-gray-800' : ''}">
                        ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                    </span>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Status</p>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        ${user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${user.status === 'active' ? 'Aktif' : 'Non-Aktif'}
                    </span>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Provider</p>
                    <p class="text-sm font-medium text-gray-900">${providerIcon}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Nomor Telepon</p>
                    <p class="text-sm font-medium text-gray-900">${user.phone_number || '-'}</p>
                </div>
            </div>

            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-calendar-check text-blue-600 mr-2"></i>
                    Statistik Booking
                </h4>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">${user.stats.total_bookings}</p>
                        <p class="text-xs text-gray-600">Total Booking</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">${user.stats.completed_bookings}</p>
                        <p class="text-xs text-gray-600">Completed</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-amber-600">Rp ${user.stats.total_spent.toLocaleString('id-ID')}</p>
                        <p class="text-xs text-gray-600">Total Pengeluaran</p>
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-trophy text-purple-600 mr-2"></i>
                    Event Registration
                </h4>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-purple-600">${user.stats.total_events}</p>
                        <p class="text-xs text-gray-600">Total Event</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-amber-600">Rp ${user.stats.total_event_spent.toLocaleString('id-ID')}</p>
                        <p class="text-xs text-gray-600">Total Biaya Event</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Terdaftar Sejak</p>
                        <p class="font-medium text-gray-900">${new Date(user.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Update Terakhir</p>
                        <p class="font-medium text-gray-900">${new Date(user.updated_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</p>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('userDetailContent').innerHTML = html;
    }

    // Close View User Modal
    function closeViewUserModal() {
        document.getElementById('viewUserModal').classList.add('hidden');
    }

    // Submit Add User Form
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = document.getElementById('submitUserBtn');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
        
        fetch('{{ route("admin.users.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'User berhasil ditambahkan',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    closeAddUserModal();
                    location.reload();
                });
            } else {
                // Display validation errors
                let errorMessage = 'Terjadi kesalahan:\n';
                if (data.errors) {
                    Object.values(data.errors).forEach(error => {
                        errorMessage += '• ' + error[0] + '\n';
                    });
                } else {
                    errorMessage = data.message || 'Gagal menambahkan user';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: errorMessage,
                    confirmButtonColor: '#d33'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan sistem',
                confirmButtonColor: '#d33'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // Toggle password visibility
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const icon = event.target;
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Close modals when clicking outside
    document.getElementById('addUserModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeAddUserModal();
    });

    document.getElementById('viewUserModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeViewUserModal();
    });
</script>
@endsection
