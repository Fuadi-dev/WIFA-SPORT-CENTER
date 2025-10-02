@extends('admin.app')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail User</h1>
            <p class="text-gray-600 mt-1">Informasi lengkap tentang user</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-amber-500 to-orange-500 text-white">
                    <div class="text-center">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="h-20 w-20 rounded-full object-cover mx-auto mb-4 border-4 border-white shadow-lg">
                        @else
                            <div class="h-20 w-20 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-2xl mx-auto mb-4 border-4 border-white shadow-lg">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <h3 class="text-xl font-bold">{{ $user->name }}</h3>
                        <p class="text-amber-100">{{ $user->email }}</p>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Role -->
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Role:</span>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                {{ $user->role === 'owner' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $user->role === 'user' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                        
                        <!-- Status -->
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->status === 'active' ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </div>
                        
                        <!-- Provider -->
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Provider:</span>
                            <div class="flex items-center">
                                @if($user->provider === 'google')
                                    <i class="fab fa-google mr-2 text-red-500"></i>
                                    <span>Google</span>
                                @else
                                    <i class="fas fa-user mr-2 text-gray-500"></i>
                                    <span>Manual</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Registration Date -->
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Terdaftar:</span>
                            <span class="text-gray-800">{{ $user->created_at->format('d F Y') }}</span>
                        </div>
                        
                        <!-- Last Update -->
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Terakhir Update:</span>
                            <span class="text-gray-800">{{ $user->updated_at->format('d F Y') }}</span>
                        </div>
                    </div>
                    
                    @if($user->id !== auth()->user()->id)
                        <!-- Action Buttons -->
                        <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                            <!-- Toggle Status -->
                            <form method="POST" action="{{ route('admin.users.toggleStatus', $user) }}" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="w-full px-4 py-2 rounded-lg font-semibold transition-colors
                                        {{ $user->status === 'active' ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}"
                                        onclick="return confirm('Yakin ingin mengubah status user ini?')">
                                    <i class="fas fa-toggle-{{ $user->status === 'active' ? 'on' : 'off' }} mr-2"></i>
                                    {{ $user->status === 'active' ? 'Nonaktifkan User' : 'Aktifkan User' }}
                                </button>
                            </form>
                            
                            <!-- Delete Button -->
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition-colors"
                                        onclick="return confirm('Yakin ingin menghapus user {{ $user->name }}? Tindakan ini tidak dapat dibatalkan.')">
                                    <i class="fas fa-trash mr-2"></i>Hapus User
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mt-6 pt-6 border-t border-gray-200 text-center text-gray-500">
                            <i class="fas fa-info-circle mr-2"></i>
                            Ini adalah akun Anda sendiri
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- User Activity & Statistics -->
        <div class="lg:col-span-2">
            <!-- Booking Statistics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-chart-bar mr-2 text-amber-600"></i>
                        Statistik Booking
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $user->bookings_count ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Total Booking</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 mb-2">
                                {{ $user->bookings()->where('status', 'confirmed')->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Booking Selesai</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-yellow-600 mb-2">
                                {{ $user->bookings()->where('status', 'pending')->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Booking Pending</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-calendar-check mr-2 text-amber-600"></i>
                        Booking Terbaru
                    </h3>
                </div>
                <div class="p-6">
                    @if($user->bookings()->latest()->limit(5)->count() > 0)
                        <div class="space-y-4">
                            @foreach($user->bookings()->with(['sport', 'court'])->latest()->limit(5)->get() as $booking)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="p-2 rounded-full bg-amber-100 text-amber-600">
                                            <i class="fas fa-calendar text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $booking->sport->name ?? 'N/A' }} - {{ $booking->court->name ?? 'N/A' }}</p>
                                            <p class="text-sm text-gray-600">
                                                {{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }} • 
                                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                            {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                        <p class="text-sm text-gray-600 mt-1">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-600">User belum memiliki booking</p>
                        </div>
                    @endif
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
    // Auto-hide messages after 5 seconds
    setTimeout(function() {
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        if (successMessage) successMessage.remove();
        if (errorMessage) errorMessage.remove();
    }, 5000);
</script>
@endsection