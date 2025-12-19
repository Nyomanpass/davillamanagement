<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pendapatan {{ $villaName }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 9pt; color: #333; margin: 0; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #b45309; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16pt; color: #b45309; text-transform: uppercase; margin-bottom: 10px; }
        .header table { border: none; width: 100%; margin-top: 5px; table-layout: fixed; }
        .header table td { border: none; padding: 2px 0; font-size: 9pt; vertical-align: top; }
            
        table { width: 100%; border-collapse: collapse; margin-top: 15px; table-layout: fixed; }
        th, td { border: 1px solid #e2e8f0; padding: 8px 5px; word-wrap: break-word; }
        th { background-color: #b45309; color: white; text-transform: uppercase; font-size: 8pt; text-align: center; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .total-row td { background-color: #fef3c7; font-weight: bold; border-top: 2px solid #b45309; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 7pt; color: #999; }
        .badge { background: #f1f5f9; padding: 2px 4px; border-radius: 3px; font-size: 7pt; color: #475569; border: 1px solid #cbd5e1; }
    </style>
</head>
<body>

   <div class="header">
    <h1>LAPORAN PENDAPATAN VILLA</h1>
    <table>
         <tr>
            <td width="12%"><strong>Villa</strong></td>
            <td width="38%">: {{ $villaName }}</td>
            
            <td width="12%"><strong>Kategori</strong></td>
            <td width="38%">: {{ $categoryName }}</td>
        </tr>
        <tr>
            <td width="12%"><strong>Periode</strong></td>
            <td width="38%">: 
                @php
                    use Carbon\Carbon;
                    $start = $filterParams['start'] ?? null;
                    $end = $filterParams['end'] ?? null;
                    $bulan = (isset($filterParams['bulan']) && $filterParams['bulan'] !== '') ? (int)$filterParams['bulan'] : null;
                    $tahun = (isset($filterParams['tahun']) && $filterParams['tahun'] !== '') ? (int)$filterParams['tahun'] : null;
                @endphp
                @if ($start && $end)
                    {{ Carbon::parse($start)->translatedFormat('d M Y') }} - {{ Carbon::parse($end)->translatedFormat('d M Y') }}
                @elseif ($bulan && $tahun)
                    {{ Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
                @elseif ($tahun)
                    Tahun {{ $tahun }}
                @else
                    Semua Data
                @endif
            </td>
            
            <td width="12%"><strong>Dicetak</strong></td>
            <td width="38%">: {{ Carbon::now()->format('d M Y H:i') }}</td>
        </tr>
    </table>
</div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 9%;">Tanggal</th>
                <th style="width: 12%;">Kategori</th>
                <th style="width: 25%;">Detail Item / Booking</th>
                <th style="width: 10%;">Qty / Night</th>
                <th style="width: 13%;">Harga Satuan</th>
                <th style="width: 15%;">Total Nominal</th>
                <th style="width: 13%;">Metode</th>
            </tr>
        </thead>
        <tbody>
            @php $totalNominal = 0; @endphp
            @foreach($dataPendapatan as $index => $item)
                @php 
                    $totalNominal += $item->nominal; 
                    $isRoom = str_contains(strtolower($item->category->name ?? ''), 'room');
                    
                    if ($isRoom) {
                        $qtyText = $item->nights . " Malam";
                        $hargaSat = $item->nominal / ($item->nights > 0 ? $item->nights : 1);
                        $detail = "Booking (" . Carbon::parse($item->check_in)->format('d/m') . " - " . Carbon::parse($item->check_out)->format('d/m') . ")";
                    } else {
                        $qtyText = $item->qty . "x";
                        $hargaSat = $item->harga_satuan ?? ($item->nominal / ($item->qty > 0 ? $item->qty : 1));
                        $detail = $item->item_name;
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td class="text-center"><span class="badge">{{ $item->category->name ?? '-' }}</span></td>
                    <td class="font-bold">{{ $detail }}</td>
                    <td class="text-center">{{ $qtyText }}</td>
                    <td class="text-right">Rp {{ number_format($hargaSat, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $listMetodePembayaran[$item->metode_pembayaran] ?? $item->metode_pembayaran }}</td>
                </tr>
            @endforeach
            
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL PENDAPATAN</td>
                <td class="text-right">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Halaman 1 dari 1 | Dokumen Digital Villa Management System
    </div>

</body>
</html>