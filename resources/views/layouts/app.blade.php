<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-icon.png') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap');

        body {
            font-family: "DM Sans", sans-serif;
        }

        .main-nav-left ul li {
            margin-bottom: 10px;
        }

        .main-nav-left ul li.active a {
            background-color: #F5F6FA;
            color: #0053FF;
        }
        
        /* Alpine.js x-cloak */
        [x-cloak] { display: none !important; }
    </style>
    <!-- Styles -->
    @stack('styles')
    @livewireStyles

    <!-- Vite -->
    @vite([ 'resources/js/app.js'])

</head>

<body class="flex-1 flex flex-col overflow-hidden">

    <x-banner />

    <div class="flex h-screen bg-gray-100 overflow-hidden">

        {{-- Sidebar --}}
        @include('livewire.sidebar')

        {{-- Content --}}
        <div
            class="flex-1 transition-all duration-300"
            :class="{ 'ml-72': $store.sidebar?.isOpen && window.innerWidth >= 768, 'ml-16': !$store.sidebar?.isOpen && window.innerWidth >= 768 }"
            x-data>

            @livewire('navigation-menu')

            @if (isset($header))
            <header class="bg-white shadow">
                <div>
                    {{ $header }}
                </div>
            </header>
            @endif

            @php
            // No spacing only for Change Password page
            $mainClasses = request()->routeIs('password.change')
            ? 'flex-1 overflow-y-auto !p-0 !m-0'
            : 'flex-1 overflow-y-auto p-6';
            @endphp

            <main class="{{ $mainClasses }} h-[calc(100vh-74px)] mx-0">
                @if(session('subscription_required'))
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        let currentRoute = "{{ Route::currentRouteName() }}";

                        // Timer: 4000ms for dashboard, 4000ms for redirect pages
                        let timer = 4000;

                        Swal.fire({
                            title: "Subscription Required",
                            text: "You don’t have an active subscription. Please subscribe to use all features.",
                            icon: "warning",
                            confirmButtonText: "Go to Subscription",
                            confirmButtonColor: "#0053FF", // 🔹 Replace with your theme color
                            customClass: {
                                confirmButton: 'text-white' // 🔹 Tailwind text color (or custom CSS class)
                            },
                            timer: currentRoute === "dashboard" ? 4000 : timer,
                            timerProgressBar: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('subscription.page') }}";
                            } else if (currentRoute !== "dashboard") {
                                // Auto redirect after popup closes for non-dashboard pages
                                window.location.href = "{{ route('subscription.page') }}";
                            }
                        });

                        // Extra: auto-close popup for dashboard after 4s without redirect
                        if (currentRoute === "dashboard") {
                            setTimeout(() => {
                                Swal.close();
                            }, 4000);
                        }
                    });
                </script>
                @endif
                {{ $slot }}
            </main>

        </div>
    </div>

    @stack('modals')
    @livewireScripts
    @stack('scripts')
    
    <script>
        // Initialize Alpine store for sidebar - ensure it's available early
        if (typeof Alpine !== 'undefined') {
            document.addEventListener('alpine:init', () => {
                Alpine.store('sidebar', {
                    isOpen: window.innerWidth >= 768,
                    toggle() {
                        this.isOpen = !this.isOpen;
                    }
                });
            });
        } else {
            // Fallback: wait for Alpine to load
            document.addEventListener('DOMContentLoaded', () => {
                if (window.Alpine) {
                    window.Alpine.store('sidebar', {
                        isOpen: window.innerWidth >= 768,
                        toggle() {
                            this.isOpen = !this.isOpen;
                        }
                    });
                }
            });
        }
    </script>
</body>

</html>