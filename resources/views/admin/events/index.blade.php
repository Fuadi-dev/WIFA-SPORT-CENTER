@extends('admin.layouts.admin')

@section('title', 'Kelola Event')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="px-6 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Event</h1>
                    <p class="mt-2 text-gray-600">Manajemen event dan turnamen olahraga</p>
                </div>
                
                <div class="mt-4 md:mt-0">
                    <button onclick="openAddEventModal()" 
                       class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Buat Event Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="px-6 py-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="GET" action="{{ route('admin.events.index') }}" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
                <!-- Search -->
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Event</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Nama event..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>
                
                <!-- Status Filter -->
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="open_registration" {{ request('status') === 'open_registration' ? 'selected' : '' }}>Buka Pendaftaran</option>
                        <option value="registration_closed" {{ request('status') === 'registration_closed' ? 'selected' : '' }}>Tutup Pendaftaran</option>
                        <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Berlangsung</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                
                <!-- Sport Filter -->
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Olahraga</label>
                    <select name="sport_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Semua Olahraga</option>
                        @foreach($sports as $sport)
                            <option value="{{ $sport->id }}" {{ request('sport_id') == $sport->id ? 'selected' : '' }}>
                                {{ $sport->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.events.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        <i class="fas fa-times mr-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Events List -->
    <div class="px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($events->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Olahraga</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pendaftar</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($events as $event)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($event->poster)
                                                <img src="{{ $event->poster_url }}" alt="{{ $event->title }}" 
                                                     class="h-12 w-12 rounded-lg object-cover mr-3">
                                            @else
                                                <div class="h-12 w-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center mr-3">
                                                    <i class="fas fa-trophy text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $event->title }}</div>
                                                <div class="text-sm text-gray-500">{{ $event->event_code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $event->event_date->format('d M Y') }}</div>
                                        <div class="text-sm text-gray-500">
                                            @php
                                                $startTime = is_string($event->start_time) ? $event->start_time : $event->start_time->format('H:i');
                                                $endTime = is_string($event->end_time) ? $event->end_time : $event->end_time->format('H:i');
                                                
                                                // Ensure time format is HH:MM
                                                if (strlen($startTime) === 8) $startTime = substr($startTime, 0, 5);
                                                if (strlen($endTime) === 8) $endTime = substr($endTime, 0, 5);
                                            @endphp
                                            {{ $startTime }} - {{ $endTime }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $event->sport->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $event->court->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $event->registered_teams_count }}/{{ $event->max_teams }}</div>
                                        <div class="text-sm text-gray-500">{{ $event->available_slots }} slot tersisa</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($event->status === 'draft') bg-gray-100 text-gray-800
                                            @elseif($event->status === 'open_registration') bg-green-100 text-green-800
                                            @elseif($event->status === 'registration_closed') bg-yellow-100 text-yellow-800
                                            @elseif($event->status === 'ongoing') bg-blue-100 text-blue-800
                                            @elseif($event->status === 'completed') bg-purple-100 text-purple-800
                                            @elseif($event->status === 'cancelled') bg-red-100 text-red-800
                                            @endif">
                                            @if($event->status === 'open_registration') Buka Pendaftaran
                                            @elseif($event->status === 'registration_closed') Tutup Pendaftaran
                                            @else {{ ucfirst($event->status) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.events.show', $event) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button onclick="openEditEventModal({{ $event->id }})" 
                                               class="text-amber-600 hover:text-amber-900">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="{{ route('admin.events.registrations', $event) }}" 
                                               class="text-green-600 hover:text-green-900" title="Lihat Pendaftar">
                                                <i class="fas fa-users"></i>
                                            </a>
                                            @if($event->registrations->count() === 0)
                                                <button onclick="confirmDelete({{ $event->id }}, '{{ $event->title }}')" 
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
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $events->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-trophy text-white text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Event</h3>
                    <p class="text-gray-500 mb-4">Mulai buat event pertama Anda</p>
                    <button onclick="openAddEventModal()" 
                       class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Buat Event Baru
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div id="addEventModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-[9999] hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">Buat Event Baru</h3>
                    <button onclick="closeAddEventModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="addEventForm" method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="md:col-span-2">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h4>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Judul Event</label>
                            <input type="text" name="title" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                   placeholder="Nama event...">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Olahraga</label>
                            <select name="sport_id" required onchange="loadCourts(this.value, 'add')"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="">Pilih Olahraga</option>
                                @foreach($sports as $sport)
                                    <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lapangan</label>
                            <select name="court_id" id="addCourtSelect" required disabled
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="">Pilih lapangan terlebih dahulu</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Event</label>
                            <input type="date" name="event_date" required min="{{ now()->addDays(32)->format('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <p class="text-xs text-gray-500 mt-1">Minimal 32 hari dari sekarang</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Mulai</label>
                            <input type="time" name="start_time" required value="08:00"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Selesai</label>
                            <input type="time" name="end_time" required value="17:00"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <!-- Registration Details -->
                        <div class="md:col-span-2">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Detail Pendaftaran</h4>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Pendaftaran</label>
                            <input type="number" name="registration_fee" required min="0" step="1000"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                   placeholder="0">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maksimal Tim</label>
                            <input type="number" name="max_teams" required min="2" max="64" value="16"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Batas Waktu Pendaftaran</label>
                            <input type="date" name="registration_deadline" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Event</label>
                            <textarea name="description" required rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                      placeholder="Deskripsi lengkap event..."></textarea>
                        </div>
                        
                        <!-- Optional Fields -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Persyaratan (Opsional)</label>
                            <textarea name="requirements" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                      placeholder="Persyaratan untuk mengikuti event..."></textarea>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Informasi Hadiah (Opsional)</label>
                            <textarea name="prize_info" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                      placeholder="Informasi hadiah untuk pemenang..."></textarea>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Poster Event (Opsional)</label>
                            <input type="file" name="poster" id="addPosterInput" accept="image/*" onchange="previewAddPoster()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB</p>
                            
                            <!-- Preview Container -->
                            <div id="addPosterPreview" class="mt-4 hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preview:</label>
                                <div class="relative inline-block">
                                    <img id="addPosterImg" src="" alt="Poster preview" class="h-40 w-auto object-cover rounded-lg border-2 border-gray-200">
                                    <button type="button" onclick="removeAddPoster()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors">
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3 mt-6">
                        <button type="button" onclick="closeAddEventModal()" 
                                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                            Buat Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Event Modal -->
<div id="editEventModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-[9999] hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">Edit Event</h3>
                    <button onclick="closeEditEventModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="editEventForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="md:col-span-2">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h4>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Judul Event</label>
                            <input type="text" name="title" id="editTitle" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Olahraga</label>
                            <select name="sport_id" id="editSportId" required onchange="loadCourts(this.value, 'edit')"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="">Pilih Olahraga</option>
                                @foreach($sports as $sport)
                                    <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lapangan</label>
                            <select name="court_id" id="editCourtSelect" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="">Pilih lapangan</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Event</label>
                            <input type="date" name="event_date" id="editEventDate" required min="{{ now()->addDays(32)->format('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Mulai</label>
                            <input type="time" name="start_time" id="editStartTime" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Selesai</label>
                            <input type="time" name="end_time" id="editEndTime" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <!-- Status -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Event</label>
                            <select name="status" id="editStatus" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="draft">Draft</option>
                                <option value="open_registration">Buka Pendaftaran</option>
                                <option value="registration_closed">Tutup Pendaftaran</option>
                                <option value="ongoing">Berlangsung</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                        
                        <!-- Registration Details -->
                        <div class="md:col-span-2">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Detail Pendaftaran</h4>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Pendaftaran</label>
                            <input type="number" name="registration_fee" id="editRegistrationFee" required min="0" step="1000"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maksimal Tim</label>
                            <input type="number" name="max_teams" id="editMaxTeams" required min="2" max="64"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Batas Waktu Pendaftaran</label>
                            <input type="date" name="registration_deadline" id="editRegistrationDeadline" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Event</label>
                            <textarea name="description" id="editDescription" required rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></textarea>
                        </div>
                        
                        <!-- Optional Fields -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Persyaratan (Opsional)</label>
                            <textarea name="requirements" id="editRequirements" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></textarea>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Informasi Hadiah (Opsional)</label>
                            <textarea name="prize_info" id="editPrizeInfo" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></textarea>
                        </div>
                        
                        <div class="md:col-span-2">
                            <div id="currentPosterDiv" class="mb-3 hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Poster Saat Ini</label>
                                <div class="relative inline-block">
                                    <img id="currentPoster" src="" alt="Current poster" class="h-40 w-auto object-cover rounded-lg border-2 border-gray-200">
                                </div>
                            </div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Poster Event Baru (Opsional)</label>
                            <input type="file" name="poster" id="editPosterInput" accept="image/*" onchange="previewEditPoster()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah poster.</p>
                            
                            <!-- New Poster Preview Container -->
                            <div id="editPosterPreview" class="mt-4 hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preview Poster Baru:</label>
                                <div class="relative inline-block">
                                    <img id="editPosterImg" src="" alt="New poster preview" class="h-40 w-auto object-cover rounded-lg border-2 border-amber-200">
                                    <button type="button" onclick="removeEditPoster()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors">
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3 mt-6">
                        <button type="button" onclick="closeEditEventModal()" 
                                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
                            Update Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gradient-to-br from-black/60 via-black/50 to-black/60 backdrop-blur-sm z-[9999] hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-trash text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Hapus Event</h3>
                </div>
                
                <p class="text-gray-600 mb-6">
                    Apakah Anda yakin ingin menghapus event "<span id="deleteEventTitle" class="font-semibold"></span>"?
                    Tindakan ini tidak dapat dibatalkan.
                </p>
                
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeDeleteModal()" 
                                class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Hapus
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
    // Check for modal opening instruction from server
    @if(session('openModal') === 'create')
        document.addEventListener('DOMContentLoaded', function() {
            openAddEventModal();
        });
    @endif

    // Delete Modal Functions
    function confirmDelete(eventId, eventTitle) {
        Swal.fire({
            title: 'Hapus Event?',
            text: `Apakah Anda yakin ingin menghapus event "${eventTitle}"? Tindakan ini tidak dapat dibatalkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit delete form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/events/${eventId}`;
                form.style.display = 'none';
                
                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                form.appendChild(csrfInput);
                
                // Add DELETE method
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
    
    // Add Event Modal Functions
    function openAddEventModal() {
        document.getElementById('addEventModal').classList.remove('hidden');
        // Reset form
        document.getElementById('addEventForm').reset();
        // Reset court select
        const courtSelect = document.getElementById('addCourtSelect');
        courtSelect.innerHTML = '<option value="">Pilih lapangan terlebih dahulu</option>';
        courtSelect.disabled = true;
        // Reset poster preview
        removeAddPoster();
    }
    
    function closeAddEventModal() {
        document.getElementById('addEventModal').classList.add('hidden');
    }
    
    // Edit Event Modal Functions
    function openEditEventModal(eventId) {
        console.log('Opening edit modal for event ID:', eventId);
        
        // Fetch event data
        fetch(`/admin/events/${eventId}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    populateEditForm(data.event, data.courts);
                    document.getElementById('editEventModal').classList.remove('hidden');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Data',
                        text: data.message || 'Terjadi kesalahan saat memuat data event',
                        confirmButtonColor: '#d33'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: 'Terjadi kesalahan saat memuat data event: ' + error.message,
                    confirmButtonColor: '#d33'
                });
            });
    }
    
    function closeEditEventModal() {
        document.getElementById('editEventModal').classList.add('hidden');
        // Reset poster preview
        removeEditPoster();
    }
    
    function populateEditForm(event, courts) {
        // Set form action
        document.getElementById('editEventForm').action = `/admin/events/${event.id}`;
        
        // Populate basic fields
        document.getElementById('editTitle').value = event.title;
        document.getElementById('editSportId').value = event.sport_id;
        document.getElementById('editEventDate').value = event.event_date;
        document.getElementById('editStartTime').value = event.start_time;
        document.getElementById('editEndTime').value = event.end_time;
        document.getElementById('editStatus').value = event.status;
        document.getElementById('editRegistrationFee').value = event.registration_fee;
        document.getElementById('editMaxTeams').value = event.max_teams;
        document.getElementById('editRegistrationDeadline').value = event.registration_deadline;
        document.getElementById('editDescription').value = event.description;
        document.getElementById('editRequirements').value = event.requirements || '';
        document.getElementById('editPrizeInfo').value = event.prize_info || '';
        
        // Populate courts
        const courtSelect = document.getElementById('editCourtSelect');
        courtSelect.innerHTML = '<option value="">Pilih lapangan</option>';
        courts.forEach(court => {
            const option = document.createElement('option');
            option.value = court.id;
            option.textContent = court.name;
            option.selected = court.id == event.court_id;
            courtSelect.appendChild(option);
        });
        
        // Show current poster if exists
        if (event.poster) {
            document.getElementById('currentPosterDiv').classList.remove('hidden');
            document.getElementById('currentPoster').src = event.poster_url;
        } else {
            document.getElementById('currentPosterDiv').classList.add('hidden');
        }
    }
    
    // Load courts based on sport selection
    async function loadCourts(sportId, modalType) {
        const courtSelect = document.getElementById(modalType === 'add' ? 'addCourtSelect' : 'editCourtSelect');
        
        if (!sportId) {
            courtSelect.innerHTML = '<option value="">Pilih lapangan terlebih dahulu</option>';
            courtSelect.disabled = true;
            return;
        }
        
        try {
            const response = await fetch(`/admin/events/courts-by-sport/${sportId}`);
            const data = await response.json();
            
            courtSelect.innerHTML = '<option value="">Pilih lapangan</option>';
            data.courts.forEach(court => {
                const option = document.createElement('option');
                option.value = court.id;
                option.textContent = court.name;
                courtSelect.appendChild(option);
            });
            courtSelect.disabled = false;
        } catch (error) {
            console.error('Error loading courts:', error);
            courtSelect.innerHTML = '<option value="">Error loading courts</option>';
        }
    }
    
    // Close modals when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
    
    document.getElementById('addEventModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddEventModal();
        }
    });
    
    document.getElementById('editEventModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditEventModal();
        }
    });
    
    // Form submission handlers
    document.getElementById('addEventForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        
        submitButton.disabled = true;
        submitButton.textContent = 'Menyimpan...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeAddEventModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Event berhasil dibuat',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    location.reload(); // Refresh page to show new event
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: data.message || 'Terjadi kesalahan saat menyimpan event',
                    confirmButtonColor: '#d33'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menyimpan',
                text: 'Terjadi kesalahan saat menyimpan event',
                confirmButtonColor: '#d33'
            });
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        });
    });
    
    document.getElementById('editEventForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        
        submitButton.disabled = true;
        submitButton.textContent = 'Menyimpan...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeEditEventModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Event berhasil diupdate',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    location.reload(); // Refresh page to show updated event
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengupdate',
                    text: data.message || 'Terjadi kesalahan saat mengupdate event',
                    confirmButtonColor: '#d33'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Mengupdate',
                text: 'Terjadi kesalahan saat mengupdate event',
                confirmButtonColor: '#d33'
            });
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        });
    });
    
    // Image Preview Functions
    function previewAddPoster() {
        const input = document.getElementById('addPosterInput');
        const preview = document.getElementById('addPosterPreview');
        const img = document.getElementById('addPosterImg');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal adalah 2MB',
                    confirmButtonColor: '#d33'
                });
                input.value = '';
                return;
            }
            
            // Validate file type
            if (!file.type.match('image.*')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format File Salah',
                    text: 'Hanya file gambar (JPG, PNG) yang diizinkan',
                    confirmButtonColor: '#d33'
                });
                input.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }
    
    function removeAddPoster() {
        document.getElementById('addPosterInput').value = '';
        document.getElementById('addPosterPreview').classList.add('hidden');
        document.getElementById('addPosterImg').src = '';
    }
    
    function previewEditPoster() {
        const input = document.getElementById('editPosterInput');
        const preview = document.getElementById('editPosterPreview');
        const img = document.getElementById('editPosterImg');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal adalah 2MB',
                    confirmButtonColor: '#d33'
                });
                input.value = '';
                return;
            }
            
            // Validate file type
            if (!file.type.match('image.*')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format File Salah',
                    text: 'Hanya file gambar (JPG, PNG) yang diizinkan',
                    confirmButtonColor: '#d33'
                });
                input.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }
    
    function removeEditPoster() {
        document.getElementById('editPosterInput').value = '';
        document.getElementById('editPosterPreview').classList.add('hidden');
        document.getElementById('editPosterImg').src = '';
    }
</script>
@endpush