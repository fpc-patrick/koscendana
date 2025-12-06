<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kamar;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $barangs = Barang::with('kamar')
            ->when($search, function($q) use ($search){
                $q->where('kodebarang','like',"%$search%")
                  ->orWhere('keterangan','like',"%$search%")
                  ->orWhereHas('kamar', fn($q2)=> $q2->where('nokamar','like',"%$search%"));
            })
            ->orderBy('tanggallaporan','desc')
            ->paginate(10);

        return view('barangs.index', compact('barangs','search'));
    }

    public function create()
    {
        $kamars = Kamar::all();
        return view('barangs.create', compact('kamars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kodebarang'=>'required|unique:barangs,kodebarang',
            'nokamar'=>'required|exists:kamars,nokamar',
            'keterangan'=>'nullable',
            'tanggallaporan'=>'nullable|date',
            'harga'=>'required|numeric',
            'status'=>'required',
        ]);

        Barang::create($request->all());
        return redirect()->route('barangs.index')->with('success','Barang berhasil ditambahkan');
    }

    public function edit(Barang $barang)
    {
        $kamars = Kamar::all();
        return view('barangs.edit', compact('barang','kamars'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nokamar'=>'required|exists:kamars,nokamar',
            'keterangan'=>'nullable',
            'tanggallaporan'=>'nullable|date',
            'harga'=>'required|numeric',
            'status'=>'required',
        ]);

        $barang->update($request->all());
        return redirect()->route('barangs.index')->with('success','Barang berhasil diupdate');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();
        return redirect()->route('barangs.index')->with('success','Barang berhasil dihapus');
    }
}