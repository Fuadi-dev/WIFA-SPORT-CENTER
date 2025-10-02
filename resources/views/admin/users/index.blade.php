@extends('admin.layouts.admin')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola User</h1>
            <p class="text-gray-600 mt-1">Kelola data user yang terdaftar di sistem</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>Tambah User
        </a>
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
                                    <a href="{{ route('admin.users.show', $user) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition-colors"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
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

<script>
    // Auto-hide messages after 5 seconds
    setTimeout(function() {
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        if (successMessage) successMessage.remove();
        if (errorMessage) errorMessage.remove();
    }, 5000);
</script>
@endsection
