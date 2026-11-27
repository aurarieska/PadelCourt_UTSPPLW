@extends('layouts.admin')

@section('title', 'Lapangan')

@section('content')
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Lapangan</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola data lapangan padel</p>
        </div>

        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('admin.lapangan.index') }}" class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari lapangan..."
                       class="w-64 rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-teal-600">
            </form>

            <a href="{{ route('admin.lapangan.create') }}"
               class="flex items-center gap-2 rounded-xl bg-lime-400 px-5 py-2.5 text-sm font-bold text-lime-950 hover:bg-lime-300">
                <i data-lucide="plus" class="h-4 w-4"></i> Tambah Lapangan
            </a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-100/70">
                <tr class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <th class="px-6 py-4">Foto</th>
                    <th class="px-6 py-4">Nama Lapangan</th>
                    <th class="px-6 py-4">Tipe</th>
                    <th class="px-6 py-4">Harga per Sesi</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($lapangan as $l)
                    <tr>
                        <td class="px-6 py-4">
                            @if ($l->foto_url)
                                <img src="{{ $l->foto_url }}" alt="{{ $l->nama_lapangan }}" class="h-12 w-16 rounded-lg object-cover">
                            @else
                                <div class="flex h-12 w-16 items-center justify-center rounded-lg bg-slate-100 text-slate-300">
                                    <i data-lucide="image" class="h-5 w-5"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-semibold">{{ $l->nama_lapangan }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ ucfirst($l->jenis_lapangan) }}</td>
                        <td class="px-6 py-4 font-bold text-teal-800">Rp{{ number_format($l->harga_per_sesi, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            @if ($l->status_lapangan === 'aktif')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-lime-100 px-3 py-1 text-xs font-semibold text-lime-800">
                                    <span class="h-1.5 w-1.5 rounded-full bg-lime-600"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.lapangan.edit', $l) }}"
                                   class="flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-teal-800 hover:bg-slate-200">
                                    <i data-lucide="pencil" class="h-3.5 w-3.5"></i> Edit
                                </a>
                                <button type="button"
                                        data-url="{{ route('admin.lapangan.destroy', $l) }}"
                                        data-nama="{{ $l->nama_lapangan }}"
                                        onclick="bukaHapus(this)"
                                        class="flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-100">
                                    <i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            {{ $cari !== '' ? 'Tidak ada lapangan yang cocok dengan pencarian.' : 'Belum ada data lapangan.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Dialog konfirmasi hapus --}}
    <div id="modalHapus" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50">
        <div class="w-full max-w-sm rounded-2xl bg-white p-8 text-center shadow-xl">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-red-600">
                <i data-lucide="triangle-alert" class="h-7 w-7"></i>
            </div>
            <h3 class="mt-4 text-lg font-extrabold">Hapus Lapangan?</h3>
            <p class="mt-2 text-sm text-slate-500">
                Data <span id="namaHapus" class="font-semibold text-slate-700"></span> akan dihapus dan tidak dapat dikembalikan.
            </p>

            <form id="formHapus" method="POST" class="mt-6 flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="tutupHapus()"
                        class="flex-1 rounded-xl bg-slate-100 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-200">Batal</button>
                <button type="submit"
                        class="flex-1 rounded-xl bg-red-600 py-3 text-sm font-bold text-white hover:bg-red-700">Ya, Hapus</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function bukaHapus(tombol) {
        document.getElementById('formHapus').action = tombol.dataset.url;
        document.getElementById('namaHapus').textContent = tombol.dataset.nama;
        const modal = document.getElementById('modalHapus');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function tutupHapus() {
        const modal = document.getElementById('modalHapus');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endpush
