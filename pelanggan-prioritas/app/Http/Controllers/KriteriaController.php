<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{

    public function index()
    {
        $kriteria = Kriteria::all();
        return view('kriteria.index', compact('kriteria'));
    }    public function create()
    {
        $totalBobot = Kriteria::sum('bobot');
        $sisaBobot = 1 - $totalBobot;
        return view('kriteria.create', compact('sisaBobot'));
    }

    public function store(Request $request)
    {
        $totalBobotExisting = Kriteria::sum('bobot');
        $newBobot = (float) str_replace(',', '.', $request->bobot);
        
        if ($totalBobotExisting + $newBobot > 1) {
            return back()
                ->withInput()
                ->withErrors(['bobot' => 'Total bobot tidak boleh melebihi 100% (1.00). Sisa bobot yang tersedia: ' . number_format(1 - $totalBobotExisting, 2)]);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'bobot' => 'required|numeric|between:0,1',
            'tipe' => 'required|in:benefit,cost',        ]);

        // Format bobot to always have 2 decimal places
        $validated['bobot'] = number_format($newBobot, 2, '.', '');

        Kriteria::create($validated);

        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria berhasil ditambahkan');
    }    public function edit(Kriteria $kriteria)
    {
        $totalBobot = Kriteria::where('id', '!=', $kriteria->id)->sum('bobot');
        $sisaBobot = 1 - $totalBobot;
        return view('kriteria.edit', compact('kriteria', 'sisaBobot'));
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        $totalBobotOthers = Kriteria::where('id', '!=', $kriteria->id)->sum('bobot');
        $newBobot = (float) str_replace(',', '.', $request->bobot);
        
        if ($totalBobotOthers + $newBobot > 1) {
            return back()
                ->withInput()
                ->withErrors(['bobot' => 'Total bobot tidak boleh melebihi 100% (1.00). Sisa bobot yang tersedia: ' . number_format(1 - $totalBobotOthers, 2)]);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'bobot' => 'required|numeric|between:0,1',
            'tipe' => 'required|in:benefit,cost',
        ]);        // Format bobot to always have 2 decimal places
        $validated['bobot'] = number_format($newBobot, 2, '.', '');

        $kriteria->update($validated);

        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria berhasil diperbarui');
    }

    public function destroy(Kriteria $kriteria)
    {
        $kriteria->delete();

        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria berhasil dihapus');
    }
}
