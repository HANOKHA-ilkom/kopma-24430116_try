@extends('layouts.app')

@section('title', 'Edit Anggota - ' . $member->user->name)
@section('page_title', 'Edit Data Anggota')
@section('page_subtitle', 'Perbarui data identitas mahasiswa')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="glass-card rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Perbarui Profil Anggota</h3>
                <p class="text-xs text-slate-500">Nomor Anggota: <span class="font-mono font-bold text-emerald-700">{{ $member->nomor_anggota }}</span></p>
            </div>
            <a href="{{ route('members.show', $member->id) }}" class="text-xs text-slate-500 hover:text-slate-700 font-semibold flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali
            </a>
        </div>

        <form action="{{ route('members.update', $member->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Mahasiswa *</label>
                <input type="text" name="name" value="{{ old('name', $member->user->name) }}" required
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">NIM (Tidak dapat diubah)</label>
                    <input type="text" value="{{ $member->nim }}" disabled
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-xs font-mono font-bold text-slate-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status Keanggotaan *</label>
                    <select name="status" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none">
                        <option value="aktif" {{ $member->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="non_aktif" {{ $member->status === 'non_aktif' ? 'selected' : '' }}>Non-Aktif</option>
                        <option value="alumni" {{ $member->status === 'alumni' ? 'selected' : '' }}>Alumni</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Program Studi *</label>
                    <input type="text" name="program_studi" value="{{ old('program_studi', $member->program_studi) }}" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Fakultas *</label>
                    <input type="text" name="fakultas" value="{{ old('fakultas', $member->fakultas) }}" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Angkatan *</label>
                    <input type="text" name="angkatan" value="{{ old('angkatan', $member->angkatan) }}" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $member->user->phone) }}"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('members.show', $member->id) }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200 transition-all">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-700/20 transition-all flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
