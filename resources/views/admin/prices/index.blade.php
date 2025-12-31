@extends('admin.layouts.admin')

@section('title', 'Manajemen Harga')
@section('page-title', 'Manajemen Harga')
@section('page-description', 'Kelola harga lapangan berdasarkan waktu dan hari')

@section('content')
<!-- Info Card -->
<div class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-4">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-amber-500 text-xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-amber-800">Informasi Harga</h3>
            <div class="mt-2 text-sm text-amber-700">
                <ul class="list-disc list-inside space-y-1">
                    <li><strong>Pagi:</strong> 08:00 - 12:00</li>
                    <li><strong>Siang:</strong> 12:00 - 18:00</li>
                    <li><strong>Malam:</strong> 18:00 - 00:00</li>
                    <li><strong>Weekend:</strong> Jumat, Sabtu, Minggu</li>
                    <li><strong>Weekday:</strong> Senin - Kamis</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Price Management Form -->
<form action="{{ route('admin.prices.updateBulk') }}" method="POST">
    @csrf
    @method('PATCH')
    
    <div class="space-y-6">
        @foreach($sports as $sport)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Sport Header -->
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
                <div class="flex items-center">
                    <div class="h-12 w-12 rounded-full bg-white/20 flex items-center justify-center mr-4">
                        <i class="{{ $sport->icon ?? 'fas fa-futbol' }} text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $sport->name }}</h3>
                        <p class="text-amber-100 text-sm">Harga per jam</p>
                    </div>
                </div>
            </div>
            
            <!-- Price Table -->
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Waktu</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Jam</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-briefcase mr-2 text-blue-500"></i>
                                        Weekday (Sen-Kam)
                                    </span>
                                </th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-sun mr-2 text-orange-500"></i>
                                        Weekend (Jum-Min)
                                    </span>
                                </th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timeSlots as $slotKey => $slot)
                                @php
                                    $existingPrice = $sport->prices->where('time_slot', $slotKey)->first();
                                    $index = $loop->parent->index * 3 + $loop->index;
                                @endphp
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center">
                                            @if($slotKey === 'morning')
                                                <span class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                                                    <i class="fas fa-sun text-yellow-500"></i>
                                                </span>
                                            @elseif($slotKey === 'afternoon')
                                                <span class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center mr-3">
                                                    <i class="fas fa-cloud-sun text-orange-500"></i>
                                                </span>
                                            @else
                                                <span class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                                    <i class="fas fa-moon text-indigo-500"></i>
                                                </span>
                                            @endif
                                            <span class="font-medium text-gray-900">
                                                {{ $slotKey === 'morning' ? 'Pagi' : ($slotKey === 'afternoon' ? 'Siang' : 'Malam') }}
                                            </span>
                                        </div>
                                        <input type="hidden" name="prices[{{ $index }}][sport_id]" value="{{ $sport->id }}">
                                        <input type="hidden" name="prices[{{ $index }}][time_slot]" value="{{ $slotKey }}">
                                    </td>
                                    <td class="py-4 px-4 text-gray-600">
                                        {{ $slot['start'] }} - {{ $slot['end'] }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                            <input type="number" 
                                                   name="prices[{{ $index }}][weekday_price]" 
                                                   value="{{ $existingPrice ? (int)$existingPrice->weekday_price : (int)$sport->price_per_hour }}"
                                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                                   min="0"
                                                   step="1000">
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                            <input type="number" 
                                                   name="prices[{{ $index }}][weekend_price]" 
                                                   value="{{ $existingPrice ? (int)$existingPrice->weekend_price : (int)$sport->price_per_hour }}"
                                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                                   min="0"
                                                   step="1000">
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if($existingPrice)
                                            <button type="button" 
                                                    onclick="togglePriceStatus({{ $existingPrice->id }}, this)"
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium transition-colors
                                                        {{ $existingPrice->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                                <i class="fas {{ $existingPrice->is_active ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                                {{ $existingPrice->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </button>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                                                <i class="fas fa-plus-circle mr-1"></i>
                                                Baru
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Default Price Info -->
                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1 text-gray-400"></i>
                        Harga default (jika tidak ada setting): <strong>Rp {{ number_format($sport->price_per_hour, 0, ',', '.') }}</strong> per jam
                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Submit Button -->
    <div class="mt-6 flex justify-end">
        <button type="submit" 
                class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-semibold rounded-lg shadow-lg hover:from-amber-600 hover:to-orange-600 transition-all duration-200 flex items-center">
            <i class="fas fa-save mr-2"></i>
            Simpan Semua Perubahan
        </button>
    </div>
</form>

<!-- Quick Add Modal -->
<div id="quickAddModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-plus-circle text-amber-500 mr-2"></i>
                        Tambah Harga Baru
                    </h3>
                    <button onclick="closeQuickAddModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form action="{{ route('admin.prices.store') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Olahraga</label>
                            <select name="sport_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500" required>
                                <option value="">Pilih Olahraga</option>
                                @foreach($sports as $sport)
                                    <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Waktu</label>
                            <select name="time_slot" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500" required>
                                <option value="">Pilih Waktu</option>
                                @foreach($timeSlots as $key => $slot)
                                    <option value="{{ $key }}">{{ $slot['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Weekday</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                <input type="number" name="weekday_price" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500" required min="0" step="1000">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Weekend</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                <input type="number" name="weekend_price" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500" required min="0" step="1000">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex space-x-3">
                        <button type="button" onclick="closeQuickAddModal()" 
                                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors">
                            <i class="fas fa-save mr-2"></i>Simpan
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
    function openQuickAddModal() {
        document.getElementById('quickAddModal').classList.remove('hidden');
    }
    
    function closeQuickAddModal() {
        document.getElementById('quickAddModal').classList.add('hidden');
    }
    
    function togglePriceStatus(priceId, button) {
        fetch(`/admin/prices/${priceId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.is_active) {
                    button.classList.remove('bg-red-100', 'text-red-800', 'hover:bg-red-200');
                    button.classList.add('bg-green-100', 'text-green-800', 'hover:bg-green-200');
                    button.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Aktif';
                } else {
                    button.classList.remove('bg-green-100', 'text-green-800', 'hover:bg-green-200');
                    button.classList.add('bg-red-100', 'text-red-800', 'hover:bg-red-200');
                    button.innerHTML = '<i class="fas fa-times-circle mr-1"></i>Nonaktif';
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat mengubah status.'
            });
        });
    }
    
    // Close modal when clicking outside
    document.getElementById('quickAddModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeQuickAddModal();
        }
    });
</script>
@endpush
