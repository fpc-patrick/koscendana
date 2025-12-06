<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Saldo;

class SaldoController extends Controller
{
    public function index()
    {
        $saldo = Saldo::orderBy('namapengirim', 'asc')->get();

        return view('saldo.saldo', [
            'saldo' => $saldo
        ]);;
    }


    public function create()
    {
        return view('saldo.saldo-add');
    }

    public function store(Request $request)
    {
        // Validation logic here if needed

        Saldo::create([
            'date' => $request->input('date'),
            'namapengirim' => $request->input('namapengirim'),
            'jumlah' => $request->input('jumlah'),
            'note' => $request->input('note'),
            // Add other fields as needed
        ]);

        return redirect('/pengaturansaldo')->with('success', 'Saldo added successfully');
    }

    public function edit($id_saldo)
    {
        try {
            $saldo = Saldo::findOrFail($id_saldo);
            return view('saldo.saldo-edit', compact('saldo'));
        } catch (\Exception $ex) {
            abort(404); // Redirect to 404 page
        }
    }


    public function update(Request $request, $id_saldo)
    {
        // Find the saldo by ID
        $saldo = Saldo::findOrFail($id_saldo);

        // Update the fields based on the form data
        $saldo->date = $request->input('date');
        $saldo->namapengirim = $request->input('namapengirim');
        $saldo->jumlah = $request->input('jumlah');
        $saldo->note = $request->input('note');
        $saldo->save();

        return redirect('/pengaturansaldo')->with('success', 'Saldo updated successfully');
    }




    public function destroy($id_saldo)
    {
        try {
            $saldo = Saldo::findOrFail($id_saldo);
            $saldo->delete(); // Use delete for soft delete

            return redirect('/pengaturansaldo')->with('success', 'Saldo deleted successfully');
        } catch (\Exception $ex) {
            return redirect('/pengaturansaldo')->with('error', 'Failed to delete saldo');
        }
    }
}
