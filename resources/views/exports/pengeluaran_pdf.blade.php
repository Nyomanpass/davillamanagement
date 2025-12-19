<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengeluaran PDF</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; margin: 0; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #1e293b; padding-bottom: 10px; }
        .header h1 { font-size: 18px; margin: 0; color: #1e293b; text-transform: uppercase; }
        .info-table { width: 100%; margin-top: 5px; }
        .info-table td { border: none; padding: 2px 0; font-size: 11px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th { background-color: #1e293b; color: white; padding: 8px 5px; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        td { border: 1px solid #cbd5e1; padding: 6px 5px; vertical-align: top; word-wrap: break-word; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .total-row { background-color: #f1f5f9; font-weight: bold; font-size: 11px; }
        .footer-note { margin-top: 15px; font-style: italic; font-size: 9px; color: #64748b; }
        .badge { background: #f1f5f9; padding: 2px 4px; border-radius: 3px; font-size: 8px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Pengeluaran Villa</h1>
        <table class="info-table">
            <tr>
                <td width="15%"><strong>Villa</strong></td>
                <td width="35%">: {{ $villaName }}</td>
                <td width="15%"><strong>Kategori</strong></td>
                <td width="35%">: {{ $categoryName }}</td>
            </tr>
            <tr>
                <td><strong>Periode</strong></td>
                <td>: 
                    @php
                        use Carbon\Carbon;
                        $start = $filterParams['start'] ?? null;
                        $end = $filterParams['end'] ?? null;
                        $bulan = isset($filterParams['bulan']) && $filterParams['bulan'] !== '' ? (int)$filterParams['bulan'] : null;
                        $tahun = isset($filterParams['tahun']) && $filterParams['tahun'] !== '' ? (int)$filterParams['tahun'] : null;
                    @endphp
                    @if ($start && $end)
                        {{ Carbon::parse($start)->format('d M Y') }} - {{ Carbon::parse($end)->format('d M Y') }}
                    @elseif ($bulan && $tahun)
                        {{ Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
                    @else
                        Semua Data
                    @endif
                </td>
                <td><strong>Dicetak</strong></td>
                <td>: {{ Carbon::now()->format('d M Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="8%">Tanggal</th>
                <th width="10%">Kategori</th>
                <th width="24%">Nama Pengeluaran</th>
                <th width="10%">Qty</th>
                <th width="12%">Harga Satuan</th>
                <th width="13%">Total Nominal</th>
                <th width="8%">Metode</th>
                <th width="12%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $totalSemua = 0; @endphp
            @foreach ($dataPengeluaran as $index => $item)
                @php $totalSemua += $item->nominal; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td class="text-center"><span class="badge">{{ $item->category->name ?? '-' }}</span></td>
                    <td class="font-bold">{{ $item->nama_pengeluaran }}</td>
                    <td class="text-center">{{ (float)$item->qty }} {{ $item->satuan }}</td>
                    <td class="text-right">Rp{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp{{ number_format($item->nominal, 0, ',', '.') }}</td>
                    <td class="text-center">{{ strtoupper($item->metode_pembayaran) }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
            
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL PENGELUARAN</td>
                <td class="text-right">Rp{{ number_format($totalSemua, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer-note">
        * Laporan ini dibuat secara otomatis melalui sistem manajemen villa pada {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>