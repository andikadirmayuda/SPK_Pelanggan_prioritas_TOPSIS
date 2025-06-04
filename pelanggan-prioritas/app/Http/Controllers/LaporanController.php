<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\Kriteria;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{    public function index(Request $request)
    {
        // Get available years from penilaian
        $tahunTersedia = Penilaian::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Get selected year from request or use the latest year
        $tahunTerpilih = $request->input('tahun', $tahunTersedia->first());

        if ($request->has('print')) {
            return $this->printPDF($tahunTerpilih);
        }

        // Get all pelanggan with their penilaian for the selected year
        $pelangganDenganNilai = Pelanggan::whereHas('penilaian', function($query) use ($tahunTerpilih) {
            $query->where('tahun', $tahunTerpilih);
        })->with(['penilaian' => function($query) use ($tahunTerpilih) {
            $query->where('tahun', $tahunTerpilih);
        }])->get();

        // Get all kriteria with their weights
        $kriterias = Kriteria::all();
        
        // Calculate TOPSIS
        $matriksKeputusan = [];
        $pembagi = [];
        
        // Step 1: Create decision matrix and calculate divisors
        foreach ($pelangganDenganNilai as $pelanggan) {
            foreach ($kriterias as $kriteria) {
                $nilai = $pelanggan->penilaian
                    ->where('kriteria_id', $kriteria->id)
                    ->first()
                    ->subKriteria
                    ->nilai ?? 0;
                
                $matriksKeputusan[$pelanggan->id][$kriteria->id] = $nilai;
                
                if (!isset($pembagi[$kriteria->id])) {
                    $pembagi[$kriteria->id] = 0;
                }
                $pembagi[$kriteria->id] += pow($nilai, 2);
            }
        }
        
        // Calculate final divisors (square root)
        foreach ($pembagi as &$nilai) {
            $nilai = sqrt($nilai);
        }
        
        // Step 2: Create normalized and weighted matrix
        $matriksNormalisasiTerbobot = [];
        $solusiIdeal = ['positif' => [], 'negatif' => []];
        
        foreach ($matriksKeputusan as $pelangganId => $nilaiKriteria) {
            foreach ($nilaiKriteria as $kriteriaId => $nilai) {
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
                    } else { // cost
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
                $dPlus += pow($solusiIdeal['positif'][$kriteriaId] - $nilai, 2);
                $dMin += pow($solusiIdeal['negatif'][$kriteriaId] - $nilai, 2);
            }
            
            $dPlus = sqrt($dPlus);
            $dMin = sqrt($dMin);
            
            // Calculate preference value
            $preferensi = $dMin / ($dMin + $dPlus);
            
            $hasilAkhir[] = [
                'pelanggan' => $pelangganDenganNilai->find($pelangganId),
                'nilai_preferensi' => $preferensi
            ];
        }
        
        // Sort by preference value in descending order
        usort($hasilAkhir, function($a, $b) {
            return $b['nilai_preferensi'] <=> $a['nilai_preferensi'];
        });

        return view('laporan.index', compact('hasilAkhir', 'tahunTersedia', 'tahunTerpilih'));
    }

    private function printPDF($tahunTerpilih)
    {
        // Get all pelanggan with their penilaian for the selected year
        $pelangganDenganNilai = Pelanggan::whereHas('penilaian', function($query) use ($tahunTerpilih) {
            $query->where('tahun', $tahunTerpilih);
        })->with(['penilaian' => function($query) use ($tahunTerpilih) {
            $query->where('tahun', $tahunTerpilih);
        }])->get();

        // Get all kriteria with their weights
        $kriterias = Kriteria::all();
        
        // Calculate TOPSIS
        $matriksKeputusan = [];
        $pembagi = [];
        
        // Step 1: Create decision matrix and calculate divisors
        foreach ($pelangganDenganNilai as $pelanggan) {
            foreach ($kriterias as $kriteria) {
                $nilai = $pelanggan->penilaian
                    ->where('kriteria_id', $kriteria->id)
                    ->first()
                    ->subKriteria
                    ->nilai ?? 0;
                
                $matriksKeputusan[$pelanggan->id][$kriteria->id] = $nilai;
                
                if (!isset($pembagi[$kriteria->id])) {
                    $pembagi[$kriteria->id] = 0;
                }
                $pembagi[$kriteria->id] += pow($nilai, 2);
            }
        }
        
        // Calculate final divisors (square root)
        foreach ($pembagi as &$nilai) {
            $nilai = sqrt($nilai);
        }
        
        // Step 2: Create normalized and weighted matrix
        $matriksNormalisasiTerbobot = [];
        $solusiIdeal = ['positif' => [], 'negatif' => []];
        
        foreach ($matriksKeputusan as $pelangganId => $nilaiKriteria) {
            foreach ($nilaiKriteria as $kriteriaId => $nilai) {
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
                    } else { // cost
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
                $dPlus += pow($solusiIdeal['positif'][$kriteriaId] - $nilai, 2);
                $dMin += pow($solusiIdeal['negatif'][$kriteriaId] - $nilai, 2);
            }
            
            $dPlus = sqrt($dPlus);
            $dMin = sqrt($dMin);
            
            // Calculate preference value
            $preferensi = $dMin / ($dMin + $dPlus);
            
            $hasilAkhir[] = [
                'pelanggan' => $pelangganDenganNilai->find($pelangganId),
                'nilai_preferensi' => $preferensi
            ];
        }
        
        // Sort by preference value in descending order
        usort($hasilAkhir, function($a, $b) {
            return $b['nilai_preferensi'] <=> $a['nilai_preferensi'];
        });
        
        return response()->view('laporan.print', compact('hasilAkhir', 'tahunTerpilih'))
            ->header('Content-Type', 'text/html')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }

    public function downloadPDF($tahunTerpilih)
    {
        // Get all pelanggan with their penilaian for the selected year
        $pelangganDenganNilai = Pelanggan::whereHas('penilaian', function($query) use ($tahunTerpilih) {
            $query->where('tahun', $tahunTerpilih);
        })->with(['penilaian' => function($query) use ($tahunTerpilih) {
            $query->where('tahun', $tahunTerpilih);
        }])->get();

        // Get all kriteria with their weights
        $kriterias = Kriteria::all();
        
        // Calculate TOPSIS
        $matriksKeputusan = [];
        $pembagi = [];
        
        // Step 1: Create decision matrix and calculate divisors
        foreach ($pelangganDenganNilai as $pelanggan) {
            foreach ($kriterias as $kriteria) {
                $nilai = $pelanggan->penilaian
                    ->where('kriteria_id', $kriteria->id)
                    ->first()
                    ->subKriteria
                    ->nilai ?? 0;
                
                $matriksKeputusan[$pelanggan->id][$kriteria->id] = $nilai;
                
                if (!isset($pembagi[$kriteria->id])) {
                    $pembagi[$kriteria->id] = 0;
                }
                $pembagi[$kriteria->id] += pow($nilai, 2);
            }
        }
        
        // Calculate final divisors (square root)
        foreach ($pembagi as &$nilai) {
            $nilai = sqrt($nilai);
        }
        
        // Step 2: Create normalized and weighted matrix
        $matriksNormalisasiTerbobot = [];
        $solusiIdeal = ['positif' => [], 'negatif' => []];
        
        foreach ($matriksKeputusan as $pelangganId => $nilaiKriteria) {
            foreach ($nilaiKriteria as $kriteriaId => $nilai) {
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
                    } else { // cost
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
                $dPlus += pow($solusiIdeal['positif'][$kriteriaId] - $nilai, 2);
                $dMin += pow($solusiIdeal['negatif'][$kriteriaId] - $nilai, 2);
            }
            
            $dPlus = sqrt($dPlus);
            $dMin = sqrt($dMin);
            
            // Calculate preference value
            $preferensi = $dMin / ($dMin + $dPlus);
            
            $hasilAkhir[] = [
                'pelanggan' => $pelangganDenganNilai->find($pelangganId),
                'nilai_preferensi' => $preferensi
            ];
        }
        
        // Sort by preference value in descending order
        usort($hasilAkhir, function($a, $b) {
            return $b['nilai_preferensi'] <=> $a['nilai_preferensi'];
        });

        $pdf = PDF::loadView('laporan.print', compact('hasilAkhir', 'tahunTerpilih'));
        return $pdf->download('laporan-pelanggan-prioritas-'.$tahunTerpilih.'.pdf');
    }
}
