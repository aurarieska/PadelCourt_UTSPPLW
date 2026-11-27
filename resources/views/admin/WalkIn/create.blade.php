@extends('layouts.admin')

@section('title', 'Pesanan Walk-in')

@section('content')
<div class="flex items-center gap-3">
    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-700 text-white">
        <i data-lucide="user-plus" class="h-5 w-5"></i>
    </div>
    <h1 class="text-3xl font-extrabold text-slate-900">Pesanan Walk-in</h1>
</div>
<p class="mt-1 text-sm text-slate-500">Input pesanan customer yang datang langsung</p>

<form id="formWalkin" method="POST" action="{{ route('admin.walkin.store') }}" class="mt-6 space-y-6">
    @csrf

    {{-- Data customer --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 font-bold"><i data-lucide="user" class="h-4 w-4 text-teal-700"></i> Data Customer</h2>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold tracking-wider text-slate-500">WAJIB DIISI</span>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-5">
            <div>
                <label for="nama_pemesan" class="text-xs font-semibold text-slate-600">Nama Customer</label>
                <div class="mt-2 flex items-center gap-3 rounded-xl border bg-slate-50 px-4 focus-within:border-teal-600 {{ $errors->has('nama_pemesan') ? 'border-red-500' : 'border-slate-200' }}">
                    <i data-lucide="id-card" class="h-4 w-4 text-slate-400"></i>
                    <input id="nama_pemesan" type="text" name="nama_pemesan" value="{{ old('nama_pemesan') }}"
                        placeholder="Contoh: Andi Pratama" class="w-full bg-transparent py-3 text-sm outline-none">
                </div>
                @error('nama_pemesan')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="no_telepon_pemesan" class="text-xs font-semibold text-slate-600">No. HP</label>
                <div class="mt-2 flex items-center gap-3 rounded-xl border bg-slate-50 px-4 focus-within:border-teal-600 {{ $errors->has('no_telepon_pemesan') ? 'border-red-500' : 'border-slate-200' }}">
                    <i data-lucide="phone" class="h-4 w-4 text-slate-400"></i>
                    <input id="no_telepon_pemesan" type="text" inputmode="tel" name="no_telepon_pemesan" value="{{ old('no_telepon_pemesan') }}"
                        placeholder="0812 3456 7890" class="w-full bg-transparent py-3 text-sm outline-none">
                </div>
                @error('no_telepon_pemesan')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Tambah lapangan & sesi --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 font-bold"><i data-lucide="trophy" class="h-4 w-4 text-teal-700"></i> Tambah Lapangan &amp; Sesi</h2>
            <span class="text-xs text-slate-500">Pilih lapangan, waktu dan jam bermain</span>
        </div>

        @if ($lapangan->isEmpty())
        <p class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-700">Belum ada lapangan aktif. Aktifkan lapangan di menu Lapangan terlebih dahulu.</p>
        @endif

        <div class="mt-4 grid grid-cols-2 gap-5">
            <div>
                <label for="id_lapangan" class="text-xs font-semibold text-slate-600">Pilihan Lapangan</label>
                <select id="id_lapangan" class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-teal-600">
                    @foreach ($lapangan as $l)
                    <option value="{{ $l->id_lapangan }}">{{ $l->nama_lapangan }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="tanggal" class="text-xs font-semibold text-slate-600">Tanggal</label>
                <input id="tanggal" type="date" value="{{ $hariIni }}" min="{{ $hariIni }}"
                    class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-teal-600">
            </div>
        </div>

        <div class="mt-5 flex items-center justify-between">
            <p class="text-xs font-semibold text-slate-600">Pilih Sesi Jam</p>
            <div class="flex items-center gap-4 text-[11px] font-semibold text-slate-500">
                <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span> Terisi</span>
                <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-slate-100 ring-1 ring-slate-300"></span> Tersedia</span>
                <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-teal-700"></span> Terpilih</span>
            </div>
        </div>

        <div id="gridSesi" class="mt-3 grid grid-cols-7 gap-2"></div>

        <div class="mt-5 flex justify-end">
            <button id="btnTambah" type="button" disabled
                class="flex items-center gap-2 rounded-xl bg-slate-100 px-5 py-3 text-sm font-bold text-teal-800 hover:bg-slate-200 disabled:cursor-not-allowed disabled:opacity-50">
                <i data-lucide="circle-plus" class="h-4 w-4"></i> Tambah ke Daftar
            </button>
        </div>
    </div>

    {{-- Daftar pesanan --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 font-bold"><i data-lucide="receipt-text" class="h-4 w-4 text-teal-700"></i> Daftar Pesanan</h2>
            <span id="jumlahSesi" class="text-xs text-slate-500">0 sesi</span>
        </div>

        @php
        $galatItems = collect($errors->get('items'))
        ->merge(collect($errors->get('items.*'))->flatten())
        ->unique();
        @endphp
        @if ($galatItems->isNotEmpty())
        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-600">
            @foreach ($galatItems as $pesan)
            <p>{{ $pesan }}</p>
            @endforeach
        </div>
        @endif

        <table class="mt-4 w-full text-left text-sm">
            <thead class="bg-slate-100/70">
                <tr class="text-xs font-semibold text-slate-500">
                    <th class="rounded-l-lg px-4 py-3">Lapangan</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Sesi</th>
                    <th class="px-4 py-3">Harga</th>
                    <th class="rounded-r-lg px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="daftarBody" class="divide-y divide-slate-100"></tbody>
        </table>

        <div id="inputItems"></div>

        <div class="mt-4 flex items-center justify-between rounded-xl bg-slate-100 px-5 py-4">
            <span class="flex items-center gap-2 font-bold"><i data-lucide="banknote" class="h-5 w-5 text-teal-700"></i> Total Harga</span>
            <span id="totalHarga" class="text-3xl font-extrabold text-teal-800">Rp0</span>
        </div>
    </div>

    {{-- Metode pembayaran + aksi --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-600">Metode Pembayaran</p>
            <div class="mt-2 inline-grid grid-cols-2 gap-1 rounded-xl bg-slate-200/70 p-1">
                @foreach (['tunai' => 'Tunai', 'QRIS' => 'QRIS'] as $nilai => $label)
                <label class="cursor-pointer">
                    <input type="radio" name="metode_pembayaran" value="{{ $nilai }}" class="peer sr-only"
                        @checked(old('metode_pembayaran', 'tunai' )===$nilai)>
                    <span class="block rounded-lg px-6 py-2 text-sm font-semibold text-slate-500 peer-checked:bg-white peer-checked:text-teal-700 peer-checked:shadow">{{ $label }}</span>
                </label>
                @endforeach
            </div>
            @error('metode_pembayaran')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.walkin.create') }}"
                class="rounded-xl bg-slate-200 px-8 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-300">Batal</a>
            <button id="btnLunas" type="submit" disabled
                class="flex items-center gap-2 rounded-xl bg-lime-400 px-8 py-3 text-lg font-bold text-lime-950 shadow hover:bg-lime-300 disabled:cursor-not-allowed disabled:opacity-50">
                <i data-lucide="circle-check" class="h-5 w-5"></i> Tandai Lunas
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    (function() {
        const LAPANGAN = @json($lapangan -> keyBy('id_lapangan'));
        const JAM = @json($jam);
        const URL_KETERSEDIAAN = @json(route('admin.walkin.ketersediaan'));

        let daftar = @json(old('items', [])); // [{id_lapangan, tanggal, jam_mulai}]
        let terisi = []; // jam terisi dari server (lapangan + tanggal terpilih)
        let dipilih = []; // jam yang sedang dicentang di grid

        const $ = (id) => document.getElementById(id);
        const rupiah = (n) => 'Rp' + Number(n).toLocaleString('id-ID');
        const jamAkhir = (jam) => String(parseInt(jam, 10) + 1).padStart(2, '0') + ':00';
        const tglPendek = (t) => new Date(t + 'T00:00:00')
            .toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        const esc = (s) => String(s).replace(/[&<>"']/g,
            (c) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [c]));

        async function muatKetersediaan() {
            dipilih = [];
            const id = $('id_lapangan').value;
            const tgl = $('tanggal').value;

            if (!id || !tgl) {
                terisi = [];
                renderSesi();
                return;
            }

            try {
                const res = await fetch(`${URL_KETERSEDIAAN}?id_lapangan=${id}&tanggal=${tgl}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                terisi = res.ok ? (await res.json()).terisi : [];
            } catch (e) {
                terisi = [];
            }
            renderSesi();
        }

        function renderSesi() {
            const id = Number($('id_lapangan').value);
            const tgl = $('tanggal').value;
            const grid = $('gridSesi');
            grid.innerHTML = '';

            JAM.forEach((jam) => {
                const penuh = terisi.includes(jam);
                const sudahDiDaftar = daftar.some((d) =>
                    Number(d.id_lapangan) === id && d.tanggal === tgl && d.jam_mulai === jam);
                const terpilih = dipilih.includes(jam);

                const b = document.createElement('button');
                b.type = 'button';
                b.textContent = jam;
                let kelas = 'rounded-lg py-3 text-sm font-semibold ';

                if (penuh) {
                    kelas += 'cursor-not-allowed bg-slate-200 text-slate-400 line-through';
                    b.disabled = true;
                } else if (sudahDiDaftar) {
                    kelas += 'cursor-not-allowed bg-teal-50 text-teal-600';
                    b.disabled = true;
                    b.title = 'Sudah ada di daftar pesanan';
                } else if (terpilih) {
                    kelas += 'bg-teal-700 text-white';
                    b.textContent = '✓ ' + jam;
                    b.onclick = () => toggle(jam);
                } else {
                    kelas += 'bg-slate-100 text-slate-700 hover:bg-slate-200';
                    b.onclick = () => toggle(jam);
                }

                b.className = kelas;
                grid.appendChild(b);
            });

            $('btnTambah').disabled = dipilih.length === 0;
        }

        function toggle(jam) {
            dipilih = dipilih.includes(jam) ? dipilih.filter((j) => j !== jam) : [...dipilih, jam];
            renderSesi();
        }

        function tambah() {
            const id = Number($('id_lapangan').value);
            const tgl = $('tanggal').value;
            dipilih.forEach((jam) => daftar.push({
                id_lapangan: id,
                tanggal: tgl,
                jam_mulai: jam
            }));
            dipilih = [];
            renderDaftar();
            renderSesi();
        }

        function hapus(index) {
            daftar.splice(index, 1);
            renderDaftar();
            renderSesi();
        }

        function renderDaftar() {
            const body = $('daftarBody');
            const inputs = $('inputItems');
            body.innerHTML = '';
            inputs.innerHTML = '';
            let total = 0;

            if (daftar.length === 0) {
                body.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">' +
                    'Belum ada sesi. Pilih lapangan, tanggal, dan jam lalu klik "Tambah ke Daftar".</td></tr>';
            }

            daftar.forEach((d, i) => {
                const l = LAPANGAN[d.id_lapangan];
                if (!l) return;
                total += Number(l.harga_per_sesi);

                const tr = document.createElement('tr');
                tr.innerHTML =
                    `<td class="px-4 py-3 font-semibold">${esc(l.nama_lapangan)}</td>` +
                    `<td class="px-4 py-3">${tglPendek(d.tanggal)}</td>` +
                    `<td class="px-4 py-3"><span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold">${esc(d.jam_mulai)} - ${jamAkhir(d.jam_mulai)}</span></td>` +
                    `<td class="px-4 py-3 font-semibold text-teal-800">${rupiah(l.harga_per_sesi)}</td>` +
                    `<td class="px-4 py-3 text-right"><button type="button" data-hapus="${i}" class="inline-flex items-center gap-1 text-xs font-semibold text-red-600 hover:text-red-700"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Hapus</button></td>`;
                body.appendChild(tr);

                inputs.insertAdjacentHTML('beforeend',
                    `<input type="hidden" name="items[${i}][id_lapangan]" value="${esc(d.id_lapangan)}">` +
                    `<input type="hidden" name="items[${i}][tanggal]" value="${esc(d.tanggal)}">` +
                    `<input type="hidden" name="items[${i}][jam_mulai]" value="${esc(d.jam_mulai)}">`);
            });

            body.querySelectorAll('[data-hapus]').forEach((tombol) => {
                tombol.onclick = () => hapus(Number(tombol.dataset.hapus));
            });

            $('totalHarga').textContent = rupiah(total);
            $('jumlahSesi').textContent = daftar.length + ' sesi';
            $('btnLunas').disabled = daftar.length === 0;
            lucide.createIcons();
        }

        $('id_lapangan').addEventListener('change', muatKetersediaan);
        $('tanggal').addEventListener('change', muatKetersediaan);
        $('btnTambah').addEventListener('click', tambah);

        $('formWalkin').addEventListener('submit', (e) => {
            if (daftar.length === 0) {
                e.preventDefault();
                return;
            }
            $('btnLunas').disabled = true; // cegah klik ganda
        });

        renderDaftar();
        muatKetersediaan();
    })();
</script>
@endpush