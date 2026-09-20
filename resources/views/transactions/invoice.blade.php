<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $transaction->no_faktur }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #e9ecef;
            color: #333;
        }

        .invoice-box {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            background: #fff;
            position: relative;
            overflow: hidden;
        }

        @media print {
            body {
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice-box {
                margin: 0;
                box-shadow: none;
                border: none;
                padding: 10px;
                max-width: 100%;
                overflow: hidden;
            }

            .no-print {
                display: none !important;
            }
        }

        .watermark {
            position: absolute;
            top: 60%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 3.5rem;
            color: rgba(255, 59, 48, 0.08);
            pointer-events: none;
            z-index: 0;
            font-weight: 800;
            letter-spacing: 0.2rem;
            white-space: nowrap;
        }

        .table-custom {
            border-color: #f1f1f1;
        }

        .table-custom th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            border-bottom: 2px solid #e9ecef;
            padding: 10px 10px;
        }

        .table-custom td {
            vertical-align: middle;
            font-size: 0.8rem;
            padding: 10px 10px;
            border-color: #f1f1f1;
        }
    </style>
</head>

<body onload="window.print()">
    @php $setting = \App\Models\Setting::first(); @endphp

    <div class="container invoice-box">
        <!-- Z-Index 1 overlay text so watermark stays behind -->
        <div class="position-relative z-index-1">
            <div class="watermark">BELUM LUNAS</div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    @if($setting->shop_logo)
                        <img src="{{ asset('storage/' . $setting->shop_logo) }}" alt="Logo"
                            style="max-height: 45px; margin-right: 15px;">
                    @endif
                    <div>
                        <h4 class="mb-0 text-primary fw-bold">{{ $setting->shop_name }}</h4>
                        <div class="text-muted" style="font-size: 0.8rem; max-width: 450px;">
                            {{ str_replace(["\r\n", "\r", "\n"], ' ', $setting->shop_address) }} | <strong>Telp:</strong> {{ $setting->shop_phone }}
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <div class="mb-1 text-muted text-uppercase fw-bold" style="letter-spacing: 1px; font-size: 0.95rem;">TAGIHAN / INVOICE
                    </div>
                    <span class="fs-6"><strong>#{{ $transaction->no_faktur }}</strong></span>
                </div>
            </div>
            <hr class="text-muted mb-4">

            <div class="row mb-4">
                <div class="col-sm-6">
                    <p class="text-muted mb-1 text-uppercase" style="font-size: 0.65rem; font-weight:600;">Tagihan
                        Kepada:</p>
                    <div class="fw-bold" style="font-size: 0.95rem;">
                        {{ $transaction->nama_pelanggan ? $transaction->nama_pelanggan : 'Pelanggan Tunai / Umum' }}
                    </div>
                </div>
                <div class="col-sm-6 text-end">
                    <p class="text-muted mb-1 text-uppercase" style="font-size: 0.65rem; font-weight:600;">Informasi
                        Pesanan</p>
                    <div class="mb-1" style="font-size: 0.8rem;">Tanggal:
                        <strong>{{ $transaction->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                    <div>Status: <span class="badge bg-danger rounded-pill px-2 py-1"
                            style="font-size: 0.7rem !important; font-weight:500;">BELUM DIBAYAR</span></div>
                </div>
            </div>

            <table class="table table-custom table-borderless mb-4">
                <thead>
                    <tr>
                        <th class="ps-2">Item Barang</th>
                        <th class="text-center">Kuantitas</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-end pe-2">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="border-bottom">
                    @foreach($transaction->details as $d)
                        <tr>
                            <td class="ps-2">{{ $d->product->nama_barang }}</td>
                            <td class="text-center">{{ (float) $d->jumlah }} {{ $d->product->satuan ?? 'Pcs' }}</td>
                            <td class="text-end">Rp {{ number_format($d->harga_jual, 0, ',', '.') }}</td>
                            <td class="text-end fw-medium pe-2">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end pt-3">
                            <span class="text-muted fw-bold text-uppercase" style="font-size: 0.75rem">Total
                                Tagihan</span>
                        </td>
                        <td class="text-end pt-2 pe-2">
                            <span class="text-primary fw-bold mb-0" style="font-size: 1.25rem;">Rp
                                {{ number_format($transaction->total_harga, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <div class="row mt-4">
                <div class="col-8">
                    @if($setting->bank_account)
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="mb-1 text-primary fw-bold" style="font-size: 0.8rem;"><i
                                    class="fas fa-university"></i> Mohon Transfer Ke Rekening Berikut:
                            </div>
                            <div style="white-space: pre-line; font-size: 0.8rem;" class="text-dark">
                                {{ $setting->bank_account }}
                            </div>
                        </div>
                    @endif
                    <p class="text-muted small" style="font-size: 0.7rem; line-height: 1.4;"><strong>Penting:</strong>
                        Invoice ini adalah dokumen tagihan resmi.
                        Harap segera selesaikan pembayaran untuk faktur ini. Barang yang tertera pada faktur ini telah
                        dipotong dari stok gudang/toko, pending status berlaku selama belum lunas.</p>
                </div>
                <div class="col-4 text-center" style="font-size: 0.85rem;">
                    <p class="mb-1"><strong>Hormat Kami,</strong></p>
                    @if($setting->owner_signature)
                        <img src="{{ asset('storage/' . $setting->owner_signature) }}" alt="TTD"
                            style="max-height: 60px; margin: 10px 0;">
                    @else
                        <br><br><br>
                    @endif
                    <p class="mb-0">
                        <u><strong>{{ $setting->owner_name ?? '(' . $setting->shop_name . ')' }}</strong></u>
                    </p>
                </div>
            </div>

            <div class="mt-5 text-center no-print">
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary rounded-pill px-4"><i
                        class="fas fa-arrow-left"></i> Kembali ke POS</a>
                <a href="{{ route('transactions.paymentForm', $transaction->id) }}"
                    class="btn btn-success rounded-pill px-4 ms-2"><i class="fas fa-money-bill-wave"></i> Lanjutkan
                    Pembayaran</a>
            </div>
        </div>
    </div>

</body>

</html>