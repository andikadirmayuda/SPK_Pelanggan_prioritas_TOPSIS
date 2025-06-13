<x-app-layout>    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Hasil Perhitungan TOPSIS') }}
            </h2>
            @if($tahunTersedia->isNotEmpty())
            <div class="flex items-center">
                <form action="{{ route('hasil-topsis.index') }}" method="GET" class="flex items-center space-x-4">
                    <select name="tahun" id="tahun" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($tahunTersedia as $tahun)
                            <option value="{{ $tahun }}" {{ $tahun == $tahunTerpilih ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            @endif
        </div>
    </x-slot>

    @if(session('error'))
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    </div>
    @endif


<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <h1>halo</h1>
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-full">

                <!-- 1. Matriks Keputusan -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-3 bg-gray-50 p-2 rounded">1. Matriks Keputusan (X)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 border">Pelanggan</th>
                                    @foreach ($kriterias as $k)
                                        <th class="px-4 py-2 border">{{ $k->nama }}<br><span class="text-xs">({{ $k->tipe }})</span></th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($matriks as $pid => $row)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $namaPelanggan[$pid] }}</td>
                                    @foreach ($kriterias as $k)
                                        <td class="px-4 py-2 border text-center">{{ $row[$k->id] }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Normalisasi Matriks -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-3 bg-gray-50 p-2 rounded">2. Normalisasi Matriks</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 border">Pelanggan</th>
                                    @foreach ($kriterias as $k)
                                        <th class="px-4 py-2 border">{{ $k->nama }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($normal as $pid => $row)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $namaPelanggan[$pid] }}</td>
                                    @foreach ($kriterias as $k)
                                        <td class="px-4 py-2 border text-center">{{ number_format($row[$k->id], 4) }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Normalisasi Terbobot -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-3 bg-gray-50 p-2 rounded">3. Matriks Ternormalisasi Terbobot</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 border">Pelanggan</th>
                                    @foreach ($kriterias as $k)
                                        <th class="px-4 py-2 border">{{ $k->nama }}<br><span class="text-xs">(bobot: {{ number_format($k->bobot, 2) }})</span></th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($terbobot as $pid => $row)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $namaPelanggan[$pid] }}</td>
                                    @foreach ($kriterias as $k)
                                        <td class="px-4 py-2 border text-center">{{ number_format($row[$k->id], 4) }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Solusi Ideal -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-3 bg-gray-50 p-2 rounded">4. Solusi Ideal Positif (A⁺) dan Negatif (A⁻)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 border">Jenis</th>
                                    @foreach ($kriterias as $k)
                                        <th class="px-4 py-2 border">{{ $k->nama }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-4 py-2 border">A⁺</td>
                                    @foreach ($kriterias as $k)
                                        <td class="px-4 py-2 border text-center">{{ number_format($Aplus[$k->id], 4) }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 border">A⁻</td>
                                    @foreach ($kriterias as $k)
                                        <td class="px-4 py-2 border text-center">{{ number_format($Amin[$k->id], 4) }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 5. Jarak Solusi -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-3 bg-gray-50 p-2 rounded">5. Jarak ke Solusi Ideal</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 border">Pelanggan</th>
                                    <th class="px-4 py-2 border">D⁺</th>
                                    <th class="px-4 py-2 border">D⁻</th>
                                    <th class="px-4 py-2 border">Nilai Preferensi (V)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($preferensi as $p)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $p['nama'] }}</td>
                                    <td class="px-4 py-2 border text-center">{{ number_format($p['dplus'], 4) }}</td>
                                    <td class="px-4 py-2 border text-center">{{ number_format($p['dmin'], 4) }}</td>
                                    <td class="px-4 py-2 border text-center font-semibold">{{ number_format($p['nilai'], 4) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 6. Hasil Akhir -->
                <div>
                    <h3 class="text-lg font-semibold mb-3 bg-gray-50 p-2 rounded">6. Hasil Akhir (Peringkat)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 border">Peringkat</th>
                                    <th class="px-4 py-2 border">Nama Pelanggan</th>
                                    <th class="px-4 py-2 border">Nilai Preferensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($hasil as $index => $row)
                                <tr class="{{ $index === 0 ? 'bg-green-50' : '' }}">
                                    <td class="px-4 py-2 border text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 border">{{ $row['pelanggan'] }}</td>
                                    <td class="px-4 py-2 border text-center font-semibold">{{ number_format($row['nilai'], 4) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>

