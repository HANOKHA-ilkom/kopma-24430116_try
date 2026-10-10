<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Informasi KOPMA') - Koperasi Mahasiswa</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        kopma: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        navy: {
                            800: '#0f172a',
                            900: '#020617',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .sidebar-active {
            background: linear-gradient(135deg, #15803d 0%, #166534 100%);
            color: #ffffff !important;
            box-shadow: 0 4px 14px 0 rgba(22, 101, 52, 0.35);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col md:flex-row">

    <!-- Sidebar Navigation -->
    <aside class="w-full md:w-64 bg-slate-900 text-slate-300 flex-shrink-0 flex flex-col border-r border-slate-800">
        <!-- Brand Header -->
        <div class="h-20 flex items-center px-6 gap-3 border-b border-slate-800 bg-slate-950/50">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white shadow-lg shadow-emerald-900/30 font-extrabold text-xl">
                K
            </div>
            <div>
                <h1 class="text-white font-bold text-lg tracking-tight leading-none">SI-KOPMA</h1>
                <span class="text-xs text-emerald-400 font-medium">Koperasi Mahasiswa</span>
            </div>
        </div>

        <!-- User Role Profile Badge -->
        <div class="p-4 mx-3 my-4 rounded-xl bg-slate-800/60 border border-slate-700/60 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-emerald-700 text-white font-bold flex items-center justify-center flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="inline-block w-2 h-2 rounded-full {{ Auth::user()->role === 'admin' ? 'bg-amber-400' : (Auth::user()->role === 'pengurus' ? 'bg-blue-400' : 'bg-emerald-400') }}"></span>
                    <span class="text-xs uppercase tracking-wider font-semibold text-slate-300">
                        {{ Auth::user()->role }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 space-y-1.5 overflow-y-auto pb-6 text-sm font-medium">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('dashboard') ? 'sidebar-active' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Dashboard</span>
            </a>

            @if(Auth::user()->role !== 'anggota')
            <a href="{{ route('members.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('members.*') ? 'sidebar-active' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span>Data Anggota</span>
            </a>
            @else
            @if(Auth::user()->member)
            <a href="{{ route('members.show', Auth::user()->member->id) }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('members.show') ? 'sidebar-active' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                <i data-lucide="user-check" class="w-5 h-5"></i>
                <span>Profil Anggota</span>
            </a>
            @endif
            @endif

            <a href="{{ route('simpanans.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('simpanans.*') ? 'sidebar-active' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                <i data-lucide="wallet" class="w-5 h-5"></i>
                <span>Simpanan</span>
            </a>

            <a href="{{ route('pinjamans.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('pinjamans.*') ? 'sidebar-active' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                <i data-lucide="banknote" class="w-5 h-5"></i>
                <span>Pinjaman</span>
            </a>

            <div class="pt-3 pb-1.5 px-3.5 text-xs uppercase font-bold tracking-wider text-slate-500">
                Unit Toko KOPMA
            </div>

            <a href="{{ route('pos.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('pos.*') ? 'sidebar-active' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                <i data-lucide="shopping-cart" class="w-5 h-5 text-emerald-400"></i>
                <span>Kasir POS (Toko)</span>
            </a>

            <a href="{{ route('produks.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('produks.*') ? 'sidebar-active' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                <i data-lucide="package" class="w-5 h-5"></i>
                <span>Katalog Produk</span>
            </a>

            <div class="pt-3 pb-1.5 px-3.5 text-xs uppercase font-bold tracking-wider text-slate-500">
                Keuangan & Laporan
            </div>

            <a href="{{ route('shu.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('shu.*') ? 'sidebar-active' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                <i data-lucide="pie-chart" class="w-5 h-5"></i>
                <span>Pembagian SHU</span>
            </a>

            <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all {{ request()->routeIs('laporan.*') ? 'sidebar-active' : 'hover:bg-slate-800 hover:text-white text-slate-300' }}">
                <i data-lucide="file-text" class="w-5 h-5"></i>
                <span>Laporan Keuangan</span>
            </a>
        </nav>

        <!-- Logout Bottom Section -->
        <div class="p-3 border-t border-slate-800 mt-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-rose-400 hover:bg-rose-950/40 hover:text-rose-300 text-sm font-semibold transition-all">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top App Bar -->
        <header class="h-20 bg-white border-b border-slate-200 px-6 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div>
                <h2 class="text-xl font-bold text-slate-900">@yield('page_title', 'Dashboard')</h2>
                <p class="text-xs text-slate-500">@yield('page_subtitle', 'Sistem Informasi Koperasi Mahasiswa')</p>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-xs bg-slate-100 text-slate-600 px-3 py-1.5 rounded-full font-medium hidden sm:inline-block border border-slate-200">
                    <i data-lucide="database" class="w-3.5 h-3.5 inline mr-1 text-emerald-600"></i> DB: kopma_24430116
                </span>
                <span class="text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded-full font-semibold">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 inline mr-1"></i> {{ date('d M Y') }}
                </span>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="p-6 pb-0">
            @if(session('success'))
            <div class="mb-4 flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-sm animate-pulse">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 flex items-center gap-3 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
            @endif

            @if(session('info'))
            <div class="mb-4 flex items-center gap-3 p-4 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 shadow-sm">
                <i data-lucide="info" class="w-5 h-5 text-sky-600 flex-shrink-0"></i>
                <div class="text-sm font-medium">{{ session('info') }}</div>
            </div>
            @endif
        </div>

        <!-- Page Body -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="py-4 px-6 border-t border-slate-200 text-center text-xs text-slate-500 bg-white">
            &copy; {{ date('Y') }} Sistem Informasi KOPMA (Koperasi Mahasiswa) &bull; Database: <span class="font-semibold text-slate-700">kopma_24430116</span>
        </footer>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
