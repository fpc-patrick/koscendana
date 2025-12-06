@extends('template.main')
@section('title','Tambah Barang')
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
                        <li class="breadcrumb-item"><a href="{{ route('barangs.index') }}">Barang</a></li>
                        <li class="breadcrumb-item active">@yield('title')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card">
                        <!-- Card Header: Tombol Back -->
                        <div class="card-header text-right">
                            <a href="{{ route('barangs.index') }}" class="btn btn-warning btn-sm">
                                <i class="fa-solid fa-arrow-rotate-left"></i> Back
                            </a>
                        </div>

                        <!-- Form Create Barang -->
                        <form action="{{ route('barangs.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <!-- Kode Barang -->
                                    <div class="col-lg-6 mb-3">
                                        <label for="kodebarang">Kode Barang</label>
                                        <input type="text" name="kodebarang" id="kodebarang" class="form-control @error('kodebarang') is-invalid @enderror" value="{{ old('kodebarang') }}" required>
                                        @error('kodebarang')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Kamar -->
                                    <div class="col-lg-6 mb-3">
                                        <label for="nokamar">Kamar</label>
                                        <select name="nokamar" id="nokamar" class="form-control @error('nokamar') is-invalid @enderror" required>
                                            <option value="">-- Pilih Kamar --</option>
                                            @foreach($kamars as $k)
                                                <option value="{{ $k->nokamar }}" {{ old('nokamar') == $k->nokamar ? 'selected' : '' }}>
                                                    {{ $k->nokamar }} - {{ $k->lantai }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('nokamar')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Keterangan -->
                                    <div class="col-lg-6 mb-3">
                                        <label for="keterangan">Keterangan</label>
                                        <textarea name="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="4">{{ old('keterangan') }}</textarea>
                                        @error('keterangan')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Tanggal Laporan -->
                                    <div class="col-lg-6 mb-3">
                                        <label for="tanggallaporan">Tanggal Laporan</label>
                                        <input type="date" name="tanggallaporan" id="tanggallaporan" class="form-control @error('tanggallaporan') is-invalid @enderror" value="{{ old('tanggallaporan') }}">
                                        @error('tanggallaporan')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Harga -->
                                    <div class="col-lg-6 mb-3">
                                        <label for="harga">Harga</label>
                                        <input type="number" name="harga" id="harga" min="0" class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga') }}" required>
                                        @error('harga')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div class="col-lg-6 mb-3">
                                        <label for="status">Status</label>
                                        <input type="text" name="status" id="status" class="form-control @error('status') is-invalid @enderror" value="{{ old('status','Baik') }}" required>
                                        @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer: Reset & Submit -->
                            <div class="card-footer text-right">
                                <button type="reset" class="btn btn-dark mr-1"><i class="fa-solid fa-arrows-rotate"></i> Reset</button>
                                <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
