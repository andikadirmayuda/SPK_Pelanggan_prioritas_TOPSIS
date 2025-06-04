<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\SubKriteria;
use Illuminate\Http\Request;

class SubKriteriaController extends Controller
{
    protected $nilaiOptions = [
        1 => 'Kurang',
        2 => 'Cukup',
        3 => 'Baik',
        4 => 'Sangat Baik'
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all kriteria with their sub-kriteria
        $kriterias = Kriteria::with(['subKriteria' => function($query) {
            $query->orderBy('nilai');
        }])->get();
        return view('sub-kriteria.index', compact('kriterias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kriterias = Kriteria::all();
        $nilaiOptions = $this->nilaiOptions;
        return view('sub-kriteria.create', compact('kriterias', 'nilaiOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */    public function store(Request $request)
    {
        $validated = $request->validate([
            'kriteria_id' => 'required|exists:kriteria,id',
            'nama' => 'required|string|max:255',
        ]);

        // Get the selected kriteria
        $kriteria = Kriteria::findOrFail($validated['kriteria_id']);
        
        // Create sub-kriteria with predefined nilai options
        $subKriteriaNama = $validated['nama'];
        foreach ($this->nilaiOptions as $nilai => $keterangan) {
            SubKriteria::create([
                'kriteria_id' => $kriteria->id,
                'nama' => $subKriteriaNama . ' - ' . $keterangan,
                'nilai' => $nilai,
                'keterangan' => $keterangan
            ]);
        }

        return redirect()->route('sub-kriteria.index')
            ->with('success', 'Sub Kriteria berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */    public function edit(SubKriteria $sub_kriteria)
    {
        $kriterias = Kriteria::all();
        $nilaiOptions = $this->nilaiOptions;
        return view('sub-kriteria.edit', compact('sub_kriteria', 'kriterias', 'nilaiOptions'));
    }

    /**
     * Update the specified resource in storage.
     */    public function update(Request $request, SubKriteria $subKriteria)
    {
        $validated = $request->validate([
            'kriteria_id' => 'required|exists:kriteria,id',
            'nama' => 'required|string|max:255',
            'nilai' => 'required|numeric|between:0,100',
            'keterangan' => 'required|string|max:255',
        ]);

        $subKriteria->update($validated);

        return redirect()->route('sub-kriteria.index')
            ->with('success', 'Sub Kriteria berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */    public function destroy(SubKriteria $sub_kriteria)
    {
        $sub_kriteria->delete();

        return redirect()->route('sub-kriteria.index')
            ->with('success', 'Sub Kriteria berhasil dihapus');
    }
}