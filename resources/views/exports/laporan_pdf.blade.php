<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan Villa - {{ $villaName }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; color: #333; }
        
        .info-table { width: 100%; margin-bottom: 20px; border: none; }
        .info-table td { border: none; padding: 2px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #343a40; color: #ffffff; font-weight: bold; border: 1px solid #343a40; padding: 10px; text-align: left; text-transform: uppercase; font-size: 9px; }
        td { border: 1px solid #dee2e6; padding: 8px; vertical-align: middle; }
        
        .section-title { background-color: #f1f3f5; font-weight: bold; font-size: 10px; color: #495057; }
        .text-right { text-align: right; }
        .text-red { color: #c92a2a; font-weight: bold; }
        .text-green { color: #2b8a3e; font-weight: bold; }
        .font-bold { font-weight: bold; }
        
        .highlight-row { background-color: #fff9db; }
        .owner-box { background-color: #ebfbee; border: 2px solid #2b8a3e !important; }
        .owner-text { font-size: 14px; color: #2b8a3e; font-weight: black; }
        
        .footer { margin-top: 50px; }
        .signature-space { height: 60px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN REKAPITULASI KEUANGAN</h1>
        <p style="margin: 5px 0; font-size: 14px; font-weight: bold;">{{ $villaName }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Periode</td>
            <td width="35%">: <strong>{{ $periode }}</strong></td>
            <td width="15%" class="text-right">Tanggal Cetak</td>
            <td width="35%" class="text-right">: {{ date('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="70%">RINCIAN KETERANGAN</th>
                <th width="30%" class="text-right">NOMINAL (IDR)</th>
            </tr>
        </thead>
        <tbody>
            {{-- BAGIAN A --}}
            <tr class="section-title">
                <td colspan="2">A. KATEGORI KHUSUS (DIHITUNG SERVICE)</td>
            </tr>
            <tr>
                <td>Total Pendapatan Khusus</td>
                <td class="text-right text-green">+ {{ number_format($pKhusus, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran Khusus</td>
                <td class="text-right text-red">- {{ number_format($exKhusus, 0, ',', '.') }}</td>
            </tr>
            <tr class="font-bold" style="background-color: #f8f9fa;">
                <td>Margin Kategori Khusus</td>
                <td class="text-right text-green">{{ number_format($marginKhusus, 0, ',', '.') }}</td>
            </tr>
            <tr class="highlight-row">
                <td>Potongan Service Karyawan ({{ (float)$servicePercentage }}%)</td>
                <td class="text-right text-red">- {{ number_format($serviceNominal, 0, ',', '.') }}</td>
            </tr>

            {{-- BAGIAN B --}}
            <tr class="section-title">
                <td colspan="2">B. KATEGORI UMUM (TANPA SERVICE)</td>
            </tr>
            <tr>
                <td>Total Pendapatan Umum</td>
                <td class="text-right text-green">+ {{ number_format($pUmum, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran Umum</td>
                <td class="text-right text-red">- {{ number_format($exUmum, 0, ',', '.') }}</td>
            </tr>

            {{-- PERHITUNGAN AKHIR --}}
            <tr class="section-title">
                <td colspan="2">C. TOTAL PENDAPATAN KOTOR (GROSS INCOME)</td>
            </tr>
            <tr class="font-bold">
                <td>Pendapatan Sebelum Fee Manajemen</td>
                <td class="text-right">{{ number_format($pendapatanKotor, 0, ',', '.') }}</td>
            </tr>
            <tr class="highlight-row">
                <td>Fee Manajemen ({{ (float)$feePercentage }}%)</td>
                <td class="text-right text-red">- {{ number_format($feeNominal, 0, ',', '.') }}</td>
            </tr>

            {{-- OWNER BOX --}}
            <tr class="owner-box">
                <td class="font-bold" style="font-size: 12px;">HASIL BERSIH OWNER (NET PROFIT)</td>
                <td class="text-right owner-text">Rp {{ number_format($pendapatanOwner, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; width: 60%; font-size: 9px; color: #666; vertical-align: top;">
                    <strong>Keterangan:</strong><br>
                    1. Margin Khusus = Pendapatan Khusus - Pengeluaran Khusus.<br>
                    2. Service Karyawan dihitung dari Margin Khusus jika hasil positif.<br>
                    3. Laporan ini sah dan dihasilkan secara sistematis.
                </td>
              
            </tr>
        </table>
    </div>

</body>
</html>