<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penilaian Pelanggan Prioritas - Tahun {{ $tahunTerpilih }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2.5cm;
            font-size: 12pt;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 14pt;
            margin-bottom: 5px;
        }
        .info {
            font-size: 12pt;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 10px;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 12pt;
        }
        td {
            font-size: 11pt;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .signature {
            margin-top: 80px;
        }
        .page-break {
            page-break-after: always;
        }
        @page {
            size: A4;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">LAPORAN HASIL PENILAIAN PELANGGAN PRIORITAS</div>
        <div class="subtitle">Metode TOPSIS</div>
        <div class="info">Periode Tahun: {{ $tahunTerpilih }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 10%">Peringkat</th>
                <th style="width: 60%">Nama Pelanggan</th>
                <th class="text-center" style="width: 30%">Nilai Preferensi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hasilAkhir as $index => $hasil)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $hasil['pelanggan']->nama }}</td>
                    <td class="text-center">{{ number_format($hasil['nilai_preferensi'], 4) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div>{{ config('app.name', 'Laravel') }}, {{ now()->translatedFormat('d F Y') }}</div>
        <div class="signature">
            <div>Manager</div>
            <br><br><br>
            <div>( ...................................... )</div>
            <div style="font-size: 10pt; margin-top: 5px;">Nama Terang</div>
        </div>
    </div>
</body>
</html>
