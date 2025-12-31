<!-- Admin Sidebar -->
<aside class="admin-sidebar fixed left-0 top-0 w-64 h-full bg-gradient-to-b from-amber-900 to-amber-950 text-white shadow-lg z-50">
    <div class="flex flex-col h-full">
        <!-- Logo & Brand -->
        <div class="flex items-center justify-center p-6 border-b border-amber-800/30">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('asset/wifa.jpeg') }}" alt="WIFA Logo" class="h-12 w-12 rounded-full object-cover border-2 border-amber-400">
                <div>
                    <h2 class="text-xl font-bold text-white">WIFA</h2>
                    <p class="text-amber-300 text-sm">Admin Panel</p>
                </div>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            <div class="mb-6">
                <h3 class="text-amber-300 text-xs uppercase tracking-wider font-semibold mb-3 px-3">Menu Utama</h3>
                
                <!-- Dashboard -->
                <a href="{{ url('admin/dashboard') }}" class="menu-item flex items-center px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-amber-100 hover:text-white' }}">
                    <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                    Dashboard
                </a>
                
                <!-- Bookings Management -->
                <a href="{{ route('admin.bookings.index') }}" class="menu-item flex items-center px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.bookings.*') ? 'active' : 'text-amber-100 hover:text-white' }}">
                    <i class="fas fa-calendar-check w-5 mr-3"></i>
                    Kelola Booking
                    @if(isset($pendingBookings) && $pendingBookings > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ $pendingBookings }}</span>
                    @endif
                </a>
                
                <!-- Events Management -->
                <a href="{{ route('admin.events.index') }}" class="menu-item flex items-center px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.events.*') ? 'active' : 'text-amber-100 hover:text-white' }}">
                    <i class="fas fa-trophy w-5 mr-3"></i>
                    Kelola Event
                </a>
                
                <!-- Users Management -->
                <a href="{{ route('admin.users.index') }}" class="menu-item flex items-center px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'active' : 'text-amber-100 hover:text-white' }}">
                    <i class="fas fa-users w-5 mr-3"></i>
                    Kelola User
                </a>
                
                <!-- Promo Management -->
                <div class="mt-2">
                    <button onclick="togglePromoMenu()" class="menu-item flex items-center justify-between w-full px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.promo.*') ? 'active' : 'text-amber-100 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-tags w-5 mr-3"></i>
                            Kelola Promo
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform" id="promoChevron"></i>
                    </button>
                    <div id="promoSubmenu" class="ml-8 mt-1 space-y-1 {{ request()->routeIs('admin.promo.*') ? '' : 'hidden' }}">
                        <a href="{{ route('admin.promo.codes.index') }}" class="flex items-center px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.promo.codes.*') ? 'bg-amber-700/50 text-white' : 'text-amber-200 hover:text-white hover:bg-amber-800/30' }}">
                            <i class="fas fa-ticket-alt w-4 mr-2"></i>
                            Kode Promo
                        </a>
                        <a href="{{ route('admin.promo.auto.index') }}" class="flex items-center px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.promo.auto.*') ? 'bg-amber-700/50 text-white' : 'text-amber-200 hover:text-white hover:bg-amber-800/30' }}">
                            <i class="fas fa-clock w-4 mr-2"></i>
                            Promo Otomatis
                        </a>
                    </div>
                </div>
                
                <!-- Price Management -->
                <a href="{{ route('admin.prices.index') }}" class="menu-item flex items-center px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.prices.*') ? 'active' : 'text-amber-100 hover:text-white' }}">
                    <i class="fas fa-money-bill-alt w-5 mr-3"></i>
                    Manajemen Harga
                </a>
            </div>
            
            <div class="mb-6">
                <h3 class="text-amber-300 text-xs uppercase tracking-wider font-semibold mb-3 px-3">Laporan</h3>
                
                <!-- Reports -->
                <a href="{{ route('admin.reports.bookings') }}" class="menu-item flex items-center px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.reports.bookings') ? 'active' : 'text-amber-100 hover:text-white' }}">
                    <i class="fas fa-chart-bar w-5 mr-3"></i>
                    Laporan Booking
                </a>
                
                    <!-- Financial Reports -->
                    <a href="{{ route('admin.reports.financial') }}" class="menu-item flex items-center px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.reports.financial') ? 'active' : 'text-amber-100 hover:text-white' }}">
                        <i class="fas fa-money-bill-wave w-5 mr-3"></i>
                        Laporan Keuangan
                    </a>
               
            </div>
            
            <div>
                <h3 class="text-amber-300 text-xs uppercase tracking-wider font-semibold mb-3 px-3">Beranda</h3>
                <!-- Back to Website -->
                <a href="{{ url('/booking') }}" class="menu-item flex items-center px-3 py-3 rounded-lg text-sm font-medium text-amber-100 hover:text-white">
                    <i class="fas fa-globe w-5 mr-3"></i>
                    Kembali ke Website
                </a>
            </div>
        </nav>
        
        <!-- Admin Info -->
        <div class="p-4 border-t border-amber-800/30">
            <div class="flex items-center space-x-3">
                @if(auth()->user() && auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" alt="Admin" class="h-10 w-10 rounded-full object-cover border-2 border-amber-400">
                @else
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-semibold">
                        {{ auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A' }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                    <p class="text-xs text-amber-300 truncate">{{ auth()->user()->email ?? 'admin@wifa.com' }}</p>
                </div>
            </div>
        </div>
    </div>
</aside>

<script>
    function togglePromoMenu() {
        const submenu = document.getElementById('promoSubmenu');
        const chevron = document.getElementById('promoChevron');
        submenu.classList.toggle('hidden');
        chevron.classList.toggle('rotate-180');
    }
</script>