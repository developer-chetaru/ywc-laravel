<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>YWC</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="description" content="Yacht Workers Council - The premier platform for yacht crew networking, career management, and industry insights. Join our waitlist today!">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- ========== main css ========== -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- ========== main css ========== -->
</head>

<body>


    <!-- header section -->
    <header>
        <div class="container">
            <div class="row">
                <div class="logo">
                    <a href="{{ route('landing') }}">
                        <img src="{{ asset('assets/images/header-logo.svg') }}" alt="YWC Logo">
                    </a>
                </div>
                <div class="header-btn">
                    <a href="{{ route('login') }}" class="login-btn common-btn">Login</a>
                </div>
            </div>
        </div>
    </header>
    <!-- header section -->

    <!-- banner section -->
    <section class="banner" style="background-image: url({{ asset('assets/images/banner-img.jpg') }});">
        <div class="container">
            <div class="row">
                <div class="banner-left">
                    <div class="banner-left-content">
                        <h1>Built for Crew, by Crew</h1>
                        <p>The premier platform for yacht crew networking, career management, and industry insights.
                            Join thousands of crew members already on board.</p>
                    </div>
                </div>
                <div class="banner-right">
                    <div class="banner-form" id="waitlist">
                        <h3>Join Our Waitlist</h3>
                        <p>Be among the first to access Yacht Workers Council when we launch</p>
                        
                        @if(session('success'))
                            <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                                <p style="font-weight: bold; margin-bottom: 8px;">Please correct the following errors:</p>
                                <ul style="list-style: disc; padding-left: 20px; margin: 0;">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('error'))
                            <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('waitlist.join') }}" method="POST" id="waitlist-form">
                            @csrf

                            <div class="gfield">
                                <label for="first_name">First Name</label>
                                <input type="text" name="first_name" id="first_name" placeholder="Enter your first name" value="{{ old('first_name') }}">
                            </div>

                            <div class="gfield">
                                <label for="last_name">Last Name</label>
                                <input type="text" name="last_name" id="last_name" placeholder="Enter your last name" value="{{ old('last_name') }}">
                            </div>

                            <div class="gfield">
                                <label for="email">Email Address*</label>
                                <input type="email" name="email" id="email" placeholder="Enter you email address" value="{{ old('email') }}" required>
                            </div>

                            <div class="gfield">
                                <label for="role">Your Role (Optional)</label>
                                <select name="role" id="role">
                                    <option value="">Select your role</option>
                                    <option value="captain" {{ old('role') == 'captain' ? 'selected' : '' }}>Captain</option>
                                    <option value="deck" {{ old('role') == 'deck' ? 'selected' : '' }}>Deck</option>
                                    <option value="interior" {{ old('role') == 'interior' ? 'selected' : '' }}>Interior</option>
                                    <option value="engineer" {{ old('role') == 'engineer' ? 'selected' : '' }}>Engineer</option>
                                    <option value="chef" {{ old('role') == 'chef' ? 'selected' : '' }}>Chef</option>
                                    <option value="stewardess" {{ old('role') == 'stewardess' ? 'selected' : '' }}>Stewardess</option>
                                    <option value="other" {{ old('role') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <input type="submit" value="Join Waitlist" id="submit-btn">
                            <p>By joining, you agree to receive updates about Yacht Workers Council. We respect your
                                privacy.</p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- banner section -->

    <!-- features section -->
    <section class="features-section">
        <div class="container">
            <div class="section-heading">
                <h2>Built to Make Life at Sea Smoother</h2>
                <p>From communication to contractor reviews, these intelligent tools support captains and crew at every stage of onboard operations.</p>
            </div>
            <div class="features-grid">
                <a href="#" class="features-box">
                    <div class="features-img">
                        <img src="{{ asset('assets/images/features1.jpg') }}" alt="Verified Crew Network">
                    </div>
                    <div class="features-content">
                        <h4>Verified Crew Network</h4>
                        <p>Discover nearby crew, share opportunities, and manage trusted connections securely.</p>
                    </div>
                </a>
                <a href="#" class="features-box">
                    <div class="features-img">
                        <img src="{{ asset('assets/images/features2.jpg') }}" alt="Industry Review System">
                    </div>
                    <div class="features-content">
                        <h4>Industry Review System</h4>
                        <p>Get honest reviews about contractors, marinas, yachts, suppliers, and service providers from verified crew members.</p>
                    </div>
                </a>
                <a href="#" class="features-box">
                    <div class="features-img">
                        <img src="{{ asset('assets/images/features3.jpg') }}" alt="Live Department Forums">
                    </div>
                    <div class="features-content">
                        <h4>Live Department Forums</h4>
                        <p>Host focused discussions for captains, deck, engineering, and interior teams with smart
                            notifications.</p>
                    </div>
                </a>
                <a href="#" class="features-box">
                    <div class="features-img">
                        <img src="{{ asset('assets/images/features4live.jpg') }}" alt="Find Training Providers">
                    </div>
                    <div class="features-content">
                        <h4>Find Training Providers</h4>
                        <p>Discover verified training providers, compare courses, and access approved certification programs all in one place.</p>
                    </div>
                </a>
            </div>
        </div>
    </section>
    <!-- features section -->

    <!-- platform section -->
    <section class="platform">
        <div class="container">
            <div class="section-heading">
                <h2>Everything You Need—All in One Platform</h2>
                <p>Simplifying your life at sea with tools built for the realities of yacht work.</p>
            </div>
            <div class="platform-grid">
                <div class="platform-box">
                    <div class="platform-icon">
                        <img src="{{ asset('assets/images/platform1.svg') }}" alt="Documents & Career History">
                    </div>
                    <div class="platform-content">
                        <h4>Documents & Career History</h4>
                        <p>Keep all your certificates, contracts, and experience neatly organised with automated
                            reminders.</p>
                    </div>
                </div>

                <div class="platform-box">
                    <div class="platform-icon">
                        <img src="{{ asset('assets/images/platform2.svg') }}" alt="Legal Support">
                    </div>
                    <div class="platform-content">
                        <h4>Legal Support</h4>
                        <p>Access trusted maritime legal guidance whenever you need help with contracts, disputes, or
                            rights.</p>
                    </div>
                </div>

                <div class="platform-box">
                    <div class="platform-icon">
                        <img src="{{ asset('assets/images/platform3.svg') }}" alt="Training & Resources">
                    </div>
                    <div class="platform-content">
                        <h4>Training & Resources</h4>
                        <p>Find approved training courses, skill-building material, and career development resources.
                        </p>
                    </div>
                </div>

                <div class="platform-box">
                    <div class="platform-icon">
                        <img src="{{ asset('assets/images/platform4.svg') }}" alt="Mental Health Support">
                    </div>
                    <div class="platform-content">
                        <h4>Mental Health Support</h4>
                        <p>Confidential, crew-focused mental wellness support designed for life at sea.</p>
                    </div>
                </div>

                <div class="platform-box">
                    <div class="platform-icon">
                        <img src="{{ asset('assets/images/platform5.svg') }}" alt="Pension & Investment Advice">
                    </div>
                    <div class="platform-content">
                        <h4>Pension & Investment Advice</h4>
                        <p>Get expert guidance on building a stable financial future with global pension and investment
                            options.</p>
                    </div>
                </div>

                <div class="platform-box">
                    <div class="platform-icon">
                        <img src="{{ asset('assets/images/platform6.svg') }}" alt="Department Forums">
                    </div>
                    <div class="platform-content">
                        <h4>Department Forums</h4>
                        <p>Connect with crew from every department to share knowledge, ask questions, and stay informed.
                        </p>
                    </div>
                </div>

                <div class="platform-box">
                    <div class="platform-icon">
                        <img src="{{ asset('assets/images/platform7.svg') }}" alt="Work Log">
                    </div>
                    <div class="platform-content">
                        <h4>Work Log</h4>
                        <p>Easily track your working hours, sea time, and onboard duties for accurate career records.
                        </p>
                    </div>
                </div>

                <div class="platform-box">
                    <div class="platform-icon">
                        <img src="{{ asset('assets/images/platform8.svg') }}" alt="Contractors">
                    </div>
                    <div class="platform-content">
                        <h4>Contractors</h4>
                        <p>The Complete, Fully Searchable Contractor Ecosystem for the Yachting Industry.</p>
                        <!-- @guest
                            <a href="{{ route('financial.calculators.index') }}" 
                               class="inline-block mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                Get Started →
                            </a>
                        @else
                            <a href="{{ route('financial.dashboard') }}" 
                               class="inline-block mt-3 text-blue-600 hover:text-blue-800 font-medium text-sm">
                                Access Dashboard →
                            </a>
                        @endguest -->
                    </div>
                </div>

                <div class="platform-box">
                    <div class="platform-icon">
                        <img src="{{ asset('assets/images/platform9.svg') }}" alt="Industry Review System">
                    </div>
                    <div class="platform-content">
                        <h4>Industry Review System</h4>
                        <p>Get honest reviews about yachts, marinas, and service providers from verified crew members.
                        </p>
                    </div>
                </div>

                <div class="platform-box">
                    <div class="platform-icon">
                        <img src="{{ asset('assets/images/platform10.svg') }}" alt="Marketplace">
                    </div>
                    <div class="platform-content">
                        <h4>Marketplace</h4>
                        <p>Sell parts, spares, tenders, and all yacht related equipment in one dedicated marketplace.</p>
                    </div>
                </div>

                <div class="platform-box">
                    <div class="platform-icon">
                        <img src="{{ asset('assets/images/platform11.svg') }}" alt="Itinerary System">
                    </div>
                    <div class="platform-content">
                        <h4>Itinerary System</h4>
                        <p>Create, manage, and share professional trip itineraries with photos, maps, and daily
                            breakdowns.</p>
                    </div>
                </div>

                <div class="platform-box">
                    <div class="platform-icon">
                        <img src="{{ asset('assets/images/platform12.svg') }}" alt="Crew Discovery">
                    </div>
                    <div class="platform-content">
                        <h4>Crew Discovery</h4>
                        <p>Find and connect with verified crew members based on skills, experience, and roles.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- platform section -->

    <!-- waitlist section -->
    <section class="waitlist">
        <div class="container">
            <div class="row">
                <div class="wishlist-left">
                    <div class="section-heading">
                        <h2>From Waitlist to Crew Ready</h2>
                        <p>The first 1,000 members receive a permanent 2× engagement score.
                        Early contributors shape the platform. To recognise those who help build YWC from day one, founding members earn double engagement credit on all activity.</p>
                    </div>
                </div>
                <div class="wishlist-right">
                    <div class="wishlist-right-content">
                        <div class="wishlist-box row">
                            <div class="wishlist-box-left">
                                <span>01</span>
                            </div>
                            <div class="wishlist-box-right">
                                <h4>Join the waitlist</h4>
                                <p>Create your profile and select your role. Applications are reviewed to keep the platform credible, relevant, and crew-focused.</p>
                            </div>
                        </div>

                        <div class="wishlist-box row">
                            <div class="wishlist-box-left">
                                <span>02</span>
                            </div>
                            <div class="wishlist-box-right">
                                <h4>Role-based onboarding</h4>
                                <p>You’re onboarded based on how you actually work on board. Access is structured by role, not hierarchy or job title.</p>
                            </div>
                        </div>

                        <div class="wishlist-box row">
                            <div class="wishlist-box-left">
                                <span>03</span>
                            </div>
                            <div class="wishlist-box-right">
                                <h4>Shape what gets built</h4>
                                <p>Early members help steer YWC. Focused feedback, feature testing, and direct input into what gets built next.</p>
                            </div>
                        </div>

                        <div class="wishlist-box row">
                            <div class="wishlist-box-left">
                                <span>04</span>
                            </div>
                            <div class="wishlist-box-right">
                                <h4>Full platform access</h4>
                                <p>Contractors and reviews. Operational tools. Compliance visibility. Crew networks. One platform. Built for life on board.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- waitlist section -->

    <!-- transform section -->
     <section class="transform">
        <div class="container">
            <div class="row">
                <div class="transform-left">
                    <div class="section-heading">
                        <h2>Ready to Transform Your Yachting Career?</h2>
                    </div>
                </div>
                <div class="transform-right">
                    <div class="transform-right-content">
                        <p>Join our waitlist today and be among the first to experience the future of crew networking.</p>
                        <a href="#waitlist" class="common-btn">Join Waitlist Now</a>
                    </div>
                </div>
            </div>
        </div>
     </section>
    <!-- transform section -->

    <!-- footer section -->
     <footer>
        <div class="container">
            <a href="{{ route('landing') }}" class="footer-logo">
                <img src="{{ asset('assets/images/footer-logo.svg') }}" alt="YWC Logo">
                
            </a>
            <h3>Built for crew, by crew. The premier platform for yacht crew professionals.</h3>
            <div class="footer-social row">
                <a href="#" target="_blank" rel="noopener noreferrer">
                    <img src="{{ asset('assets/images/linkedin.svg') }}" alt="LinkedIn">
                </a>
                <a href="https://www.instagram.com/yachtworkerscouncil/" target="_blank" rel="noopener noreferrer">
                    <img src="{{ asset('assets/images/instagram.svg') }}" alt="Instagram">
                </a>
                <a href="https://www.youtube.com/@Yachtworkerscouncil" target="_blank" rel="noopener noreferrer">
                    <img src="{{ asset('assets/images/youtube.svg') }}" alt="YouTube">
                </a>
            </div>
            <p class="footer-copy">
                © {{ date('Y') }} Yacht Workers Council
            </p>
        </div>
     </footer>
    <!-- footer section -->

    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offset = 100;
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - offset;
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Form submission handling with AJAX (optional enhancement)
        const waitlistForm = document.getElementById('waitlist-form');
        if (waitlistForm) {
            waitlistForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const submitBtn = document.getElementById('submit-btn');
                const formData = new FormData(waitlistForm);
                
                // Disable submit button
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.value = 'Submitting...';
                }
                
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;
                    
                    const response = await fetch(waitlistForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Show success message
                        const successDiv = document.createElement('div');
                        successDiv.style.cssText = 'background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px;';
                        successDiv.textContent = data.message;
                        waitlistForm.parentElement.insertBefore(successDiv, waitlistForm);
                        
                        // Reset form
                        waitlistForm.reset();
                        
                        // Scroll to success message
                        successDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        // Show errors
                        let errorHtml = '<div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px;"><p style="font-weight: bold; margin-bottom: 8px;">Please correct the following errors:</p><ul style="list-style: disc; padding-left: 20px; margin: 0;">';
                        
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                data.errors[key].forEach(error => {
                                    errorHtml += '<li>' + error + '</li>';
                                });
                            });
                        } else {
                            errorHtml += '<li>' + (data.message || 'Something went wrong. Please try again.') + '</li>';
                        }
                        
                        errorHtml += '</ul></div>';
                        
                        const existingError = waitlistForm.parentElement.querySelector('div[style*="background-color: #f8d7da"]');
                        if (existingError) {
                            existingError.outerHTML = errorHtml;
                        } else {
                            waitlistForm.parentElement.insertAdjacentHTML('afterbegin', errorHtml);
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    // Fall back to normal form submission
                    waitlistForm.submit();
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.value = 'Join Waitlist';
                    }
                }
            });
        }
    </script>

</body>

</html>
