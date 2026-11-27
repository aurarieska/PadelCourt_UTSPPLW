@php $j = $jenis ?? null; @endphp

<div>
    <label for="nama_jenis" class="text-sm font-semibold">Nama Jenis <span class="text-red-500">*</span></label>
    <input id="nama_jenis" type="text" name="nama_jenis" value="{{ old('nama_jenis', $j->nama_jenis ?? '') }}"
           placeholder="Contoh: Panoramic"
           class="mt-2 w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm outline-none focus:border-teal-600 {{ $errors->has('nama_jenis') ? 'border-red-500' : 'border-slate-200' }}">
    @error('nama_jenis')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mt-6">
    <label for="keterangan" class="text-sm font-semibold">Keterangan</label>
    <textarea id="keterangan" name="keterangan" rows="3"
              class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-teal-600">{{ old('keterangan', $j->keterangan ?? '') }}</textarea>
    @error('keterangan')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
