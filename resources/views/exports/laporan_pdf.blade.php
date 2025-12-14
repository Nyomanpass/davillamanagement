<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan Villa</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
        h2, h3 { margin: 0; padding: 0; }
    </style>
</head>
<body>
    <h2>Laporan Keuangan Villa</h2>
    <h3>Villa: {{ $villaName }}</h3>
    <h3>Periode: {{ $periode }}</h3>

    <table>
        <tr>
            <th>Keterangan</th>
            <th class="text-right">Nominal (Rp)</th>
        </tr>
        <tr>
            <td>Total Pendapatan</td>
            <td class="text-right">{{ number_format($totalPendapatan,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Total Pengeluaran</td>
            <td class="text-right">{{ number_format($totalPengeluaran,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Pendapatan Bersih</td>
            <td class="text-right">{{ number_format($pendapatanBersih,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Service ({{ $servicePercentage }}%)</td>
            <td class="text-right">{{ number_format($serviceNominal,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Pendapatan Kotor</td>
            <td class="text-right">{{ number_format($pendapatanKotor,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Fee Manajemen ({{ $feePercentage }}%)</td>
            <td class="text-right">{{ number_format($feeNominal,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Pendapatan Owner</td>
            <td class="text-right">{{ number_format($pendapatanOwner,0,',','.') }}</td>
        </tr>
    </table>
</body>
</html>
