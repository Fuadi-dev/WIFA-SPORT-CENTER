@extends('admin.layouts.admin')

@section('title', 'Kelola Kode Promo')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="px-6 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Kode Promo</h1>
                    <p class="mt-2 text-gray-600">Manajemen kode promo dan diskon</p>
                </div>
                
                <div class="mt-4 md:mt-0">
                    <button onclick="openAddModal()" 
                       class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Buat Kode Promo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="px-6 py-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('admin.promo.codes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Kode</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Kode promo..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Semua Tipe</option>
                        <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Persentase</option>
                        <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Nominal</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                
                <div class="flex items-end space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.promo.codes.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        <i class="fas fa-times mr-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Promo Codes Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($promoCodes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Limit</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($promoCodes as $promo)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-amber-600">{{ $promo->code }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $promo->type === 'percentage' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $promo->type === 'percentage' ? 'Persentase' : 'Nominal' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $promo->type === 'percentage' ? $promo->value . '%' : 'Rp ' . number_format($promo->value, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $promo->start_date->format('d M Y') }}</div>
                                        <div class="text-sm text-gray-500">s/d {{ $promo->expiry_date ? $promo->expiry_date->format('d M Y') : 'Tidak terbatas' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            {{ $promo->usage_limit ?? 'Unlimited' }}
                                            @if($promo->usage_limit)
                                                <span class="text-gray-500">({{ $promo->bookings()->count() }} digunakan)</span>
                                            @endif
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
                                            @if($promo->bookings()->count() === 0)
                                                <button onclick="confirmDelete({{ $promo->id }}, '{{ $promo->code }}')" 
                                                        class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $promoCodes->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-ticket-alt text-white text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Kode Promo</h3>
                    <p class="text-gray-500 mb-4">Mulai buat kode promo pertama Anda</p>
                    <button onclick="openAddModal()" 
                       class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Buat Kode Promo
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="promoModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 id="modalTitle" class="text-xl font-semibold text-gray-800">Buat Kode Promo</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="promoForm">
                    @csrf
                    <input type="hidden" id="promoId" name="id">
                    <input type="hidden" id="formMethod" value="POST">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kode Promo</label>
                            <input type="text" name="code" id="code" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 uppercase"
                                   placeholder="CONTOH: DISKON50">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Diskon</label>
                            <select name="type" id="type" required onchange="updateValueLabel()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="percentage">Persentase (%)</option>
                                <option value="fixed">Nominal (Rp)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" id="valueLabel">Nilai Diskon (%)</label>
                            <input type="number" name="value" id="value" required min="0" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                   placeholder="0">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berakhir (Opsional)</label>
                            <input type="date" name="expiry_date" id="expiry_date"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Batas Penggunaan (Opsional)</label>
                            <input type="number" name="usage_limit" id="usage_limit" min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                   placeholder="Kosongkan untuk unlimited">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" checked
                                       class="w-4 h-4 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
                                <span class="ml-2 text-sm text-gray-700">Aktifkan kode promo</span>
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
    function updateValueLabel() {
        const type = document.getElementById('type').value;
        const label = document.getElementById('valueLabel');
        label.textContent = type === 'percentage' ? 'Nilai Diskon (%)' : 'Nilai Diskon (Rp)';
    }
    
    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Buat Kode Promo';
        document.getElementById('promoForm').reset();
        document.getElementById('promoId').value = '';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('is_active').checked = true;
        document.getElementById('promoModal').classList.remove('hidden');
    }
    
    function openEditModal(id) {
        fetch(`/admin/promo/codes/${id}/edit`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const promo = data.promoCode;
                document.getElementById('modalTitle').textContent = 'Edit Kode Promo';
                document.getElementById('promoId').value = promo.id;
                document.getElementById('formMethod').value = 'PATCH';
                document.getElementById('code').value = promo.code;
                document.getElementById('type').value = promo.type;
                document.getElementById('value').value = promo.value;
                document.getElementById('start_date').value = promo.start_date;
                document.getElementById('expiry_date').value = promo.expiry_date || '';
                document.getElementById('usage_limit').value = promo.usage_limit || '';
                document.getElementById('is_active').checked = promo.is_active;
                updateValueLabel();
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
        const url = promoId ? `/admin/promo/codes/${promoId}` : '/admin/promo/codes';
        
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
        fetch(`/admin/promo/codes/${id}/toggle-status`, {
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
    
    function confirmDelete(id, code) {
        Swal.fire({
            title: 'Hapus Kode Promo?',
            text: `Apakah Anda yakin ingin menghapus kode "${code}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/promo/codes/${id}`, {
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
</script>
@endpush
