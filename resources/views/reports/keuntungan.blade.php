@extends('layouts.app')
@section('title', 'Laporan Keuntungan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card dash-card mb-4">
            <div class="card-header bg-white pb-0 d-flex justify-content-between align-items-center border-bottom-0 pt-4 px-4">
                <h5 class="card-title fw-bold text-primary mb-0"><i class="fas fa-chart-pie me-2"></i> Laporan Keuntungan</h5>
                <button type="button" class="btn btn-secondary btn-sm" id="btnCetakPdf">
                    <i class="fas fa-print me-1"></i> Cetak PDF
                </button>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('reports.keuntungan') }}" method="GET" class="row g-3 mb-4 align-items-end" id="filterForm">
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-semibold">Tanggal Awal</label>
                        <input type="date" class="form-control form-control-lg" name="start_date" id="start_date"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-semibold">Tanggal Akhir</label>
                        <input type="date" class="form-control form-control-lg" name="end_date" id="end_date"
                            value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-filter me-2"></i> Filter Data
                        </button>
                    </div>
                </form>

                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="fas fa-info-circle fa-2x me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Total Keuntungan Kotor</h5>
                        <h3 class="mb-0 fw-bold">Rp {{ number_format($total_keuntungan, 0, ',', '.') }}</h3>
                    </div>
                </div>

                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-striped" id="keuntunganTable" width="100%">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu Transaksi</th>
                                <th>No Faktur</th>
                                <th>Item Terjual</th>
                                <th>Keuntungan Kotor</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal PDF -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-pdf text-danger me-2"></i> Pratinjau PDF Laporan Keuntungan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" style="height: 75vh;">
                <iframe id="pdfFrame" src="" width="100%" height="100%" style="border:none;"></iframe>
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
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();

            $('#keuntunganTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("reports.keuntungan.data") }}',
                    data: function(d) {
                        d.start_date = startDate;
                        d.end_date = endDate;
                    }
                },
                order: [[0, 'desc']],
                columns: [
                    { data: 'waktu', name: 'created_at' },
                    { data: 'no_faktur', name: 'no_faktur' },
                    { data: 'items', name: 'items', orderable: false, searchable: false },
                    { data: 'keuntungan', name: 'keuntungan', orderable: false, searchable: false }
                ]
            });

            $('#btnCetakPdf').on('click', function() {
                let printUrl = '{{ route("reports.keuntungan.print") }}' + '?start_date=' + startDate + '&end_date=' + endDate;
                $('#pdfFrame').attr('src', printUrl);
                let myModal = new bootstrap.Modal(document.getElementById('pdfModal'));
                myModal.show();
            });
        });
    </script>
@endpush
