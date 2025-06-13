<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\Kriteria;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{    
    private function calculateTopsis($tahunTerpilih)
    {
        // Get all pelanggan with their penilaian for the selected year
        $pelangganDenganNilai = Pelanggan::whereHas('penilaian', function($query) use ($tahunTerpilih) {
            $query->where('tahun', $tahunTerpilih);
        })->with(['penilaian' => function($query) use ($tahunTerpilih) {
            $query->where('tahun', $tahunTerpilih);
        }])->get();

        if ($pelangganDenganNilai->isEmpty()) {
            throw new \Exception('Tidak ada data penilaian untuk tahun ' . $tahunTerpilih);
        }

        // Get all kriteria with their weights
        $kriterias = Kriteria::all();
        
        if ($kriterias->isEmpty()) {
            throw new \Exception('Belum ada kriteria yang ditambahkan.');
        }
        
        // Calculate TOPSIS
        $matriksKeputusan = [];
        $pembagi = [];
        
        // Step 1: Create decision matrix and calculate divisors
        foreach ($pelangganDenganNilai as $pelanggan) {
            $hasValidData = false;
            foreach ($kriterias as $kriteria) {
                $penilaian = $pelanggan->penilaian
                    ->where('kriteria_id', $kriteria->id)
                    ->first();
                
                if (!$penilaian || !$penilaian->subKriteria) {
                    continue;
                }
                
                $nilai = $penilaian->subKriteria->nilai;
                if ($nilai === 0) {
                    continue; // Skip zero values to prevent division by zero
                }
                
                $matriksKeputusan[$pelanggan->id][$kriteria->id] = $nilai;
                $hasValidData = true;
                
                if (!isset($pembagi[$kriteria->id])) {
                    $pembagi[$kriteria->id] = 0;
                }
                $pembagi[$kriteria->id] += pow($nilai, 2);
            }
            
            if (!$hasValidData) {
                throw new \Exception('Data penilaian tidak lengkap untuk beberapa pelanggan');
            }
        }
        
        // Validate pembagi
        foreach ($pembagi as $kriteriaId => $nilai) {
            if ($nilai <= 0) {
                throw new \Exception('Data tidak valid untuk perhitungan. Pastikan semua kriteria memiliki nilai yang valid.');
            }
            $pembagi[$kriteriaId] = sqrt($nilai);
        }
        
        // Step 2: Create normalized and weighted matrix
        $matriksNormalisasiTerbobot = [];
        $solusiIdeal = ['positif' => [], 'negatif' => []];
        
        foreach ($matriksKeputusan as $pelangganId => $nilaiKriteria) {
            foreach ($nilaiKriteria as $kriteriaId => $nilai) {
                // Skip if pembagi is 0 to avoid division by zero
                if ($pembagi[$kriteriaId] == 0) {
                    continue;
                }
                
                // Normalize
                $nilaiNormal = $nilai / $pembagi[$kriteriaId];
                
                // Apply weight
                $kriteria = $kriterias->find($kriteriaId);
                $nilaiNormalTerbobot = $nilaiNormal * $kriteria->bobot;
                
                $matriksNormalisasiTerbobot[$pelangganId][$kriteriaId] = $nilaiNormalTerbobot;
                
                // Determine ideal solutions
                if (!isset($solusiIdeal['positif'][$kriteriaId])) {
                    $solusiIdeal['positif'][$kriteriaId] = $nilaiNormalTerbobot;
                    $solusiIdeal['negatif'][$kriteriaId] = $nilaiNormalTerbobot;
                } else {
                    if ($kriteria->tipe === 'benefit') {
                        $solusiIdeal['positif'][$kriteriaId] = max($solusiIdeal['positif'][$kriteriaId], $nilaiNormalTerbobot);
                        $solusiIdeal['negatif'][$kriteriaId] = min($solusiIdeal['negatif'][$kriteriaId], $nilaiNormalTerbobot);
                    } else {
                        $solusiIdeal['positif'][$kriteriaId] = min($solusiIdeal['positif'][$kriteriaId], $nilaiNormalTerbobot);
                        $solusiIdeal['negatif'][$kriteriaId] = max($solusiIdeal['negatif'][$kriteriaId], $nilaiNormalTerbobot);
                    }
                }
            }
        }
        
        // Step 3: Calculate distances and preference values
        $hasilAkhir = [];
        foreach ($matriksNormalisasiTerbobot as $pelangganId => $nilaiKriteria) {
            $dPlus = 0;
            $dMin = 0;
            
            foreach ($nilaiKriteria as $kriteriaId => $nilai) {
                if (!isset($solusiIdeal['positif'][$kriteriaId]) || !isset($solusiIdeal['negatif'][$kriteriaId])) {
                    continue;
                }
                $dPlus += pow($solusiIdeal['positif'][$kriteriaId] - $nilai, 2);
                $dMin += pow($solusiIdeal['negatif'][$kriteriaId] - $nilai, 2);
            }
            
            // Prevent negative numbers under sqrt
            $dPlus = $dPlus < 0 ? 0 : sqrt($dPlus);
            $dMin = $dMin < 0 ? 0 : sqrt($dMin);
            
            // Calculate preference value with division by zero protection
            $preferensi = 0;
            $denominator = $dMin + $dPlus;
            
            if ($denominator > 0) {
                $preferensi = $dMin / $denominator;
            }
            
            $hasilAkhir[] = [
                'pelanggan' => $pelangganDenganNilai->find($pelangganId),
                'nilai_preferensi' => $preferensi
            ];
        }
        
        if (empty($hasilAkhir)) {
            throw new \Exception('Tidak dapat menghitung hasil akhir karena data tidak valid.');
        }
        
        // Sort by preference value in descending order
        usort($hasilAkhir, function($a, $b) {
            return $b['nilai_preferensi'] <=> $a['nilai_preferensi'];
        });
        
        return $hasilAkhir;
    }

    public function index(Request $request)
    {
        try {
            // Get available years from penilaian
            $tahunTersedia = Penilaian::select('tahun')
                ->distinct()
                ->orderBy('tahun', 'desc')
                ->pluck('tahun');

            if ($tahunTersedia->isEmpty()) {
                return back()->with('error', 'Belum ada data penilaian yang tersedia.');
            }

            $tahunTerpilih = $request->input('tahun', $tahunTersedia->first());

            if ($request->has('print')) {
                return $this->printPDF($tahunTerpilih);
            }

            $hasilAkhir = $this->calculateTopsis($tahunTerpilih);
            return view('laporan.index', compact('hasilAkhir', 'tahunTersedia', 'tahunTerpilih'));

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan dalam perhitungan: ' . $e->getMessage());
        }
    }

    public function printPDF($tahunTerpilih)
    {
        try {
            $hasilAkhir = $this->calculateTopsis($tahunTerpilih);
            $pdf = PDF::loadView('laporan.print', compact('hasilAkhir', 'tahunTerpilih'));
            return $pdf->stream('laporan-pelanggan-prioritas-'.$tahunTerpilih.'.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan dalam membuat PDF: ' . $e->getMessage());
        }
    }

    public function downloadPDF($tahunTerpilih)
    {
        try {
            $hasilAkhir = $this->calculateTopsis($tahunTerpilih);
            $pdf = PDF::loadView('laporan.print', compact('hasilAkhir', 'tahunTerpilih'));
            return $pdf->download('laporan-pelanggan-prioritas-'.$tahunTerpilih.'.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan dalam mengunduh PDF: ' . $e->getMessage());
        }
    }
}
