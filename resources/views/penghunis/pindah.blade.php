@extends('template.main')
@section('title', 'Pindah Kamar')
@section('content')
<div class="content-wrapper p-4">

    <h3>Pindah Kamar : {{ $penghuni->nama }}</h3>

    <form action="{{ route('penghunis.pindah.store', $penghuni->idpenghuni) }}" method="POST">
        @csrf

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Pilih</th>
                    <th>Kamar Lama</th>
                    <th>Tgl Masuk</th>
                    <th>Tgl Keluar</th>
                    <th>Kamar Baru</th>
                    <th>Tgl Masuk Baru</th>
                </tr>
            </thead>

            <tbody>
                @foreach($penghuni->kamars as $k)
                <tr>
                    <td>
                        <input type="checkbox"
                               name="kamar_lama[]"
                               value="{{ $k->nokamar }}"
                               class="pilih-kamar"
                               data-target="{{ $k->nokamar }}">
                    </td>

                    <td>{{ $k->nokamar }}</td>
                    <td>{{ $k->pivot->tgl_masuk }}</td>
                    <td>{{ $k->pivot->tgl_keluar ?? '-' }}</td>

                    {{-- KAMAR BARU --}}
                    <td>
                        <select name="kamar_baru[{{ $k->nokamar }}]"
                                class="form-control kamar-baru kamar-baru-{{ $k->nokamar }}"
                                style="display:none;">
                            <option value="">-- Pilih kamar baru --</option>
                            @foreach($kamars as $baru)
                                <option value="{{ $baru->nokamar }}">
                                    {{ $baru->nokamar }} - Rp {{ number_format($baru->hargastandar) }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    {{-- TGL MASUK BARU --}}
                    <td>
                        <input type="date"
                               name="tgl_masuk_baru[{{ $k->nokamar }}]"
                               class="form-control tgl-baru tgl-baru-{{ $k->nokamar }}"
                               style="display:none;">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Pindah Kamar</button>
        <a href="{{ route('penghunis.index') }}" class="btn btn-secondary">Batal</a>
    </form>

</div>

<script>
    document.querySelectorAll('.pilih-kamar').forEach(cb => {
        cb.addEventListener('change', function() {
            let kamar = this.dataset.target;

            // element by class
            let kamarBaru = document.querySelector('.kamar-baru-' + kamar);
            let tglBaru = document.querySelector('.tgl-baru-' + kamar);

            if (this.checked) {
                kamarBaru.style.display = 'block';
                tglBaru.style.display = 'block';
            } else {
                kamarBaru.style.display = 'none';
                tglBaru.style.display = 'none';

                // clear value supaya tidak mengirim data kosong
                kamarBaru.value = "";
                tglBaru.value = "";
            }
        });
    });
</script>

@endsection
