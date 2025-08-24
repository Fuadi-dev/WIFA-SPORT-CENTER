<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WIFA Sport Center - Home</title>
    @vite('resources/css/app.css')
    <style>
        html {
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
    </style>
</head>
<body class="bg-cream-50">
    <!-- Navigation -->
    @include('components.navbar')

    <!-- Hero Section -->
    <section id="hero" class="relative h-screen overflow-hidden">
        @include('pages.hero')
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-gradient-to-br from-amber-50 via-cream-100 to-amber-100 relative overflow-hidden">
        @include('pages.services')
    </section>
    
    <!-- About Section -->
    <section id="about" class="py-20 bg-gradient-to-br from-amber-100 via-cream-100 to-amber-50 relative overflow-hidden">
        @include('pages.about')
    </section>

    <!-- Facilities Section -->
    <section id="facilities" class="py-20 bg-gradient-to-br from-cream-100 via-white to-amber-50 relative overflow-hidden">
        @include('pages.facilities')
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gradient-to-br from-amber-950 via-amber-900 to-black text-white relative overflow-hidden">
        @include('pages.contact')
    </section>

    <!-- Footer -->
    <footer class="bg-amber-950 text-cream-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-amber-200">
                © 2025 WIFA Sport Center. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>