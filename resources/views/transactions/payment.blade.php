@extends('layouts.app')
@section('title', 'Pembayaran')
@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card dash-card overflow-hidden">
                <div class="row g-0">

                    <!-- Kiri: Info Tagihan -->
                    <div
                        class="col-md-5 bg-primary text-white p-4 p-lg-5 d-flex flex-column justify-content-center position-relative">
                        <!-- Pola Latar -->
                        <div style="position: absolute; right: -20px; top: -20px; opacity: 0.1;">
                            <i class="fas fa-money-bill-wave" style="font-size: 15rem;"></i>
                        </div>

                        <div class="position-relative z-1">
                            <span class="badge bg-white text-primary mb-3">No Faktur: {{ $transaction->no_faktur }}</span>
                            <h5 class="text-white-50 mb-1">Total Tagihan</h5>
                            <h2 class="display-6 fw-bold mb-4 text-white">Rp
                                {{ number_format($transaction->total_harga, 0, ',', '.') }}
                            </h2>

                            <hr class="border-white opacity-25">
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span><i class="fas fa-boxes me-2"></i> Total Item:</span>
                                <strong>{{ $transaction->details->count() }} Jenis</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span><i class="fas fa-calendar-alt me-2"></i> Tanggal:</span>
                                <strong>{{ $transaction->created_at->format('d/m/Y') }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Kanan: Form Pembayaran -->
                    <div class="col-md-7 p-4 p-lg-5">
                        <h5 class="fw-bold text-dark mb-4"><i class="fas fa-wallet text-primary me-2"></i> Selesaikan
                            Pembayaran</h5>

                        <form action="{{ route('transactions.processPayment', $transaction->id) }}" method="POST">
                            @csrf

                            <!-- Input Agen -->
                            <div class="mb-3">
                                <label class="form-label text-muted fw-medium" style="font-size: 0.85rem;">Nama Pembeli /
                                    Agen (Opsional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="fas fa-user text-muted"></i></span>
                                    <input type="text" name="nama_pelanggan" id="nama_pelanggan"
                                        class="form-control border-start-0 ps-0 text-dark fw-medium"
                                        placeholder="Umum / Kosongkan bila cash" value="{{ $transaction->nama_pelanggan }}">
                                </div>
                                <small class="text-success d-none mt-1 fw-bold" id="saveStatus"><i class="fas fa-check"></i>
                                    Tersimpan otomatis</small>
                            </div>

                            <!-- Input Bayar Utama -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="form-label text-muted fw-medium" style="font-size: 0.85rem;">Terima Uang
                                        Dari Pelanggan</label>
                                    <div class="input-group">
                                        <span
                                            class="input-group-text bg-light text-dark fw-bold fs-5 border-end-0">Rp</span>
                                        <input type="number" name="jumlah_bayar" id="jumlah_bayar"
                                            class="form-control border-start-0 ps-0 fw-bold fs-4 text-primary" required
                                            autofocus min="{{ $transaction->total_harga }}" autocomplete="off"
                                            style="letter-spacing: 1px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Kalkulasi Tampilan -->
                            <div
                                class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-4 border">
                                <span class="text-muted fw-bold">Kembalian:</span>
                                <span id="kembalian_text" class="fw-bold fs-4 text-success">Rp 0</span>
                            </div>

                            <!-- Tombol Eksekusi -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" style="padding: 12px;" id="btnProses"
                                    disabled>
                                    <i class="fas fa-check-circle me-1"></i> Selesaikan Transaksi Berhasil
                                </button>

                                <div class="row g-2 mt-1">
                                    <div class="col-6">
                                        <button type="button" onclick="bukaNota()" class="btn btn-outline-secondary w-100">
                                            <i class="fas fa-file-invoice"></i> Nota Hutang
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('transactions.index') }}"
                                            class="btn btn-light w-100 border text-dark">
                                            <i class="fas fa-chevron-left"></i> Simpan (POS)
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="mt-4 pt-3 border-top text-center">
                            <form action="{{ route('transactions.cancel', $transaction->id) }}" method="POST"
                                class="form-confirm"
                                data-confirm-message="Seluruh item akan diretur ke stok awal. Yakin batalkan transaksi ini?">
                                @csrf
                                <button type="submit" class="btn btn-link text-danger text-decoration-none p-0"
                                    style="font-size: 0.85rem;">
                                    <i class="fas fa-times-circle"></i> Buang & Batalkan Transaksi Ini
                                </button>
                            </form>
                        </div>

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
                    <h5 class="modal-title"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i> Pratinjau Nota
                        Hutang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0" style="height: 75vh;">
                    <iframe id="printFrame" src="" width="100%" height="100%" style="border:none;"></iframe>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary px-4"
                        onclick="document.getElementById('printFrame').contentWindow.print()">
                        <i class="fas fa-print me-1"></i> Mulai Mencetak Nota
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $('#jumlah_bayar').on('input', function () {
            let total = {{ $transaction->total_harga }};
            let bayar = $(this).val();
            let kembalian = bayar - total;

            if (bayar >= total) {
                $('#kembalian_text').text('Rp ' + kembalian.toLocaleString('id-ID'));
                $('#btnProses').prop('disabled', false);
            } else {
                $('#kembalian_text').text('Rp 0');
                $('#btnProses').prop('disabled', true);
            }
        });
        $('#nama_pelanggan').on('change', function () {
            let nama = $(this).val();
            $.ajax({
                url: '{{ route("transactions.updateName", $transaction->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    nama_pelanggan: nama
                },
                success: function () {
                    $('#saveStatus').removeClass('d-none').show().delay(2000).fadeOut();
                }
            });
        });

            function bukaNota() {
                let inoviceUrl = '{{ route("transactions.invoice", $transaction->id) }}';
                $('#printFrame').attr('src', inoviceUrl);
                let myModal = new bootstrap.Modal(document.getElementById('printModal'));
                myModal.show();
            }
        </script>
@endpush