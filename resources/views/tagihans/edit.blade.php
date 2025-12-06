@extends('template.main')
@section('title','Edit Tagihan')

@section('content')
<div class="content-wrapper p-4">
    <h2>Edit Tagihan / Transaksi #{{ $tagihan->transaksi->idtransaksi }}</h2>

    <form action="{{ route('tagihans.update', $tagihan->idtagihan) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Penghuni --}}
        <div class="mb-3">
            <label>Nama Penghuni</label>
            <input type="text" class="form-control" value="{{ $tagihan->transaksi->penghuni->nama }}" readonly>
        </div>

        {{-- Status bayar & tanggal bayar --}}
        <div class="row mb-3">
            <div class="col">
                <label>Status Bayar</label>
                <select name="statuspembayaran" class="form-control">
                    <option value="Belum Lunas" {{ $tagihan->statuspembayaran=='Belum Lunas'?'selected':'' }}>Belum Lunas</option>
                    <option value="Lunas" {{ $tagihan->statuspembayaran=='Lunas'?'selected':'' }}>Lunas</option>
                </select>
            </div>
        </div>

        {{-- Bukti bayar --}}
        <div class="mb-3">
            <label>Bukti Bayar (upload di transaksi, semua tagihan akan Lunas)</label>
            <input type="file" name="bukti" class="form-control">
            @if($tagihan->transaksi->bukti)
                <img src="{{ asset('storage/'.$tagihan->transaksi->bukti) }}" class="img-thumbnail mt-2" width="150">
            @endif
        </div>

        {{-- Detail kamar & tagihan --}}
        <h5>Detail Kamar & Tambahan</h5>
        @foreach($tagihan->transaksi->tagihans as $index => $t)
        <div class="card mb-3">
            <div class="card-header bg-light">
                <strong>Kamar {{ $t->kamar->nokamar ?? '-' }}</strong>
            </div>
            <div class="card-body">
                <p><strong>Tanggal Sewa:</strong> {{ $t->tglmulai }} s/d {{ $t->tglberakhir }}</p>
                <p><strong>Harga:</strong> Rp {{ number_format($t->harga,0,',','.') }}</p>

                <h6>Tambahan:</h6>
                @if(!empty($t->tambahan))
                    @foreach($t->tambahan as $item)
                        <div>{{ $item['nama'] ?? '-' }} (Rp {{ number_format($item['harga'] ?? 0,0,',','.') }})</div>
                    @endforeach
                @else
                    <div class="text-muted">Tidak ada tambahan</div>
                @endif

                <p><strong>Status Bayar:</strong> {{ $t->statuspembayaran }}</p>
                <p><strong>Denda:</strong> Rp {{ number_format($t->denda,0,',','.') }}</p>
            </div>
        </div>
        @endforeach

        <button class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
@endsection
