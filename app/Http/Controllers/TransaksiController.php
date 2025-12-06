<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Penghuni;
use App\Models\Kamar;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    // Daftar transaksi (dengan filter history tanggal)
    public function index(Request $request)
    {
        $search      = $request->search;
        $tglMulai    = $request->tglmulai_from;
        $tglAkhir    = $request->tglmulai_to;


        // Update otomatis status kamar & penghuni
        $today = Carbon::today();

        // Update kamar
        $kamars = Kamar::all();
        foreach ($kamars as $kamar) {
            $existsActiveTagihan = Tagihan::where('nokamar', $kamar->nokamar)
                ->whereDate('tglmulai', '<=', $today)
                ->whereDate('tglberakhir', '>=', $today)
                ->exists();

            $kamar->update(['statuskamar' => $existsActiveTagihan ? 'Terisi' : 'Kosong']);
        }

        // Update penghuni
        $penghunis = Penghuni::all();
        foreach ($penghunis as $penghuni) {
            $hasActive = Tagihan::whereHas('transaksi', function ($q) use ($penghuni) {
                    $q->where('penghuni_id', $penghuni->idpenghuni);
                })
                ->whereDate('tglmulai', '<=', $today)
                ->whereDate('tglberakhir', '>=', $today)
                ->exists();

            $penghuni->update(['status_penghuni' => $hasActive ? 'Aktif' : 'Nonaktif']);
        }

        // Query transaksi + Filter
        $transaksis = Transaksi::with(['penghuni', 'tagihans.kamar'])
            ->when($search, function ($query) use ($search) {
                // Filter berdasarkan nama atau kode penghuni
                $query->whereHas('penghuni', function ($subQuery) use ($search) {
                    $subQuery->where('nama', 'like', "%$search%")
                            ->orWhere('kode_penghuni', 'like', "%$search%");
                });
            })
            ->when($tglMulai && $tglAkhir, function ($query) use ($tglMulai, $tglAkhir) {
                // Filter berdasarkan tagihan yang sepenuhnya dalam rentang filter
                $query->whereHas('tagihans', function ($q) use ($tglMulai, $tglAkhir) {
                    $q->whereDate('tglmulai', '>=', $tglMulai)
                    ->whereDate('tglberakhir', '<=', $tglAkhir);
                });
            })
            ->orderByDesc('idtransaksi')
            ->paginate(10);


        return view('transaksis.index', compact('transaksis', 'search', 'tglMulai', 'tglAkhir'));
    }

    // Form tambah transaksi
    public function create()
    {
        $penghunis = Penghuni::with('kamars')->get();
        return view('transaksis.create', compact('penghunis'));
    }

    // Simpan transaksi baru
    public function store(Request $request)
    {
        $request->validate([
            'penghuni_id'       => 'required|exists:penghunis,idpenghuni',
            'tglbayar'          => 'nullable|date',
            'statusbayar'       => 'nullable|in:Lunas,Belum Lunas',
            'buktibayar'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nokamar'           => 'required|array',
            'tglmulai'          => 'required|array',
            'durasi'            => 'required|array',
            'tagihan_deskripsi' => 'nullable|array',
            'tagihan_nominal'   => 'nullable|array',
        ]);

        $filename = null;
        $statusBayar = $request->statusbayar ?? 'Belum Lunas';

        if ($request->hasFile('buktibayar')) {
            $filename = $request->file('buktibayar')->store('uploads/bukti', 'public');
            $statusBayar = 'Lunas';
        }

        $transaksi = Transaksi::create([
            'penghuni_id' => $request->penghuni_id,
            'tglbayar'    => $request->tglbayar ?? now(),
            'statusbayar' => $statusBayar,
            'buktibayar'  => $filename,
        ]);

        foreach ($request->nokamar as $index => $nokamar) {
            $kamar = Kamar::find($nokamar);
            if (!$kamar) continue;

            $tglmulai = Carbon::parse($request->tglmulai[$index]);
            $durasi = max((int) $request->durasi[$index], 1);
            $tglberakhir = (clone $tglmulai)->addMonths($durasi)->subDay();

            $tambahan = [];
            if (isset($request->tagihan_deskripsi[$index]) && is_array($request->tagihan_deskripsi[$index])) {
                foreach ($request->tagihan_deskripsi[$index] as $i => $desc) {
                    if (!empty($desc)) {
                        $tambahan[] = [
                            'nama' => $desc,
                            'harga' => (int) ($request->tagihan_nominal[$index][$i] ?? 0),
                        ];
                    }
                }
            }

            $totalTambahan = array_sum(array_column($tambahan, 'harga'));
            $hargaKamar = $kamar->hargastandar ?? 0;
            $totalHarga = ($hargaKamar + $totalTambahan) * $durasi;

            Tagihan::create([
                'transaksi_id'     => $transaksi->idtransaksi,
                'nokamar'          => $nokamar,
                'tglmulai'         => $tglmulai->format('Y-m-d'),
                'tglberakhir'      => $tglberakhir->format('Y-m-d'),
                'tambahan'         => $tambahan,
                'harga'            => $totalHarga,
                'statuspembayaran' => $statusBayar,
            ]);

            // set status kamar & penghuni
            if (Carbon::now()->gte($tglmulai)) {
                $kamar->update(['statuskamar' => 'Terisi']);
                $transaksi->penghuni->update(['status_penghuni' => 'Aktif']);
            } else {
                $kamar->update(['statuskamar' => 'Kosong']);
                $transaksi->penghuni->update(['status_penghuni' => 'Nonaktif']);
            }
        }

        return redirect()->route('transaksis.index')->with('success', 'Transaksi berhasil dibuat.');
    }

    // Edit transaksi
    public function edit(Transaksi $transaksi)
    {
        $penghunis = Penghuni::with('kamars')->get();
        $transaksi->load('tagihans.kamar');
        return view('transaksis.edit', compact('transaksi', 'penghunis'));
    }

    // Update transaksi
    public function update(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'tglbayar' => 'nullable|date',
            'statusbayar' => 'nullable|in:Lunas,Belum Lunas',
            'buktibayar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'tglbayar'     => $request->tglbayar ?? $transaksi->tglbayar,
            'statusbayar'  => $request->statusbayar ?? $transaksi->statusbayar,
        ];

        if ($request->hasFile('buktibayar')) {
            $data['buktibayar'] = $request->file('buktibayar')->store('uploads/bukti', 'public');
            $data['statusbayar'] = 'Lunas';
        }

        $transaksi->update($data);

        foreach ($transaksi->tagihans as $tagihan) {
            $tagihan->update(['statuspembayaran' => $data['statusbayar']]);
            $tglmulai = Carbon::parse($tagihan->tglmulai);

            if ($data['statusbayar'] === 'Lunas' && Carbon::now()->gte($tglmulai)) {
                $tagihan->kamar->update(['statuskamar' => 'Terisi']);
                $transaksi->penghuni->update(['status_penghuni' => 'Aktif']);
            }
        }

        return redirect()->route('transaksis.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    // Hapus transaksi
    public function destroy(Transaksi $transaksi)
    {
        foreach ($transaksi->tagihans as $tagihan) {
            Kamar::where('nokamar', $tagihan->nokamar)
                 ->update(['statuskamar' => 'Kosong']);
        }

        $transaksi->penghuni->update(['status_penghuni' => 'Nonaktif']);
        $transaksi->delete();

        return redirect()->route('transaksis.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    // Perpanjang transaksi
    public function perpanjang($id)
    {
        $transaksiLama = Transaksi::with('tagihans.kamar', 'penghuni')->findOrFail($id);
        $penghuni = $transaksiLama->penghuni;

        // Buat transaksi baru
        $transaksiBaru = Transaksi::create([
            'penghuni_id' => $penghuni->idpenghuni,
            'tglbayar'    => null,
            'statusbayar' => 'Belum Lunas',
            'buktibayar'  => null,
        ]);

        foreach ($transaksiLama->tagihans as $tagihan) {
            $durasi = max(Carbon::parse($tagihan->tglberakhir)->diffInMonths(Carbon::parse($tagihan->tglmulai)), 1);
            $tglmulai = Carbon::parse($tagihan->tglberakhir)->addDay();
            $tglberakhir = (clone $tglmulai)->addMonths($durasi)->subDay();

            $kamar = $tagihan->kamar;
            $hargaKamar = $kamar->hargastandar ?? 0;

            $totalTambahan = 0;
            if (!empty($tagihan->tambahan) && is_array($tagihan->tambahan)) {
                foreach ($tagihan->tambahan as $item) {
                    if (isset($item['harga'])) $totalTambahan += $item['harga'];
                }
            }

            $totalHarga = ($hargaKamar + $totalTambahan) * $durasi;

            // Buat tagihan baru
            Tagihan::create([
                'transaksi_id'     => $transaksiBaru->idtransaksi,
                'nokamar'          => $tagihan->nokamar,
                'tglmulai'         => $tglmulai->format('Y-m-d'),
                'tglberakhir'      => $tglberakhir->format('Y-m-d'),
                'tambahan'         => $tagihan->tambahan,
                'harga'            => $totalHarga,
                'statuspembayaran' => 'Belum Lunas',
            ]);

            // Update status kamar
            $kamar->update(['statuskamar' => Carbon::now()->gte($tglmulai) ? 'Terisi' : 'Kosong']);

            // Update pivot table penghunian
            if ($penghuni->kamars()->wherePivot('nokamar', $kamar->nokamar)->exists()) {
                $penghuni->kamars()->updateExistingPivot($kamar->nokamar, [
                    'tgl_masuk'  => $tglmulai,
                    'tgl_keluar' => $tglberakhir,
                ]);
            } else {
                $penghuni->kamars()->attach($kamar->nokamar, [
                    'tgl_masuk'    => $tglmulai,
                    'tgl_keluar'   => $tglberakhir,
                    'penyewa_nama' => $penghuni->nama,
                    'penyewa_nohp' => $penghuni->nohp,
                ]);
            }
        }

        // Set status penghuni
        $penghuni->update(['status_penghuni' => 'Aktif']);

        return redirect()->route('transaksis.index')->with('success', 'Transaksi berhasil diperpanjang, transaksi baru telah dibuat.');
    }


    // Batalkan Pesanan Kamar
    public function batalKamar($idtransaksi, $nokamar)
    {
        $transaksi = Transaksi::with('tagihans.kamar', 'penghuni')->findOrFail($idtransaksi);
        $penghuni = $transaksi->penghuni;

        // Cek apakah kamar memang ada di transaksi ini
        $tagihan = $transaksi->tagihans()->where('nokamar', $nokamar)->first();
        if(!$tagihan){
            return redirect()->back()->with('error', 'Kamar tidak ditemukan pada transaksi ini.');
        }

        // Update status kamar menjadi kosong
        $tagihan->kamar->update(['statuskamar' => 'Kosong']);

        // Hapus tagihan kamar yang dibatalkan
        $tagihan->delete();

        // ================================
        // 🔥 KEMBALIKAN TANGGAL DI PIVOT 🔥
        // ================================
        $tagihanSebelumnya = Tagihan::where('nokamar', $nokamar)
            ->where('transaksi_id', '!=', $transaksi->idtransaksi) // bukan transaksi sekarang
            ->orderByDesc('tglberakhir') // ambil yang terakhir
            ->first();

        if ($tagihanSebelumnya) {
            // Kalau ada riwayat sebelum ini → kembalikan ke tanggal sebelumnya
            $penghuni->kamars()->updateExistingPivot($nokamar, [
                'tgl_masuk'  => $tagihanSebelumnya->tglmulai,
                'tgl_keluar' => $tagihanSebelumnya->tglberakhir,
            ]);
        } else {
            // Kalau tidak ada riwayat sama sekali → hapus hubungan pivot
            $penghuni->kamars()->detach($nokamar);
        }

        // Cek sisa kamar di transaksi ini
        $sisaTagihan = $transaksi->tagihans()->count();

        if($sisaTagihan == 0){
            $transaksi->delete();
            $penghuni->update(['status_penghuni' => 'Nonaktif']);
            return redirect()->route('transaksis.index')->with('success', 'Semua kamar dibatalkan, transaksi dihapus.');
        }

        // Update status penghuni berdasarkan sisa kamar aktif
        $penghuni->update(['status_penghuni' => 'Aktif']);

        return redirect()->back()->with('success', "Kamar {$nokamar} berhasil dibatalkan dari transaksi.");
    }

}
