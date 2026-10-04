@php
    $l = $lapangan ?? null;
    $jenis = old('jenis_lapangan', $l->jenis_lapangan ?? 'indoor');
    // Setelah gagal validasi, ikuti isian lama; saat pertama dibuka, default aktif
    $aktif = old() ? old('aktif') == '1' : ($l ? $l->status_lapangan === 'aktif' : true);
@endphp

<div class="grid grid-cols-2 gap-6">
    {{-- Nama --}}
    <div>
        <label for="nama_lapangan" class="text-sm font-semibold">Nama Lapangan <span class="text-red-500">*</span></label>
        <input id="nama_lapangan" type="text" name="nama_lapangan"
               value="{{ old('nama_lapangan', $l->nama_lapangan ?? '') }}"
               placeholder="Contoh: Lapangan 4 - Panoramic Glass"
               class="mt-2 w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm outline-none focus:border-teal-600 {{ $errors->has('nama_lapangan') ? 'border-red-500' : 'border-slate-200' }}">
        @error('nama_lapangan')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Tipe --}}
    <div>
        <label class="text-sm font-semibold">Tipe <span class="text-red-500">*</span></label>
        <div class="mt-2 grid grid-cols-2 gap-1 rounded-xl bg-slate-100 p-1">
            <label class="cursor-pointer">
                <input type="radio" name="jenis_lapangan" value="indoor" class="peer sr-only" @checked($jenis === 'indoor')>
                <span class="flex items-center justify-center gap-2 rounded-lg py-2.5 text-sm font-semibold text-slate-500 peer-checked:bg-white peer-checked:text-teal-700 peer-checked:shadow">
                    <i data-lucide="house" class="h-4 w-4"></i> Indoor
                </span>
            </label>
            <label class="cursor-pointer">
                <input type="radio" name="jenis_lapangan" value="outdoor" class="peer sr-only" @checked($jenis === 'outdoor')>
                <span class="flex items-center justify-center gap-2 rounded-lg py-2.5 text-sm font-semibold text-slate-500 peer-checked:bg-white peer-checked:text-teal-700 peer-checked:shadow">
                    <i data-lucide="sun" class="h-4 w-4"></i> Outdoor
                </span>
            </label>
        </div>
        @error('jenis_lapangan')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- Deskripsi --}}
<div class="mt-6">
    <label for="deskripsi" class="text-sm font-semibold">Deskripsi</label>
    <textarea id="deskripsi" name="deskripsi" rows="4"
              placeholder="Masukkan deskripsi detail lapangan, jenis lantai, dan keunggulan..."
              class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-teal-600">{{ old('deskripsi', $l->deskripsi ?? '') }}</textarea>
</div>

{{-- Harga --}}
<div class="mt-6">
    <label for="harga_per_sesi" class="text-sm font-semibold">Harga per Sesi (1 jam) <span class="text-red-500">*</span></label>
    <div class="mt-2 flex w-72 items-center rounded-xl border bg-slate-50 px-4 focus-within:border-teal-600 {{ $errors->has('harga_per_sesi') ? 'border-red-500' : 'border-slate-200' }}">
        <span class="text-sm font-bold text-slate-600">Rp</span>
        <input id="harga_per_sesi" type="text" inputmode="numeric" name="harga_per_sesi"
               value="{{ old('harga_per_sesi', isset($l) ? number_format($l->harga_per_sesi, 0, ',', '.') : '') }}"
               placeholder="350.000"
               class="w-full bg-transparent px-3 py-3 text-sm outline-none">
    </div>
    @error('harga_per_sesi')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Foto --}}
<div class="mt-6">
    <label class="text-sm font-semibold">Foto Lapangan</label>
    <div class="mt-2 rounded-xl border border-dashed bg-slate-50 p-5 {{ $errors->has('foto') ? 'border-red-500' : 'border-slate-300' }}">
        <div class="flex items-center gap-5">
            <div class="flex h-28 w-40 items-center justify-center overflow-hidden rounded-xl bg-white text-slate-300">
                <i id="fotoIkon" data-lucide="image" class="h-8 w-8 {{ ($l->foto_url ?? null) ? 'hidden' : '' }}"></i>
                <img id="fotoPreview" src="{{ $l->foto_url ?? '' }}" alt="Pratinjau"
                     class="h-full w-full object-cover {{ ($l->foto_url ?? null) ? '' : 'hidden' }}">
            </div>
            <div>
                <p id="fotoNama" class="text-sm font-semibold">{{ $l->foto ?? 'Belum ada foto' }}</p>
                <p class="mt-1 text-xs text-slate-500">Format JPG, PNG maks 5MB</p>
                <label for="foto"
                       class="mt-3 inline-flex cursor-pointer items-center gap-2 rounded-lg bg-white px-4 py-2 text-xs font-semibold text-teal-700 shadow-sm hover:bg-slate-100">
                    <i data-lucide="image" class="h-4 w-4"></i> {{ ($l->foto ?? null) ? 'Ganti foto' : 'Pilih foto lapangan' }}
                </label>
                <input id="foto" type="file" name="foto" accept="image/png,image/jpeg" class="hidden">
            </div>
        </div>
    </div>
    @error('foto')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- Status --}}
<div class="mt-6 flex items-center justify-between">
    <div>
        <p class="text-sm font-semibold">Status Lapangan</p>
        <p id="statusTeks" class="mt-1 text-xs font-medium {{ $aktif ? 'text-lime-700' : 'text-slate-500' }}">
            {{ $aktif ? 'Aktif (Dapat dipesan customer)' : 'Nonaktif (Tidak tampil bagi customer)' }}
        </p>
    </div>
    <label class="relative inline-flex cursor-pointer items-center">
        <input id="aktif" type="checkbox" name="aktif" value="1" class="peer sr-only" @checked($aktif)>
        <div class="h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-lime-400
                    after:absolute after:left-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:bg-white
                    after:transition after:content-[''] peer-checked:after:translate-x-5"></div>
    </label>
</div>

@push('scripts')
<script>
    // Pratinjau foto + pengecekan ukuran di sisi browser (validasi utama tetap di server)
    document.getElementById('foto').addEventListener('change', function () {
        const berkas = this.files[0];
        if (!berkas) return;

        if (berkas.size > 5 * 1024 * 1024) {
            alert('Ukuran foto maksimal 5 MB.');
            this.value = '';
            return;
        }

        const img = document.getElementById('fotoPreview');
        img.src = URL.createObjectURL(berkas);
        img.classList.remove('hidden');
        document.getElementById('fotoIkon').classList.add('hidden');
        document.getElementById('fotoNama').textContent = berkas.name;
    });

    // Teks status mengikuti toggle
    document.getElementById('aktif').addEventListener('change', function () {
        const teks = document.getElementById('statusTeks');
        teks.textContent = this.checked ? 'Aktif (Dapat dipesan customer)' : 'Nonaktif (Tidak tampil bagi customer)';
        teks.className = 'mt-1 text-xs font-medium ' + (this.checked ? 'text-lime-700' : 'text-slate-500');
    });
</script>
@endpush
