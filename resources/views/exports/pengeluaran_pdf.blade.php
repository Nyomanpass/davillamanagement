<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengeluaran</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; margin: 0; padding: 0; }
        .header { margin-bottom: 20px; }
        .header h1 { font-size: 16px; margin: 0; color: #800000; } /* Merah Maroon */
        .header h2 { font-size: 12px; margin: 5px 0 0 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
        th { background-color: #800000; color: white; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background-color: #FAE3E3; font-weight: bold; }
        .total-row td { border-top: 2px solid #000; }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN PENGELUARAN VILLA</h1>
        <h2>Villa: {{ $villaName }}</h2>
        {{-- Opsional: Tampilkan Filter Tanggal jika ada di $filterParams['start_date'] dan $filterParams['end_date'] --}}
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%">No.</th>
                <th class="text-center" style="width: 15%">Tanggal</th>
                <th class="text-center" style="width: 25%">Jenis Pengeluaran</th>
                <th class="text-center" style="width: 20%">Nominal (Rp)</th>
                <th class="text-center" style="width: 35%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalNominal = 0;
            @endphp
            @foreach ($dataPengeluaran as $index => $item)
                @php
                    $totalNominal += $item->nominal;
                    $jenis = $listJenisPengeluaran[$item->jenis_pengeluaran] ?? $item->jenis_pengeluaran;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    {{-- 🛑 BARIS PERBAIKAN: Ditambahkan backslash (\) di depan Carbon 🛑 --}}
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $jenis }}</td>
                    <td class="text-right">Rp{{ number_format($item->nominal, 0, ',', '.') }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
            
            {{-- Baris Total --}}
            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL PENGELUARAN</td>
                <td class="text-right">Rp{{ number_format($totalNominal, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

</body>
</html>