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
            <div class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-8">
                    <a href="#home" class="nav-link text-white hover:text-amber-300 px-4 py-2 rounded-md text-base font-semibold tracking-wide transition-all duration-300 hover:bg-amber-800/20 relative group text-shadow-sm" data-section="home">
                        Home
                        <span class="nav-underline absolute bottom-0 left-0 w-0 h-0.5 bg-amber-400 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#about" class="nav-link text-white hover:text-amber-300 px-4 py-2 rounded-md text-base font-semibold tracking-wide transition-all duration-300 hover:bg-amber-800/20 relative group text-shadow-sm" data-section="about">
                        About
                        <span class="nav-underline absolute bottom-0 left-0 w-0 h-0.5 bg-amber-400 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#services" class="nav-link text-white hover:text-amber-300 px-4 py-2 rounded-md text-base font-semibold tracking-wide transition-all duration-300 hover:bg-amber-800/20 relative group text-shadow-sm" data-section="services">
                        Services
                        <span class="nav-underline absolute bottom-0 left-0 w-0 h-0.5 bg-amber-400 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#facilities" class="nav-link text-white hover:text-amber-300 px-4 py-2 rounded-md text-base font-semibold tracking-wide transition-all duration-300 hover:bg-amber-800/20 relative group text-shadow-sm" data-section="facilities">
                        Facilities
                        <span class="nav-underline absolute bottom-0 left-0 w-0 h-0.5 bg-amber-400 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#contact" class="nav-link text-white hover:text-amber-300 px-4 py-2 rounded-md text-base font-semibold tracking-wide transition-all duration-300 hover:bg-amber-800/20 relative group text-shadow-sm" data-section="contact">
                        Contact
                        <span class="nav-underline absolute bottom-0 left-0 w-0 h-0.5 bg-amber-400 transition-all duration-300 group-hover:w-full"></span>
                    </a>
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
            <a href="#home" class="nav-link text-white hover:text-amber-300 block px-4 py-3 rounded-md text-lg font-semibold tracking-wide hover:bg-amber-800/20 transition-all duration-300 text-shadow-sm" data-section="home">
                Home
            </a>
            <a href="#about" class="nav-link text-white hover:text-amber-300 block px-4 py-3 rounded-md text-lg font-semibold tracking-wide hover:bg-amber-800/20 transition-all duration-300 text-shadow-sm" data-section="about">
                About
            </a>
            <a href="#services" class="nav-link text-white hover:text-amber-300 block px-4 py-3 rounded-md text-lg font-semibold tracking-wide hover:bg-amber-800/20 transition-all duration-300 text-shadow-sm" data-section="services">
                Services
            </a>
            <a href="#facilities" class="nav-link text-white hover:text-amber-300 block px-4 py-3 rounded-md text-lg font-semibold tracking-wide hover:bg-amber-800/20 transition-all duration-300 text-shadow-sm" data-section="facilities">
                Facilities
            </a>
            <a href="#contact" class="nav-link text-white hover:text-amber-300 block px-4 py-3 rounded-md text-lg font-semibold tracking-wide hover:bg-amber-800/20 transition-all duration-300 text-shadow-sm" data-section="contact">
                Contact
            </a>
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
        let ticking = false;

        // Navbar scroll behavior with throttling
        function updateNavbar() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const heroSection = document.getElementById('hero');
            const heroHeight = heroSection ? heroSection.offsetHeight : window.innerHeight;

            // Show/hide navbar based on scroll direction
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling down - hide navbar
                navbar.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling up - show navbar
                navbar.style.transform = 'translateY(0)';
            }

            // Change navbar background based on position
            if (scrollTop < heroHeight - 100) {
                // In hero section - transparent with amber gradient
                navbar.style.background = 'linear-gradient(180deg, rgba(120,53,15,0.8) 0%, rgba(146,64,14,0.5) 50%, transparent 100%)';
                navbar.style.backdropFilter = 'none';
                navbar.style.boxShadow = 'none';
            } else {
                // Below hero section - solid amber background with blur
                navbar.style.background = 'rgba(120,53,15,0.95)';
                navbar.style.backdropFilter = 'blur(10px)';
                navbar.style.boxShadow = '0 4px 6px -1px rgba(120, 53, 15, 0.3)';
            }

            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
            ticking = false;
        }

        function requestTick() {
            if (!ticking) {
                requestAnimationFrame(() => {
                    updateNavbar();
                    updateActiveSection();
                    ticking = false;
                });
                ticking = true;
            }
        }

        window.addEventListener('scroll', requestTick);

        // Active section detection
        function updateActiveSection() {
            const sections = [
                { id: 'hero', nav: 'home' },
                { id: 'about', nav: 'about' },
                { id: 'services', nav: 'services' },
                { id: 'facilities', nav: 'facilities' },
                { id: 'contact', nav: 'contact' }
            ];
            let currentSection = 'home';
            
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            sections.forEach(section => {
                const element = document.getElementById(section.id);
                if (element) {
                    const rect = element.getBoundingClientRect();
                    const elementTop = scrollTop + rect.top;
                    const elementBottom = elementTop + element.offsetHeight;
                    const offset = 120; // Offset for navbar height
                    
                    // Check if we're in this section
                    if (scrollTop + offset >= elementTop && scrollTop + offset < elementBottom) {
                        currentSection = section.nav;
                    }
                }
            });
            
            // Special case for hero section - if we're at the very top
            if (scrollTop < 100) {
                currentSection = 'home';
            }
            
            // Update active nav links
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                const section = link.getAttribute('data-section');
                const underline = link.querySelector('.nav-underline');
                
                if (section === currentSection) {
                    // Active state
                    link.classList.add('text-amber-300', 'bg-amber-800/30');
                    link.classList.remove('text-white');
                    if (underline) {
                        underline.classList.add('w-full');
                        underline.classList.remove('w-0');
                    }
                } else {
                    // Inactive state
                    link.classList.remove('text-amber-300', 'bg-amber-800/30');
                    link.classList.add('text-white');
                    if (underline) {
                        underline.classList.remove('w-full');
                        underline.classList.add('w-0');
                    }
                }
            });
        }

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

        // Smooth scrolling for navigation links
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetSection = this.getAttribute('data-section');
                let targetElement;
                
                // Map navigation sections to actual element IDs
                if (targetSection === 'home') {
                    targetElement = document.getElementById('hero');
                } else {
                    targetElement = document.getElementById(targetSection);
                }
                
                if (targetElement) {
                    const offsetTop = targetElement.offsetTop - 80;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Initial call to set navbar state
        updateNavbar();
        updateActiveSection();
    });
</script>