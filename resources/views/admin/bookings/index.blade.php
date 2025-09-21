@extends('layouts.admin')

@section('title', 'Kelola Booking')
@section('page-title', 'Kelola Booking')
@section('page-description', 'Manajemen semua booking WIFA Sport Center')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <!-- Total Bookings -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-4 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-xs font-medium">Total Booking</p>
                <p class="text-2xl font-bold">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="bg-blue-400/30 p-2 rounded-lg">
                <i class="fas fa-calendar-check text-lg"></i>
            </div>
        </div>
    </div>
    
    <!-- Pending -->
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white p-4 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-xs font-medium">Pending</p>
                <p class="text-2xl font-bold">{{ number_format($stats['pending']) }}</p>
            </div>
            <div class="bg-yellow-400/30 p-2 rounded-lg">
                <i class="fas fa-clock text-lg"></i>
            </div>
        </div>
    </div>
    
    <!-- Confirmed -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-4 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-xs font-medium">Confirmed</p>
                <p class="text-2xl font-bold">{{ number_format($stats['confirmed']) }}</p>
            </div>
            <div class="bg-green-400/30 p-2 rounded-lg">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
        </div>
    </div>
    
    <!-- Completed -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-4 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-xs font-medium">Completed</p>
                <p class="text-2xl font-bold">{{ number_format($stats['completed']) }}</p>
            </div>
            <div class="bg-purple-400/30 p-2 rounded-lg">
                <i class="fas fa-trophy text-lg"></i>
            </div>
        </div>
    </div>
    
    <!-- Revenue -->
    <div class="bg-gradient-to-br from-amber-500 to-orange-500 text-white p-4 rounded-xl shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-amber-100 text-xs font-medium">Pendapatan Bulan Ini</p>
                <p class="text-xl font-bold">Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-amber-400/30 p-2 rounded-lg">
                <i class="fas fa-money-bill-wave text-lg"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Search -->
<div class="bg-white p-6 rounded-xl shadow-lg mb-6">
    <form method="GET" action="{{ route('admin.bookings.index') }}" class="space-y-4">
        <div class="flex flex-wrap gap-4 items-end">
            <!-- Search -->
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Booking</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Kode booking, nama tim, user, olahraga..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            </div>
            
            <!-- Status Filter -->
            <div class="min-w-40">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">Semua Status</option>
                    <option value="pending_payment" {{ request('status') == 'pending_payment' ? 'selected' : '' }}>Pending Payment</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <!-- Payment Method -->
            <div class="min-w-40">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pembayaran</label>
                <select name="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">Semua Metode</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="midtrans" {{ request('payment_method') == 'midtrans' ? 'selected' : '' }}>Online</option>
                </select>
            </div>
            
            <!-- Date From -->
            <div class="min-w-40">
                <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            </div>
            
            <!-- Date To -->
            <div class="min-w-40">
                <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            </div>
            
            <!-- Actions -->
            <div class="flex space-x-2">
                <button type="submit" class="px-6 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.bookings.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Bookings Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-list mr-2 text-amber-600"></i>
                Daftar Booking
            </h3>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'booking_code', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
                           class="flex items-center space-x-1 hover:text-amber-600">
                            <span>Kode Booking</span>
                            <i class="fas fa-sort text-xs"></i>
                        </a>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'booking_date', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
                           class="flex items-center space-x-1 hover:text-amber-600">
                            <span>Tanggal & Waktu</span>
                            <i class="fas fa-sort text-xs"></i>
                        </a>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Olahraga & Lapangan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tim</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'total_price', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc']) }}" 
                           class="flex items-center space-x-1 hover:text-amber-600">
                            <span>Total</span>
                            <i class="fas fa-sort text-xs"></i>
                        </a>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $booking->booking_code }}</div>
                            <div class="text-xs text-gray-500">{{ $booking->created_at->format('d M Y H:i') }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-semibold text-xs mr-3">
                                    {{ strtoupper(substr($booking->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->user->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $booking->user->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $booking->sport->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $booking->court->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $booking->team_name }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($booking->payment_method === 'cash') bg-green-100 text-green-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                @if($booking->payment_method === 'cash')
                                    <i class="fas fa-money-bill mr-1"></i> Tunai
                                @else
                                    <i class="fas fa-credit-card mr-1"></i> Online
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                @elseif($booking->status === 'pending_payment') bg-yellow-100 text-yellow-800
                                @elseif($booking->status === 'completed') bg-blue-100 text-blue-800
                                @elseif($booking->status === 'paid') bg-purple-100 text-purple-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <!-- View Details -->
                                <a href="{{ route('admin.bookings.show', $booking) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-1 rounded" 
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Delete - Only if booking date + time hasn't passed -->
                                @php
                                    $bookingDate = \Carbon\Carbon::parse($booking->booking_date);
                                    $startTime = \Carbon\Carbon::parse($booking->start_time);
                                    $bookingDateTime = $bookingDate->setTimeFromTimeString($booking->start_time);
                                    $canDelete = $bookingDateTime->isFuture();
                                @endphp
                                
                                @if($canDelete)
                                    <button class="text-red-600 hover:text-red-900 p-1 rounded" 
                                            onclick="confirmDelete({{ $booking->id }}, '{{ $booking->booking_code }}')"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    <span class="text-gray-400 p-1 rounded cursor-not-allowed" 
                                          title="Tidak dapat menghapus booking yang sudah lewat waktu">
                                        <i class="fas fa-trash"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500 text-lg">Tidak ada booking ditemukan</p>
                                <p class="text-gray-400 text-sm">Coba ubah filter pencarian Anda</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($bookings->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $bookings->links() }}
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Konfirmasi Hapus</h3>
                <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus booking <span id="deleteBookingCode" class="font-semibold"></span>? Tindakan ini tidak dapat dibatalkan.</p>
            
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Hapus Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(bookingId, bookingCode) {
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteForm').action = `/admin/bookings/${bookingId}`;
        document.getElementById('deleteBookingCode').textContent = bookingCode;
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
@endpush