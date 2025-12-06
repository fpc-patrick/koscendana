<?php

namespace App\Http\Controllers;
use App\Models\Transaksi;
use App\Models\Tagihan;
use Carbon\Carbon;

use App\Models\Penghuni;
use App\Models\Kamar;
use Illuminate\Http\Request;

class PenghuniController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $tglMulai = $request->tgl_mulai;
        $tglSelesai = $request->tgl_selesai;

        $penghunis = Penghuni::with('kamars')
            ->when($search, function($q) use ($search) {
                $q->where('nama','like',"%$search%")
                  ->orWhere('nohp','like',"%$search%")
                  ->orWhere('kode_penghuni','like',"%$search%");
            })
            ->when($tglMulai && $tglSelesai, function($q) use ($tglMulai, $tglSelesai) {
                $q->whereHas('kamars', function($rel) use ($tglMulai, $tglSelesai) {
                    $rel->where(function($date) use ($tglMulai, $tglSelesai) {
                        $date->whereBetween('tgl_masuk', [$tglMulai, $tglSelesai])
                             ->orWhereBetween('tgl_keluar', [$tglMulai, $tglSelesai])
                             ->orWhereRaw("'$tglMulai' BETWEEN tgl_masuk AND tgl_keluar")
                             ->orWhereRaw("'$tglSelesai' BETWEEN tgl_masuk AND tgl_keluar");
                    });
                });
            })
            ->paginate(10);

        return view('penghunis.index', compact('penghunis','search','tglMulai','tglSelesai'));
    }

    public function create()
    {
        $kamars = Kamar::where('statuskamar','Kosong')->get();

        // Generate kode penghuni
        $last = Penghuni::orderBy('idpenghuni','desc')->first();
        if ($last) {
            $num = (int) substr($last->kode_penghuni, 1);
            $newKode = 'P' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newKode = 'P001';
        }

        return view('penghunis.create', compact('kamars','newKode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_penghuni' => 'required|unique:penghunis,kode_penghuni',
            'nama' => 'required',
            'nohp' => 'required',
            'nohp_orangtua' => 'nullable',
            'alamat' => 'nullable',
            'fotoktp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload foto KTP
        if ($request->hasFile('fotoktp')) {
            $file = $request->file('fotoktp');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/ktp'), $filename);
            $validated['fotoktp'] = $filename;
        }

        $penghuni = Penghuni::create($validated);

        // Relasi kamar
        if ($request->has('kamar')) {
            foreach ($request->kamar as $nokamar => $data) {
                if (!isset($data['selected'])) continue; 
                $penghuni->kamars()->attach($nokamar, [
                    'tgl_masuk' => $data['tgl_masuk'] ?? null,
                    'tgl_keluar' => $data['tgl_keluar'] ?? null,
                    'penyewa_nama' => $data['penyewa_nama'] ?? null,
                    'penyewa_nohp' => $data['penyewa_nohp'] ?? null,
                ]);

                Kamar::where('nokamar', $nokamar)->update(['statuskamar' => 'Terisi']);
            }
        }

        return redirect()->route('penghunis.index')->with('success','Penghuni berhasil ditambahkan');
    }

    public function edit(Penghuni $penghuni)
    {
        $kamars = Kamar::where('statuskamar', 'Kosong')
            ->orWhereHas('penghunis', function ($q) use ($penghuni) {
                $q->where('penghuni_id', $penghuni->idpenghuni);
            })
            ->get();

        return view('penghunis.edit', compact('penghuni', 'kamars'));
    }

    public function update(Request $request, Penghuni $penghuni)
    {
        $validated = $request->validate([
            'nama'=>'required',
            'nohp'=>'required',
            'nohp_orangtua'=>'nullable',
            'alamat'=>'nullable',
            'fotoktp'=>'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('fotoktp')) {
            $file = $request->file('fotoktp');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/ktp'), $filename);
            $validated['fotoktp'] = $filename;
        }

        $penghuni->update($validated);

        $oldRooms = $penghuni->kamars->pluck('nokamar')->toArray();
        $syncData = [];

        if ($request->has('kamar')) {
            foreach ($request->kamar as $nokamar => $data) {
                if (!isset($data['selected'])) continue;

                $syncData[$nokamar] = [
                    'tgl_masuk' => $data['tgl_masuk'] ?? null,
                    'tgl_keluar' => $data['tgl_keluar'] ?? null,
                    'penyewa_nama' => $data['penyewa_nama'] ?? null,
                    'penyewa_nohp' => $data['penyewa_nohp'] ?? null,
                ];

                Kamar::where('nokamar', $nokamar)->update(['statuskamar' => 'Terisi']);
            }
        }

        // Update pivot kamar
        $penghuni->kamars()->sync($syncData);

        // Kosongkan kamar lama yang tidak dipilih
        foreach ($oldRooms as $old) {
            if (!isset($syncData[$old])) {
                Kamar::where('nokamar', $old)->update(['statuskamar' => 'Kosong']);
            }
        }

        return redirect()->route('penghunis.index')->with('success','Penghuni berhasil diupdate');
    }

    public function destroy(Penghuni $penghuni)
    {
        $penghuni->delete();
        return redirect()->route('penghunis.index')->with('success','Penghuni berhasil dihapus');
    }

    public function getKamars($id)
    {
        $penghuni = Penghuni::with('kamars')->findOrFail($id);
        return response()->json($penghuni->kamars);
    }

    public function formPindah($id)
    {
        $penghuni = Penghuni::findOrFail($id);
        $kamars = Kamar::where('statuskamar', 'Kosong')->get();

        return view('penghunis.pindah', compact('penghuni', 'kamars'));
    }

    public function pindahKamar(Request $request, $id)
    {
        // VALIDASI INPUT
        $request->validate([
            'kamar_lama' => 'required|array',
            'kamar_baru' => 'required|array',
            'kamar_baru.*' => 'nullable|exists:kamars,nokamar',

            'tgl_masuk_baru' => 'required|array',
            'tgl_masuk_baru.*' => 'nullable|date',
        ]);

        $penghuni = Penghuni::findOrFail($id);
        $transaksi = $penghuni->transaksis()->latest('created_at')->first();

        if (!$transaksi) {
            return back()->with('error', 'Transaksi terakhir tidak ditemukan.');
        }

        $today = Carbon::now();

        foreach ($request->kamar_lama as $kamarLama) {

            // Ambil data kamar baru dan tanggal masuk baru
            $kamarBaruId = $request->kamar_baru[$kamarLama] ?? null;
            $tglMasukBaru = $request->tgl_masuk_baru[$kamarLama] ?? null;

            if (!$kamarBaruId || !$tglMasukBaru) continue;

            $kamarBaru = Kamar::findOrFail($kamarBaruId);

            // Ambil tagihan berdasarkan kamar lama
            $tagihan = $transaksi->tagihans()
                ->where('nokamar', $kamarLama)
                ->latest('tglmulai')
                ->first();

            if (!$tagihan) continue;

            // Decode tambahan jika string
            $tambahan = is_string($tagihan->tambahan) ? json_decode($tagihan->tambahan, true) ?: [] : $tagihan->tambahan;

            $tglMulai = Carbon::parse($tagihan->tglmulai);
            $tglBerakhir = Carbon::parse($tagihan->tglberakhir);

            // Hitung harga kamar
            if ($today->lessThanOrEqualTo($tglBerakhir)) {
                $hargaKamar = $tagihan->harga;
                $denda = $tagihan->denda ?? 0;
            } else {
                $hargaKamar = $kamarBaru->hargastandar ?? 0;
                $denda = $tagihan->denda ?? 0;
            }

            // Update tagihan mengarah ke kamar baru
            $tagihan->update([
                'nokamar'   => $kamarBaru->nokamar,
                'fasilitas' => $kamarBaru->fasilitas ?? null,
                'harga'     => $hargaKamar,
                'tambahan'  => $tambahan,
                'denda'     => $denda,
            ]);

            // Update pivot kamar lama
            $penghuni->kamars()->updateExistingPivot($kamarLama, [
                'tgl_keluar' => $tglBerakhir,
            ]);

            // Update status kamar lama hanya jika tanggal mulai kamar baru tercapai
            if ($today->gte(Carbon::parse($tglMasukBaru))) {
                Kamar::where('nokamar', $kamarLama)->update(['statuskamar' => 'Kosong']);
            }

            // Attach kamar baru di pivot
            $penghuni->kamars()->attach($kamarBaru->nokamar, [
                'tgl_masuk'     => $tglMasukBaru,
                'tgl_keluar'    => $tglBerakhir,
                'penyewa_nama'  => $penghuni->nama,
                'penyewa_nohp'  => $penghuni->nohp,
            ]);

            // Update status kamar baru hanya jika tanggal mulai tercapai
            if ($today->gte(Carbon::parse($tglMasukBaru))) {
                $kamarBaru->update(['statuskamar' => 'Terisi']);
            } else {
                $kamarBaru->update(['statuskamar' => 'Kosong']);
            }
        }

        // Hitung ulang total transaksi
        $totalTransaksi = $transaksi->tagihans->sum(function($t){
            $tambahan = is_string($t->tambahan) ? json_decode($t->tambahan,true) ?: [] : $t->tambahan;
            $totalTambahan = array_sum(array_column($tambahan,'harga'));
            $denda = $t->denda ?? 0;
            $durasi = Carbon::parse($t->tglberakhir)->diffInMonths(Carbon::parse($t->tglmulai)) + 1;
            return ($t->harga + $totalTambahan + $denda) * $durasi;
        });

        $transaksi->update(['total_bayar' => $totalTransaksi]);

        return redirect()->route('penghunis.index')->with('success', 'Penghuni berhasil pindah kamar (multi), sesuai tanggal mulai.');
    }


}
