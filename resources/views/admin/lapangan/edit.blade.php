@extends('layouts.admin')

@section('title', 'Ubah Lapangan')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-extrabold">Ubah Lapangan</h1>
    <a href="{{ route('admin.lapangan.index') }}" class="text-sm font-semibold text-teal-700 hover:underline">&larr; Kembali</a>
</div>

<form method="POST" action="{{ route('admin.lapangan.update', $lapangan) }}" enctype="multipart/form-data"
    class="rounded-2xl bg-white p-8 shadow-sm">
    @csrf
    @method('PUT')
    @include('admin.lapangan._form')

    <div class="mt-8 flex justify-end gap-3">
        <a href="{{ route('admin.lapangan.index') }}"
            class="rounded-xl bg-slate-100 px-6 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-200">Batal</a>
        <button type="submit"
            class="rounded-xl bg-teal-700 px-6 py-3 text-sm font-semibold text-white hover:bg-teal-800">Perbarui</button>
    </div>
</form>
@endsection