<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Yacht Workers Council - The premier platform for yacht crew networking, career management, and industry insights. Join our waitlist today!">
    
    <title>Yacht Workers Council - Connect, Grow, Succeed | Join Our Waitlist</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Google Analytics -->
    @if(config('services.google_analytics.id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google_analytics.id') }}');
    </script>
    @endif

    <style>
        [x-cloak] { display: none !important; }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        
        /* Smooth transitions for form elements */
        input:focus, select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,83,255,0.2);
        }
        
        /* Loading spinner */
        #submit-loading {
            display: inline-flex;
            align-items: center;
        }
        
        #submit-loading::after {
            content: '';
            width: 16px;
            height: 16px;
            margin-left: 8px;
            border: 2px solid currentColor;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Poster slider */
        .poster-slider {
            position: relative;
            overflow: hidden;
            border-radius: 1.75rem;
            box-shadow: 0 25px 65px rgba(15, 23, 42, 0.2);
            background: linear-gradient(120deg, #030b28 0%, #06133f 100%);
            border: 1px solid rgba(255,255,255,0.08);
        }

        .poster-track {
            display: flex;
            transition: transform 0.6s ease;
            width: 100%;
        }

        .poster-slide {
            min-width: 100%;
            height: 320px;
            position: relative;
        }

        .poster-overlay {
            position: absolute;
            inset: 0;
            padding: 2.75rem;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            color: #fff;
            background: linear-gradient(180deg, rgba(1,5,26,0.15) 0%, rgba(2,8,35,0.65) 100%);
        }

        .poster-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: rgba(255,255,255,0.15);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            backdrop-filter: blur(6px);
        }

        .poster-icon svg {
            width: 30px;
            height: 30px;
            color: #fff;
        }

        .poster-tag {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            opacity: 0.85;
            font-weight: 600;
        }

        .poster-title {
            font-size: 2.4rem;
            font-weight: 700;
            margin: 0.6rem 0;
        }

        .poster-text {
            font-size: 1rem;
            opacity: 0.85;
        }

        .poster-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            border-radius: 999px;
            border: none;
            background: rgba(255,255,255,0.95);
            color: #0f172a;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .poster-nav:hover {
            transform: translateY(-50%) scale(1.05);
            box-shadow: 0 10px 25px rgba(15,23,42,0.2);
        }

        .poster-prev { left: 1.5rem; }
        .poster-next { right: 1.5rem; }

        .poster-dots {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 0 0.5rem;
        }

        .poster-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,0.5);
            border: none;
            cursor: pointer;
            transition: width 0.2s ease, background 0.2s ease;
        }

        .poster-dot.active {
            width: 32px;
            background: #fff;
        }

        .poster-progress {
            height: 4px;
            width: 80%;
            margin: 0 auto 1.5rem;
            background: rgba(255,255,255,0.2);
            border-radius: 999px;
            position: relative;
            overflow: hidden;
        }

        .poster-progress-bar {
            position: absolute;
            inset: 0;
            width: 100%;
            transform: translateX(-100%);
            background: linear-gradient(90deg, rgba(255,255,255,0.9), rgba(255,255,255,0.5));
            border-radius: inherit;
        }

        .stat-card {
            padding: 1.75rem;
            border-radius: 1.25rem;
            background: #fff;
            border: 1px solid rgba(15,23,42,0.06);
            box-shadow: 0 15px 35px rgba(15,23,42,0.08);
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
        }

        .stat-label {
            margin-top: 0.4rem;
            color: #6b7280;
        }

        /* Module slider */
        .module-slider {
            position: relative;
            overflow: hidden;
        }

        .module-track {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 1.5rem;
            transition: transform 0.4s ease;
        }

        .module-card {
            background: #fff;
            border-radius: 1.25rem;
            border: 1px solid rgba(15,23,42,0.06);
            box-shadow: 0 20px 50px rgba(15,23,42,0.08);
            padding: 1.75rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .module-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(0,83,255,0.1);
            color: #0053FF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.1rem;
        }

        .module-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .module-text {
            color: #6b7280;
            font-size: 0.95rem;
        }

        .module-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            background: rgba(0,83,255,0.08);
            color: #0053FF;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        @media (max-width: 1024px) {
            .module-track {
                grid-template-columns: repeat(6, minmax(320px, 1fr));
            }
        }

        @media (max-width: 640px) {
            .module-track {
                grid-template-columns: repeat(6, minmax(260px, 1fr));
            }
        }

        .timeline {
            border-left: 1px solid rgba(148,163,184,0.35);
            padding-left: 2.5rem;
            position: relative;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 2.5rem;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-badge {
            position: absolute;
            left: -3.15rem;
            top: 0;
            width: 2.4rem;
            height: 2.4rem;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(59,130,246,0.2), rgba(59,130,246,0.45));
            border: 1px solid rgba(148,163,184,0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .timeline-item h3 {
            font-size: 1.45rem;
            font-weight: 600;
            margin-bottom: 0.45rem;
        }

        .timeline-item p {
            color: rgba(226,232,240,0.9);
        }

        @media (max-width: 768px) {
            .poster-slide {
                height: 260px;
            }

            .poster-overlay {
                padding: 1.75rem;
            }

            .poster-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-blue-50 via-white to-blue-50">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-blue-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <img src="{{ asset('images/ywc-logo.svg') }}" alt="Yacht Workers Council Logo" class="h-10 w-auto">
                    <span class="ml-3 text-xl font-bold text-[#0053FF]">Yacht Workers Council</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-[#0053FF] px-4 py-2 rounded-md text-sm font-medium transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-[#0053FF] text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-[#0043ef] transition">
                        Sign Up
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(0,83,255,0.18),transparent)]"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32 relative">
            <div class="text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
                    Built for Crew,
                    <span class="text-[#0053FF]">by Crew</span>
                </h1>
                <p class="text-xl sm:text-2xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    The premier platform for yacht crew networking, career management, and industry insights. 
                    Join thousands of crew members already on board.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4 text-sm text-gray-500 mb-10">
                    <span class="px-4 py-2 rounded-full bg-white shadow-sm border border-gray-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Live compliance tracking
                    </span>
                    <span class="px-4 py-2 rounded-full bg-white shadow-sm border border-gray-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Real crew-only community
                    </span>
                    <span class="px-4 py-2 rounded-full bg-white shadow-sm border border-gray-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span> Global weather intelligence
                    </span>
                </div>
                
                <!-- Poster Slider -->
                <div class="mb-12 max-w-5xl mx-auto">
                    <div class="poster-slider" id="poster-slider">
                        <div class="poster-track" data-track>
                            <div class="poster-slide bg-gradient-to-br from-[#0F4CFF] via-[#1768FF] to-[#1E90FF]">
                                <div class="poster-overlay">
                                    <div class="poster-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <p class="poster-tag">Community</p>
                                    <h3 class="poster-title">Live Department Forums</h3>
                                    <p class="poster-text">Host focused discussions for captains, deck, engineering, and interior teams with smart notifications.</p>
                                </div>
                            </div>
                            <div class="poster-slide bg-gradient-to-br from-[#7F2BFF] via-[#A147FF] to-[#C24BFF]">
                                <div class="poster-overlay">
                                    <div class="poster-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                                            <circle cx="12" cy="12" r="9"/>
                                        </svg>
                                    </div>
                                    <p class="poster-tag">Itinerary</p>
                                    <h3 class="poster-title">Weather-aware Route Builder</h3>
                                    <p class="poster-text">Design routes with real-time forecasts, marina intel, and export to PDF, CSV, or GPX.</p>
                                </div>
                            </div>
                            <div class="poster-slide bg-gradient-to-br from-[#0BB97F] via-[#05C9A7] to-[#07E0C1]">
                                <div class="poster-overlay">
                                    <div class="poster-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h10m-10 5h16"/>
                                        </svg>
                                    </div>
                                    <p class="poster-tag">Compliance</p>
                                    <h3 class="poster-title">Captain Dashboard</h3>
                                    <p class="poster-text">Track crew certificates and work hours in one view with instant compliance alerts.</p>
                                </div>
                            </div>
                            <div class="poster-slide bg-gradient-to-br from-[#FF7A18] via-[#FF6A00] to-[#FF3C00]">
                                <div class="poster-overlay">
                                    <div class="poster-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </div>
                                    <p class="poster-tag">Marketplace</p>
                                    <h3 class="poster-title">Verified Crew Network</h3>
                                    <p class="poster-text">Discover nearby crew, share opportunities, and manage trusted connections securely.</p>
                                </div>
                            </div>
                        </div>
                        <button class="poster-nav poster-prev" type="button" aria-label="Previous poster">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button class="poster-nav poster-next" type="button" aria-label="Next poster">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div class="poster-progress" data-progress></div>
                        <div class="poster-dots" data-dots></div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-4xl mx-auto mb-16">
                    <div class="stat-card">
                        <p class="stat-value">15,000+</p>
                        <p class="stat-label">Crew profiles verified</p>
                    </div>
                    <div class="stat-card">
                        <p class="stat-value">320</p>
                        <p class="stat-label">Yachts sharing itineraries</p>
                    </div>
                    <div class="stat-card">
                        <p class="stat-value">48</p>
                        <p class="stat-label">Departments & subforums</p>
                    </div>
                </div>

                <!-- Trust Logos -->
                <div class="bg-white/70 border border-gray-100 rounded-2xl shadow-lg px-6 py-4 max-w-5xl mx-auto mb-20">
                    <p class="text-xs uppercase tracking-[0.35em] text-gray-500 text-center mb-4">Trusted By Crew From</p>
                    <div class="flex flex-wrap items-center justify-center gap-8 text-gray-400 text-sm">
                        <span class="uppercase tracking-widest">Abu Dhabi Yacht Club</span>
                        <span class="w-10 h-[1px] bg-gray-100 hidden sm:block"></span>
                        <span class="uppercase tracking-widest">Monaco Crew Guild</span>
                        <span class="w-10 h-[1px] bg-gray-100 hidden sm:block"></span>
                        <span class="uppercase tracking-widest">Fort Lauderdale Fleet</span>
                        <span class="w-10 h-[1px] bg-gray-100 hidden sm:block"></span>
                        <span class="uppercase tracking-widest">Sydney Harbour Crew</span>
                    </div>
                </div>

                <!-- Waitlist Signup Form -->
                <div id="waitlist" class="max-w-2xl mx-auto scroll-mt-20">
                    @if(session('success'))
                        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg animate-fade-in">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg">
                            <p class="font-semibold mb-2">Please correct the following errors:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('waitlist.join') }}" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100" id="waitlist-form">
                        @csrf
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Join Our Waitlist</h2>
                        <p class="text-center text-gray-600 mb-6">Be among the first to access Yacht Workers Council when we launch</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-transparent">
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-transparent">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-transparent">
                        </div>

                        <div class="mb-6">
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Your Role (Optional)</label>
                            <select name="role" id="role" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0053FF] focus:border-transparent">
                                <option value="">Select your role</option>
                                <option value="captain">Captain</option>
                                <option value="deck">Deck</option>
                                <option value="interior">Interior</option>
                                <option value="engineer">Engineer</option>
                                <option value="chef">Chef</option>
                                <option value="stewardess">Stewardess</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <button type="submit" 
                            class="w-full bg-[#0053FF] text-white px-6 py-4 rounded-lg font-semibold text-lg hover:bg-[#0043ef] transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-70 disabled:cursor-not-allowed"
                            id="submit-btn">
                            Join Waitlist
                        </button>

                        <p class="text-sm text-gray-500 text-center mt-4">
                            By joining, you agree to receive updates about Yacht Workers Council. We respect your privacy.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.4em] text-blue-500 mb-3">Crew Modules</p>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">Everything You Need to Succeed</h2>
                    <p class="text-gray-500 mt-3 max-w-2xl">Swipe through live modules already available for our early access crew. Each one is designed with captain and department workflows in mind.</p>
                </div>
            </div>

            <div class="module-slider">
                <div class="module-track" data-module-track>
                    <div class="module-card">
                        <span class="module-tag">Crew</span>
                        <div>
                            <div class="module-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <p class="module-title">Crew Networking</p>
                            <p class="module-text">Connect with fellow crew members, share experiences, and build your professional network.</p>
                        </div>
                    </div>

                    <div class="module-card">
                        <span class="module-tag">Career</span>
                        <div>
                            <div class="module-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="module-title">Career Management</p>
                            <p class="module-text">Track your certificates, manage documents, and build your professional profile all in one place.</p>
                        </div>
                    </div>

                    <div class="module-card">
                        <span class="module-tag">Reviews</span>
                        <div>
                            <div class="module-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <p class="module-title">Industry Reviews</p>
                            <p class="module-text">Get honest reviews about yachts, marinas, and service providers from verified crew members.</p>
                        </div>
                    </div>

                    <div class="module-card">
                        <span class="module-tag">Compliance</span>
                        <div>
                            <div class="module-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="module-title">Work Log Tracking</p>
                            <p class="module-text">Track your work hours and rest periods to ensure MLC 2006 compliance effortlessly.</p>
                        </div>
                    </div>

                    <div class="module-card">
                        <span class="module-tag">Routes</span>
                        <div>
                            <div class="module-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                            </div>
                            <p class="module-title">Itinerary Planner</p>
                            <p class="module-text">Create and share custom sailing routes with real-time weather data and local attractions.</p>
                        </div>
                    </div>

                    <div class="module-card">
                        <span class="module-tag">Forums</span>
                        <div>
                            <div class="module-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <p class="module-title">Department Forums</p>
                            <p class="module-text">Join role-specific discussions and connect with crew members in your department.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="py-20 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="text-xs uppercase tracking-[0.45em] text-blue-300 mb-3">Launch Journey</p>
                <h2 class="text-3xl sm:text-4xl font-bold">From Waitlist to Crew Ready</h2>
                <p class="text-base text-slate-300 max-w-2xl mx-auto mt-4">We’re inviting the first 1,000 crew to shape the future of yacht careers. Here’s how you’ll experience Yacht Workers Council.</p>
            </div>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-badge">Step 01</div>
                    <h3>Join the curated waitlist</h3>
                    <p>Submit your profile and crew role. We verify every submission to keep the community authentic.</p>
                </div>
                <div class="timeline-item">
                    <div class="timeline-badge">Step 02</div>
                    <h3>Captain-approved onboarding</h3>
                    <p>Receive an invite with personalized onboarding, role-based access, and first access to crew forums.</p>
                </div>
                <div class="timeline-item">
                    <div class="timeline-badge">Step 03</div>
                    <h3>Collaborative feature sprints</h3>
                    <p>Participate in exclusive roadmap sessions and feedback loops directly with the founding team.</p>
                </div>
                <div class="timeline-item">
                    <div class="timeline-badge">Step 04</div>
                    <h3>Full platform unlock</h3>
                    <p>Access itinerary marketplace, compliance dashboards, and the verified crew network.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-[#0053FF] to-[#0043ef]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
                Ready to Transform Your Yachting Career?
            </h2>
            <p class="text-xl text-blue-100 mb-8">
                Join our waitlist today and be among the first to experience the future of crew networking.
            </p>
            <a href="#waitlist" class="inline-block bg-white text-[#0053FF] px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition shadow-lg transform hover:scale-105">
                Join Waitlist Now
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <img src="{{ asset('images/ywc-logo.svg') }}" alt="Yacht Workers Council Logo" class="h-8 w-auto">
                        <span class="ml-2 text-xl font-bold">Yacht Workers Council</span>
                    </div>
                    <p class="text-gray-400">Built for crew, by crew. The premier platform for yacht crew professionals.</p>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Login</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Sign Up</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Contact</h3>
                    <p class="text-gray-400">For inquiries, please reach out through our platform.</p>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} Yacht Workers Council. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Smooth scroll for anchor links and form handling -->
    <script>
        // Poster slider
        (function() {
            const slider = document.getElementById('poster-slider');
            if (!slider) return;
            
            const track = slider.querySelector('[data-track]');
            const slides = Array.from(track.children);
            const dotsContainer = slider.querySelector('[data-dots]');
            const progressContainer = slider.querySelector('[data-progress]');
            let progressBar;
            let index = 0;
            let autoPlayTimeout;
            const autoPlayDelay = 6000;

            const createDots = () => {
                slides.forEach((_, i) => {
                    const dot = document.createElement('button');
                    dot.className = 'poster-dot' + (i === 0 ? ' active' : '');
                    dot.setAttribute('aria-label', `Show slide ${i + 1}`);
                    dot.addEventListener('click', () => goToSlide(i));
                    dotsContainer.appendChild(dot);
                });
            };

            const updateDots = () => {
                dotsContainer.querySelectorAll('.poster-dot').forEach((dot, i) => {
                    dot.classList.toggle('active', i === index);
                });
            };

            const createProgressBar = () => {
                if (!progressContainer) return;
                progressBar = document.createElement('div');
                progressBar.className = 'poster-progress-bar';
                progressContainer.appendChild(progressBar);
            };

            const animateProgress = () => {
                if (!progressBar) return;
                progressBar.style.transition = 'none';
                progressBar.style.transform = 'translateX(-100%)';
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        progressBar.style.transition = `transform ${autoPlayDelay}ms linear`;
                        progressBar.style.transform = 'translateX(0)';
                    });
                });
            };

            const goToSlide = (i) => {
                index = (i + slides.length) % slides.length;
                track.style.transform = `translateX(-${index * 100}%)`;
                updateDots();
                resetAutoplay();
            };

            const nextSlide = () => goToSlide(index + 1);
            const prevSlide = () => goToSlide(index - 1);

            const startAutoplay = () => {
                animateProgress();
                autoPlayTimeout = setTimeout(() => goToSlide(index + 1), autoPlayDelay);
            };

            const resetAutoplay = () => {
                clearTimeout(autoPlayTimeout);
                startAutoplay();
            };

            slider.querySelector('.poster-next').addEventListener('click', nextSlide);
            slider.querySelector('.poster-prev').addEventListener('click', prevSlide);

            slider.addEventListener('mouseenter', () => clearTimeout(autoPlayTimeout));
            slider.addEventListener('mouseleave', startAutoplay);

            createDots();
            createProgressBar();
            startAutoplay();
        })();

        // Module slider - removed navigation buttons, cards display in grid

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offset = 80; // Account for sticky nav
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - offset;
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Form submission handling with AJAX
        const form = document.getElementById('waitlist-form');
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const submitBtn = document.getElementById('submit-btn');
                const formData = new FormData(form);
                
                // Disable submit button
                if (submitBtn) {
                    submitBtn.disabled = true;
                }
                
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Remove any existing messages
                        const existingMessages = form.parentElement.querySelectorAll('.bg-green-50, .bg-red-50');
                        existingMessages.forEach(msg => msg.remove());
                        
                        // Show success message with icon
                        const successDiv = document.createElement('div');
                        successDiv.className = 'mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg animate-fade-in';
                        successDiv.innerHTML = `
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>${data.message}</span>
                            </div>
                        `;
                        form.parentElement.insertBefore(successDiv, form);
                        
                        // Reset form
                        form.reset();
                        
                        // Track conversion
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'waitlist_signup', {
                                'event_category': 'conversion',
                                'event_label': 'waitlist_form'
                            });
                        }
                        
                        // Scroll to success message
                        successDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        // Show errors
                        let errorHtml = '<div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg"><p class="font-semibold mb-2">Please correct the following errors:</p><ul class="list-disc list-inside space-y-1">';
                        
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                data.errors[key].forEach(error => {
                                    errorHtml += `<li>${error}</li>`;
                                });
                            });
                        } else {
                            errorHtml += `<li>${data.message || 'Something went wrong. Please try again.'}</li>`;
                        }
                        
                        errorHtml += '</ul></div>';
                        
                        const existingError = form.parentElement.querySelector('.bg-red-50');
                        if (existingError) {
                            existingError.outerHTML = errorHtml;
                        } else {
                            form.parentElement.insertAdjacentHTML('afterbegin', errorHtml);
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Something went wrong. Please try again.');
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                    }
                }
            });
        }

        // Track form views for analytics
        if (typeof gtag !== 'undefined') {
            document.getElementById('waitlist-form')?.addEventListener('focus', function() {
                gtag('event', 'waitlist_form_view', {
                    'event_category': 'engagement',
                    'event_label': 'waitlist_form'
                });
            }, true);
        }
    </script>
</body>
</html>

