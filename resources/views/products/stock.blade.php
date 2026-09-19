@extends('layouts.app')
@section('title', 'Stok Barang')
@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Manajemen Stok</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="stockTable" width="100%">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Stok Saat Ini</th>
                            <th width="200">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Stock Modal (Single) -->
    <div class="modal fade" id="stockModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form id="stockForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="stockModalTitle">Update Stok</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Tipe Update</label>
                            <select name="tipe" class="form-select" id="tipeSelect">
                                <option value="tambah">Tambah Stok (+)</option>
                                <option value="kurang">Kurangi Stok (-)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Jumlah</label>
                            <input type="number" step="any" min="0.01" name="stok" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary w-100">Simpan</button>
                    </div>
                </form>
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
            $('#stockTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("products.data_stock") }}',
                columns: [
                    { data: 'kode_barang', name: 'kode_barang' },
                    { data: 'nama_barang', name: 'nama_barang' },
                    { data: 'stok_badge', name: 'stok' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });

        function updateStockModal(id, nama, type) {
            $('#stockForm').attr('action', '/stock/' + id);
            $('#stockModalTitle').text('Update Stok: ' + nama);
            $('#tipeSelect').val(type);
            new bootstrap.Modal(document.getElementById('stockModal')).show();
        }
    </script>
@endpush