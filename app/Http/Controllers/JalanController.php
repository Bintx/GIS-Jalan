<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jalan;

class JalanController extends Controller
{
    public function index()
    {
        $jalans = Jalan::all();
        return view('jalan.index', compact('jalans'));
    }

    public function create()
    {
        return view('jalan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jalan' => 'required',
            'lokasi' => 'required',
            'panjang' => 'required|numeric',
            'lebar' => 'required|numeric',
            'tipe_perkerasan' => 'required',
            'status' => 'required',
        ]);

        Jalan::create($request->all());
        return redirect()->route('jalan.index')->with('success', 'Data jalan berhasil ditambahkan');
    }

    public function show(Jalan $jalan)
    {
        return view('jalan.show', compact('jalan'));
    }

    public function edit(Jalan $jalan)
    {
        return view('jalan.edit', compact('jalan'));
    }

    public function update(Request $request, Jalan $jalan)
    {
        $request->validate([
            'nama_jalan' => 'required',
            'lokasi' => 'required',
            'panjang' => 'required|numeric',
            'lebar' => 'required|numeric',
            'tipe_perkerasan' => 'required',
            'status' => 'required',
        ]);

        $jalan->update($request->all());
        return redirect()->route('jalan.index')->with('success', 'Data jalan berhasil diperbarui');
    }

    public function destroy(Jalan $jalan)
    {
        $jalan->delete();
        return redirect()->route('jalan.index')->with('success', 'Data jalan berhasil dihapus');
    }
}

