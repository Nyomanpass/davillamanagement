<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pendapatan {{ $villaName }}</title>
    <style>
        body { 
            font-family: 'Helvetica', sans-serif; /* Font yang lebih umum */ 
            font-size: 10pt; 
            margin: 0;
            padding: 0;
        }
        .header { 
            margin-bottom: 25px; 
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        .header h1 { 
            margin: 0; 
            font-size: 18pt; 
            color: #333;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 14pt;
            color: #555;
        }
        .header p { 
            margin: 0; 
            font-size: 10pt; 
            color: #666; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left; 
        }
        th { 
            background-color: #f0f0f0; 
            font-size: 9pt; 
            color: #333;
            text-transform: uppercase;
        }
        .footer { 
            position: fixed; 
            bottom: 0; 
            width: 100%; 
            text-align: right; 
            font-size: 8pt; 
            color: #888; 
            padding: 10px 0;
        }
        .total-row td { 
            background-color: #d1e7dd; /* Warna hijau muda */
            font-weight: bold; 
            border-top: 2px solid #333;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN PENDAPATAN</h1>
        {{-- MENGGANTI ID VILLA DENGAN NAMA VILLA --}}
        <h2>Villa: {{ $villaName }}</h2>
        
        {{-- TAMPILKAN PARAMETER FILTER --}}
        <p>Periode Filter: 
            @php
                use Carbon\Carbon;
                $start = $filterParams['start'] ?? null;
                $end = $filterParams['end'] ?? null;
                $bulan = $filterParams['bulan'] ?? null;
                $tahun = $filterParams['tahun'] ?? null;
            @endphp

            @if ($start && $end)
                Tanggal {{ Carbon::parse($start)->format('d F Y') }} s/d {{ Carbon::parse($end)->format('d F Y') }}
            @elseif ($bulan && $tahun)
                Bulan {{ date('F', mktime(0, 0, 0, $bulan, 10)) }} Tahun {{ $tahun }}
            @else
                Semua Data Pendapatan
            @endif
        </p>
    </div>

    {{-- TABEL DATA --}}
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 30%;">Jenis Pendapatan</th>
                <th style="width: 20%;">Metode Bayar</th>
                <th style="width: 30%; text-align: right;">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalNominal = 0; @endphp
            @forelse($dataPendapatan as $index => $item)
                @php $totalNominal += $item->nominal; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    {{-- Kita hapus kolom Villa karena sudah ada di header --}}
                    <td>{{ $listJenisPendapatan[$item->jenis_pendapatan] ?? $item->jenis_pendapatan }}</td>
                    <td>{{ $listMetodePembayaran[$item->metode_pembayaran] ?? $item->metode_pembayaran }}</td>
                    <td style="text-align: right;">{{ number_format($item->nominal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data pendapatan yang ditemukan.</td>
                </tr>
            @endforelse
            
            {{-- BARIS TOTAL --}}
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">TOTAL PENDAPATAN</td>
                <td style="text-align: right;">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh Sistem pada: {{ Carbon::now()->format('d M Y H:i:s') }}
    </div>

</body>
</html>