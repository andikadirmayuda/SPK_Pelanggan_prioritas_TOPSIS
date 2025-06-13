<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Pelanggan;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class HasilTopsisController extends Controller
{
    public function index(Request $request)
    {
        $kriterias = Kriteria::all();
        
        // Get available years from penilaian
        $tahunTersedia = Penilaian::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
            
        // If no penilaian data exists yet, return early with message
        if ($tahunTersedia->isEmpty()) {
            return back()->with('error', 'Belum ada data penilaian yang tersedia.');
        }

        // Get selected year from request or use the latest year
        $tahunTerpilih = $request->input('tahun', $tahunTersedia->first());
        
        $pelanggans = Pelanggan::whereHas('penilaian', function($query) use ($tahunTerpilih) {
            $query->where('tahun', $tahunTerpilih);
        })->get();
        
        // If no pelanggan data for selected year, return with message
        if ($pelanggans->isEmpty()) {
            return back()->with('error', 'Tidak ada data penilaian untuk tahun ' . $tahunTerpilih);
        }
        
        // 1. Membuat matriks keputusan
        $matriks = [];
        $namaPelanggan = [];
        
        foreach ($pelanggans as $pelanggan) {
            $namaPelanggan[$pelanggan->id] = $pelanggan->nama;
            foreach ($kriterias as $kriteria) {
                $penilaian = Penilaian::where('pelanggan_id', $pelanggan->id)
                    ->where('kriteria_id', $kriteria->id)
                    ->where('tahun', $tahunTerpilih)
                    ->first();
                
                $matriks[$pelanggan->id][$kriteria->id] = $penilaian ? $penilaian->subKriteria->nilai : 0;
            }
        }

        // 2. Normalisasi matriks keputusan
        $normal = [];
        foreach ($kriterias as $kriteria) {
            $sumSquare = 0;
            foreach ($pelanggans as $pelanggan) {
                if (!isset($matriks[$pelanggan->id][$kriteria->id])) {
                    continue;
                }
                $sumSquare += pow($matriks[$pelanggan->id][$kriteria->id], 2);
            }
            $sqrt = sqrt($sumSquare);
            
            foreach ($pelanggans as $pelanggan) {
                if (!isset($matriks[$pelanggan->id][$kriteria->id])) {
                    $normal[$pelanggan->id][$kriteria->id] = 0;
                    continue;
                }
                
                if ($sqrt != 0) {
                    $normal[$pelanggan->id][$kriteria->id] = $matriks[$pelanggan->id][$kriteria->id] / $sqrt;
                } else {
                    $normal[$pelanggan->id][$kriteria->id] = 0;
                }
            }
        }

        // 3. Menghitung matriks terbobot
        $terbobot = [];
        foreach ($pelanggans as $pelanggan) {
            foreach ($kriterias as $kriteria) {
                if (!isset($normal[$pelanggan->id][$kriteria->id])) {
                    $terbobot[$pelanggan->id][$kriteria->id] = 0;
                    continue;
                }
                $terbobot[$pelanggan->id][$kriteria->id] = $normal[$pelanggan->id][$kriteria->id] * $kriteria->bobot;
            }
        }

        // 4. Menentukan solusi ideal positif (A+) dan negatif (A-)
        $Aplus = [];
        $Amin = [];
        foreach ($kriterias as $kriteria) {
            $values = array_column($terbobot, $kriteria->id);
            if (empty($values)) {
                $Aplus[$kriteria->id] = 0;
                $Amin[$kriteria->id] = 0;
                continue;
            }
            
            if ($kriteria->tipe === 'benefit') {
                $Aplus[$kriteria->id] = max($values);
                $Amin[$kriteria->id] = min($values);
            } else { // cost
                $Aplus[$kriteria->id] = min($values);
                $Amin[$kriteria->id] = max($values);
            }
        }

        // 5. Menghitung jarak solusi dan nilai preferensi
        $preferensi = [];
        foreach ($pelanggans as $pelanggan) {
            $dplus = 0;
            $dmin = 0;
            foreach ($kriterias as $kriteria) {
                if (!isset($terbobot[$pelanggan->id][$kriteria->id])) {
                    continue;
                }
                $dplus += pow($terbobot[$pelanggan->id][$kriteria->id] - $Aplus[$kriteria->id], 2);
                $dmin += pow($terbobot[$pelanggan->id][$kriteria->id] - $Amin[$kriteria->id], 2);
            }
            $dplus = sqrt($dplus);
            $dmin = sqrt($dmin);
            
            $nilai = ($dplus + $dmin) != 0 ? $dmin / ($dmin + $dplus) : 0;
            $preferensi[] = [
                'nama' => $pelanggan->nama,
                'dplus' => $dplus,
                'dmin' => $dmin,
                'nilai' => $nilai
            ];
        }

        // 6. Mengurutkan hasil akhir
        usort($preferensi, function($a, $b) {
            return $b['nilai'] <=> $a['nilai'];
        });

        $hasil = array_map(function($p) {
            return [
                'pelanggan' => $p['nama'],
                'nilai' => $p['nilai']
            ];
        }, $preferensi);

        return view('hasil_topsis.index', compact(
            'kriterias', 
            'matriks', 
            'namaPelanggan', 
            'normal', 
            'terbobot', 
            'Aplus', 
            'Amin', 
            'preferensi',
            'hasil',
            'tahunTersedia',
            'tahunTerpilih'
        ));
    }
}
