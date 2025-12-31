@extends('admin.layouts.admin')

@section('title', 'Detail Booking')
@section('page-title', 'Detail Booking')
@section('page-description', 'Informasi lengkap booking ' . $booking->booking_code)

@section('content')
<!-- Back Button -->
<div class="mb-6">
    <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Booking
    </a>
</div>

<!-- Booking Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Main Booking Info -->
    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-info-circle mr-2 text-amber-600"></i>
                Informasi Booking
            </h3>
            
            <!-- Status Badge -->
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                @elseif($booking->status === 'pending_payment') bg-yellow-100 text-yellow-800
                @elseif($booking->status === 'completed') bg-blue-100 text-blue-800
                @elseif($booking->status === 'paid') bg-purple-100 text-purple-800
                @else bg-red-100 text-red-800
                @endif">
                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
            </span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Kode Booking</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $booking->booking_code }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Booking</label>
                    <p class="text-lg text-gray-900">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->locale('id')->translatedFormat('l, d F Y') }}
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Waktu</label>
                    <p class="text-lg text-gray-900">
                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} WIB
                        <span class="text-sm text-gray-500">
                            ({{ \Carbon\Carbon::parse($booking->start_time)->diffInHours(\Carbon\Carbon::parse($booking->end_time)) }} jam)
                        </span>
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Olahraga</label>
                    <p class="text-lg text-gray-900">{{ $booking->sport->name ?? 'N/A' }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Lapangan</label>
                    <p class="text-lg text-gray-900">{{ $booking->court->name ?? 'N/A' }}</p>
                    @if($booking->court && $booking->court->physical_location)
                        <p class="text-sm text-gray-500">{{ $booking->court->physical_location }}</p>
                    @endif
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Nama Tim/Pemain</label>
                    <p class="text-lg text-gray-900">{{ $booking->team_name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Total Harga</label>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Metode Pembayaran</label>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($booking->payment_method === 'cash') bg-green-100 text-green-800
                        @else bg-blue-100 text-blue-800
                        @endif">
                        @if($booking->payment_method === 'cash')
                            <i class="fas fa-money-bill mr-2"></i> Tunai
                        @else
                            <i class="fas fa-credit-card mr-2"></i> Online Payment
                        @endif
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Dibuat</label>
                    <p class="text-lg text-gray-900">{{ $booking->created_at->format('d M Y, H:i') }}</p>
                </div>
                
                @if($booking->confirmed_at)
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Dikonfirmasi</label>
                    <p class="text-lg text-gray-900">{{ $booking->confirmed_at->format('d M Y, H:i') }}</p>
                </div>
                @endif
                
                @if($booking->paid_at)
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Dibayar</label>
                    <p class="text-lg text-gray-900">{{ $booking->paid_at->format('d M Y, H:i') }}</p>
                </div>
                @endif
            </div>
        </div>
        
        @if($booking->notes)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <label class="block text-sm font-medium text-gray-500 mb-2">Catatan Customer</label>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-700">{{ $booking->notes }}</p>
            </div>
        </div>
        @endif
        
        @if($booking->admin_notes)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <label class="block text-sm font-medium text-gray-500 mb-2">Catatan Admin</label>
            <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                <p class="text-amber-800">{{ $booking->admin_notes }}</p>
            </div>
        </div>
        @endif
    </div>
    
    <!-- User Info & Actions -->
    <div class="space-y-6">
        <!-- Customer Info -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-user mr-2 text-amber-600"></i>
                Informasi Customer
            </h3>
            
            <div class="flex items-center mb-4">
                @if($booking->user && $booking->user->avatar)
                    <img src="{{ $booking->user->avatar }}" alt="User" class="h-16 w-16 rounded-full object-cover border-2 border-amber-400 mr-4">
                @else
                    <div class="h-16 w-16 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold text-xl mr-4">
                        {{ $booking->user ? strtoupper(substr($booking->user->name, 0, 1)) : 'U' }}
                    </div>
                @endif
                <div>
                    <h4 class="text-lg font-semibold text-gray-900">{{ $booking->user->name ?? 'N/A' }}</h4>
                    <p class="text-gray-600">{{ $booking->user->email ?? 'N/A' }}</p>
                    @if($booking->user && $booking->user->phone_number)
                        <p class="text-gray-600">
                            <i class="fas fa-phone mr-1"></i>{{ $booking->user->phone_number }}
                        </p>
                    @endif
                </div>
            </div>
            
            @if($booking->user)
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Bergabung:</span>
                    <span class="text-sm text-gray-900">{{ $booking->user->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Total Booking:</span>
                    <span class="text-sm text-gray-900">{{ $booking->user->bookings()->count() }} kali</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Status:</span>
                    <span class="text-sm font-medium
                        @if($booking->user->status === 'active') text-green-600
                        @else text-red-600
                        @endif">
                        {{ ucfirst($booking->user->status ?? 'Unknown') }}
                    </span>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Actions -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-cogs mr-2 text-amber-600"></i>
                Aksi
            </h3>
            
            <div class="space-y-3">
                <!-- Hubungi Pemesan -->
                @if($booking->user && $booking->user->phone_number)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $booking->user->phone_number) }}?text=Halo%20{{ urlencode($booking->user->name) }},%20mengenai%20booking%20{{ urlencode($booking->booking_code) }}" 
                       target="_blank"
                       class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                        <i class="fab fa-whatsapp mr-2"></i>Hubungi Pemesan
                    </a>
                @endif
                
                <!-- Cancel Booking - Only before start time and not already cancelled/completed -->
                @php
                    $bookingDate = \Carbon\Carbon::parse($booking->booking_date);
                    $bookingDateTime = $bookingDate->setTimeFromTimeString($booking->start_time);
                    $canCancel = $bookingDateTime->isFuture() && !in_array($booking->status, ['cancelled', 'completed']);
                @endphp
                
                @if($canCancel)
                    <button onclick="confirmCancelBooking('{{ $booking->slug }}', '{{ $booking->booking_code }}')" 
                            class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                        <i class="fas fa-ban mr-2"></i>Batalkan Booking
                    </button>
                @else
                    @if(!in_array($booking->status, ['cancelled', 'completed']))
                        <div class="w-full px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed text-center">
                            <i class="fas fa-ban mr-2"></i>Tidak Dapat Dibatalkan (Sudah Lewat Waktu)
                        </div>
                    @endif
                @endif
                
                <!-- Print/Download -->
                <button onclick="window.print()" 
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-print mr-2"></i>Print Detail
                </button>
                
                <!-- Complete Booking -->
                @if(!in_array($booking->status, ['completed', 'cancelled']))
                    <button onclick="confirmCompleteBooking()" 
                            class="w-full px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        <i class="fas fa-check-circle mr-2"></i>Complete Booking
                    </button>
                @else
                    <div class="w-full px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed text-center">
                        <i class="fas fa-check-circle mr-2"></i>{{ $booking->status === 'completed' ? 'Sudah Selesai' : 'Booking Dibatalkan' }}
                    </div>
                @endif
                
                
                <!-- Delete - Only if booking date + time hasn't passed -->
                    @php
                        $bookingDate = \Carbon\Carbon::parse($booking->booking_date);
                        $bookingDateTime = $bookingDate->setTimeFromTimeString($booking->start_time);
                        $canDelete = $bookingDateTime->isFuture();
                    @endphp                @if($canDelete)
                    <button onclick="confirmDelete('{{ $booking->slug }}', '{{ $booking->booking_code }}')" 
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Hapus Booking
                    </button>
                @else
                    <div class="w-full px-4 py-2 bg-gray-400 text-white rounded-lg cursor-not-allowed text-center">
                        <i class="fas fa-trash mr-2"></i>Tidak Dapat Dihapus (Sudah Lewat Waktu)
                    </div>
                @endif
            </div>
        </div>
        
        @if($booking->payment_method === 'midtrans')
        <!-- Payment Info -->
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-credit-card mr-2 text-amber-600"></i>
                Informasi Pembayaran
            </h3>
            
            <div class="space-y-2">
                @if($booking->midtrans_order_id)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Order ID:</span>
                    <span class="text-sm text-gray-900 font-mono">{{ $booking->midtrans_order_id }}</span>
                </div>
                @endif
                
                @if($booking->midtrans_snap_token)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Snap Token:</span>
                    <span class="text-sm text-gray-900 font-mono">{{ substr($booking->midtrans_snap_token, 0, 10) }}...</span>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Include delete modal -->
@include('admin.bookings.partials.delete-modal')

<!-- Cancel Booking Modal -->
<div id="cancelBookingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Konfirmasi Pembatalan</h3>
                <button onclick="closeCancelBookingModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin membatalkan booking <span id="cancelBookingCode" class="font-semibold"></span>? Status akan diubah menjadi "Cancelled".</p>
            
            <form id="cancelBookingForm" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCancelBookingModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                        <i class="fas fa-ban mr-1"></i> Batalkan Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Booking Modal -->
<div id="completeBookingModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-check-circle text-green-600 mr-3 text-2xl"></i>
                    <h3 class="text-lg font-semibold text-gray-800">Konfirmasi Complete Booking</h3>
                </div>
                
                <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menyelesaikan booking <span class="font-semibold text-amber-600">{{ $booking->booking_code }}</span>? Status akan diubah menjadi "Completed".</p>
                
                <form id="completeBookingForm" method="POST" action="{{ route('admin.bookings.updateStatus', $booking) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeCompleteBookingModal()" 
                                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>Complete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function confirmDelete(bookingSlug, bookingCode) {
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteForm').action = `/admin/bookings/${bookingSlug}`;
        document.getElementById('deleteBookingCode').textContent = bookingCode;
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
    
    function confirmCancelBooking(bookingSlug, bookingCode) {
        document.getElementById('cancelBookingModal').classList.remove('hidden');
        document.getElementById('cancelBookingForm').action = `/admin/bookings/${bookingSlug}/cancel`;
        document.getElementById('cancelBookingCode').textContent = bookingCode;
    }
    
    function closeCancelBookingModal() {
        document.getElementById('cancelBookingModal').classList.add('hidden');
    }
    
    function confirmCompleteBooking() {
        document.getElementById('completeBookingModal').classList.remove('hidden');
    }
    
    function closeCompleteBookingModal() {
        document.getElementById('completeBookingModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
    
    document.getElementById('cancelBookingModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCancelBookingModal();
        }
    });
    
    document.getElementById('completeBookingModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCompleteBookingModal();
        }
    });
</script>
</script>
@endpush

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            font-size: 12px;
        }
        
        .shadow-lg {
            box-shadow: none !important;
        }
    }
</style>
@endpush