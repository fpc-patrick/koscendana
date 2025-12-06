@extends('template.main')
@section('title', 'Add Saldo')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('title')</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item"><a href="/pengaturansaldo">Saldo</a></li>
                            <li class="breadcrumb-item active">@yield('title')</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="text-right">
                                    <a href="/pengaturansaldo" class="btn btn-warning btn-sm"><i class="fa-solid fa-arrow-rotate-left"></i>
                                        Back
                                    </a>
                                </div>
                            </div>
                            <form class="needs-validation" novalidate action="/pengaturansaldo" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="date">Tanggal</label>
                                                <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" id="date" placeholder="Tanggal" value="{{old('date')}}" required>
                                                @error('date')
                                                <span class="invalid-feedback text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="namapengirim">Nama Pengirim</label>
                                                <input type="text" name="namapengirim" class="form-control @error('namapengirim') is-invalid @enderror" id="namapengirim" placeholder="Nama Pengirim" value="{{old('namapengirim')}}" required>
                                                @error('namapengirim')
                                                <span class="invalid-feedback text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="jumlah">Jumlah</label>
                                                <input type="number" name="jumlah" min="1" class="form-control @error('jumlah') is-invalid @enderror" id="jumlah" placeholder="Jumlah" value="{{old('jumlah')}}" required>
                                                @error('jumlah')
                                                <span class="invalid-feedback text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="note">Note</label>
                                                <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" cols="10" rows="5" placeholder="Note">{{old('note')}}</textarea>
                                                @error('note')
                                                <span class="invalid-feedback text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button class="btn btn-dark mr-1" type="reset"><i class="fa-solid fa-arrows-rotate"></i>
                                        Reset</button>
                                    <button class="btn btn-success" type="submit"><i class="fa-solid fa-floppy-disk"></i>
                                        Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
