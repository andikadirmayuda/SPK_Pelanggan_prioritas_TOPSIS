<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Laporan Penilaian Pelanggan') }}
            </h2>
            <div class="flex items-center no-print">
                <form action="{{ route('laporan.index') }}" method="GET" class="flex items-center space-x-4">
                    <select name="tahun" id="tahun" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($tahunTersedia as $tahun)
                            <option value="{{ $tahun }}" {{ $tahun == $tahunTerpilih ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </form>                <a href="{{ route('laporan.index', ['tahun' => $tahunTerpilih, 'print' => true]) }}" target="_blank" class="ml-4 px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                    {{ __('Preview') }}
                </a>
                <a href="{{ route('laporan.download', ['tahun' => $tahunTerpilih]) }}" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    {{ __('Download PDF') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg print:shadow-none">
                <div class="p-6 text-gray-900">
                    <div class="print:block hidden text-center mb-8">
                        <h1 class="text-2xl font-bold mb-2">LAPORAN HASIL PENILAIAN PELANGGAN PRIORITAS</h1>
                        <h2 class="text-xl">Metode TOPSIS</h2>
                        <h3 class="text-lg mt-2">Periode Tahun: {{ $tahunTerpilih }}</h3>
                        <div class="text-sm mt-4">Tanggal Cetak: {{ now()->format('d/m/Y') }}</div>
                    </div>
                    
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 print:text-black">
                            <thead class="text-xs uppercase bg-gray-50 print:bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3 border">Peringkat</th>
                                    <th scope="col" class="px-6 py-3 border">Nama Pelanggan</th>
                                    <th scope="col" class="px-6 py-3 border">Nilai Preferensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hasilAkhir as $index => $hasil)
                                    <tr class="bg-white">
                                        <td class="px-6 py-4 border text-center">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 border">{{ $hasil['pelanggan']->nama }}</td>
                                        <td class="px-6 py-4 border text-center">{{ number_format($hasil['nilai_preferensi'], 4) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="print:block hidden mt-20">
                        <div class="float-right text-center">
                            <p>........................., {{ now()->format('d/m/Y') }}</p>
                            <p class="mb-20">Manager</p>
                            <p>( .................................. )</p>
                            <p class="text-sm">Nama Terang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    @push('styles')
        <style>
            @media print {
                body {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                    font-size: 14pt;
                    line-height: 1.5;
                }
                .no-print {
                    display: none !important;
                }
                @page {
                    size: A4;
                    margin: 1.5cm;
                }
                .print\:block {
                    display: block !important;
                }
                .print\:shadow-none {
                    box-shadow: none !important;
                }
                .print\:bg-gray-100 {
                    background-color: #f3f4f6 !important;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                    margin-bottom: 20px;
                }
                th {
                    background-color: #f3f4f6 !important;
                    font-size: 14pt;
                    font-weight: bold;
                }
                td {
                    font-size: 12pt;
                }
                th, td {
                    border: 1.5px solid #000000;
                    padding: 12px;
                }
                tr {
                    page-break-inside: avoid;
                }
                h1 {
                    font-size: 18pt !important;
                    margin-bottom: 16px !important;
                }
                h2 {
                    font-size: 16pt !important;
                    margin-bottom: 14px !important;
                }
                h3 {
                    font-size: 14pt !important;
                    margin-bottom: 12px !important;
                }
                .text-sm {
                    font-size: 12pt !important;
                }
            }
            .print\:block {
                display: none;
            }
        </style>
    @endpush
</x-app-layout>
