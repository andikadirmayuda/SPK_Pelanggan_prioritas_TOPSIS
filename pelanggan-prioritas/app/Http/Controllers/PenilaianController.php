<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\Pelanggan;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index()
    {
        $penilaians = Penilaian::with(['pelanggan', 'kriteria', 'subKriteria'])->get();
        return view('penilaian.index', compact('penilaians'));
    }

    public function create()
    {
        $pelanggans = Pelanggan::all();
        $kriterias = Kriteria::with('subKriteria')->get();
        return view('penilaian.create', compact('pelanggans', 'kriterias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'kriteria_id' => 'required|exists:kriteria,id',
            'sub_kriteria_id' => 'required|exists:sub_kriteria,id'
        ]);

        // Validasi bahwa sub_kriteria_id sesuai dengan kriteria_id
        $subKriteria = SubKriteria::findOrFail($request->sub_kriteria_id);
        if ($subKriteria->kriteria_id != $request->kriteria_id) {
            return back()->withErrors(['sub_kriteria_id' => 'Sub kriteria tidak sesuai dengan kriteria yang dipilih.']);
        }

        Penilaian::create($request->all());

        return redirect()->route('penilaian.index')
            ->with('success', 'Penilaian berhasil ditambahkan.');
    }

    public function edit(Penilaian $penilaian)
    {
        $pelanggans = Pelanggan::all();
        $kriterias = Kriteria::with('subKriteria')->get();
        return view('penilaian.edit', compact('penilaian', 'pelanggans', 'kriterias'));
    }

    public function update(Request $request, Penilaian $penilaian)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'kriteria_id' => 'required|exists:kriteria,id',
            'sub_kriteria_id' => 'required|exists:sub_kriteria,id'
        ]);

        // Validasi bahwa sub_kriteria_id sesuai dengan kriteria_id
        $subKriteria = SubKriteria::findOrFail($request->sub_kriteria_id);
        if ($subKriteria->kriteria_id != $request->kriteria_id) {
            return back()->withErrors(['sub_kriteria_id' => 'Sub kriteria tidak sesuai dengan kriteria yang dipilih.']);
        }

        $penilaian->update($request->all());

        return redirect()->route('penilaian.index')
            ->with('success', 'Penilaian berhasil diperbarui.');
    }

    public function destroy(Penilaian $penilaian)
    {
        $penilaian->delete();

        return redirect()->route('penilaian.index')
            ->with('success', 'Penilaian berhasil dihapus.');
    }

    public function getSubKriteria($kriteria_id)
    {
        $subKriterias = SubKriteria::where('kriteria_id', $kriteria_id)->get();
        return response()->json($subKriterias);
    }
}
