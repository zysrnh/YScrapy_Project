<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} — YScrapy</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
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
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
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
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.01em;
            min-height: 100vh;
        }

        /* Neumorphism (Soft UI) Styles */
        .neu-card {
            background: #E7E7DD;
            border-radius: 1.25rem;
            box-shadow: 9px 9px 20px #c5c5ba, -9px -9px 20px #ffffff;
            border: none;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .neu-card:hover {
            transform: translateY(-3px);
            box-shadow: 12px 12px 26px #bebeb3, -12px -12px 26px #ffffff;
        }

        .neu-panel {
            background: #E7E7DD;
            border-radius: 1.25rem;
            box-shadow: 8px 8px 18px #c5c5ba, -8px -8px 18px #ffffff;
            border: none;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .neu-sidebar {
            background: #E7E7DD;
            box-shadow: 6px 0 20px #c5c5ba;
            border: none;
        }

        .neu-subcard {
            background: #E7E7DD;
            border-radius: 1rem;
            box-shadow: 5px 5px 12px #c5c5ba, -5px -5px 12px #ffffff;
            border: none;
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .neu-subcard:hover {
            transform: translateY(-2px);
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
            box-shadow: inset 5px 5px 10px #bebeb3, inset -5px -5px 10px #ffffff, 0 0 0 1px rgba(29, 29, 27, 0.2);
        }

        .neu-btn-primary {
            background: #1D1D1B;
            color: #FFFFF7;
            border-radius: 0.875rem;
            box-shadow: 5px 5px 14px rgba(29, 29, 27, 0.3), -3px -3px 8px #ffffff;
            border: none;
            transition: transform 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
        }

        .neu-btn-primary:hover {
            background: #2b2b28;
            transform: translateY(-2px);
            box-shadow: 6px 6px 16px rgba(29, 29, 27, 0.38);
        }

        .neu-btn-primary:active {
            transform: translateY(1px);
            box-shadow: 2px 2px 6px rgba(29, 29, 27, 0.4);
        }

        .neu-btn-secondary {
            background: #E7E7DD;
            color: #1D1D1B;
            border-radius: 0.875rem;
            box-shadow: 5px 5px 12px #c5c5ba, -5px -5px 12px #ffffff;
            border: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .neu-btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 7px 7px 15px #bebeb3, -7px -7px 15px #ffffff;
        }

        .neu-btn-secondary:active,
        .neu-btn-active {
            transform: translateY(1px);
            box-shadow: inset 3px 3px 6px #c5c5ba, inset -3px -3px 6px #ffffff;
        }

        /* Nav Item Sidebar */
        .neu-nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 0.875rem;
            font-size: 0.8125rem;
            font-weight: 600;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .neu-nav-item.active {
            box-shadow: inset 4px 4px 8px #c5c5ba, inset -4px -4px 8px #ffffff;
            color: #1D1D1B;
        }

        .neu-nav-item:not(.active) {
            color: #555552;
        }

        .neu-nav-item:not(.active):hover {
            color: #1D1D1B;
            transform: translateX(4px);
            box-shadow: 4px 4px 10px #c5c5ba, -4px -4px 10px #ffffff;
        }

        /* Keyframes */
        @keyframes neuFadeUp {
            0% {
                opacity: 0;
                transform: translateY(18px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes neuPulseLive {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.4;
                transform: scale(1.18);
            }
        }

        .neu-entrance {
            animation: neuFadeUp 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .neu-delay-1 { animation-delay: 0.05s; }
        .neu-delay-2 { animation-delay: 0.10s; }
        .neu-delay-3 { animation-delay: 0.15s; }
        .neu-delay-4 { animation-delay: 0.20s; }
        .neu-delay-5 { animation-delay: 0.25s; }
        .neu-delay-6 { animation-delay: 0.30s; }

        .neu-pulse-dot {
            animation: neuPulseLive 2s infinite ease-in-out;
        }

        /* Subtle scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #E7E7DD; }
        ::-webkit-scrollbar-thumb { background: #c5c5ba; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #aeaea2; }
    </style>
</head>
<body class="flex min-h-screen bg-[#E7E7DD]">
    <!-- Neumorphic Left Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 md:w-72 neu-sidebar flex flex-col justify-between p-6 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="space-y-8">
            <!-- Brand Logo -->
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-2xl bg-[#1D1D1B] flex items-center justify-center font-extrabold text-sm text-[#FFFFF7] shadow-[4px_4px_10px_rgba(29,29,27,0.35),-2px_-2px_6px_#ffffff]">
                    YS
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-[#1D1D1B] block">YScrapy</span>
                    <span class="text-[9px] font-bold tracking-widest text-[#555552] uppercase block">Public Sentiment AI</span>
                </div>
            </div>

            <!-- Action Button -->
            <div>
                <a href="{{ route('scraper.index') }}" class="neu-btn-primary w-full py-3 px-4 text-xs font-bold flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Mulai Scrape Baru</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="neu-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4 mr-3 {{ request()->routeIs('dashboard') ? 'text-[#1D1D1B]' : 'text-[#555552]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Scraper Manual -->
                <a href="{{ route('scraper.index') }}" 
                   class="neu-nav-item {{ request()->routeIs('scraper.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 mr-3 {{ request()->routeIs('scraper.*') ? 'text-[#1D1D1B]' : 'text-[#555552]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span>Scraper Engine</span>
                </a>

                <!-- Isu Trending -->
                <a href="{{ route('trending.index') }}" 
                   class="neu-nav-item {{ request()->routeIs('trending.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 mr-3 {{ request()->routeIs('trending.*') ? 'text-[#1D1D1B]' : 'text-[#555552]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    <span>Isu Trending</span>
                </a>

                <!-- Pantauan Otomatis -->
                <a href="{{ route('watchlist.index') }}" 
                   class="neu-nav-item {{ request()->routeIs('watchlist.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 mr-3 {{ request()->routeIs('watchlist.*') ? 'text-[#1D1D1B]' : 'text-[#555552]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Pantauan Otomatis</span>
                </a>

                <!-- Semua Feedback -->
                <a href="{{ route('feedbacks.index') }}" 
                   class="neu-nav-item {{ request()->routeIs('feedbacks.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 mr-3 {{ request()->routeIs('feedbacks.*') ? 'text-[#1D1D1B]' : 'text-[#555552]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span>Semua Feedback</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Bottom: System Live Indicator -->
        <div class="space-y-3 pt-6">
            <div class="p-3.5 rounded-xl neu-inset">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#2E7D32] neu-pulse-dot"></span>
                    <span class="text-xs font-bold text-[#1D1D1B]">Sistem Siap & Aktif</span>
                </div>
                <p class="text-[10px] text-[#555552] mt-1">Lexicon Indonesia + Multi-Sumber</p>
            </div>

            <div class="text-[10px] text-[#555552] text-center">
                YScrapy &bull; Soft UI
            </div>
        </div>
    </aside>

    <!-- Overlay for mobile drawer -->
    <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/20 z-40 hidden md:hidden"></div>

    <!-- Main Content Area (Beside Sidebar) -->
    <div class="flex-1 flex flex-col min-h-screen md:ml-64 lg:ml-72 transition-all">
        <!-- Top App Bar -->
        <header class="sticky top-0 z-30 bg-[#E7E7DD]/90 backdrop-blur-md px-6 py-4 flex items-center justify-between shadow-[0_4px_14px_rgba(197,197,186,0.5)]">
            <div class="flex items-center space-x-3">
                <!-- Mobile Menu Button -->
                <button type="button" onclick="toggleSidebar()" class="md:hidden p-2 rounded-xl neu-btn-secondary text-[#1D1D1B]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-[#1D1D1B]">{{ $title ?? 'Dashboard Sentimen' }}</h2>
                    <p class="text-[11px] text-[#555552]">Platform Analisis Opini Publik Nasional</p>
                </div>
            </div>

            <div class="flex items-center space-x-2.5">
                <a href="{{ route('export.csv') }}" class="neu-btn-secondary px-3.5 py-1.5 text-xs font-semibold flex items-center space-x-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span class="hidden sm:inline">Export CSV</span>
                </a>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-grow px-6 sm:px-8 lg:px-10 py-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 neu-card text-[#2E7D32] flex items-center justify-between text-xs sm:text-sm font-semibold neu-entrance">
                    <span>{{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-[#2E7D32] hover:opacity-75 font-bold text-base">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 neu-card text-[#C62828] flex items-center justify-between text-xs sm:text-sm font-semibold neu-entrance">
                    <span>{{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-[#C62828] hover:opacity-75 font-bold text-base">&times;</button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-[#E7E7DD] py-6 px-6 sm:px-8 lg:px-10 text-xs text-[#555552] flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                <span class="font-extrabold text-[#1D1D1B]">YScrapy</span> &mdash; Multi-Platform Web Scraper & Indonesian Sentiment Analyzer
            </div>
            <div>
                <span>Veersa Palette &bull; Neumorphic Interface</span>
            </div>
        </footer>
    </div>

    <script>
        function toggleSidebar() {
            const sb = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sb.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
