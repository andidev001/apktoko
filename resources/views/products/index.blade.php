@extends('layouts.app')
@section('title', 'Data Barang')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Barang</h5>
            <div>
                <button class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#importModal"><i
                        class="fas fa-file-excel"></i> Import Excel</button>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal"><i
                        class="fas fa-plus"></i> Tambah Barang</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataBarangTable" width="100%">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Satuan & Stok</th>
                            <th>Harga Jual (Ecer & Grosir)</th>
                            <th>Harga Beli</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Barang via Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="mb-3">Gunakan template Excel yang tersedia untuk disesuaikan datanya sebelum di-upload.
                        </p>
                        <a href="{{ route('products.template') }}" class="btn btn-sm btn-outline-success mb-4"><i
                                class="fas fa-download"></i> Unduh Template Excel (.xlsx)</a>

                        <div class="text-start">
                            <label class="form-label fw-bold">Upload File Excel (.xlsx / .xls)</label>
                            <input type="file" name="file_excel" class="form-control"
                                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Proses Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Barang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label>Kode Barang</label><input type="text" name="kode_barang"
                                class="form-control" required></div>
                        <div class="mb-3"><label>Nama Barang</label><input type="text" name="nama_barang"
                                class="form-control" required></div>
                        <div class="mb-3"><label>Satuan</label>
                            <input type="text" name="satuan" class="form-control" list="satuanList" required
                                placeholder="Contoh: Pcs, Kg, Dus, Karung, Pack">
                        </div>
                        <div class="mb-3"><label>Harga Beli (Rp)</label><input type="number" name="harga_beli"
                                class="form-control" required></div>
                        <div class="mb-3"><label>Harga Jual (Rp)</label><input type="number" name="harga_jual"
                                class="form-control" required></div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label>Minimal Grosir</label>
                                <input type="number" name="minimal_grosir" class="form-control" placeholder="Cth: 12">
                                <small class="text-muted" style="font-size:0.7rem;">Kosongkan jika tidak ada diskon
                                    grosir.</small>
                            </div>
                            <div class="col-6">
                                <label>Harga Grosir (Rp)</label>
                                <input type="number" name="harga_grosir" class="form-control" placeholder="Cth: 2500">
                            </div>
                        </div>
                        <div class="mb-3"><label>Deskripsi</label><textarea name="deskripsi"
                                class="form-control"></textarea></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal (Single) -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Barang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label>Kode Barang</label><input type="text" name="kode_barang" id="e_kode"
                                class="form-control" required></div>
                        <div class="mb-3"><label>Nama Barang</label><input type="text" name="nama_barang" id="e_nama"
                                class="form-control" required></div>
                        <div class="mb-3"><label>Satuan</label>
                            <input type="text" name="satuan" id="e_satuan" class="form-control" list="satuanList" required>
                        </div>
                        <div class="mb-3"><label>Harga Beli (Rp)</label><input type="number" name="harga_beli" id="e_beli"
                                class="form-control" required></div>
                        <div class="mb-3"><label>Harga Jual (Rp)</label><input type="number" name="harga_jual" id="e_jual"
                                class="form-control" required></div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label>Minimal Grosir</label>
                                <input type="number" name="minimal_grosir" id="e_min_grosir" class="form-control">
                            </div>
                            <div class="col-6">
                                <label>Harga Grosir (Rp)</label>
                                <input type="number" name="harga_grosir" id="e_harga_grosir" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3"><label>Deskripsi</label><textarea name="deskripsi" id="e_desc"
                                class="form-control"></textarea></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <datalist id="satuanList">
        <option value="Pcs">
        <option value="Kg">
        <option value="Dus">
        <option value="Karung">
        <option value="Lembar">
        <option value="Pack">
        <option value="Bal">
        <option value="Lusin">
        <option value="Kodi">
    </datalist>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush
@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#dataBarangTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("products.data_index") }}',
                columns: [
                    { data: 'kode_barang', name: 'kode_barang' },
                    { data: 'nama_barang', name: 'nama_barang' },
                    { data: 'stok_satuan_fmt', name: 'stok' },
                    { data: 'harga_jual_fmt', name: 'harga_jual' },
                    { data: 'harga_beli_fmt', name: 'harga_beli' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });

        function editProduct(id, kode, nama, satuan, beli, jual, desk, grosir, minGrosir) {
            $('#editForm').attr('action', '/products/' + id);
            $('#e_kode').val(kode);
            $('#e_nama').val(nama);
            $('#e_satuan').val(satuan);
            $('#e_beli').val(beli);
            $('#e_jual').val(jual);
            $('#e_harga_grosir').val(grosir);
            $('#e_min_grosir').val(minGrosir);
            $('#e_desc').val(desk);
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }
    </script>
@endpush