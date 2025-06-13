<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\Pelanggan;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{    public function index(Request $request)
    {
        // Get tahun from request or use current year as default
        $tahunTerpilih = $request->input('tahun', date('Y'));
        
        // Get pelanggan with their penilaian for the selected year only
        $pelanggans = Pelanggan::with(['penilaian' => function($query) use ($tahunTerpilih) {
            $query->where('tahun', $tahunTerpilih)
                  ->with(['kriteria', 'subKriteria']);
        }])->get();

        // Filter out pelanggan without penilaian for the selected year
        $pelanggans = $pelanggans->filter(function($pelanggan) {
            return $pelanggan->penilaian->isNotEmpty();
        });

        // Get available years for the filter
        $tahunTersedia = Penilaian::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('penilaian.index', compact('pelanggans', 'tahunTersedia', 'tahunTerpilih'));
    }

    public function create()
    {
        $pelanggans = Pelanggan::all();
        // Get kriteria with their sub-kriteria, ordered by nilai
        $kriterias = Kriteria::with(['subKriteria' => function($query) {
            $query->orderBy('nilai');
        }])->get();
        return view('penilaian.create', compact('pelanggans', 'kriterias'));
    }    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'tahun' => 'required|digits:4|integer|min:1900|max:'.(date('Y')+1),
            'penilaian' => 'required|array',
            'penilaian.*.kriteria_id' => 'required|exists:kriteria,id',
            'penilaian.*.sub_kriteria_id' => 'required|exists:sub_kriteria,id',
        ]);

        foreach ($request->penilaian as $nilai) {
            Penilaian::create([
                'pelanggan_id' => $request->pelanggan_id,
                'tahun' => $request->tahun,
                'kriteria_id' => $nilai['kriteria_id'],
                'sub_kriteria_id' => $nilai['sub_kriteria_id']
            ]);
        }

        return redirect()->route('penilaian.index')
            ->with('success', 'Penilaian berhasil ditambahkan.');
    }

    public function edit(Penilaian $penilaian)
    {
        $pelanggans = Pelanggan::all();
        $kriterias = Kriteria::with('subKriteria')->get();
        return view('penilaian.edit', compact('penilaian', 'pelanggans', 'kriterias'));
    }    public function update(Request $request, Penilaian $penilaian)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'tahun' => 'required|digits:4|integer|min:1900|max:'.(date('Y')+1),
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
