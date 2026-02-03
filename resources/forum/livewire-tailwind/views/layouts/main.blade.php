<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>
        @if (isset($thread))
            {{ $thread->title }} —
        @endif
        @if (isset($category))
            {{ $category->title }} —
        @endif
        {{ trans('forum::general.home_title') }}
    </title>

    <!-- Fonts (Designer style) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-icon.png') }}">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind (Designer style) -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Quill.js Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <style>
        .ql-editor {
            min-height: 200px;
            font-size: 14px;
        }
        .ql-editor.ql-blank::before {
            color: #9ca3af;
            font-style: normal;
        }
        .ql-container {
            font-family: 'DM Sans', sans-serif;
        }
        .ql-snow .ql-tooltip {
            z-index: 1000;
        }
        .ql-snow .ql-picker {
            color: #374151;
        }
        .ql-snow .ql-stroke {
            stroke: #374151;
        }
        .ql-snow .ql-fill {
            fill: #374151;
        }
    </style>
    
    <!-- Compiled CSS/JS -->
    @vite([
        'resources/forum/livewire-tailwind/css/forum.css',
        'resources/forum/livewire-tailwind/js/forum.js',
        'resources/css/app.css',
        'resources/js/app.js',
    ])
    <style>
    .user-forum .chat-box {
    height: calc(100vh - 100px) !important;
}
</style>
</head>
<body class="forum h-screen overflow-hidden font-[Figtree] bg-gray-100 text-gray-800">

    {{-- Sidebar (Fixed Position) --}}
    @include('livewire.sidebar')

    {{-- Content (Adjusts margin based on sidebar state) --}}
    <div 
        class="flex flex-col h-screen transition-all duration-300 overflow-hidden"
        :class="{ 'ml-72': $store.sidebar?.isOpen && window.innerWidth >= 768, 'ml-16': !$store.sidebar?.isOpen && window.innerWidth >= 768 }"
        x-data>
        @livewire('navigation-menu')

        @if (isset($header))
            <header class="bg-white shadow px-6 py-4 flex-shrink-0 border-b border-gray-200">
                <div class="text-lg font-semibold text-gray-900">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="flex-1 overflow-y-auto p-4">
            {{ $slot }}
        </main>

        <livewire:forum::components.alerts />
    </div>

    <!-- Alpine + Navbar -->
    <script type="module">
        document.addEventListener('alpine:init', () => {
            Alpine.store('time', {
                now: new Date(),
                init() {
                    setInterval(() => {
                        this.now = new Date();
                    }, 1000);
                }
            })
        });

        Alpine.data('navbar', () => {
            return {
                isMenuCollapsed: true,
                isUserDropdownCollapsed: true,
                toggleMenu() {
                    this.isMenuCollapsed = !this.isMenuCollapsed;
                },
                closeMenu() {
                    this.isMenuCollapsed = true;
                },
                toggleUserDropdown() {
                    this.isUserDropdownCollapsed = !this.isUserDropdownCollapsed;
                },
                closeUserDropdown() {
                    this.isUserDropdownCollapsed = true;
                },
                logout() {
                    const csrfToken = document.head.querySelector("[name=csrf-token]").content;

                    fetch('/logout', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    }).then(() => { window.location.reload(); });
                }
            }
        });
    </script>
</body>
</html>

