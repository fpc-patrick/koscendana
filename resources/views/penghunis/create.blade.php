@extends('template.main')
@section('title','Tambah Penghuni')
@section('content')
<div class="content-wrapper p-4">
    <h2>Tambah Penghuni</h2>
    <form action="{{ route('penghunis.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Kode Penghuni</label>
            <input type="text" name="kode_penghuni" class="form-control" value="{{ $newKode }}" readonly>
        </div>
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>No HP</label>
            <input type="text" name="nohp" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>No HP Orang Tua</label>
            <input type="text" name="nohp_orangtua" class="form-control">
        </div>
        <div class="mb-3">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Foto KTP</label>
            <input type="file" name="fotoktp" class="form-control">
        </div>

        <h5>Pilih Kamar</h5>
        <table class="table table-bordered">
            <thead class="table-secondary">
                <tr>
                    <th>Pilih</th>
                    <th>No Kamar</th>
                    <th>Kapasitas</th>
                    <th>Fasilitas</th>
                    <th>Harga</th>
                    <th>Tgl Masuk</th>
                    <th>Tgl Keluar</th>
                    <th>Nama Penyewa</th>
                    <th>No HP Penghuni</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kamars as $k)
                    <tr>
                        <td>
                            <input type="checkbox" name="kamar[{{ $k->nokamar }}][selected]" value="1">
                        </td>
                        <td>{{ $k->nokamar }}</td>
                        <td>{{ $k->kapasitas }}</td>
                        <td>{{ $k->fasilitas }}</td>
                        <td>Rp{{ number_format($k->hargastandar, 0, ',', '.') }}</td>
                        <td>
                            <input type="date" name="kamar[{{ $k->nokamar }}][tgl_masuk]">
                        </td>
                        <td>
                            <input type="date" name="kamar[{{ $k->nokamar }}][tgl_keluar]">
                        </td>
                        <td>
                            <input type="text" name="kamar[{{ $k->nokamar }}][penyewa_nama]" value="{{ old('nama') }}">
                        </td>
                        <td>
                            <input type="text" name="kamar[{{ $k->nokamar }}][penyewa_nohp]" value="{{ old('nohp') }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
