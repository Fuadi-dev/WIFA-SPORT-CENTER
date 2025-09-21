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
                
                <!-- Courts Management -->
                <a href="{{ url('admin.courts.index') ?? '#' }}" class="menu-item flex items-center px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.courts.*') ? 'active' : 'text-amber-100 hover:text-white' }}">
                    <i class="fas fa-map-marked-alt w-5 mr-3"></i>
                    Kelola Lapangan
                </a>
                
                <!-- Sports Management -->
                <a href="{{ url('admin.sports.index') ?? '#' }}" class="menu-item flex items-center px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.sports.*') ? 'active' : 'text-amber-100 hover:text-white' }}">
                    <i class="fas fa-futbol w-5 mr-3"></i>
                    Kelola Olahraga
                </a>
                
                <!-- Users Management -->
                <a href="{{ url('admin.users.index') ?? '#' }}" class="menu-item flex items-center px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'active' : 'text-amber-100 hover:text-white' }}">
                    <i class="fas fa-users w-5 mr-3"></i>
                    Kelola User
                </a>
            </div>
            
            <div class="mb-6">
                <h3 class="text-amber-300 text-xs uppercase tracking-wider font-semibold mb-3 px-3">Laporan</h3>
                
                <!-- Reports -->
                <a href="{{ url('admin.reports.bookings') ?? '#' }}" class="menu-item flex items-center px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.reports.*') ? 'active' : 'text-amber-100 hover:text-white' }}">
                    <i class="fas fa-chart-bar w-5 mr-3"></i>
                    Laporan Booking
                </a>
                
                <!-- Financial Reports -->
                <a href="{{ url('admin.reports.financial') ?? '#' }}" class="menu-item flex items-center px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.reports.financial') ? 'active' : 'text-amber-100 hover:text-white' }}">
                    <i class="fas fa-money-bill-wave w-5 mr-3"></i>
                    Laporan Keuangan
                </a>
            </div>
            
            <div>
                <h3 class="text-amber-300 text-xs uppercase tracking-wider font-semibold mb-3 px-3">Pengaturan</h3>
                
                <!-- Settings -->
                <a href="{{ url('admin.settings') ?? '#' }}" class="menu-item flex items-center px-3 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.settings') ? 'active' : 'text-amber-100 hover:text-white' }}">
                    <i class="fas fa-cog w-5 mr-3"></i>
                    Pengaturan
                </a>
                
                <!-- Back to Website -->
                <a href="{{ url('home') }}" class="menu-item flex items-center px-3 py-3 rounded-lg text-sm font-medium text-amber-100 hover:text-white">
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