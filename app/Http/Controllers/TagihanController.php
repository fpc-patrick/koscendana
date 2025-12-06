<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Transaksi;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TagihanController extends Controller
{
    // Menampilkan daftar tagihan + notifikasi + filter tanggal
    public function index(Request $request)
    {
        $search = $request->search;
        $tglMulai = $request->tgl_mulai;
        $tglSelesai = $request->tgl_selesai;

        $transaksis = Transaksi::with(['penghuni', 'tagihans.kamar'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('penghuni', fn($q2) => $q2->where('nama', 'like', "%$search%"))
                  ->orWhereHas('tagihans.kamar', fn($q3) => $q3->where('nokamar', 'like', "%$search%"));
            })
            ->when($tglMulai && $tglSelesai, function ($q) use ($tglMulai, $tglSelesai) {
                $q->whereHas('tagihans', fn($q2) => 
                    $q2->whereBetween('tglmulai', [$tglMulai, $tglSelesai])
                );
            })
            ->orderBy('tglbayar', 'desc')
            ->paginate(10);

        $today = Carbon::today();
        $notifications = [];

        foreach ($transaksis as $transaksi) {
            foreach ($transaksi->tagihans as $tagihan) {
                $due = Carbon::parse($tagihan->tglmulai)->endOfDay();

                // Update denda jika lewat tanggal bayar
                if ($today->gt($due) && $tagihan->statuspembayaran == 'Belum Lunas' && $tagihan->denda != 100000) {
                    $tagihan->update(['denda' => 100000]);
                }

                $selisihHari = $today->diffInDays($due, false);

                // Notifikasi H-3 sebelum tanggal bayar
                if ($selisihHari <= 3 && $selisihHari >= 0 && $tagihan->statuspembayaran == 'Belum Lunas') {
                    $notifications[] = [
                        'type' => 'warning',
                        'message' => "⚠️ Tagihan kamar {$tagihan->kamar->nokamar} untuk {$transaksi->penghuni->nama} akan jatuh tempo dalam {$selisihHari} hari!"
                    ];
                }

                // Notifikasi keterlambatan
                if ($today->gt($due) && $tagihan->statuspembayaran == 'Belum Lunas') {
                    $notifications[] = [
                        'type' => 'danger',
                        'message' => "❌ Tagihan kamar {$tagihan->kamar->nokamar} untuk {$transaksi->penghuni->nama} telah lewat tanggal bayar dan dikenakan denda Rp100.000!"
                    ];
                }

                // Update status penghuni otomatis berdasarkan tglberakhir
                $tglAkhirSewa = Carbon::parse($tagihan->tglberakhir)->endOfDay();
                $transaksi->penghuni->update(['status_penghuni' => $today->gt($tglAkhirSewa) ? 'Nonaktif' : 'Aktif']);
            }
        }

        return view('tagihans.index', compact('transaksis', 'search', 'tglMulai', 'tglSelesai', 'notifications'));
    }

    // Form tambah tagihan
    public function create()
    {
        $transaksis = Transaksi::with('penghuni', 'tagihans')->get();
        $kamars = Kamar::all();
        return view('tagihans.create', compact('transaksis', 'kamars'));
    }

    // Simpan tagihan baru
    public function store(Request $request)
    {
        $request->validate([
            'transaksi_id' => 'required|exists:transaksis,idtransaksi',
            'nokamar' => 'required|exists:kamars,nokamar',
            'tglmulai' => 'required|date',
            'tglberakhir' => 'required|date|after_or_equal:tglmulai',
            'harga' => 'required|numeric|min:0',
        ]);

        $transaksi = Transaksi::findOrFail($request->transaksi_id);

        Tagihan::create([
            'transaksi_id' => $transaksi->idtransaksi,
            'nokamar' => $request->nokamar,
            'tglmulai' => $request->tglmulai,
            'tglberakhir' => $request->tglberakhir,
            'tambahan' => $transaksi->tambahan ?? [],
            'harga' => $request->harga,
            'denda' => 0,
            'statuspembayaran' => 'Belum Lunas',
        ]);

        $transaksi->penghuni->update(['status_penghuni' => 'Aktif']);

        return redirect()->route('tagihans.index')->with('success', 'Tagihan berhasil dibuat');
    }

    // Lihat detail tagihan
    public function show($id)
    {
        $transaksi = Transaksi::with(['penghuni', 'tagihans.kamar'])->findOrFail($id);
        return view('tagihans.show', compact('transaksi'));
    }

    // Form edit tagihan
    public function edit(Tagihan $tagihan)
    {
        return view('tagihans.edit', compact('tagihan'));
    }

    // Update tagihan & status pembayaran
    public function update(Request $request, Tagihan $tagihan)
    {
        $request->validate([
            'statuspembayaran' => 'required|in:Lunas,Belum Lunas',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $transaksi = $tagihan->transaksi;
        $penghuni = $transaksi->penghuni;
        $status = $request->statuspembayaran;
        $today = Carbon::today();

        // Upload bukti bayar
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('public/bukti', $filename);

            $transaksi->update(['bukti' => $filename, 'statusbayar' => 'Lunas']);

            foreach ($transaksi->tagihans as $t) {
                $t->update(['statuspembayaran' => 'Lunas']);
                $t->kamar->update(['statuskamar' => 'Terisi']);
            }

            $penghuni->update(['status_penghuni' => 'Aktif']);

            return redirect()->route('tagihans.index')->with('success', 'Semua tagihan transaksi berhasil Lunas');
        }

        // Hitung denda
        $due = Carbon::parse($tagihan->tglmulai)->endOfDay();
        $denda = $tagihan->denda;
        if ($status === 'Belum Lunas' && $today->gt($due) && $denda != 100000) {
            $denda = 100000;
        }

        $tagihan->update([
            'statuspembayaran' => $status,
            'denda' => $denda,
        ]);

        // Update status kamar & penghuni berdasarkan tglberakhir
        $tglAkhirSewa = Carbon::parse($tagihan->tglberakhir)->endOfDay();
        if ($status === 'Lunas') {
            $tagihan->kamar->update(['statuskamar' => 'Terisi']);
        }
        $penghuni->update(['status_penghuni' => $today->gt($tglAkhirSewa) ? 'Nonaktif' : 'Aktif']);

        // Update status transaksi utama
        if ($transaksi->tagihans()->where('statuspembayaran', 'Belum Lunas')->count() == 0) {
            $transaksi->update(['statusbayar' => 'Lunas']);
        } else {
            $transaksi->update(['statusbayar' => 'Belum Lunas']);
        }

        return redirect()->route('tagihans.index')->with('success', 'Tagihan berhasil diupdate');
    }

    // Hapus tagihan
    public function destroy(Tagihan $tagihan)
    {
        $kamar = $tagihan->kamar;
        $transaksi = $tagihan->transaksi;
        $penghuni = $transaksi->penghuni;

        $tagihan->delete();

        if ($kamar->tagihans()->where('statuspembayaran', 'Belum Lunas')->count() == 0) {
            $kamar->update(['statuskamar' => 'Kosong']);
        }

        $masihAktif = $penghuni->transaksis()->whereHas('tagihans', function ($q) {
            $q->whereDate('tglberakhir', '>=', Carbon::today());
        })->exists();

        $penghuni->update(['status_penghuni' => $masihAktif ? 'Aktif' : 'Nonaktif']);

        return redirect()->route('tagihans.index')->with('success', 'Tagihan berhasil dihapus');
    }

    // Auto cek expired harian (scheduler)
    public function autoCheckExpired()
    {
        $today = Carbon::today();
        $expired = Tagihan::with('transaksi.penghuni', 'kamar')->get();

        foreach ($expired as $t) {
            $due = Carbon::parse($t->tglmulai)->endOfDay();
            if ($today->gt($due) && $t->statuspembayaran == 'Belum Lunas' && $t->denda != 100000) {
                $t->update(['denda' => 100000]);
            }

            $tglAkhirSewa = Carbon::parse($t->tglberakhir)->endOfDay();
            $t->kamar->update(['statuskamar' => $today->gt($tglAkhirSewa) ? 'Kosong' : 'Terisi']);
            $t->transaksi->penghuni->update(['status_penghuni' => $today->gt($tglAkhirSewa) ? 'Nonaktif' : 'Aktif']);
        }
    }
}
