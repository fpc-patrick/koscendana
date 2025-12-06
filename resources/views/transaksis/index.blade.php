@extends('template.main')
@section('title','Daftar Transaksi')
@section('content')
<div class="content-wrapper p-4">

    {{-- FORM SEARCH + FILTER --}}
    <div class="d-flex justify-content-between mb-3">
        <form action="{{ route('transaksis.index') }}" method="GET" class="d-flex align-items-center gap-2">
            {{-- Search penghuni --}}
            <input type="text" name="search" class="form-control"
                   placeholder="Search"
                   value="{{ request('search') }}">
            {{-- Filter tanggal mulai --}}
            <input type="date" name="tglmulai_from" class="form-control"
                   value="{{ request('tglmulai_from') }}">
            <input type="date" name="tglmulai_to" class="form-control"
                   value="{{ request('tglmulai_to') }}">
            <button class="btn btn-secondary">Filter</button>
            @if(request('tglmulai_from') || request('tglmulai_to') || request('search'))
                <a href="{{ route('transaksis.index') }}" class="btn btn-warning">Reset</a>
            @endif
        </form>

        <a href="{{ route('transaksis.create') }}" class="btn btn-primary">Tambah Transaksi</a>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- TABEL TRANSAKSI --}}
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Kode Transaksi</th>
                <th>Penghuni</th>
                <th>Kamar & Tagihan</th>
                <th>Tgl Bayar</th>
                <th>Status Bayar</th>
                <th>Total Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $no = ($transaksis->currentPage()-1) * $transaksis->perPage() + 1;
            @endphp

            @forelse($transaksis as $t)
                @php
                    $tagihanLama = $t->tagihans->filter(fn($tag) => $t->tglbayar ? $tag->tglmulai <= $t->tglbayar : true);
                    $tagihanBaru = $t->tagihans->filter(fn($tag) => $t->tglbayar ? $tag->tglmulai > $t->tglbayar : collect());
                    $totalLama = $tagihanLama->sum('harga') + $tagihanLama->sum('denda');
                    $totalBaru = $tagihanBaru->sum('harga') + $tagihanBaru->sum('denda');
                    $tglBayar = $t->tglbayar ?? $t->tagihans->min('tglmulai') ?? '-';
                    $transaksiTerbaru = \App\Models\Transaksi::where('penghuni_id', $t->penghuni_id)
                        ->orderByDesc('idtransaksi')
                        ->first();
                @endphp

                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $t->idtransaksi }}</td>
                    <td>
                        {{ $t->penghuni->nama ?? '-' }} 
                        ({{ $t->penghuni->kode_penghuni ?? '-' }})
                    </td>

                    <td>
                        @foreach($t->tagihans as $tag)
                            <div class="mb-1 p-2 border rounded bg-light">
                                <strong>{{ $tag->kamar->nokamar ?? '-' }}</strong> <br>
                                <small>{{ $tag->tglmulai }} s/d {{ $tag->tglberakhir }}</small><br>

                                @if($tag->tambahan)
                                    <small>
                                        @php
                                            $tambahan = is_string($tag->tambahan) ? json_decode($tag->tambahan, true) : $tag->tambahan;
                                        @endphp
                                        @foreach($tambahan as $item)
                                            {{ $item['nama'] ?? 'Tambahan' }}:
                                            {{ number_format($item['harga'] ?? 0,0,',','.') }} <br>
                                        @endforeach
                                    </small>
                                @endif

                                <strong>Harga: {{ number_format($tag->harga + ($tag->denda ?? 0),0,',','.') }}</strong>

                                {{-- BUTTON BATAL KAMAR --}}
                                <form action="{{ route('transaksi.batalkamar', [$t->idtransaksi, $tag->nokamar]) }}" method="POST" onsubmit="return confirm('Yakin batal kamar ini?')" class="mt-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Batal Kamar</button>
                                </form>
                            </div>
                        @endforeach
                    </td>

                    <td>{{ $tglBayar }}</td>

                    <td>
                        @php $allStatus = $t->tagihans->pluck('statuspembayaran')->unique(); @endphp
                        @foreach($allStatus as $status)
                            <span class="badge {{ $status=='Lunas' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $status }}
                            </span><br>
                        @endforeach
                    </td>

                    <td>
                        @if($totalLama > 0)
                            <strong>{{ number_format($totalLama,0,',','.') }}</strong><br>
                        @endif
                        @if($totalBaru > 0)
                            <strong>{{ number_format($totalBaru,0,',','.') }}</strong>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('transaksis.edit',$t->idtransaksi) }}" class="btn btn-success btn-sm">Edit</a>

                        <form action="{{ route('transaksis.destroy',$t->idtransaksi) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Hapus transaksi ini?')" class="btn btn-danger btn-sm">
                                Hapus
                            </button>
                        </form>

                        @if($transaksiTerbaru && $transaksiTerbaru->idtransaksi == $t->idtransaksi)
                            <form action="{{ route('transaksi.perpanjang', $t->idtransaksi) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm">Perpanjang</button>
                            </form>
                        @endif
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="8" class="text-center">Data kosong</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $transaksis->links() }}
    </div>

</div>
@endsection
