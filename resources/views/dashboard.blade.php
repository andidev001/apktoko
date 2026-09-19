@extends('layouts.app')
@section('title', 'Dashboard Overview')

@section('content')
    <div class="row g-4 mb-4">
        <!-- Hari Ini -->
        <div class="col-sm-6 col-xl-3">
            <div class="card dash-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-min-width">
                            <div class="text-title-small mb-2 text-truncate">Penjualan Hari Ini</div>
                            <div class="text-value" style="color: #7367f0;">Rp {{ number_format($todaySales, 0, ',', '.') }}</div>
                        </div>
                        <div class="icon-box bg-light-primary flex-shrink-0">
                            <i class="fas fa-coins text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Omset -->
        <div class="col-sm-6 col-xl-3">
            <div class="card dash-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-min-width">
                            <div class="text-title-small mb-2 text-truncate">Total Pendapatan</div>
                            <div class="text-value" style="color: #28c76f;">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
                        </div>
                        <div class="icon-box bg-light-success flex-shrink-0">
                            <i class="fas fa-wallet text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaksi -->
        <div class="col-sm-6 col-xl-3">
            <div class="card dash-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-min-width">
                            <div class="text-title-small mb-2 text-truncate">Total Transaksi</div>
                            <div class="text-value" style="color: #00cfe8;">{{ number_format($totalTransactions, 0, ',', '.') }}</div>
                        </div>
                        <div class="icon-box bg-light-info flex-shrink-0">
                            <i class="fas fa-shopping-cart text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stok -->
        <div class="col-sm-6 col-xl-3">
            <div class="card dash-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-min-width">
                            <div class="text-title-small mb-2 text-truncate">Database Barang</div>
                            <div class="text-value" style="color: #ff9f43;">{{ number_format($totalProducts, 0, ',', '.') }}</div>
                        </div>
                        <div class="icon-box bg-light-warning flex-shrink-0">
                            <i class="fas fa-box text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Bawah -->
    <div class="row g-4">
        <!-- Transaksi Terakhir -->
        <div class="col-lg-8">
            <div class="card dash-card h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0">Transaksi Terbaru</h5>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Buka Point of Sale</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive px-3 pb-3">
                        <table class="table table-modern table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>NO FAKTUR</th>
                                    <th>WAKTU</th>
                                    <th>TOTAL</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $transaction)
                                    <tr class="border-bottom">
                                        <td>
                                            <span class="text-primary fw-medium">{{ $transaction->no_faktur }}</span>
                                        </td>
                                        <td><span class="text-muted">{{ $transaction->created_at->diffForHumans() }}</span></td>
                                        <td><strong class="text-dark">Rp {{ number_format($transaction->total_harga, 0, ',', '.') }}</strong></td>
                                        <td><span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">Sukses</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada transaksi hari ini...</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Peringatan Stok -->
        <div class="col-lg-4">
            <div class="card dash-card h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-3">
                    <h5 class="card-title fw-bold mb-0"><i class="fas fa-exclamation-triangle text-danger me-2"></i> Peringatan Stok Menipis</h5>
                </div>
                <div class="card-body p-3 pt-0">
                    <div class="d-flex flex-column gap-2">
                        @forelse($lowStocks as $brg)
                            <div class="d-flex justify-content-between align-items-center p-2 list-hover transition shadow-sm rounded border">
                                <div>
                                    <h6 class="mb-0 text-dark fw-bold" style="font-size: 0.95rem;">{{ $brg->nama_barang }}</h6>
                                    <small class="text-muted">{{ $brg->kode_barang }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-danger rounded-pill">{{ ($brg->stok == floor($brg->stok)) ? (int)$brg->stok : $brg->stok }} {{ $brg->satuan }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-check-circle text-success fs-1 mb-2"></i><br>
                                Stok barang Anda masih aman!
                            </div>
                        @endforelse
                    </div>
                    @if(count($lowStocks) > 0)
                        <div class="mt-3 text-center">
                            <a href="{{ route('products.stock') }}" class="btn btn-sm btn-light w-100 text-primary">Kelola Stok Barang</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection