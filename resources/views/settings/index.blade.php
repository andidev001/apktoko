@extends('layouts.app')
@section('title', 'Pengaturan Toko')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Informasi & Identitas Toko</h5>
                </div>
                <div class="card-body mt-3">
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Kolom Info Toko -->
                            <div class="col-md-6 border-end">
                                <h6 class="mb-3 text-primary"><i class="fas fa-store"></i> Data Toko</h6>
                                <div class="mb-3">
                                    <label class="form-label">Nama Toko / Bisnis</label>
                                    <input type="text" name="shop_name" class="form-control"
                                        value="{{ $setting->shop_name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">No. Telepon / WhatsApp</label>
                                    <input type="text" name="shop_phone" class="form-control"
                                        value="{{ $setting->shop_phone }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Info Rekening Pembayaran (Opsional)</label>
                                    <textarea name="bank_account" class="form-control" rows="3" placeholder="Contoh: BCA 1234567890 a.n. Budi Santoso&#10;Mandiri 0987654321">{{ $setting->bank_account }}</textarea>
                                    <small class="text-muted">Akan ditampilkan di invoice untuk pembayaran via transfer.</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alamat Toko</label>
                                    <textarea name="shop_address" class="form-control" rows="3"
                                        required>{{ $setting->shop_address }}</textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Logo Toko (Opsional)</label>
                                    <input type="file" name="shop_logo" class="form-control" accept="image/*">
                                    @if($setting->shop_logo)
                                        <div class="mt-2 text-muted">Preview Logo:</div>
                                        <img src="{{ asset('storage/' . $setting->shop_logo) }}" alt="Logo"
                                            class="img-thumbnail mt-1" style="max-height: 80px;">
                                    @endif
                                </div>
                            </div>

                            <!-- Kolom Info Pemilik -->
                            <div class="col-md-6 ps-4">
                                <h6 class="mb-3 text-primary"><i class="fas fa-user-tie"></i> Data Pemilik (TTD Invoice)
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label">Nama Representatif / Pemilik</label>
                                    <input type="text" name="owner_name" class="form-control"
                                        value="{{ $setting->owner_name }}" placeholder="Misal: Budi Santoso">
                                    <small class="text-muted">Akan ditampilkan di bagian bawah invoice.</small>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Unggah Tanda Tangan (Opsional)</label>
                                    <input type="file" name="owner_signature" class="form-control" accept="image/*">
                                    <small class="text-muted">Gunakan gambar (.png) berlatar transparan / putih untuk hasil
                                        maksimal.</small>
                                    @if($setting->owner_signature)
                                        <div class="mt-2 text-muted">Preview Tanda Tangan:</div>
                                        <img src="{{ asset('storage/' . $setting->owner_signature) }}" alt="Signature"
                                            class="img-thumbnail mt-1" style="max-height: 80px;">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr>
                        <button type="submit" class="btn btn-primary w-100 btn-lg"><i class="fas fa-save"></i> Simpan Semua
                            Pengaturan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection