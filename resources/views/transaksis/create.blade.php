@extends('template.main')
@section('title','Tambah Transaksi')
@section('content')
<div class="content-wrapper p-4">
    <h2>Tambah Transaksi</h2>

    <form action="{{ route('transaksis.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- DATA TRANSAKSI UTAMA --}}
        <div class="mb-3">
            <label>Penghuni</label>
            <select id="penghuni-select" name="penghuni_id" class="form-control" required>
                <option value="">Pilih Penghuni</option>
                @foreach($penghunis as $p)
                    <option value="{{ $p->idpenghuni }}">{{ $p->nama }} ({{ $p->kode_penghuni }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Tanggal Bayar</label>
            <input type="date" name="tglbayar" class="form-control">
        </div>

        <div class="mb-3">
            <label>Status Bayar</label>
            <select name="statusbayar" class="form-control" required>
                <option value="Belum Lunas">Belum Lunas</option>
                <option value="Lunas">Lunas</option>
            </select>
        </div>

        <div class="mb-4">
            <label>Bukti Bayar</label>
            <input type="file" name="buktibayar" class="form-control">
        </div>

        {{-- ===================== --}}
        {{-- DAFTAR KAMAR PENGHUNI --}}
        {{-- ===================== --}}
        <h5>Pilih Kamar</h5>
        <table class="table table-bordered mb-3" id="kamar-table">
            <thead class="table-secondary">
                <tr>
                    <th>Pilih</th>
                    <th>No Kamar</th>
                    <th>Kapasitas</th>
                    <th>Fasilitas</th>
                    <th>Tgl Mulai</th>
                    <th>Durasi (Bulan)</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        {{-- TAGIHAN TAMBAHAN PER KAMAR --}}
        <div id="tagihan-wrapper"></div>

        <button class="btn btn-primary mt-3">Simpan</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const penghunis = @json($penghunis);
    const penghuniSelect = document.getElementById('penghuni-select');
    const tbody = document.querySelector('#kamar-table tbody');
    const tagihanWrapper = document.getElementById('tagihan-wrapper');

    // Saat penghuni dipilih
    penghuniSelect.addEventListener('change', function() {
        tbody.innerHTML = '';
        tagihanWrapper.innerHTML = '';

        const id = parseInt(this.value);
        if (!id) return;

        const penghuni = penghunis.find(x => x.idpenghuni === id);
        penghuni.kamars.forEach((k, index) => {
            const tglMulai = k.pivot?.tgl_masuk ?? '';
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="checkbox" class="kamar-checkbox" data-index="${index}" name="nokamar[]" value="${k.nokamar}"></td>
                <td>${k.nokamar}</td>
                <td>${k.kapasitas}</td>
                <td>${k.fasilitas}</td>
                <td><input type="date" name="tglmulai[]" value="${tglMulai}" class="form-control" readonly></td>
                <td><input type="number" name="durasi[]" value="1" class="form-control" min="1"></td>
            `;
            tbody.appendChild(row);
        });
    });

    // Saat kamar dicentang → tampilkan form tambahan untuk kamar itu
    tbody.addEventListener('change', function(e) {
        if (e.target.classList.contains('kamar-checkbox')) {
            const idx = e.target.dataset.index;
            const kamarId = e.target.value;
            const existing = document.querySelector(`#tagihan-kamar-${idx}`);

            if (e.target.checked) {
                // Tambahkan blok tagihan baru untuk kamar ini
                const div = document.createElement('div');
                div.classList.add('card', 'mt-3');
                div.id = `tagihan-kamar-${idx}`;
                div.innerHTML = `
                    <div class="card-header bg-light">
                        <strong>Tambahan untuk Kamar ${kamarId}</strong>
                    </div>
                    <div class="card-body" id="wrapper-${idx}">
                        <div class="row mb-2 tagihan-item">
                            <div class="col">
                                <input type="text" name="tagihan_deskripsi[${idx}][]" placeholder="Deskripsi" class="form-control">
                            </div>
                            <div class="col">
                                <input type="number" name="tagihan_nominal[${idx}][]" placeholder="Nominal" class="form-control">
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-danger btn-sm remove-tagihan">Hapus</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-info btn-sm add-tagihan" data-index="${idx}">Tambah Tambahan</button>
                    </div>
                `;
                tagihanWrapper.appendChild(div);
            } else if (existing) {
                existing.remove();
            }
        }
    });

    // Tambah / hapus tagihan tambahan
    tagihanWrapper.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-tagihan')) {
            const idx = e.target.dataset.index;
            const wrapper = document.getElementById(`wrapper-${idx}`);
            const div = document.createElement('div');
            div.classList.add('row', 'mb-2', 'tagihan-item');
            div.innerHTML = `
                <div class="col">
                    <input type="text" name="tagihan_deskripsi[${idx}][]" placeholder="Deskripsi" class="form-control">
                </div>
                <div class="col">
                    <input type="number" name="tagihan_nominal[${idx}][]" placeholder="Nominal" class="form-control">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-danger btn-sm remove-tagihan">Hapus</button>
                </div>
            `;
            wrapper.insertBefore(div, e.target);
        }

        if (e.target.classList.contains('remove-tagihan')) {
            e.target.closest('.tagihan-item').remove();
        }
    });
});
</script>
@endsection