<!DOCTYPE html>
<html>
<head>
    <title>Laporan Fee Manajemen</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #d97706; }
        .header p { margin: 5px 0; color: #666; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f8f9fa; color: #444; font-weight: bold; padding: 12px; border: 1px solid #dee2e6; text-align: left; }
        td { padding: 10px; border: 1px solid #dee2e6; font-size: 14px; }
        .text-right { text-align: right; }
        
        .summary-box { background-color: #fffbeb; border: 1px solid #fef3c7; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .total-row { background-color: #f1f5f9; font-weight: bold; }
        .footer { margin-top: 50px; text-align: right; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Fee Manajemen</h1>
        <p>Periode: {{ $periode }}</p>
    </div>

    <div class="summary-box">
        <table style="border: none; margin: 0;">
            <tr style="border: none;">
                <td style="border: none;">Total Pendapatan Kotor: <strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong></td>
                <td style="border: none;" class="text-right">Total Fee Manajemen: <strong style="color: #b45309;">Rp {{ number_format($totalFee, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <table>
    <thead>
        <tr>
            <th>Nama Villa</th>
            <th class="text-right">Pendapatan Kotor</th>
            <th class="text-center">Fee (%)</th> <th class="text-right">Nominal Fee</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reports as $report)
        <tr>
            <td>{{ $report['name'] }}</td>
            <td class="text-right">Rp {{ number_format($report['laba_kotor'], 0, ',', '.') }}</td>
            <td class="text-center">{{ $report['fee_percent'] }}%</td> <td class="text-right">Rp {{ number_format($report['fee_amount'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td>TOTAL KESELURUHAN</td>
            <td class="text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            <td class="text-center">-</td> <td class="text-right">Rp {{ number_format($totalFee, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>

    <div class="footer">
        Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}
    </div>
</body>
</html>