<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi KOPMA</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .bg-mesh {
            background-color: #064e3b;
            background-image: 
                radial-gradient(at 80% 0%, hsla(160, 84%, 39%, 0.4) 0px, transparent 50%),
                radial-gradient(at 0% 50%, hsla(158, 64%, 22%, 0.6) 0px, transparent 50%),
                radial-gradient(at 80% 100%, hsla(168, 76%, 26%, 0.5) 0px, transparent 50%),
                radial-gradient(at 0% 0%, hsla(217, 91%, 20%, 0.7) 0px, transparent 50%);
        }
        .glass-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
        }
    </style>
</head>
<body class="bg-mesh min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Logo & Header Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-300 items-center justify-center text-white shadow-xl shadow-emerald-950/40 font-black text-3xl mb-3">
                K
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">SI-KOPMA</h1>
            <p class="text-emerald-200 text-sm font-medium mt-1">Sistem Informasi Koperasi Mahasiswa</p>
            <p class="text-xs text-emerald-300/80 mt-0.5">Database: <span class="font-mono font-semibold">kopma_24430116</span></p>
        </div>

        <!-- Login Card -->
        <div class="glass-box rounded-3xl p-8 border border-white/40 shadow-2xl">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-900">Masuk ke Akun Anda</h2>
                <p class="text-xs text-slate-500 mt-1">Silakan masukkan email dan password untuk melanjutkan</p>
            </div>

            <!-- Error Notifications -->
            @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0 text-rose-600"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            @if(session('info'))
            <div class="mb-5 p-3.5 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 text-xs font-semibold flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 flex-shrink-0 text-sky-600"></i>
                <span>{{ session('info') }}</span>
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400"></i>
                        <input type="email" id="email" name="email" value="{{ old('email', 'admin@kopma.ac.id') }}" required autofocus
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all font-medium"
                            placeholder="nama@kopma.ac.id">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400"></i>
                        <input type="password" id="password" name="password" value="password" required
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all font-medium"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-600 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-lg shadow-emerald-900/20 hover:shadow-emerald-900/30 transition-all flex items-center justify-center gap-2 mt-2">
                    <span>Masuk ke SI-KOPMA</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- Quick Demo Login Presets -->
            <div class="mt-8 pt-6 border-t border-slate-200">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider text-center mb-3">
                    Akses Demo Cepat (1-Klik)
                </p>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="fillLogin('admin@kopma.ac.id')"
                        class="p-2 rounded-xl bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-900 text-xs font-bold text-center transition-all">
                        👑 Admin
                    </button>
                    <button type="button" onclick="fillLogin('pengurus@kopma.ac.id')"
                        class="p-2 rounded-xl bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-900 text-xs font-bold text-center transition-all">
                        💼 Pengurus
                    </button>
                    <button type="button" onclick="fillLogin('ahmad.fauzi@student.ac.id')"
                        class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-900 text-xs font-bold text-center transition-all">
                        🎓 Anggota
                    </button>
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-emerald-200/70 mt-6">
            &copy; {{ date('Y') }} Sistem Informasi Koperasi Mahasiswa &bull; Laravel 11
        </p>
    </div>

    <script>
        lucide.createIcons();
        function fillLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    </script>
</body>
</html>
