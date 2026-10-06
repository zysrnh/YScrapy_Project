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

        .neu-card {
            background: #FFFFF7;
            border-radius: 0.5rem;
            box-shadow: 6px 6px 14px #cfcfc5, -6px -6px 14px #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.7);
            transition: all 0.2s ease-in-out;
        }

        .neu-panel {
            background: #E7E7DD;
            border-radius: 0.5rem;
            box-shadow: 6px 6px 14px #cfcfc5, -6px -6px 14px #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .neu-inset {
            background: #E7E7DD;
            border-radius: 0.375rem;
            box-shadow: inset 3px 3px 6px #cfcfc5, inset -3px -3px 6px #ffffff;
            border: 1px solid #d5d5ca;
        }

        .neu-input {
            background: #FFFFF7;
            color: #1D1D1B;
            border-radius: 0.375rem;
            box-shadow: inset 2px 2px 5px #d8d8ce, inset -2px -2px 5px #ffffff;
            border: 1px solid #d5d5ca;
            transition: all 0.15s ease;
        }

        .neu-input:focus {
            outline: none;
            border-color: #1D1D1B;
            box-shadow: inset 2px 2px 5px #cfcfc5, 0 0 0 1px #1D1D1B;
        }

        .neu-btn-primary {
            background: #1D1D1B;
            color: #FFFFF7;
            border-radius: 0.375rem;
            box-shadow: 4px 4px 10px rgba(29, 29, 27, 0.18);
            transition: all 0.15s ease;
        }

        .neu-btn-primary:hover {
            background: #333330;
            box-shadow: 2px 2px 6px rgba(29, 29, 27, 0.25);
        }

        .neu-btn-secondary {
            background: #E7E7DD;
            color: #1D1D1B;
            border-radius: 0.375rem;
            box-shadow: 4px 4px 9px #cfcfc5, -4px -4px 9px #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.6);
            transition: all 0.15s ease;
        }

        .neu-btn-secondary:hover {
            background: #dfdfd4;
            box-shadow: 2px 2px 5px #cfcfc5, -2px -2px 5px #ffffff;
        }

        /* Subtle scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #E7E7DD; }
        ::-webkit-scrollbar-thumb { background: #cfcfc5; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #b5b5ab; }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <!-- Navbar (Neumorphic Minimalist) -->
    <header class="sticky top-0 z-50 bg-[#E7E7DD] border-b border-[#D5D5CA]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded bg-[#1D1D1B] flex items-center justify-center font-bold text-sm text-[#FFFFF7]">
                            YS
                        </div>
                        <span class="text-lg font-bold tracking-tight text-[#1D1D1B]">YScrapy</span>
                    </a>
                </div>

                <!-- Navigation Links (Tanpa Emoji) -->
                <nav class="hidden md:flex items-center space-x-2">
                    <a href="{{ route('dashboard') }}" 
                       class="px-3.5 py-1.5 rounded text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('dashboard') ? 'bg-[#FFFFF7] text-[#1D1D1B] shadow-[inset_2px_2px_4px_#cfcfc5,inset_-2px_-2px_4px_#ffffff]' : 'text-[#555552] hover:text-[#1D1D1B]' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('scraper.index') }}" 
                       class="px-3.5 py-1.5 rounded text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('scraper.*') ? 'bg-[#FFFFF7] text-[#1D1D1B] shadow-[inset_2px_2px_4px_#cfcfc5,inset_-2px_-2px_4px_#ffffff]' : 'text-[#555552] hover:text-[#1D1D1B]' }}">
                        Scraper Manual
                    </a>
                    <a href="{{ route('trending.index') }}" 
                       class="px-3.5 py-1.5 rounded text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('trending.*') ? 'bg-[#FFFFF7] text-[#1D1D1B] shadow-[inset_2px_2px_4px_#cfcfc5,inset_-2px_-2px_4px_#ffffff]' : 'text-[#555552] hover:text-[#1D1D1B]' }}">
                        Isu Trending
                    </a>
                    <a href="{{ route('watchlist.index') }}" 
                       class="px-3.5 py-1.5 rounded text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('watchlist.*') ? 'bg-[#FFFFF7] text-[#1D1D1B] shadow-[inset_2px_2px_4px_#cfcfc5,inset_-2px_-2px_4px_#ffffff]' : 'text-[#555552] hover:text-[#1D1D1B]' }}">
                        Pantauan Otomatis
                    </a>
                    <a href="{{ route('feedbacks.index') }}" 
                       class="px-3.5 py-1.5 rounded text-xs font-semibold uppercase tracking-wider transition {{ request()->routeIs('feedbacks.*') ? 'bg-[#FFFFF7] text-[#1D1D1B] shadow-[inset_2px_2px_4px_#cfcfc5,inset_-2px_-2px_4px_#ffffff]' : 'text-[#555552] hover:text-[#1D1D1B]' }}">
                        Semua Feedback
                    </a>
                </nav>

                <!-- Action Button -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('scraper.index') }}" class="neu-btn-primary px-3.5 py-1.5 text-xs font-medium flex items-center space-x-1.5">
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
            <div class="mb-6 p-4 rounded-md neu-panel border border-[#B2DFDB] text-[#004D40] flex items-center justify-between text-xs sm:text-sm">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-[#004D40] hover:opacity-75 font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-md neu-panel border border-[#FFCDD2] text-[#B71C1C] flex items-center justify-between text-xs sm:text-sm">
                <span>{{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-[#B71C1C] hover:opacity-75 font-bold">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-[#E7E7DD] border-t border-[#D5D5CA] mt-auto py-6">
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
