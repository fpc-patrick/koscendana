@extends('template.main')
@section('title','Edit Kamar')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>@yield('title')</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header text-right">
                    <a href="{{ route('kamars.index') }}" class="btn btn-warning btn-sm">
                        <i class="fa-solid fa-arrow-rotate-left"></i> Back
                    </a>
                </div>
                <form action="{{ route('kamars.update', $kamar->nokamar) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label>Lantai</label>
                            <input type="text" class="form-control" value="{{ $kamar->lantai }}" disabled>
                        </div>
                        <div class="form-group">
                            <label>Nomor Kamar</label>
                            <input type="text" class="form-control" value="{{ $kamar->nokamar }}" disabled>
                        </div>
                        <div class="form-group">
                            <label>Kapasitas</label>
                            <select name="kapasitas" class="form-control" required>
                                <option value="1" {{ $kamar->kapasitas == 1 ? 'selected' : '' }}>1 Orang</option>
                                <option value="2" {{ $kamar->kapasitas == 2 ? 'selected' : '' }}>2 Orang</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fasilitas</label>
                            <textarea name="fasilitas" class="form-control" rows="3" required>{{ $kamar->fasilitas }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Harga Standar</label>
                            <input type="number" name="hargastandar" class="form-control" value="{{ $kamar->hargastandar }}" required>
                        </div>
                        <div class="form-group">
                            <label>Status Kamar</label>
                            <select name="statuskamar" class="form-control" required>
                                <option value="Kosong" {{ $kamar->statuskamar == 'Kosong' ? 'selected' : '' }}>Kosong</option>
                                <option value="Terisi" {{ $kamar->statuskamar == 'Terisi' ? 'selected' : '' }}>Terisi</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection