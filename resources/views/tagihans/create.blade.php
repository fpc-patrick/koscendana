@extends('template.main')
@section('title','Tambah Tagihan')
@section('content')
<div class="content-wrapper p-4">

    <h4>Tambah Tagihan</h4>

    <form action="{{ route('tagihans.store') }}" method="POST">
        @csrf

        {{-- Transaksi --}}
        <div class="mb-3">
            <label class="form-label">Transaksi</label>
            <select name="transaksi_id" class="form-control" required>
                <option value="">-- Pilih Transaksi --</option>
                @foreach($transaksis as $tr)
                    <option value="{{ $tr->idtransaksi }}">
                        {{ $tr->penghuni->nama }} - Kamar {{ $tr->kamar->nokamar }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Kamar --}}
        <div class="mb-3">
            <label class="form-label">No Kamar</label>
            <select name="nokamar" class="form-control" required>
                <option value="">-- Pilih Kamar --</option>
                @foreach($kamars as $k)
                    <option value="{{ $k->nokamar }}">{{ $k->nokamar }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tanggal --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tglmulai" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Berakhir</label>
                <input type="date" name="tglberakhir" class="form-control" required>
            </div>
        </div>

        {{-- Tambahan --}}
        <div class="mb-3">
            <label class="form-label">Tambahan (JSON)</label>
            <textarea name="tambahan" class="form-control" placeholder='contoh: {"tv":100000,"kulkas":150000}'></textarea>
            <small class="text-muted">Isi dengan format JSON</small>
        </div>

        {{-- Harga --}}
        <div class="mb-3">
            <label class="form-label">Harga</label>
            <input type="number" name="harga" class="form-control" required>
        </div>

        {{-- Status Pembayaran --}}
        <div class="mb-3">
            <label class="form-label">Status Pembayaran</label>
            <select name="statuspembayaran" class="form-control">
                <option value="Belum Lunas">Belum Lunas</option>
                <option value="Lunas">Lunas</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('tagihans.index') }}" class="btn btn-secondary">Batal</a>
    </form>

</div>
@endsection