@extends('layouts.app')
@section('title', 'Transaksi Penjualan (POS)')
@push('styles')
    <!-- DataTables CSS/JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush
@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
@endpush
@section('content')
    <div class="row">
        <!-- List Barang -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Pilih Barang</h5>
                    <input type="text" id="scanBarcode" class="form-control form-control-sm w-50"
                        placeholder="Scan Barcode / Kode Barang (Enter)..." autofocus>
                </div>
                <div class="card-body mt-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="productTable" width="100%">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cart -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header border-bottom bg-light">
                    <h5 class="card-title mb-0">Keranjang <i class="fas fa-shopping-cart float-end"></i></h5>
                </div>
                <div class="card-body p-0">
                    <form action="{{ route('transactions.store') }}" method="POST" id="cartForm">
                        @csrf
                        <div id="cartItems" class="p-3" style="min-height: 200px;">
                            <p class="text-center text-muted mt-3" id="emptyCart">Keranjang kosong</p>
                        </div>
                        <div class="px-3 pb-2">
                            <label class="form-label text-muted small mb-1">Nama Pembeli / Agen (Opsional)</label>
                            <input type="text" name="nama_pelanggan" class="form-control form-control-sm"
                                placeholder="Kosongkan Jika Umum">
                        </div>
                        <div class="p-3 bg-light border-top">
                            <div class="d-flex justify-content-between mb-3">
                                <h5>Total</h5>
                                <h5 class="text-primary">Rp <span id="cartTotal">0</span></h5>
                            </div>
                            <button class="btn btn-primary w-100 btn-lg" type="submit" id="btnSubmitCart" disabled>Proses
                                Transaksi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light py-2">
                    <h6 class="modal-title mb-0"><i class="fas fa-receipt text-secondary"></i> Struk Cetak Otomatis</h6>
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
    <script>

        let cart = {};

        function addToCart(id, name, price, maxStock, satuan, hargaGrosir, minGrosir) {
            if (cart[id]) {
                if (cart[id].qty < maxStock) cart[id].qty++;
                else Toast.fire({ icon: 'warning', title: 'Stok tidak mencukupi' });
            } else {
                cart[id] = {
                    name: name, price: price, qty: 1, max: maxStock,
                    satuan: satuan || 'Pcs',
                    hargaGrosir: hargaGrosir || 0,
                    minGrosir: minGrosir || 0
                };
            }
            renderCart();
        }

        // Fungsi ketika qty dirubah manual via input
        $(document).on('change', '.item-qty', function () {
            let id = $(this).data('id');
            let val = parseFloat($(this).val());
            let max = parseFloat($(this).attr('max'));

            if (val > max) {
                val = max;
                Toast.fire({ icon: 'warning', title: 'Melebihi batas stok' });
            }
            if (val <= 0 || isNaN(val)) val = 1;

            if (cart[id]) {
                cart[id].qty = val;
                renderCart();
            }
        });

        function removeCart(id) {
            delete cart[id];
            renderCart();
        }

        function renderCart() {
            let html = '';
            let total = 0;
            let count = 0;
            for (let id in cart) {
                count++;
                let item = cart[id];

                // Cek harga grosir
                let finalPrice = item.price;
                let isGrosir = false;
                if (item.minGrosir > 0 && item.hargaGrosir > 0 && item.qty >= item.minGrosir) {
                    finalPrice = item.hargaGrosir;
                    isGrosir = true;
                }

                let subtotal = item.qty * finalPrice;
                total += subtotal;

                let grosirBadge = isGrosir ? `<span class="badge bg-success ms-2" style="font-size:0.65rem;">Harga Grosir</span>` : '';

                html += `
                    <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-2">
                        <input type="hidden" name="product_id[]" value="${id}">
                        <input type="hidden" name="qty[]" value="${item.qty}">
                        <div style="flex-grow:1;">
                            <h6 class="mb-1 text-dark">${item.name} ${grosirBadge}</h6>
                            <div class="d-flex align-items-center mt-1">
                                <input type="number" class="form-control form-control-sm text-center item-qty me-2" data-id="${id}" value="${item.qty}" min="0.01" step="any" max="${item.max}" style="width:75px;"> 
                                <span class="text-muted" style="font-size:0.85rem;">${item.satuan} x Rp ${finalPrice.toLocaleString('id-ID')}</span>
                            </div>
                        </div>
                        <div class="text-end ms-2">
                            <strong class="text-primary d-block mb-1">Rp ${subtotal.toLocaleString('id-ID')}</strong>
                            <button type="button" class="btn btn-sm btn-outline-danger p-1 border-0" onclick="removeCart(${id})" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                    `;
            }

            let cartItems = $('#cartItems');
            if (count > 0) {
                cartItems.html(html);
                $('#btnSubmitCart').prop('disabled', false);
            } else {
                cartItems.html('<p class="text-center text-muted mt-3" id="emptyCart">Keranjang kosong</p>');
                $('#btnSubmitCart').prop('disabled', true);
            }

            $('#cartTotal').text(total.toLocaleString('id-ID'));
        }

        // Scan Barcode
        $('#scanBarcode').on('keypress', function (e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                let kode = $(this).val();
                if (kode === '') return;

                $.get('/products/barcode/' + kode, function (res) {
                    if (res.success) {
                        addToCart(res.data.id, res.data.nama_barang, res.data.harga_jual, res.data.stok, res.data.satuan, res.data.harga_grosir, res.data.minimal_grosir);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Tidak Ditemukan',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }).fail(function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan sistem saat mencari barang!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });

                $(this).val('');
            }
        });

        // Initialize DataTable
        $(document).ready(function () {
            $('#productTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("products.data") }}',
                columns: [
                    { data: 'kode_barang', name: 'kode_barang' },
                    { data: 'nama_barang', name: 'nama_barang' },
                    { data: 'harga_formatted', name: 'harga_jual' },
                    { data: 'stok_satuan', name: 'stok' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
            
            // Auto Print Receipt Workflow
            @if(session('print_receipt_url'))
                $('#receiptFrame').attr('src', '{{ session('print_receipt_url') }}');
                let myModal = new bootstrap.Modal(document.getElementById('receiptModal'));
                myModal.show();
            @endif
        });
    </script>
@endpush