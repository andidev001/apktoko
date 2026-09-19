@extends('layouts.app')
@section('title', 'Laporan Penjualan')
@section('content')
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card dash-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-min-width">
                            <div class="text-title-small mb-2 text-truncate">Penjualan Harian (Hari Ini)</div>
                            <div class="text-value" style="color: #7367f0;">Rp {{ number_format($harian, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="icon-box bg-light-primary flex-shrink-0">
                            <i class="fas fa-calendar-day text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card dash-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-min-width">
                            <div class="text-title-small mb-2 text-truncate">Penjualan Bulanan (Bulan Ini)</div>
                            <div class="text-value" style="color: #28c76f;">Rp {{ number_format($bulanan, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="icon-box bg-light-success flex-shrink-0">
                            <i class="fas fa-calendar-alt text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card dash-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-min-width">
                            <div class="text-title-small mb-2 text-truncate">Rekap Total Omzet</div>
                            <div class="text-value" style="color: #00cfe8;">Rp {{ number_format($omzet, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="icon-box bg-light-info flex-shrink-0">
                            <i class="fas fa-wallet text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Riwayat Transaksi Penjualan</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="reportTable" width="100%">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>No Faktur</th>
                            <th>Items</th>
                            <th>Total Belanja</th>
                            <th>Bayar / Kembali</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light py-2">
                    <h6 class="modal-title mb-0"><i class="fas fa-receipt text-secondary"></i> Struk Belanja</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 0.75rem;"></button>
                </div>
                <div class="modal-body p-0" style="height: 550px;">
                    <iframe id="receiptFrame" src="" width="100%" height="100%" style="border:none;"></iframe>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-primary btn-sm w-100"
                        onclick="document.getElementById('receiptFrame').contentWindow.print()">
                        <i class="fas fa-print me-1"></i> Cetak ke Printer Thermal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Modal -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-invoice text-primary me-2"></i> Pratinjau Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" style="height: 75vh;">
                    <iframe id="invoiceFrame" src="" width="100%" height="100%" style="border:none;"></iframe>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary px-4"
                        onclick="document.getElementById('invoiceFrame').contentWindow.print()">
                        <i class="fas fa-print me-1"></i> Cetak Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush
@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#reportTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("reports.data") }}',
                order: [[0, 'desc']], // Urutkan berdasarkan waktu terbaru
                columns: [
                    { data: 'waktu', name: 'created_at' },
                    { data: 'no_faktur', name: 'no_faktur' },
                    { data: 'items', name: 'items', orderable: false, searchable: false },
                    { data: 'total', name: 'total_harga' },
                    { data: 'bayar_kembali', name: 'bayar_kembali', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });

        // Event listener dinamis untuk tombol Cetak Struk di Datatables (Laporan Dasar)
        $(document).on('click', '.btn-cetak-struk', function () {
            let url = $(this).data('url');
            $('#receiptFrame').attr('src', url);
            let myModal = new bootstrap.Modal(document.getElementById('receiptModal'));
            myModal.show();
        });

        // Event listener dinamis untuk tombol Cetak Invoice di Datatables
        $(document).on('click', '.btn-cetak-invoice', function () {
            let url = $(this).data('url');
            $('#invoiceFrame').attr('src', url);
            let myModal = new bootstrap.Modal(document.getElementById('invoiceModal'));
            myModal.show();
        });
    </script>
@endpush