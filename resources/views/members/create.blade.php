@extends('layouts.app')

@section('title', 'Daftar Anggota Baru')
@section('page_title', 'Form Registrasi Anggota')
@section('page_subtitle', 'Tambahkan anggota mahasiswa baru KOPMA')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="glass-card rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Data Identitas Mahasiswa</h3>
                <p class="text-xs text-slate-500">Nomor Anggota & 3 Rekening Simpanan (Pokok, Wajib, Sukarela) akan di-generate otomatis.</p>
            </div>
            <a href="{{ route('members.index') }}" class="text-xs text-slate-500 hover:text-slate-700 font-semibold flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali
            </a>
        </div>

        <form action="{{ route('members.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap Mahasiswa *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Contoh: Muhammad Ihsan">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">NIM (Nomor Induk Mahasiswa) *</label>
                    <input type="text" name="nim" value="{{ old('nim') }}" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        placeholder="Contoh: 24430116">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Angkatan (Tahun) *</label>
                    <input type="text" name="angkatan" value="{{ old('angkatan', date('Y')) }}" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        placeholder="2024">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Program Studi *</label>
                    <input type="text" name="program_studi" value="{{ old('program_studi') }}" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        placeholder="Contoh: Teknik Informatika">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Fakultas *</label>
                    <input type="text" name="fakultas" value="{{ old('fakultas') }}" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        placeholder="Contoh: Fakultas Ilmu Komputer">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        placeholder="nama@student.ac.id">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        placeholder="081234567890">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('members.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200 transition-all">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan & Buat Rekening</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
