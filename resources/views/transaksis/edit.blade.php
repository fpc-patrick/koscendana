@extends('template.main')
@section('title','Edit Transaksi')
@section('content')
<div class="content-wrapper p-4">
    <h2>Edit Transaksi #{{ $transaksi->idtransaksi }}</h2>

    <form action="{{ route('transaksis.update', $transaksi->idtransaksi) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nama Penghuni</label>
            <input type="text" class="form-control" value="{{ $transaksi->penghuni->nama }}" readonly>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label>Tanggal Bayar</label>
                <input type="date" name="tglbayar" class="form-control" value="{{ $transaksi->tglbayar }}">
            </div>
            <div class="col">
                <label>Status Bayar</label>
                <select name="statusbayar" class="form-control">
                    <option value="Belum Lunas" {{ $transaksi->statusbayar=='Belum Lunas'?'selected':'' }}>Belum Lunas</option>
                    <option value="Lunas" {{ $transaksi->statusbayar=='Lunas'?'selected':'' }}>Lunas</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label>Bukti Bayar</label>
            <input type="file" name="buktibayar" class="form-control">
            @if($transaksi->buktibayar)
                <img src="{{ asset('storage/'.$transaksi->buktibayar) }}" class="img-thumbnail mt-2" width="150">
            @endif
        </div>

        <h5>Detail Kamar & Tambahan</h5>
        @foreach($transaksi->tagihans as $index => $tagihan)
        <div class="card mb-3">
            <div class="card-header bg-light">
                <strong>Kamar {{ $tagihan->kamar->nokamar ?? '-' }}</strong>
            </div>
            <div class="card-body">
                <p><strong>Tanggal Sewa:</strong> {{ $tagihan->tglmulai }} s/d {{ $tagihan->tglberakhir }}</p>
                <p><strong>Harga:</strong> Rp {{ number_format($tagihan->harga,0,',','.') }}</p>

                <h6>Tambahan:</h6>
                @if(!empty($tagihan->tambahan))
                    @foreach($tagihan->tambahan as $item)
                        <div>{{ $item['nama'] ?? '-' }} (Rp {{ number_format($item['harga'] ?? 0,0,',','.') }})</div>
                    @endforeach
                @else
                    <div class="text-muted">Tidak ada tambahan</div>
                @endif
            </div>
        </div>
        @endforeach

        <button class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
@endsection