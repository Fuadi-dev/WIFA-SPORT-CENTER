<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 ease-in-out bg-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 md:h-20">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center space-x-3">
                <img src="{{ asset('asset/wifa.jpeg') }}" alt="WIFA SPORT CENTER Logo" class="h-12 w-12 md:h-16 md:w-16 rounded-full object-cover border-2 border-amber-400/50 shadow-lg">
                <a href="#home" class="text-xl md:text-2xl font-bold text-white hover:text-amber-300 transition-colors duration-300 text-glow">
                    WIFA SPORT CENTER
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <div class="flex items-baseline space-x-8">
                    <a href="{{ url('/') }}" class="nav-link text-white hover:text-amber-300 px-4 py-2 rounded-md text-base font-semibold tracking-wide transition-all duration-300 hover:bg-amber-800/20 relative group text-shadow-sm {{ request()->routeIs('home') || request()->is('/') ? 'text-amber-300 bg-amber-800/30' : '' }}" data-section="home">
                        Beranda
                        <span class="nav-underline absolute bottom-0 left-0 h-0.5 bg-amber-400 transition-all duration-300 {{ request()->routeIs('home') || request()->is('/') ? 'w-full' : 'w-0 group-hover:w-full' }}"></span>
                    </a>
                    <a href="{{ url('/booking') }}" class="nav-link text-white hover:text-amber-300 px-4 py-2 rounded-md text-base font-semibold tracking-wide transition-all duration-300 hover:bg-amber-800/20 relative group text-shadow-sm {{ request()->routeIs('booking.*') ? 'text-amber-300 bg-amber-800/30' : '' }}" data-section="booking">
                        Pemesanan
                        <span class="nav-underline absolute bottom-0 left-0 h-0.5 bg-amber-400 transition-all duration-300 {{ request()->routeIs('booking.*') ? 'w-full' : 'w-0 group-hover:w-full' }}"></span>
                    </a>
                    <a href="{{ url('/jadwal') }}" class="nav-link text-white hover:text-amber-300 px-4 py-2 rounded-md text-base font-semibold tracking-wide transition-all duration-300 hover:bg-amber-800/20 relative group text-shadow-sm {{ request()->is('jadwal') ? 'text-amber-300 bg-amber-800/30' : '' }}" data-section="about">
                        Jadwal
                        <span class="nav-underline absolute bottom-0 left-0 h-0.5 bg-amber-400 transition-all duration-300 {{ request()->is('jadwal') ? 'w-full' : 'w-0 group-hover:w-full' }}"></span>
                    </a>
                    <a href="{{ route('events.index') }}" class="nav-link text-white hover:text-amber-300 px-4 py-2 rounded-md text-base font-semibold tracking-wide transition-all duration-300 hover:bg-amber-800/20 relative group text-shadow-sm {{ request()->routeIs('events.*') ? 'text-amber-300 bg-amber-800/30' : '' }}" data-section="contact">
                        Acara
                        <span class="nav-underline absolute bottom-0 left-0 h-0.5 bg-amber-400 transition-all duration-300 {{ request()->routeIs('events.*') ? 'w-full' : 'w-0 group-hover:w-full' }}"></span>
                    </a>
                </div>

                <!-- Auth Section -->
                <div class="flex items-center space-x-4 ml-8">
                    @auth
                        <!-- User Profile Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-3 text-white hover:text-amber-300 px-4 py-2 rounded-md transition-all duration-300 hover:bg-amber-800/20">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="h-8 w-8 rounded-full object-cover border-2 border-amber-400/50">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-amber-600 flex items-center justify-center text-white font-semibold text-sm">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="font-semibold">{{ auth()->user()->name }}</span>
                                <svg class="h-4 w-4 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-48 bg-amber-950/95 backdrop-blur-sm rounded-md shadow-lg border border-amber-800/30 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                                <div class="py-2">
                                    <a href="{{ url('/my-bookings') }}" class="block px-4 py-2 text-white hover:text-amber-300 hover:bg-amber-800/20 transition-colors duration-200 {{ request()->routeIs('my-bookings') ? 'text-amber-300 bg-amber-800/20' : '' }}">
                                        <i class="fas fa-calendar mr-2"></i>Pemesanan Saya
                                    </a>
                                    <hr class="border-amber-800/30 my-1">
                                    <form action="{{ route('logout') }}" method="POST" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-white hover:text-amber-300 hover:bg-amber-800/20 transition-colors duration-200">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Login Button -->
                        <a href="{{ route('login') }}" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-2 rounded-lg transition-all duration-300 hover:shadow-lg hover:scale-105 border border-amber-500 hover:border-amber-400">
                            <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-white hover:text-amber-300 inline-flex items-center justify-center p-2 rounded-md hover:bg-amber-800/20 transition-all duration-300">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path class="hamburger" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path class="close hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="md:hidden hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 bg-amber-950/95 backdrop-blur-sm border-t border-amber-800/30">
            <a href="{{ url('/') }}" class="nav-link text-white hover:text-amber-300 block px-4 py-3 rounded-md text-lg font-semibold tracking-wide hover:bg-amber-800/20 transition-all duration-300 text-shadow-sm {{ request()->routeIs('home') || request()->is('/') ? 'text-amber-300 bg-amber-800/30' : '' }}" data-section="home">
                Beranda
            </a>
            <a href="{{ url('/booking') }}" class="nav-link text-white hover:text-amber-300 block px-4 py-3 rounded-md text-lg font-semibold tracking-wide hover:bg-amber-800/20 transition-all duration-300 text-shadow-sm {{ request()->routeIs('booking.*') ? 'text-amber-300 bg-amber-800/30' : '' }}" data-section="booking">
                Pemesanan
            </a>
            <a href="{{ url('/jadwal') }}" class="nav-link text-white hover:text-amber-300 block px-4 py-3 rounded-md text-lg font-semibold tracking-wide hover:bg-amber-800/20 transition-all duration-300 text-shadow-sm {{ request()->is('jadwal') ? 'text-amber-300 bg-amber-800/30' : '' }}" data-section="about">
                Jadwal
            </a>
            <a href="{{ route('events.index') }}" class="nav-link text-white hover:text-amber-300 block px-4 py-3 rounded-md text-lg font-semibold tracking-wide hover:bg-amber-800/20 transition-all duration-300 text-shadow-sm {{ request()->routeIs('events.*') ? 'text-amber-300 bg-amber-800/30' : '' }}" data-section="contact">
                Acara
            </a>
            
            <!-- Mobile Auth Section -->
            <div class="border-t border-amber-800/30 pt-3 mt-3">
                @auth
                    <!-- User Info -->
                    <div class="flex items-center px-4 py-3 text-white">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="h-10 w-10 rounded-full object-cover border-2 border-amber-400/50 mr-3">
                        @else
                            <div class="h-10 w-10 rounded-full bg-amber-600 flex items-center justify-center text-white font-semibold mr-3">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <div class="font-semibold">{{ auth()->user()->name }}</div>
                            <div class="text-amber-300 text-sm">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    
                    <!-- User Menu Items -->
                    <a href="{{ url('/my-bookings') }}" class="text-white hover:text-amber-300 block px-4 py-3 rounded-md text-lg font-semibold tracking-wide hover:bg-amber-800/20 transition-all duration-300 {{ request()->routeIs('my-bookings') ? 'text-amber-300 bg-amber-800/30' : '' }}">
                        <i class="fas fa-calendar mr-3"></i>Pemesanan Saya
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left text-white hover:text-amber-300 px-4 py-3 rounded-md text-lg font-semibold tracking-wide hover:bg-amber-800/20 transition-all duration-300">
                            <i class="fas fa-sign-out-alt mr-3"></i>Keluar
                        </button>
                    </form>
                @else
                    <!-- Mobile Login Button -->
                    <a href="{{ route('login') }}" class="block mx-4 my-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 text-center border border-amber-500 hover:border-amber-400">
                        <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.getElementById('navbar');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const hamburger = document.querySelector('.hamburger');
        const close = document.querySelector('.close');
        
        let lastScrollTop = 0;
        let isMenuOpen = false;

        // Simple navbar scroll behavior
        function updateNavbar() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            // Show/hide navbar based on scroll direction
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling down - hide navbar
                navbar.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling up - show navbar
                navbar.style.transform = 'translateY(0)';
            }

            // Change navbar background based on scroll position
            if (scrollTop > 50) {
                // Scrolled - solid background
                navbar.style.background = 'rgba(120,53,15,0.95)';
                navbar.style.backdropFilter = 'blur(10px)';
                navbar.style.boxShadow = '0 4px 6px -1px rgba(120, 53, 15, 0.3)';
            } else {
                // At top - transparent gradient
                navbar.style.background = 'linear-gradient(180deg, rgba(120,53,15,0.8) 0%, rgba(146,64,14,0.5) 50%, transparent 100%)';
                navbar.style.backdropFilter = 'none';
                navbar.style.boxShadow = 'none';
            }

            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        }

        // Throttled scroll event
        let ticking = false;
        function requestTick() {
            if (!ticking) {
                requestAnimationFrame(updateNavbar);
                ticking = true;
                setTimeout(() => { ticking = false; }, 16); // ~60fps
            }
        }

        window.addEventListener('scroll', requestTick);

        // Mobile menu toggle
        mobileMenuButton.addEventListener('click', function() {
            isMenuOpen = !isMenuOpen;
            
            if (isMenuOpen) {
                mobileMenu.classList.remove('hidden');
                hamburger.classList.add('hidden');
                close.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
                hamburger.classList.remove('hidden');
                close.classList.add('hidden');
            }
        });

        // Close mobile menu when clicking on links
        const mobileNavLinks = document.querySelectorAll('#mobile-menu .nav-link');
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
                hamburger.classList.remove('hidden');
                close.classList.add('hidden');
                isMenuOpen = false;
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileMenu.contains(e.target) && !mobileMenuButton.contains(e.target) && isMenuOpen) {
                mobileMenu.classList.add('hidden');
                hamburger.classList.remove('hidden');
                close.classList.add('hidden');
                isMenuOpen = false;
            }
        });

        // Initial navbar state
        updateNavbar();
    });
</script>