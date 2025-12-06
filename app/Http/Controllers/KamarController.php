<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    // Menampilkan daftar kamar dengan search
    public function index(Request $request) {
        $query = Kamar::query();

        if ($request->search) {
            $search = $request->search;
            $query->where('nokamar', 'like', "%$search%")
                  ->orWhere('lantai', 'like', "%$search%")
                  ->orWhere('fasilitas', 'like', "%$search%")
                  ->orWhere('kapasitas', 'like', "%$search%")
                  ->orWhere('hargastandar', 'like', "%$search%");
        }

        $kamars = $query->orderBy('nokamar', 'asc')->paginate(10);
        return view('kamars.index', compact('kamars'));
    }

    // Form tambah kamar
    public function create() {
        return view('kamars.create');
    }

    // Generate nomor kamar dan isi celah nomor
    private function generateNomorKamar($lantai) {
        $existingNumbers = Kamar::where('lantai', $lantai)
            ->orderBy('nokamar', 'asc')
            ->pluck('nokamar')
            ->map(fn($n) => (int) substr($n, 1))
            ->toArray();

        $start = $lantai == 'Lantai 1' ? 1001 : 2001;

        $newNumber = $start;
        foreach ($existingNumbers as $num) {
            if ($num == $newNumber) {
                $newNumber++;
            } else {
                break;
            }
        }

        return 'L' . $newNumber;
    }

    public function nextNumber($lantai)
    {
        $nokamar = $this->generateNomorKamar($lantai);
        return response()->json(['nokamar' => $nokamar]);
    }


    // Simpan kamar baru
    public function store(Request $request) {
        $request->validate([
            'lantai' => 'required',
            'kapasitas' => 'required',
            'fasilitas' => 'required',
            'hargastandar' => 'required|numeric'
        ]);

        $nokamar = $this->generateNomorKamar($request->lantai);

        Kamar::create([
            'nokamar' => $nokamar,
            'lantai' => $request->lantai,
            'kapasitas' => $request->kapasitas,
            'fasilitas' => $request->fasilitas,
            'hargastandar' => $request->hargastandar,
            'statuskamar' => 'Kosong'
        ]);

        return redirect()->route('kamars.index')
            ->with('success', 'Kamar berhasil ditambahkan dengan nomor ' . $nokamar);
    }

    // Form edit kamar
    public function edit(Kamar $kamar) {
        return view('kamars.edit', compact('kamar'));
    }

    // Update data kamar
    public function update(Request $request, Kamar $kamar) {
        $request->validate([
            'kapasitas' => 'required',
            'fasilitas' => 'required',
            'hargastandar' => 'required|numeric',
            'statuskamar' => 'required',
        ]);

        $kamar->update($request->only(['kapasitas','fasilitas','hargastandar','statuskamar']));

        return redirect()->route('kamars.index')->with('success', 'Data Kamar berhasil diupdate');
    }

    // Hapus kamar
    public function destroy(Kamar $kamar) {
        $kamar->delete();
        return redirect()->route('kamars.index')->with('success','Kamar berhasil dihapus');
    }
}