@extends('template.main')
@section('title', 'Detail Tagihan')
@section('content')

@php
    use Carbon\Carbon;
@endphp

<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Detail Tagihan</h3>
        <a href="{{ route('tagihans.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <strong>Transaksi #{{ $transaksi->idtransaksi }}</strong>
        </div>

        <div class="card-body">
            <p><strong>Nama Penghuni:</strong> {{ $transaksi->penghuni->nama ?? '-' }}</p>
            <p><strong>Status Bayar:</strong>
                @if($transaksi->statusbayar == 'Lunas')
                    <span class="badge bg-success">Lunas</span>
                @else
                    <span class="badge bg-warning text-dark">Belum Lunas</span>
                @endif
            </p>
            <hr>

            <h5>Detail Tagihan</h5>
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kamar</th>
                        <th>Periode</th>
                        <th>Harga Kamar (per bulan)</th>
                        <th>Tambahan (per bulan)</th>
                        <th>Denda</th> {{-- ✅ Tambahan kolom --}}
                        <th>Total Per Kamar</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalTransaksi = 0;
                    @endphp

                    @foreach($transaksi->tagihans->sortBy('tglmulai') as $tagihan)
                        @php
                            $kamar = $tagihan->kamar;
                            $basePerMonth = $kamar->hargastandar ?? 0;
                            $start = Carbon::parse($tagihan->tglmulai);
                            $end   = Carbon::parse($tagihan->tglberakhir);
                            $months = max($start->diffInMonths($end), 1);

                            // Hitung tambahan per bulan
                            $additionPerMonth = 0;
                            if (!empty($tagihan->tambahan) && is_array($tagihan->tambahan)) {
                                foreach ($tagihan->tambahan as $item) {
                                    if (is_array($item) && isset($item['harga'])) {
                                        $additionPerMonth += (int)$item['harga'];
                                    }
                                }
                            }

                            // ✅ Ambil nilai denda dari kolom tagihan (jika ada)
                            $denda = $tagihan->denda ?? 0;

                            // Hitung total untuk kamar ini
                            $totalKamar = ($basePerMonth + $additionPerMonth) * $months + $denda;
                            $totalTransaksi += $totalKamar;
                        @endphp

                        <tr>
                            <td>{{ $kamar->nokamar ?? '-' }}</td>
                            <td>{{ $tagihan->tglmulai }} s/d {{ $tagihan->tglberakhir }} ({{ $months }} bulan)</td>
                            <td>Rp {{ number_format($basePerMonth,0,',','.') }}</td>
                            <td style="text-align:left;">
                                @if(!empty($tagihan->tambahan) && is_array($tagihan->tambahan))
                                    @foreach($tagihan->tambahan as $item)
                                        @if(is_array($item) && isset($item['nama']))
                                            <div>{{ $item['nama'] }} — Rp {{ number_format($item['harga'],0,',','.') }} /bln</div>
                                        @endif
                                    @endforeach
                                @else
                                    <div class="text-muted">-</div>
                                @endif
                            </td>

                            {{-- ✅ Kolom denda --}}
                            <td>Rp {{ number_format($denda,0,',','.') }}</td>

                            <td><strong>Rp {{ number_format($totalKamar,0,',','.') }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="table-secondary">
                        <th colspan="5" class="text-end">Total Transaksi:</th>
                        <th><strong>Rp {{ number_format($totalTransaksi,0,',','.') }}</strong></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection
