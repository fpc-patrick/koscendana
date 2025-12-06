@extends('template.main')
@section('title','Daftar Tagihan')
@section('content')
<div class="content-wrapper p-4">

    {{-- Search + Filter Tanggal --}}
    <div class="d-flex justify-content-between mb-3">

        <form action="{{ route('tagihans.index') }}" method="GET" class="d-flex">

            {{-- Search --}}
            <input type="text" name="search" class="form-control" 
                   placeholder="Search" 
                   value="{{ request('search') }}" style="width: 220px;">

            {{-- Filter tanggal --}}
            <input type="date" name="tgl_mulai" class="form-control ms-2" 
                   value="{{ request('tgl_mulai') }}">

            <input type="date" name="tgl_selesai" class="form-control ms-2" 
                   value="{{ request('tgl_selesai') }}">

            <button class="btn btn-secondary ms-2">Filter</button>

            {{-- 🧹 Tombol Reset Filter (hanya muncul jika filter aktif) --}}
            @if(request('search') || request('tgl_mulai') || request('tgl_selesai'))
                <a href="{{ route('tagihans.index') }}" class="btn btn-warning ms-2">Reset</a>
            @endif

        </form>

        {{-- ❌ tombol tambah tagihan DIHAPUS --}}
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(!empty($notifications))
        @foreach($notifications as $note)
            <div class="alert alert-{{ $note['type'] }}">
                {{ $note['message'] }}
            </div>
        @endforeach
    @endif

    {{-- Tabel Tagihan --}}
    <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-dark">
            <tr>
                <th>ID Transaksi</th>
                <th>Penghuni</th>
                <th>Kamar</th>
                <th>Tanggal Sewa</th>
                <th>Tambahan</th>
                <th>Denda</th>
                <th>Total</th>
                <th>Status</th>
                <th style="width:160px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $trx)
                @php
                    $tagihans = $trx->tagihans->sortBy('tglmulai');
                    $totalTransaksi = $tagihans->sum(fn($t) => $t->harga + $t->denda);
                @endphp

                @foreach($tagihans as $i => $tag)
                    <tr>
                        @if($i == 0)
                            <td rowspan="{{ $tagihans->count() }}">{{ $trx->idtransaksi }}</td>
                            <td rowspan="{{ $tagihans->count() }}">{{ $trx->penghuni->nama ?? '-' }}</td>
                        @endif

                        <td>{{ $tag->kamar->nokamar ?? '-' }}</td>
                        <td>{{ $tag->tglmulai }} s/d {{ $tag->tglberakhir }}</td>

                        <td>
                            @php
                                $tambahan = is_string($tag->tambahan) ? json_decode($tag->tambahan, true) : $tag->tambahan;
                            @endphp
                            @if(!empty($tambahan))
                                @foreach($tambahan as $item)
                                    <span class="badge bg-info text-dark">
                                        {{ ucfirst($item['nama'] ?? 'Tambahan') }} 
                                        @if(!empty($item['harga']))(Rp {{ number_format($item['harga'],0,',','.') }})@endif
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td>
                            @if($tag->denda>0)
                                <span class="badge bg-danger">Rp {{ number_format($tag->denda,0,',','.') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        @if($i == 0)
                            <td rowspan="{{ $tagihans->count() }}">Rp {{ number_format($totalTransaksi,0,',','.') }}</td>
                        @endif

                        <td>
                            <span class="badge {{ $tag->statuspembayaran=='Lunas' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $tag->statuspembayaran }}
                            </span>
                        </td>

                        @if($i == 0)
                            <td rowspan="{{ $tagihans->count() }}">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('tagihans.show', $trx->idtransaksi) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('tagihans.edit', $tagihans->last()->idtagihan) }}" class="btn btn-sm btn-success"><i class="fas fa-pen"></i></a>
                                    <form action="{{ route('transaksis.destroy', $trx->idtransaksi) }}" method="POST" onsubmit="return confirm('Hapus transaksi & semua tagihan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="9" class="text-center">Belum ada data tagihan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $transaksis->appends(request()->query())->links() }}
    </div>
</div>
@endsection
