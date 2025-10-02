<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Event: {{ $event->title }} - WIFA Sport Center</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-amber-50 to-orange-100 min-h-screen">
    
    <!-- Include Navbar -->
    @include('components.navbar')

    <div class="container mx-auto px-4 pt-32 pb-16">
        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-600 mb-8">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-amber-600">Home</a>
                    <i class="fas fa-chevron-right mx-2"></i>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('events.index') }}" class="hover:text-amber-600">Event & Turnamen</a>
                    <i class="fas fa-chevron-right mx-2"></i>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('events.show', $event) }}" class="hover:text-amber-600">{{ $event->title }}</a>
                    <i class="fas fa-chevron-right mx-2"></i>
                </li>
                <li class="text-amber-600 font-semibold">Pendaftaran</li>
            </ol>
        </nav>

        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-amber-800 mb-4">
                    <i class="fas fa-user-plus mr-3"></i>Pendaftaran Event
                </h1>
                <p class="text-xl text-gray-700">
                    {{ $event->title }}
                </p>
            </div>

            <!-- Event Summary -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border-2 border-amber-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-info-circle mr-2 text-amber-600"></i>Ringkasan Event
                </h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt w-5 text-amber-600 mr-3"></i>
                            <span class="font-semibold text-gray-700">Tanggal:</span>
                            <span class="ml-2 text-gray-600">{{ \Carbon\Carbon::parse($event->event_date)->format('d F Y') }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-clock w-5 text-amber-600 mr-3"></i>
                            <span class="font-semibold text-gray-700">Waktu:</span>
                            <span class="ml-2 text-gray-600">
                                {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}
                            </span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt w-5 text-amber-600 mr-3"></i>
                            <span class="font-semibold text-gray-700">Tempat:</span>
                            <span class="ml-2 text-gray-600">{{ $event->court->name }}</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="{{ $event->sport->icon }} w-5 text-amber-600 mr-3"></i>
                            <span class="font-semibold text-gray-700">Olahraga:</span>
                            <span class="ml-2 text-gray-600">{{ $event->sport->name }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-money-bill w-5 text-amber-600 mr-3"></i>
                            <span class="font-semibold text-gray-700">Biaya:</span>
                            <span class="ml-2 text-gray-600">
                                @if($event->registration_fee > 0)
                                    Rp {{ number_format($event->registration_fee, 0, ',', '.') }}
                                @else
                                    <span class="text-green-600 font-semibold">Gratis</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-hourglass-end w-5 text-amber-600 mr-3"></i>
                            <span class="font-semibold text-gray-700">Batas:</span>
                            <span class="ml-2 text-gray-600">{{ \Carbon\Carbon::parse($event->registration_deadline)->format('d F Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Form -->
            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 border-2 border-amber-100">
                <h2 class="text-xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-edit mr-2 text-amber-600"></i>Formulir Pendaftaran
                </h2>

                <form action="{{ route('events.storeRegistration', $event) }}" method="POST">
                    @csrf
                    
                    <!-- Team Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                            <i class="fas fa-users mr-2 text-amber-600"></i>Informasi Tim
                        </h3>
                        
                        <div class="grid md:grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Tim *</label>
                                <input type="text" name="team_name" value="{{ old('team_name') }}" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('team_name') border-red-500 @enderror"
                                       placeholder="Masukkan nama tim Anda">
                                @error('team_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Team Members -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                            <i class="fas fa-user-friends mr-2 text-amber-600"></i>Anggota Tim
                        </h3>
                        
                        <div id="team-members">
                            <div class="team-member-item bg-gray-50 rounded-lg p-4 mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-gray-700">Anggota 1</h4>
                                </div>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                                        <input type="text" name="team_members[0][name]" value="{{ old('team_members.0.name') }}" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                               placeholder="Nama lengkap anggota">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Posisi/Peran</label>
                                        <select name="team_members[0][position]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                            <option value="">Pilih posisi</option>
                                            <option value="captain">Kapten</option>
                                            <option value="player">Pemain</option>
                                            <option value="substitute">Cadangan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" id="add-member" 
                                class="inline-flex items-center px-4 py-2 bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Tambah Anggota
                        </button>
                    </div>

                    <!-- Contact Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                            <i class="fas fa-address-book mr-2 text-amber-600"></i>Informasi Kontak
                        </h3>
                        
                        <div class="grid md:grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Penanggung Jawab *</label>
                                <input type="text" name="contact_person" value="{{ old('contact_person', auth()->user()->name) }}" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('contact_person') border-red-500 @enderror"
                                       placeholder="Nama penanggung jawab">
                                @error('contact_person')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor HP *</label>
                                    <input type="tel" name="contact_phone" value="{{ old('contact_phone', auth()->user()->phone_number) }}" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('contact_phone') border-red-500 @enderror"
                                           placeholder="08xxxxxxxxxx">
                                    @error('contact_phone')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                    <input type="email" name="contact_email" value="{{ old('contact_email', auth()->user()->email) }}" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('contact_email') border-red-500 @enderror"
                                           placeholder="email@example.com">
                                    @error('contact_email')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Informasi Tambahan</label>
                                <textarea name="additional_info" rows="4"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                          placeholder="Ceritakan tentang tim Anda, pengalaman, atau informasi lain yang relevan...">{{ old('additional_info') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="mb-8">
                        <div class="flex items-start">
                            <input type="checkbox" id="agree_terms" name="agree_terms" required
                                   class="mt-1 h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                            <label for="agree_terms" class="ml-3 text-sm text-gray-700">
                                Saya menyetujui <span class="text-amber-600 font-semibold">syarat dan ketentuan</span> 
                                yang berlaku untuk event ini dan bersedia mengikuti semua aturan yang ditetapkan.
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                        <a href="{{ route('events.show', $event) }}" 
                           class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </a>
                        
                        <button type="submit"
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold rounded-lg transition-all duration-300 hover:shadow-lg transform hover:scale-105">
                            <i class="fas fa-paper-plane mr-2"></i>Daftar Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let memberCount = 1;
        
        document.getElementById('add-member').addEventListener('click', function() {
            const membersContainer = document.getElementById('team-members');
            const memberItem = document.createElement('div');
            memberItem.className = 'team-member-item bg-gray-50 rounded-lg p-4 mb-4';
            memberItem.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-700">Anggota ${memberCount + 1}</h4>
                    <button type="button" class="remove-member text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                        <input type="text" name="team_members[${memberCount}][name]" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                               placeholder="Nama lengkap anggota">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Posisi/Peran</label>
                        <input type="text" name="team_members[${memberCount}][position]"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                               placeholder="Captain, Player, dll">
                    </div>
                </div>
            `;
            
            membersContainer.appendChild(memberItem);
            memberCount++;
            
            // Add remove functionality
            memberItem.querySelector('.remove-member').addEventListener('click', function() {
                memberItem.remove();
            });
        });
        
        // Add remove functionality to existing members (except first one)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-member')) {
                e.target.closest('.team-member-item').remove();
            }
        });
    </script>
</body>
</html>