<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} — YScrapy (Public Sentiment Scraper)</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN Fallback + Local) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        ys: {
                            darkest: '#06141B',
                            surface: '#11212D',
                            glass: '#253745',
                            border: '#4A5C6A',
                            muted: '#9BA8AB',
                            main: '#CCD0CF',
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #06141B;
            color: #CCD0CF;
            font-family: 'Inter', sans-serif;
            background-image: 
                radial-gradient(at 10% 10%, rgba(37, 55, 69, 0.45) 0px, transparent 50%),
                radial-gradient(at 90% 90%, rgba(17, 33, 45, 0.6) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(74, 92, 106, 0.15) 0px, transparent 60%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .glass-panel {
            background: rgba(17, 33, 45, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(74, 92, 106, 0.45);
            box-shadow: 0 10px 30px -5px rgba(6, 20, 27, 0.6);
        }

        .glass-card {
            background: rgba(37, 55, 69, 0.35);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(74, 92, 106, 0.35);
            transition: all 0.2s ease-in-out;
        }

        .glass-card:hover {
            background: rgba(37, 55, 69, 0.52);
            border-color: rgba(155, 168, 171, 0.45);
            box-shadow: 0 8px 24px -2px rgba(6, 20, 27, 0.6);
        }

        .glass-input {
            background: rgba(17, 33, 45, 0.85);
            border: 1px solid rgba(74, 92, 106, 0.55);
            color: #CCD0CF;
            backdrop-filter: blur(8px);
        }

        .glass-input:focus {
            border-color: #9BA8AB;
            outline: none;
            box-shadow: 0 0 0 2px rgba(155, 168, 171, 0.25);
        }

        .glass-btn-primary {
            background: #253745;
            color: #CCD0CF;
            border: 1px solid rgba(155, 168, 171, 0.35);
            transition: all 0.2s ease;
        }

        .glass-btn-primary:hover {
            background: #4A5C6A;
            color: #ffffff;
            border-color: #CCD0CF;
            box-shadow: 0 4px 14px rgba(37, 55, 69, 0.6);
        }

        .badge-pos {
            background: rgba(16, 185, 129, 0.15);
            color: #34D399;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }

        .badge-neu {
            background: rgba(155, 168, 171, 0.15);
            color: #CCD0CF;
            border: 1px solid rgba(155, 168, 171, 0.35);
        }

        .badge-neg {
            background: rgba(239, 68, 68, 0.15);
            color: #F87171;
            border: 1px solid rgba(239, 68, 68, 0.35);
        }

        /* Subtle scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #06141B; }
        ::-webkit-scrollbar-thumb { background: #253745; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #4A5C6A; }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <!-- Navbar (Glassmorphism, Minimal Rounded) -->
    <header class="sticky top-0 z-50 glass-panel border-b border-ys-border/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-md bg-ys-glass border border-ys-border flex items-center justify-center font-bold text-lg text-ys-main shadow-inner group-hover:border-ys-muted transition">
                            <span class="text-white">Y</span><span class="text-ys-muted text-sm">S</span>
                        </div>
                        <div>
                            <span class="text-xl font-bold tracking-tight text-white">YScrapy</span>
                            <span class="hidden sm:inline-block ml-2 text-xs font-medium px-2 py-0.5 rounded-md bg-ys-glass/80 text-ys-muted border border-ys-border/50">
                                Public Sentiment
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('dashboard') }}" 
                       class="px-3.5 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-ys-glass text-white border border-ys-border' : 'text-ys-muted hover:text-white hover:bg-ys-glass/40' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('scraper.index') }}" 
                       class="px-3.5 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('scraper.*') ? 'bg-ys-glass text-white border border-ys-border' : 'text-ys-muted hover:text-white hover:bg-ys-glass/40' }}">
                        Scraper Engine
                    </a>
                    <a href="{{ route('feedbacks.index') }}" 
                       class="px-3.5 py-2 rounded-md text-sm font-medium transition {{ request()->routeIs('feedbacks.*') ? 'bg-ys-glass text-white border border-ys-border' : 'text-ys-muted hover:text-white hover:bg-ys-glass/40' }}">
                        Semua Feedback
                    </a>
                </nav>

                <!-- Action Button -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('scraper.index') }}" class="glass-btn-primary px-4 py-2 rounded-md text-sm font-medium flex items-center space-x-2">
                        <svg class="w-4 h-4 text-ys-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Mulai Scrape</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-md bg-emerald-950/40 border border-emerald-500/40 text-emerald-300 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-md bg-rose-950/40 border border-rose-500/40 text-rose-300 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="glass-panel border-t border-ys-border/30 mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between text-xs text-ys-muted space-y-3 sm:space-y-0">
            <div>
                <span class="font-bold text-ys-main">YScrapy</span> &copy; {{ date('Y') }} &mdash; Multi-Platform Web Scraper & Public Sentiment Analysis
            </div>
            <div class="flex items-center space-x-4">
                <span>Color Palette: #06141B - #CCD0CF</span>
                <span>•</span>
                <span>Glassmorphism UI</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
