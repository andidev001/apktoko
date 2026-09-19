@extends('layouts.app')
@section('title', 'Rekap & Cetak Laporan')
@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Filter Laporan Transaksi</h5>
                </div>
                <div class="card-body mt-3">
                    <form id="filterForm" class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" id="start_date" name="start_date" class="form-control"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" id="end_date" name="end_date" class="form-control"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Tampilkan
                                Data</button>
                        </div>
                    </form>
                    <hr>
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" id="btnCetak" class="btn btn-success"><i class="fas fa-print"></i> Cetak
                            Laporan Keseluruhan</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="reportTable" width="100%">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>No Faktur & Pelanggan</th>
                                    <th>Detail Item</th>
                                    <th>Total Belanja</th>
                                    <th>Pembayaran</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Modal -->
    <div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-print me-2 text-primary"></i> Pratinjau Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" style="height: 75vh;">
                    <iframe id="printFrame" src="" width="100%" height="100%" style="border:none;"></iframe>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary px-4"
                        onclick="document.getElementById('printFrame').contentWindow.print()">
                        <i class="fas fa-print me-1"></i> Mulai Mencetak
                    </button>
                </div>
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
@endsection
@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            let table = $('#reportTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                ajax: {
                    url: '{{ route("reports.data") }}',
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    { data: 'waktu', name: 'created_at' },
                    { data: 'no_faktur', name: 'no_faktur' },
                    { data: 'items', name: 'items' },
                    { data: 'total', name: 'total_harga' },
                    { data: 'bayar_kembali', name: 'status' }
                ]
            });

            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                table.ajax.reload();
            });

            $('#btnCetak').on('click', function () {
                let start = $('#start_date').val();
                let end = $('#end_date').val();
                let printUrl = '{{ route("reports.print") }}?start_date=' + start + '&end_date=' + end;

                // Buka modal iframe
                $('#printFrame').attr('src', printUrl);
                let myModal = new bootstrap.Modal(document.getElementById('printModal'));
                myModal.show();
            });
        });

        // Event listener dinamis untuk tombol Cetak Struk di Datatables
        $(document).on('click', '.btn-cetak-struk', function() {
            let url = $(this).data('url');
            $('#receiptFrame').attr('src', url);
            let myModal = new bootstrap.Modal(document.getElementById('receiptModal'));
            myModal.show();
        });
    </script>
@endpush