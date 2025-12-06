@extends('template.main')
@section('title','Edit Penghuni')
@section('content')
<div class="content-wrapper p-4">
    <h2>Edit Penghuni</h2>
    <form action="{{ route('penghunis.update', $penghuni->idpenghuni) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Kode Penghuni</label>
            <input type="text" class="form-control" value="{{ $penghuni->kode_penghuni }}" readonly>
        </div>
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama', $penghuni->nama) }}" required>
        </div>
        <div class="mb-3">
            <label>No HP</label>
            <input type="text" name="nohp" class="form-control" value="{{ old('nohp', $penghuni->nohp) }}" required>
        </div>
        <div class="mb-3">
            <label>No HP Orang Tua</label>
            <input type="text" name="nohp_orangtua" class="form-control" value="{{ old('nohp_orangtua', $penghuni->nohp_orangtua) }}">
        </div>
        <div class="mb-3">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control">{{ old('alamat', $penghuni->alamat) }}</textarea>
        </div>
        <div class="mb-3">
            <label>Foto KTP</label><br>
            @if($penghuni->fotoktp)
                <img src="{{ asset('uploads/ktp/'.$penghuni->fotoktp) }}" alt="Foto KTP" width="120" class="mb-2"><br>
            @endif
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
                    <th>No HP Penyewa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kamars as $k)
                    @php
                        $pivot = $penghuni->kamars->where('nokamar', $k->nokamar)->first();
                    @endphp
                    <tr>
                        <td>
                            <input type="checkbox" name="kamar[{{ $k->nokamar }}][selected]" value="1"
                                   {{ $pivot ? 'checked' : '' }}>
                        </td>
                        <td>{{ $k->nokamar }}</td>
                        <td>{{ $k->kapasitas }}</td>
                        <td>{{ $k->fasilitas }}</td>
                        <td>Rp{{ number_format($k->hargastandar, 0, ',', '.') }}</td>
                        <td>
                            <input type="date" name="kamar[{{ $k->nokamar }}][tgl_masuk]" 
                                   value="{{ $pivot ? $pivot->pivot->tgl_masuk : '' }}">
                        </td>
                        <td>
                            <input type="date" name="kamar[{{ $k->nokamar }}][tgl_keluar]" 
                                   value="{{ $pivot ? $pivot->pivot->tgl_keluar : '' }}">
                        </td>
                        <td>
                            <input type="text" name="kamar[{{ $k->nokamar }}][penyewa_nama]" 
                                   value="{{ $pivot ? $pivot->pivot->penyewa_nama : old('nama') }}">
                        </td>
                        <td>
                            <input type="text" name="kamar[{{ $k->nokamar }}][penyewa_nohp]" 
                                   value="{{ $pivot ? $pivot->pivot->penyewa_nohp : old('nohp') }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('penghunis.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
