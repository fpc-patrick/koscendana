@extends('template.main')
@section('title','Daftar Barang')
@section('content')

<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@yield('title')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">@yield('title')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- Card Header: Tombol Tambah & Search -->
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <a href="{{ route('barangs.create') }}" class="btn btn-primary">
                                <i class="fa-solid fa-plus"></i> Tambah Barang
                            </a>

                            <form method="GET" class="d-flex">
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari barang..." style="width: 250px;">
                                <button class="btn btn-secondary ms-2"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
                            </form>
                        </div>

                        <!-- Card Body: Table -->
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped table-hover text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Kode Barang</th>
                                        <th>Kamar</th>
                                        <th>Keterangan</th>
                                        <th>Tanggal Laporan</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($barangs as $b)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $b->kodebarang }}</td>
                                            <td>{{ $b->kamar->nokamar ?? '-' }}</td>
                                            <td>{{ $b->keterangan }}</td>
                                            <td>{{ $b->tanggallaporan }}</td>
                                            <td>Rp {{ number_format($b->harga,0,',','.') }}</td>
                                            <td>
                                                <span class="badge {{ $b->status=='Rusak' ? 'bg-danger' : 'bg-success' }}">
                                                    {{ $b->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('barangs.edit',$b->kodebarang) }}" class="btn btn-sm btn-success">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <form action="{{ route('barangs.destroy',$b->kodebarang) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Belum ada data barang.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Card Footer: Pagination -->
                        <div class="card-footer d-flex justify-content-end">
                            {{ $barangs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
