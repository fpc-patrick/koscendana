@extends('template.main')
@section('title','Penghuni')
@section('content')
<div class="content-wrapper p-4">

    {{-- Form Search + Filter Tanggal + Tambah Data --}}
    <div class="d-flex justify-content-between mb-3">

        <form action="{{ route('penghunis.index') }}" method="GET" class="d-flex">

            {{-- Search --}}
            <input type="text" name="search" class="form-control" 
                   placeholder="Search" 
                   value="{{ request('search') }}" style="width: 180px;">

            {{-- Filter tanggal --}}
            <input type="date" name="tgl_mulai" class="form-control ms-2" 
                   value="{{ request('tgl_mulai') }}">

            <input type="date" name="tgl_selesai" class="form-control ms-2" 
                   value="{{ request('tgl_selesai') }}">

            <button class="btn btn-secondary ms-2">Filter</button>

            {{-- Tombol Reset Filter --}}
            @if(request('search') || request('tgl_mulai') || request('tgl_selesai'))
                <a href="{{ route('penghunis.index') }}" class="btn btn-warning ms-2">
                    Reset
                </a>
            @endif

        </form>

        <a href="{{ route('penghunis.create') }}" class="btn btn-primary">Tambah Penghuni</a>
    </div>

    {{-- Notifikasi sukses --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tabel Data Penghuni --}}
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Kode</th>
                <th>Nama Penyewa</th>
                <th>No HP</th>
                <th>No Ortu</th>
                <th>Foto KTP</th>
                <th>Kamar & Penghuni</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse($penghunis as $p)
                <tr>
                    <td>{{ $p->kode_penghuni }}</td>
                    <td>{{ $p->nama }}</td>
                    <td>{{ $p->nohp }}</td>
                    <td>{{ $p->nohp_orangtua }}</td>

                    {{-- Foto KTP --}}
                    <td>
                        @if($p->fotoktp)
                            <a href="{{ asset('uploads/ktp/'.$p->fotoktp) }}" target="_blank">
                                <img src="{{ asset('uploads/ktp/'.$p->fotoktp) }}" 
                                     alt="KTP" width="80" class="img-thumbnail">
                            </a>
                        @else
                            <span class="text-muted">Belum ada</span>
                        @endif
                    </td>

                    {{-- Riwayat kamar --}}
                    <td>
                        @forelse($p->kamars as $k)
                            <div class="mb-2 p-2 border rounded bg-light">
                                <strong>{{ $k->nokamar }}</strong><br>
                                <small>({{ $k->pivot->tgl_masuk }} - {{ $k->pivot->tgl_keluar }})</small><br>
                                <small>Penyewa: {{ $k->pivot->penyewa_nama ?? '-' }} ({{ $k->pivot->penyewa_nohp ?? '-' }})</small>
                            </div>
                        @empty
                            <span class="text-muted">Belum ada kamar</span>
                        @endforelse
                    </td>

                    {{-- Status penghuni --}}
                    <td>
                        @if($p->status_penghuni === 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                        @elseif($p->status_penghuni === 'Nonaktif')
                            <span class="badge bg-secondary">Nonaktif</span>
                        @else
                            <span class="badge bg-warning text-dark">Belum Ditentukan</span>
                        @endif
                    </td>

                    {{-- Tombol Aksi --}}
                    <td>
                        <a href="{{ route('penghunis.edit',$p->idpenghuni) }}"
                           class="btn btn-success btn-sm">Edit</a>

                        <form action="{{ route('penghunis.destroy',$p->idpenghuni) }}"
                              method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Hapus data ini?')"
                                    class="btn btn-danger btn-sm">Hapus</button>
                        </form>

                        <a href="{{ route('penghunis.pindah.form', $p->idpenghuni) }}"
                           class="btn btn-warning btn-sm">Pindah Kamar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Data kosong</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $penghunis->appends(request()->query())->links() }}
    </div>
</div>
@endsection
