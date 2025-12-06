@extends('template.main')
@section('title','Kamar')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="mb-0">@yield('title')</h1>
            <a href="{{ route('kamars.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah Kamar
            </a>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Kamar</h5>
                    <form action="{{ route('kamars.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2"
                               placeholder="Cari nomor, lantai, fasilitas, kapasitas, harga..."
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-secondary">
                            <i class="fa fa-search"></i>
                        </button>
                    </form>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nomor Kamar</th>
                                    <th>Lantai</th>
                                    <th>Kapasitas</th>
                                    <th>Fasilitas</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                    <th width="220px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kamars as $k)
                                    <tr>
                                        <td>{{ $loop->iteration + ($kamars->currentPage()-1)*$kamars->perPage() }}</td>
                                        <td>{{ $k->nokamar }}</td>
                                        <td>{{ $k->lantai }}</td>
                                        <td>{{ $k->kapasitas }} Orang</td>
                                        <td>{{ $k->fasilitas }}</td>
                                        <td>Rp {{ number_format($k->hargastandar,0,',','.') }}</td>
                                        <td>
                                            <span class="badge {{ $k->statuskamar=='Kosong'?'bg-danger':'bg-success' }}">
                                                {{ $k->statuskamar }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('kamars.edit',$k->nokamar) }}" class="btn btn-success btn-sm">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form action="{{ route('kamars.destroy',$k->nokamar) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">Data kamar tidak ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-2">
                            {{ $kamars->appends(['search'=>request('search')])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection