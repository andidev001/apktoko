<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran {{ $transaction->no_faktur }}</title>
    <style>
        body {
            font-family: monospace;
            font-size: 14px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        .receipt {
            width: 100%;
            max-width: 75mm;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            line-height: 1.4;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .divider {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 5px 0;
            vertical-align: top;
        }

        .mt-2 {
            margin-top: 20px;
        }

        .mb-2 {
            margin-bottom: 20px;
        }

        @media print {
            body {
                padding: 10px;
                margin: 0 auto;
            }

            .receipt {
                width: 100%;
                max-width: 75mm;
                margin: 0 auto;
                padding: 0;
            }

            @page {
                size: auto;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    @php $setting = \App\Models\Setting::first(); @endphp
    <div class="receipt">
        <div class="text-center font-bold mb-2">
            @if($setting->shop_logo)
                <img src="{{ asset('storage/' . $setting->shop_logo) }}" alt="Logo"
                    style="max-height: 50px; margin-bottom: 5px;">
            @else
                <h3>{{ $setting->shop_name }}</h3>
            @endif
            <div style="font-weight: normal; font-size: 12px; line-height:1.2;">
                {!! nl2br(e($setting->shop_address)) !!}<br>
                Telp: {{ $setting->shop_phone }}
            </div>
        </div>

        <div class="divider"></div>
        <table>
            <tr>
                <td>Faktur</td>
                <td class="text-right">{{ $transaction->no_faktur }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td class="text-right">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td class="text-right">{{ Auth::user()->name }}</td>
            </tr>
        </table>
        <div class="divider"></div>

        <table>
            @foreach($transaction->details as $d)
                <tr>
                    <td colspan="3">{{ $d->product->nama_barang }}</td>
                </tr>
                <tr>
                    <td>{{ (float) $d->jumlah }} {{ $d->product->satuan ?? 'Pcs' }} x</td>
                    <td class="text-right">{{ number_format($d->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($d->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>

        <div class="divider"></div>
        <table>
            <tr class="font-bold">
                <td>Subtotal</td>
                <td class="text-right">Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunai</td>
                <td class="text-right">Rp {{ number_format($transaction->payment->jumlah_bayar ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="text-right">Rp {{ number_format($transaction->payment->kembalian ?? 0, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="divider"></div>
        <div class="text-center mt-2" style="font-size: 12px;">
            Terima Kasih Atas Kunjungan Anda<br>
            Barang yang sudah dibeli tidak dapat ditukar/dikembalikan
        </div>

        <!-- Tombol navigasi (sembunyi saat print) -->
        <div class="mt-2 text-center" style="margin-top:40px;">
            <style>
                @media print {
                    .no-print {
                        display: none !important;
                    }
                }

                .btn {
                    padding: 8px 16px;
                    background: #eee;
                    border: 1px solid #ccc;
                    cursor: pointer;
                    text-decoration: none;
                    color: #333;
                    font-family: sans-serif;
                }
            </style>
            <a href="{{ route('transactions.index') }}" class="btn no-print"
                style="margin-bottom: 10px; display: block;">Transkasi Baru (POS)</a>
            <a href="{{ route('reports.index') }}" class="btn no-print" style="display: block;">Kembali ke Laporan</a>
        </div>
    </div>
</body>

</html>