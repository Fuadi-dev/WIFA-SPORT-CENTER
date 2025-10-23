@extends('admin.layouts.admin')

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

<!-- Header Actions -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Daftar Booking</h2>
        <p class="text-gray-600">Kelola semua booking fasilitas olahraga</p>
    </div>
    <button onclick="openManualBookingModal()" 
            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-lg">
        <i class="fas fa-plus mr-2"></i>
        Tambah Booking Manual
    </button>
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
                                
                                <!-- Confirm Booking - Only for pending_confirmation -->
                                @if($booking->status === 'pending_confirmation')
                                    <button onclick="confirmBooking('{{ $booking->slug }}', '{{ $booking->booking_code }}')" 
                                            class="text-green-600 hover:text-green-900 p-1 rounded" 
                                            title="Konfirmasi Booking">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                @endif
                                
                                <!-- Delete - Only if booking date + time hasn't passed -->
                                @php
                                    $bookingDate = \Carbon\Carbon::parse($booking->booking_date);
                                    $startTime = \Carbon\Carbon::parse($booking->start_time);
                                    $bookingDateTime = $bookingDate->setTimeFromTimeString($booking->start_time);
                                    $canDelete = $bookingDateTime->isFuture();
                                @endphp
                                
                                @if($canDelete)
                                    <button class="text-red-600 hover:text-red-900 p-1 rounded" 
                                            onclick="confirmDelete('{{ $booking->slug }}', '{{ $booking->booking_code }}')"
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

<!-- Confirm Booking Modal -->
<div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[9999]">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Konfirmasi Booking</h3>
                <button onclick="closeConfirmModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin mengkonfirmasi booking <span id="confirmBookingCode" class="font-semibold"></span>?</p>
            
            <form id="confirmForm" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeConfirmModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <i class="fas fa-check mr-1"></i> Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[9999]">
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
    function confirmBooking(bookingSlug, bookingCode) {
        document.getElementById('confirmModal').classList.remove('hidden');
        document.getElementById('confirmForm').action = `/admin/bookings/${bookingSlug}/confirm`;
        document.getElementById('confirmBookingCode').textContent = bookingCode;
    }
    
    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
    }
    
    function confirmDelete(bookingSlug, bookingCode) {
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteForm').action = `/admin/bookings/${bookingSlug}`;
        document.getElementById('deleteBookingCode').textContent = bookingCode;
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
    
    // Close modals when clicking outside
    document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeConfirmModal();
        }
    });
    
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
@endpush

<!-- Manual Booking Modal -->
<div id="manualBookingModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-[9999] hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[95vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-semibold text-gray-800">
                        <i class="fas fa-plus-circle text-green-600 mr-2"></i>
                        Tambah Booking Manual
                    </h3>
                    <button onclick="closeManualBookingModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="manualBookingForm">
                    @csrf
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        
                        <!-- Left Column: User & Booking Info -->
                        <div class="space-y-6">
                            <!-- User Selection -->
                            <div class="bg-blue-50 rounded-xl p-6">
                                <h4 class="text-lg font-semibold text-blue-800 mb-4">
                                    <i class="fas fa-user mr-2"></i>Informasi Penyewa
                                </h4>
                                
                                <div class="relative">
                                    <input type="text" id="userSearch" placeholder="Ketik untuk mencari nama atau email user..." 
                                           class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-2"
                                           autocomplete="off">
                                    <div id="searchLoading" class="hidden absolute right-3 top-2">
                                        <i class="fas fa-spinner fa-spin text-blue-500"></i>
                                    </div>
                                </div>
                                
                                <div id="userResults" class="hidden mb-2 max-h-60 overflow-y-auto border border-gray-300 rounded-lg bg-white shadow-lg">
                                    <!-- Search results will appear here -->
                                </div>
                                
                                <div id="selectedUserDisplay" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-lg mb-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold mr-3">
                                                <span id="selectedUserInitial">U</span>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900" id="selectedUserName">-</div>
                                                <div class="text-sm text-gray-600" id="selectedUserEmail">-</div>
                                                <div class="text-xs text-gray-500" id="selectedUserPhone">-</div>
                                            </div>
                                        </div>
                                        <button type="button" onclick="clearUserSelection()" 
                                                class="text-red-600 hover:text-red-800 px-2 py-1 rounded">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <input type="hidden" id="userIdInput" name="user_id" required>
                                
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Ketik minimal 2 karakter untuk memulai pencarian
                                </p>
                            </div>
                            
                            <!-- Team Details -->
                            <div class="bg-amber-50 rounded-xl p-6">
                                <h4 class="text-lg font-semibold text-amber-800 mb-4">
                                    <i class="fas fa-users mr-2"></i>Detail Booking
                                </h4>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Tim/Instansi/Individu *</label>
                                        <input type="text" name="team_name" required 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                               placeholder="Masukkan nama tim atau instansi">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Penanggung Jawab</label>
                                        <input type="text" name="contact_person" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                               placeholder="Nama penanggung jawab">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                                        <textarea name="notes" rows="3" 
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                                  placeholder="Catatan tambahan..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column: Schedule Selection -->
                        <div class="space-y-6">
                            <!-- Sport & Court Selection -->
                            <div class="bg-green-50 rounded-xl p-6">
                                <h4 class="text-lg font-semibold text-green-800 mb-4">
                                    <i class="fas fa-map-marker-alt mr-2"></i>Pilih Fasilitas
                                </h4>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Olahraga *</label>
                                        <select name="sport_id" id="sportSelect" required onchange="loadCourtsForBooking(this.value)"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                            <option value="">Pilih olahraga...</option>
                                            @foreach($sports as $sport)
                                                <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Lapangan *</label>
                                        <select name="court_id" id="courtSelect" required disabled
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                            <option value="">Pilih olahraga terlebih dahulu</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Date & Time Selection -->
                            <div class="bg-purple-50 rounded-xl p-6">
                                <h4 class="text-lg font-semibold text-purple-800 mb-4">
                                    <i class="fas fa-calendar-alt mr-2"></i>Pilih Jadwal
                                </h4>
                                
                                <!-- Date Selection (Horizontal Scroll like user page) -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Tanggal *</label>
                                    <div class="overflow-x-auto pb-2" id="dateSelection">
                                        <div class="flex space-x-2 min-w-max">
                                            @for($i = 0; $i < 14; $i++)
                                                @php
                                                    $date = now()->addDays($i);
                                                    $isToday = $date->isToday();
                                                    $isWeekend = in_array($date->dayOfWeek, [0, 5, 6]);
                                                @endphp
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="date" value="{{ $date->format('Y-m-d') }}" 
                                                           class="hidden" {{ $isToday ? 'checked' : '' }}>
                                                    <div class="date-card w-20 h-24 border-2 rounded-lg p-2 text-center transition-all duration-200 
                                                                {{ $isToday ? 'border-purple-500 bg-purple-100' : 'border-gray-200 bg-white hover:border-purple-300' }}">
                                                        <div class="text-xs text-gray-500 mb-1">
                                                            {{ $date->format('D') }}
                                                        </div>
                                                        <div class="text-lg font-bold text-gray-800">
                                                            {{ $date->format('j') }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $date->format('M') }}
                                                        </div>
                                                        @if($isWeekend)
                                                            <div class="text-xs text-orange-600 font-medium mt-1">Weekend</div>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Time Selection -->
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Mulai *</label>
                                        <select name="start_time" id="startTimeSelect" required onchange="updateEndTimeOptions()"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                            <option value="">Pilih waktu mulai...</option>
                                            @for($hour = 6; $hour <= 23; $hour++)
                                                <option value="{{ sprintf('%02d:00', $hour) }}">
                                                    {{ sprintf('%02d:00', $hour) }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Selesai *</label>
                                        <select name="end_time" id="endTimeSelect" required disabled
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                            <option value="">Pilih waktu mulai terlebih dahulu</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Price Preview -->
                                <div class="bg-white rounded-lg p-4 border border-purple-200">
                                    <div class="text-sm text-gray-600 mb-2">Estimasi Harga:</div>
                                    <div id="pricePreview" class="text-xl font-bold text-purple-800">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                        <button type="button" onclick="closeManualBookingModal()" 
                                class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            <i class="fas fa-times mr-2"></i>Batal
                        </button>
                        <button type="submit" id="submitBookingBtn" disabled
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-save mr-2"></i>Simpan Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Manual Booking Modal Functions
let selectedUser = null;
let searchTimeout = null;

function openManualBookingModal() {
    document.getElementById('manualBookingModal').classList.remove('hidden');
    resetForm();
}

function closeManualBookingModal() {
    document.getElementById('manualBookingModal').classList.add('hidden');
}

function resetForm() {
    document.getElementById('manualBookingForm').reset();
    
    // Reset user selection
    clearUserSelection();
    document.getElementById('userSearch').value = '';
    document.getElementById('userResults').classList.add('hidden');
    
    // Reset court select
    const courtSelect = document.getElementById('courtSelect');
    courtSelect.innerHTML = '<option value="">Pilih olahraga terlebih dahulu</option>';
    courtSelect.disabled = true;
    
    // Reset end time select
    const endTimeSelect = document.getElementById('endTimeSelect');
    endTimeSelect.innerHTML = '<option value="">Pilih waktu mulai terlebih dahulu</option>';
    endTimeSelect.disabled = true;
    
    // Reset price preview
    document.getElementById('pricePreview').textContent = '-';
    
    // Reset submit button
    document.getElementById('submitBookingBtn').disabled = true;
}

// Real-time search dengan debounce
document.getElementById('userSearch').addEventListener('input', function() {
    const searchTerm = this.value.trim();
    
    // Clear previous timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    // Hide results if search term is too short
    if (searchTerm.length < 2) {
        document.getElementById('userResults').classList.add('hidden');
        document.getElementById('searchLoading').classList.add('hidden');
        return;
    }
    
    // Show loading indicator
    document.getElementById('searchLoading').classList.remove('hidden');
    
    // Debounce search (wait 300ms after user stops typing)
    searchTimeout = setTimeout(() => {
        searchUsers(searchTerm);
    }, 300);
});

function searchUsers(searchTerm) {
    fetch(`{{ route("admin.bookings.users") }}?search=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            displayUserResults(data.users || []);
            document.getElementById('searchLoading').classList.add('hidden');
        })
        .catch(error => {
            console.error('Error searching users:', error);
            document.getElementById('searchLoading').classList.add('hidden');
            document.getElementById('userResults').innerHTML = 
                '<div class="p-4 text-center text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>Error loading users</div>';
            document.getElementById('userResults').classList.remove('hidden');
        });
}

function displayUserResults(users) {
    const resultsContainer = document.getElementById('userResults');
    
    if (users.length === 0) {
        resultsContainer.innerHTML = 
            '<div class="p-4 text-center text-gray-500"><i class="fas fa-search mr-2"></i>Tidak ada user ditemukan</div>';
        resultsContainer.classList.remove('hidden');
        return;
    }
    
    let html = '<div class="divide-y divide-gray-200">';
    users.forEach(user => {
        const initial = user.name.charAt(0).toUpperCase();
        html += `
            <div class="p-3 hover:bg-blue-50 cursor-pointer transition-colors" 
                 onclick='selectUser(${JSON.stringify(user)})'>
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-sm mr-3">
                        ${initial}
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">${user.name}</div>
                        <div class="text-sm text-gray-600">${user.email}</div>
                        ${user.phone ? `<div class="text-xs text-gray-500">${user.phone}</div>` : ''}
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    resultsContainer.innerHTML = html;
    resultsContainer.classList.remove('hidden');
}

function selectUser(user) {
    selectedUser = user;
    
    // Hide search results
    document.getElementById('userResults').classList.add('hidden');
    document.getElementById('userSearch').value = '';
    
    // Update hidden input
    document.getElementById('userIdInput').value = user.id;
    
    // Display selected user
    const initial = user.name.charAt(0).toUpperCase();
    document.getElementById('selectedUserInitial').textContent = initial;
    document.getElementById('selectedUserName').textContent = user.name;
    document.getElementById('selectedUserEmail').textContent = user.email;
    document.getElementById('selectedUserPhone').textContent = user.phone || 'No phone';
    document.getElementById('selectedUserDisplay').classList.remove('hidden');
    
    checkFormValidity();
}

function clearUserSelection() {
    selectedUser = null;
    document.getElementById('userIdInput').value = '';
    document.getElementById('selectedUserDisplay').classList.add('hidden');
    document.getElementById('userSearch').value = '';
    document.getElementById('userResults').classList.add('hidden');
    checkFormValidity();
}

// Close search results when clicking outside
document.addEventListener('click', function(e) {
    const userSearch = document.getElementById('userSearch');
    const userResults = document.getElementById('userResults');
    const selectedUserDisplay = document.getElementById('selectedUserDisplay');
    
    if (userSearch && userResults && 
        !userSearch.contains(e.target) && 
        !userResults.contains(e.target) &&
        !selectedUserDisplay.contains(e.target)) {
        userResults.classList.add('hidden');
    }
});

// Load courts based on sport selection
async function loadCourtsForBooking(sportId) {
    const courtSelect = document.getElementById('courtSelect');
    
    if (!sportId) {
        courtSelect.innerHTML = '<option value="">Pilih olahraga terlebih dahulu</option>';
        courtSelect.disabled = true;
        return;
    }
    
    try {
        const response = await fetch(`/admin/bookings/courts-by-sport/${sportId}`);
        const data = await response.json();
        
        courtSelect.innerHTML = '<option value="">Pilih lapangan...</option>';
        data.courts.forEach(court => {
            const option = document.createElement('option');
            option.value = court.id;
            option.textContent = court.name;
            courtSelect.appendChild(option);
        });
        courtSelect.disabled = false;
        checkFormValidity();
    } catch (error) {
        console.error('Error loading courts:', error);
        courtSelect.innerHTML = '<option value="">Error loading courts</option>';
    }
}

// Update end time options based on start time
function updateEndTimeOptions() {
    const startTime = document.getElementById('startTimeSelect').value;
    const endTimeSelect = document.getElementById('endTimeSelect');
    
    if (!startTime) {
        endTimeSelect.innerHTML = '<option value="">Pilih waktu mulai terlebih dahulu</option>';
        endTimeSelect.disabled = true;
        return;
    }
    
    const startHour = parseInt(startTime.split(':')[0]);
    endTimeSelect.innerHTML = '<option value="">Pilih waktu selesai...</option>';
    
    // Generate end time options (1-8 hours after start time, max 24:00)
    for (let i = 1; i <= 8; i++) {
        const endHour = startHour + i;
        if (endHour <= 24) {
            const endTimeValue = sprintf('%02d:00', endHour);
            const option = document.createElement('option');
            option.value = endTimeValue;
            option.textContent = endTimeValue + ` (${i} jam)`;
            endTimeSelect.appendChild(option);
        }
    }
    
    endTimeSelect.disabled = false;
    checkFormValidity();
}

// Helper function for zero padding
function sprintf(format, number) {
    return format.replace('%02d', number.toString().padStart(2, '0'));
}

// Date selection handling
document.querySelectorAll('input[name="date"]').forEach(input => {
    input.addEventListener('change', function() {
        // Update visual selection
        document.querySelectorAll('.date-card').forEach(card => {
            card.classList.remove('border-purple-500', 'bg-purple-100');
            card.classList.add('border-gray-200', 'bg-white');
        });
        
        this.nextElementSibling.classList.remove('border-gray-200', 'bg-white');
        this.nextElementSibling.classList.add('border-purple-500', 'bg-purple-100');
        
        calculatePrice();
        checkFormValidity();
    });
});

// Price calculation
function calculatePrice() {
    const startTime = document.getElementById('startTimeSelect').value;
    const endTime = document.getElementById('endTimeSelect').value;
    const selectedDate = document.querySelector('input[name="date"]:checked')?.value;
    const courtId = document.getElementById('courtSelect').value;
    const sportId = document.getElementById('sportSelect').value;
    
    if (!startTime || !endTime || !selectedDate || !courtId || !sportId) {
        document.getElementById('pricePreview').textContent = '-';
        return;
    }
    
    // Calculate duration
    const startHour = parseInt(startTime.split(':')[0]);
    const endHour = parseInt(endTime.split(':')[0]);
    const duration = endHour - startHour;
    
    // Check if weekend (Friday, Saturday, Sunday)
    const dateObj = new Date(selectedDate);
    const isWeekend = [0, 5, 6].includes(dateObj.getDay());
    
    // Get sport info for pricing
    const sportSelect = document.getElementById('sportSelect');
    const sportName = sportSelect.options[sportSelect.selectedIndex].text;
    
    let basePrice = 0;
    
    // Price calculation based on price list image
    if (sportName === 'Futsal' || sportName === 'Basket') {
        if (startHour >= 8 && startHour < 12) {
            // Morning: 08:00-12:00
            basePrice = isWeekend ? 65000 : 60000;
        } else if (startHour >= 12 && startHour < 18) {
            // Afternoon: 12:00-18:00  
            basePrice = isWeekend ? 85000 : 80000;
        } else {
            // Evening: 18:00-00:00
            basePrice = isWeekend ? 105000 : 100000;
        }
    } else if (sportName === 'Badminton') {
        if (startHour >= 8 && startHour < 12) {
            // Morning: 08:00-12:00
            basePrice = isWeekend ? 35000 : 30000;
        } else if (startHour >= 12 && startHour < 18) {
            // Afternoon: 12:00-18:00
            basePrice = isWeekend ? 40000 : 35000;
        } else {
            // Evening: 18:00-00:00
            basePrice = isWeekend ? 45000 : 40000;
        }
    } else if (sportName === 'Voli') {
        if (startHour >= 8 && startHour < 12) {
            // Morning: 08:00-12:00
            basePrice = isWeekend ? 55000 : 50000;
        } else if (startHour >= 12 && startHour < 18) {
            // Afternoon: 12:00-18:00
            basePrice = isWeekend ? 65000 : 60000;
        } else {
            // Evening: 18:00-00:00
            basePrice = isWeekend ? 75000 : 70000;
        }
    }
    
    const totalPrice = basePrice * duration;
    
    document.getElementById('pricePreview').textContent = 
        'Rp ' + totalPrice.toLocaleString('id-ID') + ` (${duration} jam)`;
}

// End time change handler
document.getElementById('endTimeSelect').addEventListener('change', function() {
    calculatePrice();
    checkFormValidity();
});

// Form validation
function checkFormValidity() {
    const teamName = document.querySelector('input[name="team_name"]').value;
    const sportId = document.getElementById('sportSelect').value;
    const courtId = document.getElementById('courtSelect').value;
    const selectedDate = document.querySelector('input[name="date"]:checked');
    const startTime = document.getElementById('startTimeSelect').value;
    const endTime = document.getElementById('endTimeSelect').value;
    
    // User must be selected
    const userValid = selectedUser !== null && document.getElementById('userIdInput').value !== '';
    
    const isValid = userValid && teamName && sportId && courtId && selectedDate && startTime && endTime;
    
    document.getElementById('submitBookingBtn').disabled = !isValid;
}

// Add event listeners for form validation
document.querySelector('input[name="team_name"]').addEventListener('input', checkFormValidity);
document.getElementById('sportSelect').addEventListener('change', function() {
    checkFormValidity();
    calculatePrice(); // Recalculate price when sport changes
});
document.getElementById('courtSelect').addEventListener('change', function() {
    checkFormValidity();
    calculatePrice(); // Recalculate price when court changes
});
document.getElementById('startTimeSelect').addEventListener('change', checkFormValidity);

// Form submission
document.getElementById('manualBookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = document.getElementById('submitBookingBtn');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    
    fetch('{{ route("admin.bookings.manual.store") }}', {
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
                text: 'Booking manual berhasil dibuat',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                closeManualBookingModal();
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message || 'Terjadi kesalahan saat membuat booking',
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

// Close modal when clicking outside
document.getElementById('manualBookingModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeManualBookingModal();
    }
});

// Initialize first date as selected
document.addEventListener('DOMContentLoaded', function() {
    // Auto-select today's date
    const todayInput = document.querySelector('input[name="date"]:checked');
    if (todayInput) {
        calculatePrice();
        checkFormValidity();
    }
});
</script>