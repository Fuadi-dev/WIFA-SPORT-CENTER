@extends('admin.layouts.admin')

@section('title', 'Kelola Promo Otomatis')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="px-6 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Promo Otomatis</h1>
                    <p class="mt-2 text-gray-600">Promo berdasarkan tanggal dan waktu (tanpa kode)</p>
                </div>
                
                <div class="mt-4 md:mt-0">
                    <button onclick="openAddModal()" 
                       class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Buat Promo Otomatis
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Promo List -->
    <div class="px-6 py-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($autoPromos->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diskon</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min. Trans</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($autoPromos as $promo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $promo->name }}</div>
                                        @if($promo->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($promo->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($promo->schedule_type === 'specific_date')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                <i class="fas fa-calendar-day mr-1"></i>
                                                {{ \Carbon\Carbon::parse($promo->specific_date)->format('d M Y') }}
                                            </span>
                                        @elseif($promo->schedule_type === 'day_of_week')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <i class="fas fa-calendar-week mr-1"></i>
                                                {{ $promo->days_of_week_string }}
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-calendar mr-1"></i>
                                                Setiap Hari
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            {{ substr($promo->start_time, 0, 5) }} - {{ substr($promo->end_time, 0, 5) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-amber-600">
                                            {{ $promo->discount_type === 'percentage' ? $promo->discount_value . '%' : 'Rp ' . number_format($promo->discount_value, 0, ',', '.') }}
                                        </div>
                                        @if($promo->max_discount)
                                            <div class="text-xs text-gray-500">Max: Rp {{ number_format($promo->max_discount, 0, ',', '.') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            Rp {{ number_format($promo->min_transaction, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" 
                                                   {{ $promo->is_active ? 'checked' : '' }}
                                                   onchange="toggleStatus({{ $promo->id }})">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                                        </label>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <button onclick="openEditModal({{ $promo->id }})" 
                                               class="text-amber-600 hover:text-amber-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="confirmDelete({{ $promo->id }}, '{{ $promo->name }}')" 
                                                    class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $autoPromos->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-white text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Promo Otomatis</h3>
                    <p class="text-gray-500 mb-4">Buat promo otomatis berdasarkan waktu</p>
                    <button onclick="openAddModal()" 
                       class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Buat Promo Otomatis
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="promoModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 id="modalTitle" class="text-xl font-semibold text-gray-800">Buat Promo Otomatis</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="promoForm">
                    @csrf
                    <input type="hidden" id="promoId" name="id">
                    <input type="hidden" id="formMethod" value="POST">
                    
                    <div class="space-y-6">
                        <!-- Basic Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Promo</label>
                                <input type="text" name="name" id="name" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                       placeholder="Contoh: Happy Hour, Weekend Special">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
                                <textarea name="description" id="description" rows="2" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                       placeholder="Deskripsi promo..."></textarea>
                            </div>
                        </div>
                        
                        <!-- Schedule Type -->
                        <div class="border-t pt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Tipe Jadwal</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="schedule_type" value="specific_date" 
                                           class="peer sr-only" onchange="updateScheduleFields()">
                                    <div class="p-4 border-2 border-gray-300 rounded-lg peer-checked:border-amber-600 peer-checked:bg-amber-50 hover:border-amber-400 transition-all">
                                        <i class="fas fa-calendar-day text-2xl text-gray-500 peer-checked:text-amber-600 mb-2"></i>
                                        <div class="text-sm font-medium text-gray-700">Tanggal Spesifik</div>
                                    </div>
                                </label>
                                
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="schedule_type" value="day_of_week" 
                                           class="peer sr-only" onchange="updateScheduleFields()" checked>
                                    <div class="p-4 border-2 border-gray-300 rounded-lg peer-checked:border-amber-600 peer-checked:bg-amber-50 hover:border-amber-400 transition-all">
                                        <i class="fas fa-calendar-week text-2xl text-gray-500 peer-checked:text-amber-600 mb-2"></i>
                                        <div class="text-sm font-medium text-gray-700">Hari Tertentu</div>
                                    </div>
                                </label>
                                
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="schedule_type" value="daily" 
                                           class="peer sr-only" onchange="updateScheduleFields()">
                                    <div class="p-4 border-2 border-gray-300 rounded-lg peer-checked:border-amber-600 peer-checked:bg-amber-50 hover:border-amber-400 transition-all">
                                        <i class="fas fa-calendar text-2xl text-gray-500 peer-checked:text-amber-600 mb-2"></i>
                                        <div class="text-sm font-medium text-gray-700">Setiap Hari</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Specific Date Field -->
                        <div id="specificDateField" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Tanggal</label>
                            <input type="date" name="specific_date" id="specific_date"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <!-- Day of Week Field -->
                        <div id="dayOfWeekField">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Hari</label>
                            <div class="grid grid-cols-4 md:grid-cols-7 gap-2">
                                <label class="flex items-center justify-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-amber-400 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50">
                                    <input type="checkbox" name="days_of_week[]" value="0" class="sr-only">
                                    <span class="text-sm font-medium">Min</span>
                                </label>
                                <label class="flex items-center justify-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-amber-400 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50">
                                    <input type="checkbox" name="days_of_week[]" value="1" class="sr-only">
                                    <span class="text-sm font-medium">Sen</span>
                                </label>
                                <label class="flex items-center justify-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-amber-400 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50">
                                    <input type="checkbox" name="days_of_week[]" value="2" class="sr-only">
                                    <span class="text-sm font-medium">Sel</span>
                                </label>
                                <label class="flex items-center justify-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-amber-400 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50">
                                    <input type="checkbox" name="days_of_week[]" value="3" class="sr-only">
                                    <span class="text-sm font-medium">Rab</span>
                                </label>
                                <label class="flex items-center justify-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-amber-400 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50">
                                    <input type="checkbox" name="days_of_week[]" value="4" class="sr-only">
                                    <span class="text-sm font-medium">Kam</span>
                                </label>
                                <label class="flex items-center justify-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-amber-400 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50">
                                    <input type="checkbox" name="days_of_week[]" value="5" class="sr-only">
                                    <span class="text-sm font-medium">Jum</span>
                                </label>
                                <label class="flex items-center justify-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-amber-400 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50">
                                    <input type="checkbox" name="days_of_week[]" value="6" class="sr-only">
                                    <span class="text-sm font-medium">Sab</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Time Range -->
                        <div class="grid grid-cols-2 gap-4 border-t pt-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai</label>
                                <input type="time" name="start_time" id="start_time" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai</label>
                                <input type="time" name="end_time" id="end_time" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                        
                        <!-- Discount Settings -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Diskon</label>
                                <select name="discount_type" id="discount_type" required onchange="updateDiscountLabel()"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                    <option value="percentage">Persentase (%)</option>
                                    <option value="fixed">Nominal (Rp)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" id="discountLabel">Nilai Diskon (%)</label>
                                <input type="number" name="discount_value" id="discount_value" required min="0" step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Min. Transaksi (Rp)</label>
                                <input type="number" name="min_transaction" id="min_transaction" required min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Max. Diskon (Rp, Opsional)</label>
                                <input type="number" name="max_discount" id="max_discount" min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                        
                        <!-- Validity Period (hidden for specific_date) -->
                        <div id="validityPeriodField" class="grid grid-cols-2 gap-4 border-t pt-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Berlaku Dari</label>
                                <input type="date" name="valid_from" id="valid_from"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Berlaku Sampai (Opsional)</label>
                                <input type="date" name="valid_until" id="valid_until"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                        
                        <div class="border-t pt-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" checked
                                       class="w-4 h-4 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
                                <span class="ml-2 text-sm text-gray-700">Aktifkan promo</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3 mt-6">
                        <button type="button" onclick="closeModal()" 
                                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                            Simpan
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
    function updateScheduleFields() {
        const scheduleType = document.querySelector('input[name="schedule_type"]:checked').value;
        const specificDateField = document.getElementById('specificDateField');
        const dayOfWeekField = document.getElementById('dayOfWeekField');
        const validityPeriodField = document.getElementById('validityPeriodField');
        
        if (scheduleType === 'specific_date') {
            specificDateField.classList.remove('hidden');
            dayOfWeekField.classList.add('hidden');
            validityPeriodField.classList.add('hidden');
            document.getElementById('specific_date').required = true;
            document.getElementById('valid_from').required = false;
        } else if (scheduleType === 'day_of_week') {
            specificDateField.classList.add('hidden');
            dayOfWeekField.classList.remove('hidden');
            validityPeriodField.classList.remove('hidden');
            document.getElementById('specific_date').required = false;
            document.getElementById('valid_from').required = true;
        } else {
            specificDateField.classList.add('hidden');
            dayOfWeekField.classList.add('hidden');
            validityPeriodField.classList.remove('hidden');
            document.getElementById('specific_date').required = false;
            document.getElementById('valid_from').required = true;
        }
    }
    
    function updateDiscountLabel() {
        const type = document.getElementById('discount_type').value;
        const label = document.getElementById('discountLabel');
        label.textContent = type === 'percentage' ? 'Nilai Diskon (%)' : 'Nilai Diskon (Rp)';
    }
    
    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Buat Promo Otomatis';
        document.getElementById('promoForm').reset();
        document.getElementById('promoId').value = '';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('is_active').checked = true;
        document.querySelector('input[value="day_of_week"]').checked = true;
        updateScheduleFields();
        document.getElementById('promoModal').classList.remove('hidden');
    }
    
    function openEditModal(id) {
        fetch(`/admin/promo/auto/${id}/edit`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const promo = data.autoPromo;
                document.getElementById('modalTitle').textContent = 'Edit Promo Otomatis';
                document.getElementById('promoId').value = promo.id;
                document.getElementById('formMethod').value = 'PATCH';
                document.getElementById('name').value = promo.name;
                document.getElementById('description').value = promo.description || '';
                document.querySelector(`input[name="schedule_type"][value="${promo.schedule_type}"]`).checked = true;
                document.getElementById('specific_date').value = promo.specific_date || '';
                document.getElementById('start_time').value = promo.start_time;
                document.getElementById('end_time').value = promo.end_time;
                document.getElementById('discount_type').value = promo.discount_type;
                document.getElementById('discount_value').value = promo.discount_value;
                document.getElementById('min_transaction').value = promo.min_transaction;
                document.getElementById('max_discount').value = promo.max_discount || '';
                
                // Format dates properly for date input (YYYY-MM-DD)
                if (promo.valid_from) {
                    // Handle both date object and string format
                    const validFrom = typeof promo.valid_from === 'string' 
                        ? promo.valid_from.split('T')[0] 
                        : promo.valid_from;
                    document.getElementById('valid_from').value = validFrom;
                }
                
                if (promo.valid_until) {
                    const validUntil = typeof promo.valid_until === 'string' 
                        ? promo.valid_until.split('T')[0] 
                        : promo.valid_until;
                    document.getElementById('valid_until').value = validUntil;
                }
                
                document.getElementById('is_active').checked = promo.is_active;
                
                // Reset days of week checkboxes first
                document.querySelectorAll('input[name="days_of_week[]"]').forEach(checkbox => {
                    checkbox.checked = false;
                });
                
                // Check days of week
                if (promo.days_of_week) {
                    promo.days_of_week.forEach(day => {
                        const checkbox = document.querySelector(`input[name="days_of_week[]"][value="${day}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });
                }
                
                updateScheduleFields();
                updateDiscountLabel();
                document.getElementById('promoModal').classList.remove('hidden');
            }
        });
    }
    
    function closeModal() {
        document.getElementById('promoModal').classList.add('hidden');
    }
    
    document.getElementById('promoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const promoId = document.getElementById('promoId').value;
        const method = document.getElementById('formMethod').value;
        const url = promoId ? `/admin/promo/auto/${promoId}` : '/admin/promo/auto';
        
        if (method === 'PATCH') {
            formData.append('_method', 'PATCH');
        }
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message || 'Terjadi kesalahan'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat menyimpan'
            });
        });
    });
    
    function toggleStatus(id) {
        fetch(`/admin/promo/auto/${id}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    }
    
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Promo?',
            text: `Apakah Anda yakin ingin menghapus promo "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/promo/auto/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => location.reload());
                    }
                });
            }
        });
    }
    
    // Close modal when clicking outside
    document.getElementById('promoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    
    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        updateScheduleFields();
    });
</script>
@endpush
