<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} — YScrapy</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN Fallback) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        neu: {
                            sand: '#E7E7DD',
                            white: '#FFFFF7',
                            black: '#1D1D1B',
                            muted: '#555552',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        body {
            background-color: #E7E7DD;
            color: #1D1D1B;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        /* True Neumorphism (Soft UI) Styles */
        .neu-card {
            background: #E7E7DD;
            border-radius: 1.25rem;
            box-shadow: 9px 9px 20px #c5c5ba, -9px -9px 20px #ffffff;
            border: none;
            transition: all 0.25s ease-in-out;
        }

        .neu-card:hover {
            box-shadow: 12px 12px 24px #bebeb3, -12px -12px 24px #ffffff;
        }

        .neu-panel {
            background: #E7E7DD;
            border-radius: 1.25rem;
            box-shadow: 8px 8px 18px #c5c5ba, -8px -8px 18px #ffffff;
            border: none;
        }

        .neu-subcard {
            background: #E7E7DD;
            border-radius: 1rem;
            box-shadow: 5px 5px 12px #c5c5ba, -5px -5px 12px #ffffff;
            border: none;
            transition: all 0.2s ease;
        }

        .neu-subcard:hover {
            box-shadow: 7px 7px 16px #bebeb3, -7px -7px 16px #ffffff;
        }

        .neu-inset {
            background: #E7E7DD;
            border-radius: 0.875rem;
            box-shadow: inset 4px 4px 8px #c5c5ba, inset -4px -4px 8px #ffffff;
            border: none;
        }

        .neu-input {
            background: #E7E7DD;
            color: #1D1D1B;
            border-radius: 0.875rem;
            box-shadow: inset 4px 4px 8px #c5c5ba, inset -4px -4px 8px #ffffff;
            border: none;
            transition: all 0.2s ease;
        }

        .neu-input:focus {
            outline: none;
            box-shadow: inset 5px 5px 10px #bebeb3, inset -5px -5px 10px #ffffff, 0 0 0 1px rgba(29, 29, 27, 0.15);
        }

        .neu-btn-primary {
            background: #1D1D1B;
            color: #FFFFF7;
            border-radius: 0.875rem;
            box-shadow: 5px 5px 14px rgba(29, 29, 27, 0.3), -3px -3px 8px #ffffff;
            border: none;
            transition: all 0.2s ease;
        }

        .neu-btn-primary:hover {
            background: #2b2b28;
            box-shadow: 3px 3px 8px rgba(29, 29, 27, 0.4);
        }

        .neu-btn-secondary {
            background: #E7E7DD;
            color: #1D1D1B;
            border-radius: 0.875rem;
            box-shadow: 5px 5px 12px #c5c5ba, -5px -5px 12px #ffffff;
            border: none;
            transition: all 0.2s ease;
        }

        .neu-btn-secondary:hover {
            box-shadow: 3px 3px 8px #c5c5ba, -3px -3px 8px #ffffff;
        }

        .neu-btn-secondary:active,
        .neu-btn-active {
            box-shadow: inset 3px 3px 6px #c5c5ba, inset -3px -3px 6px #ffffff;
        }

        /* Subtle scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #E7E7DD; }
        ::-webkit-scrollbar-thumb { background: #c5c5ba; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #aeaea2; }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <!-- Navbar (Neumorphic Minimalist) -->
    <header class="sticky top-0 z-50 bg-[#E7E7DD] shadow-[0_4px_14px_#c5c5ba]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 rounded-xl bg-[#1D1D1B] flex items-center justify-center font-bold text-xs text-[#FFFFF7] shadow-[3px_3px_8px_rgba(29,29,27,0.3),-2px_-2px_6px_#ffffff]">
                            YS
                        </div>
                        <span class="text-lg font-bold tracking-tight text-[#1D1D1B]">YScrapy</span>
                    </a>
                </div>

                <!-- Navigation Links (Neumorphic Soft Tabs) -->
                <nav class="hidden md:flex items-center space-x-2">
                    <a href="{{ route('dashboard') }}" 
                       class="px-4 py-2 rounded-xl text-xs font-semibold tracking-wider transition {{ request()->routeIs('dashboard') ? 'neu-inset text-[#1D1D1B]' : 'text-[#555552] hover:text-[#1D1D1B] hover:neu-btn-secondary' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('scraper.index') }}" 
                       class="px-4 py-2 rounded-xl text-xs font-semibold tracking-wider transition {{ request()->routeIs('scraper.*') ? 'neu-inset text-[#1D1D1B]' : 'text-[#555552] hover:text-[#1D1D1B] hover:neu-btn-secondary' }}">
                        Scraper Manual
                    </a>
                    <a href="{{ route('trending.index') }}" 
                       class="px-4 py-2 rounded-xl text-xs font-semibold tracking-wider transition {{ request()->routeIs('trending.*') ? 'neu-inset text-[#1D1D1B]' : 'text-[#555552] hover:text-[#1D1D1B] hover:neu-btn-secondary' }}">
                        Isu Trending
                    </a>
                    <a href="{{ route('watchlist.index') }}" 
                       class="px-4 py-2 rounded-xl text-xs font-semibold tracking-wider transition {{ request()->routeIs('watchlist.*') ? 'neu-inset text-[#1D1D1B]' : 'text-[#555552] hover:text-[#1D1D1B] hover:neu-btn-secondary' }}">
                        Pantauan Otomatis
                    </a>
                    <a href="{{ route('feedbacks.index') }}" 
                       class="px-4 py-2 rounded-xl text-xs font-semibold tracking-wider transition {{ request()->routeIs('feedbacks.*') ? 'neu-inset text-[#1D1D1B]' : 'text-[#555552] hover:text-[#1D1D1B] hover:neu-btn-secondary' }}">
                        Semua Feedback
                    </a>
                </nav>

                <!-- Action Button -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('scraper.index') }}" class="neu-btn-primary px-4 py-2 text-xs font-medium flex items-center space-x-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Scrape Baru</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 neu-card text-[#2E7D32] flex items-center justify-between text-xs sm:text-sm font-medium">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-[#2E7D32] hover:opacity-75 font-bold text-base">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 neu-card text-[#C62828] flex items-center justify-between text-xs sm:text-sm font-medium">
                <span>{{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-[#C62828] hover:opacity-75 font-bold text-base">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-[#E7E7DD] mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-[#555552] space-y-2 sm:space-y-0">
            <div>
                <span class="font-bold text-[#1D1D1B]">YScrapy</span> &mdash; Multi-Platform Web Scraper & Public Sentiment Analysis
            </div>
            <div>
                <span>Palette: Veersa (White Sand & Charcoal)</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
