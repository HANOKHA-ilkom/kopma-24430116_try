@extends('layouts.app')

@section('title', 'Data Anggota')
@section('page_title', 'Daftar Anggota KOPMA')
@section('page_subtitle', 'Kelola data identitas dan keanggotaan mahasiswa')

@section('content')
<div class="space-y-6">

    <!-- Action Header & Filters -->
    <div class="glass-card rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Search & Filter Form -->
        <form action="{{ route('members.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[200px] max-w-sm">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama, NIM, no anggota..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <select name="status" class="py-2 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="non_aktif" {{ request('status') == 'non_aktif' ? 'selected' : '' }}>Non-Aktif</option>
                <option value="alumni" {{ request('status') == 'alumni' ? 'selected' : '' }}>Alumni</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition-all">
                Filter
            </button>

            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('members.index') }}" class="text-xs text-slate-500 hover:text-slate-700 font-semibold">
                Reset
            </a>
            @endif
        </form>

        <!-- Tambah Anggota Button -->
        <a href="{{ route('members.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex-shrink-0">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah Anggota</span>
        </a>
    </div>

    <!-- Members Table -->
    <div class="glass-card rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider">
                        <th class="py-3.5 px-4">No. Anggota</th>
                        <th class="py-3.5 px-4">Nama Mahasiswa</th>
                        <th class="py-3.5 px-4">NIM</th>
                        <th class="py-3.5 px-4">Program Studi</th>
                        <th class="py-3.5 px-4">Fakultas</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Total Simpanan</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($members as $m)
                    <tr class="hover:bg-slate-50/70 transition-all">
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                            {{ $m->nomor_anggota }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center flex-shrink-0">
                                    {{ strtoupper(substr($m->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">{{ $m->user->name }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $m->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-mono">{{ $m->nim }}</td>
                        <td class="py-3.5 px-4">{{ $m->program_studi }} ({{ $m->angkatan }})</td>
                        <td class="py-3.5 px-4 text-slate-500">{{ $m->fakultas }}</td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                {{ $m->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : ($m->status === 'alumni' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700') }}">
                                {{ $m->status }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-extrabold text-slate-900">
                            Rp {{ number_format($m->simpanans->sum('saldo'), 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('members.show', $m->id) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 transition-all" title="Lihat Profil">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('members.edit', $m->id) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-blue-700 hover:bg-blue-50 transition-all" title="Edit Data">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-10 text-slate-400">
                            <i data-lucide="users" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                            Tidak ada data anggota ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $members->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
