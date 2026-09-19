<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Laporan Transaksi</title>
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #fff;
            color: #212529;
            font-size: 13px;
            margin: 0;
            padding: 20px 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eaeaea;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #111;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0;
            color: #6c757d;
            font-size: 14px;
        }

        .report-title-box {
            display: inline-block;
            background: #f8f9fa;
            border-radius: 6px;
            padding: 8px 20px;
            margin-top: 15px;
            border: 1px solid #e9ecef;
        }

        .report-title-box h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: #333;
            letter-spacing: 0.5px;
        }

        .report-title-box p {
            margin: 4px 0 0;
            font-size: 12px;
            color: #555;
            font-weight: 500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            padding: 12px 15px;
            border: 1px solid #e9ecef;
        }

        th {
            background-color: #f8f9fc;
            color: #495057;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }

        td {
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .fw-bold {
            font-weight: 700;
        }

        .text-primary {
            color: #7367f0;
        }

        .items-list {
            margin: 0;
            padding-left: 15px;
            color: #6c757d;
            font-size: 12px;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 50rem;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-success {
            background: rgba(40, 199, 111, 0.12);
            color: #28c76f;
        }

        .badge-danger {
            background: rgba(234, 84, 85, 0.12);
            color: #ea5455;
        }

        tfoot th,
        tfoot td {
            background: #fdfdfd;
            font-size: 14px;
        }

        .footer-ttd {
            float: right;
            text-align: center;
            width: 250px;
            margin-top: 20px;
        }

        .footer-ttd p {
            margin: 0 0 70px 0;
            color: #6c757d;
        }

        .footer-ttd h5 {
            margin: 0;
            font-size: 14px;
            text-decoration: underline;
            color: #333;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>{{ $setting->shop_name }}</h1>
        <p>{!! nl2br(e($setting->shop_address)) !!} | Telp: {{ $setting->shop_phone }}</p>

        <div class="report-title-box">
            <h3>LAPORAN TRANSAKSI KESELURUHAN</h3>
            <p>
                Periode:
                {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M Y') : '-' }}
                s.d
                {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M Y') : '-' }}
            </p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="15%">Waktu Transaksi</th>
                <th width="15%">No Faktur</th>
                <th width="15%">Pelanggan</th>
                <th width="30%">Item Terjual</th>
                <th class="text-end" width="10%">Subtotal</th>
                <th class="text-center" width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $totalKeseluruhan = 0; @endphp
            @forelse($transactions as $index => $trx)
                @php
                    if ($trx->status === 'lunas') {
                        $totalKeseluruhan += $trx->total_harga;
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                    <td class="fw-bold">{{ $trx->no_faktur }}</td>
                    <td>{{ $trx->nama_pelanggan ?: 'Umum' }}</td>
                    <td>
                        <ul class="items-list">
                            @foreach($trx->details as $d)
                                <li>{{ $d->product->nama_barang }}
                                    ({{ (float) $d->jumlah }}{{ $d->product->satuan ? ' ' . $d->product->satuan : '' }})</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-end">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($trx->status == 'lunas')
                            <span class="badge badge-success">Lunas</span>
                        @else
                            <span class="badge badge-danger">Belum</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 30px; color: #6c757d;">Tidak ada transaksi pada
                        periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-end fw-bold">TOTAL PENDAPATAN LUNAS:</td>
                <td class="text-end fw-bold text-primary fs-6">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-ttd">
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
        <h5>{{ $setting->owner_name ?? '(' . $setting->shop_name . ')' }}</h5>
    </div>

</body>

</html>