@extends('layouts.app')
@section('title', 'Backup & Restore Database')
@section('content')

    <div class="row g-4">

        {{-- Backup Card --}}
        <div class="col-md-6">
            <div class="card dash-card h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-light-primary flex-shrink-0 me-3">
                            <i class="fas fa-download text-primary"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Backup Database</h5>
                            <small class="text-muted">Ekspor seluruh data ke file .sql</small>
                        </div>
                    </div>

                    <p class="text-muted mb-4" style="font-size:0.9rem;">
                        Klik tombol di bawah ini untuk mengunduh salinan (<em>backup</em>) seluruh database aplikasi secara
                        penuh.
                        Simpan file <code>.sql</code> tersebut di tempat yang aman (Google Drive, flashdisk, dll) sebagai
                        cadangan data.
                    </p>

                    <div class="alert alert-info border-0 rounded-3 mb-4 d-flex align-items-start"
                        style="font-size:0.85rem; background: rgba(0,207,232,0.08);">
                        <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                        <span>Disarankan untuk melakukan backup <strong>minimal 1 kali seminggu</strong> agar data transaksi
                            Anda selalu aman.</span>
                    </div>

                    <div class="mt-auto">
                        <a href="{{ route('backup.download') }}" class="btn btn-primary w-100" style="padding: 12px;">
                            <i class="fas fa-download me-2"></i> Unduh Backup Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Restore Card --}}
        <div class="col-md-6">
            <div class="card dash-card h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-light-warning flex-shrink-0 me-3">
                            <i class="fas fa-upload text-warning"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Restore Database</h5>
                            <small class="text-muted">Pulihkan data dari file backup .sql</small>
                        </div>
                    </div>

                    <p class="text-muted mb-4" style="font-size:0.9rem;">
                        Upload file backup <code>.sql</code> yang pernah Anda buat sebelumnya untuk memulihkan seluruh isi
                        database.
                        Proses ini akan <strong class="text-danger">menimpa seluruh data yang ada</strong> saat ini.
                    </p>

                    <div class="alert alert-warning border-0 rounded-3 mb-4 d-flex align-items-start"
                        style="font-size:0.85rem; background: rgba(255,159,67,0.08);">
                        <i class="fas fa-exclamation-triangle text-warning me-2 mt-1"></i>
                        <span><strong>Peringatan:</strong> Lakukan backup data terbaru terlebih dahulu sebelum melakukan
                            restore agar data tidak hilang permanen.</span>
                    </div>

                    <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data"
                        class="mt-auto form-confirm"
                        data-confirm-message="PERHATIAN! Seluruh data saat ini akan DITIMPA oleh file backup ini. Apakah Anda sudah yakin?">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-muted fw-medium" style="font-size:0.85rem;">Pilih File Backup
                                (.sql)</label>
                            <input type="file" name="backup_file" class="form-control" accept=".sql" required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100" style="padding: 12px;">
                            <i class="fas fa-upload me-2"></i> Pulihkan Database
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Informasi Teknis --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h6 class="mb-0"><i class="fas fa-book-open text-primary me-2"></i> Panduan Singkat Backup & Restore
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <span class="badge bg-primary rounded-pill me-3 mt-1"
                                    style="min-width:28px; font-size:0.9rem;">1</span>
                                <div>
                                    <h6 class="fw-bold">Unduh Backup Rutin</h6>
                                    <p class="text-muted mb-0" style="font-size:0.85rem;">Klik "Unduh Backup" setiap akhir
                                        hari atau akhir minggu. File akan tersimpan otomatis ke komputer Anda.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <span class="badge bg-warning text-dark rounded-pill me-3 mt-1"
                                    style="min-width:28px; font-size:0.9rem;">2</span>
                                <div>
                                    <h6 class="fw-bold">Simpan di Tempat Aman</h6>
                                    <p class="text-muted mb-0" style="font-size:0.85rem;">Upload file .sql ke Google Drive,
                                        OneDrive, atau media penyimpanan eksternal seperti flashdisk.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <span class="badge bg-danger rounded-pill me-3 mt-1"
                                    style="min-width:28px; font-size:0.9rem;">3</span>
                                <div>
                                    <h6 class="fw-bold">Restore Jika Diperlukan</h6>
                                    <p class="text-muted mb-0" style="font-size:0.85rem;">Jika data hilang atau server
                                        bermasalah, gunakan file backup .sql untuk memulihkan data dalam hitungan detik.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection