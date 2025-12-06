@extends('template.main')
@section('title','Tambah Kamar')
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
                <form action="{{ route('kamars.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Lantai</label>
                            <select name="lantai" id="lantai" class="form-control" required>
                                <option value="">-- Pilih Lantai --</option>
                                <option value="Lantai 1">Lantai 1</option>
                                <option value="Lantai 2">Lantai 2</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nomor Kamar</label>
                            <input type="text" id="nokamar" class="form-control" value="" disabled>
                        </div>
                        <div class="form-group">
                            <label>Kapasitas</label>
                            <select name="kapasitas" class="form-control" required>
                                <option value="1">1 Orang</option>
                                <option value="2">2 Orang</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fasilitas</label>
                            <textarea name="fasilitas" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Harga Standar</label>
                            <input type="number" name="hargastandar" class="form-control" required>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Script untuk tampilkan nomor kamar otomatis saat pilih lantai --}}
<script>
document.getElementById('lantai').addEventListener('change', function() {
    const lantai = this.value;
    if(lantai) {
        fetch(`/kamars/next-number/${encodeURIComponent(lantai)}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('nokamar').value = data.nokamar;
            });
    } else {
        document.getElementById('nokamar').value = '';
    }
});
</script>

@endsection