<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - App Toko</title>
    @php
        $faviconSettings = \App\Models\Setting::first();
        $faviconFile = ($faviconSettings && $faviconSettings->shop_logo) ? asset('storage/' . $faviconSettings->shop_logo) : asset('favicon.ico');
    @endphp
    <link rel="icon" type="image/png" href="{{ $faviconFile }}">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        html {
            font-size: 13.5px;
        }

        body {
            font-family: 'Public Sans', sans-serif;
            background-color: #f8f8f9;
            overflow-x: hidden;
        }

        .sidebar {
            min-height: 100vh;
            background: #fff;
            box-shadow: 0 0.125rem 0.25rem rgba(165, 163, 174, 0.3);
            width: 260px;
            position: fixed;
        }

        .sidebar-brand {
            padding: 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: #7367f0;
            text-decoration: none;
            display: block;
        }

        .nav-link {
            color: #6f6b7d;
            padding: 0.625rem 1.5rem;
            margin: 0 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: rgba(115, 103, 240, 0.08);
            color: #7367f0;
        }

        .nav-link i {
            width: 1.5rem;
            text-align: center;
            margin-right: 0.5rem;
        }

        .main-content {
            margin-left: 260px;
            padding: 1.5rem 1.5rem 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: #fff;
            box-shadow: 0 0.125rem 0.25rem rgba(165, 163, 174, 0.3);
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            padding: 1rem 1.5rem;
        }

        .card {
            border: 0;
            box-shadow: 0 0.25rem 1.125rem rgba(75, 70, 92, 0.1);
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #d9d8e1;
            font-weight: 600;
        }

        .btn-primary {
            background-color: #7367f0;
            border-color: #7367f0;
        }

        .btn-primary:hover {
            background-color: #685dd8;
            border-color: #685dd8;
        }

        .text-primary {
            color: #7367f0 !important;
        }

        .bg-primary {
            background-color: #7367f0 !important;
        }

        /* Modern Global Dashboard Card Styling */
        .dash-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .dash-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.07);
        }

        .icon-box {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.35rem;
        }

        .text-title-small {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #7a7a7a;
        }

        .text-value {
            font-size: clamp(1rem, 1.8vw, 1.35rem);
            font-weight: 700;
            color: #2c3e50;
            word-wrap: break-word;
            line-height: 1.2;
        }

        .flex-min-width {
            min-width: 0;
            padding-right: 10px;
        }

        /* Subtle Tables & Lists */
        .table-modern td,
        .table-modern th {
            border-color: #f6f6f6;
            vertical-align: middle;
        }

        .table-modern th {
            font-weight: 600;
            color: #7a7a7a;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .list-hover:hover {
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        /* Icon Box Colors */
        .bg-light-primary {
            background-color: rgba(115, 103, 240, 0.16) !important;
            color: #7367f0 !important;
        }

        .bg-light-success {
            background-color: rgba(40, 199, 111, 0.16) !important;
            color: #28c76f !important;
        }

        .bg-light-info {
            background-color: rgba(0, 207, 232, 0.16) !important;
            color: #00cfe8 !important;
        }

        .bg-light-warning {
            background-color: rgba(255, 159, 67, 0.16) !important;
            color: #ff9f43 !important;
        }
    </style>
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
    @stack('styles')
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <i class="fas fa-store"></i> AppToko
            </a>
            <ul class="nav flex-column mt-3">
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                @if(auth()->check() && auth()->user()->role === 'admin')
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}"
                        href="{{ route('products.index') }}">
                        <i class="fas fa-box"></i> Data Barang
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('products.stock') ? 'active' : '' }}"
                        href="{{ route('products.stock') }}">
                        <i class="fas fa-boxes-stacked"></i> Stok Barang
                    </a>
                </li>
                @endif
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('transactions.index') || request()->routeIs('transactions.paymentForm') ? 'active' : '' }}"
                        href="{{ route('transactions.index') }}">
                        <i class="fas fa-shopping-cart"></i> Transaksi Penjualan
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" data-bs-toggle="collapse"
                        href="#laporanMenu" role="button"
                        aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}">
                        <i class="fas fa-chart-line"></i> Laporan
                        <i class="fas fa-chevron-down float-end mt-1" style="font-size: 0.8rem;"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('reports.*') ? 'show' : '' }}" id="laporanMenu">
                        <ul class="nav flex-column ms-2 mt-1">
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('reports.index') ? 'active fw-bold' : '' }}"
                                    href="{{ route('reports.index') }}" style="font-size: 0.9em; padding-left: 20px;">
                                    <i class="fas fa-list-ul me-1"></i> Laporan Transaksi
                                </a>
                            </li>
                            @if(auth()->check() && auth()->user()->role === 'admin')
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('reports.rekap') ? 'active fw-bold' : '' }}"
                                    href="{{ route('reports.rekap') }}" style="font-size: 0.9em; padding-left: 20px;">
                                    <i class="fas fa-print me-1"></i> Rekap & Cetak
                                </a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="nav-link {{ request()->routeIs('reports.keuntungan') ? 'active fw-bold' : '' }}"
                                    href="{{ route('reports.keuntungan') }}" style="font-size: 0.9em; padding-left: 20px;">
                                    <i class="fas fa-chart-pie me-1"></i> Laporan Keuntungan
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @if(auth()->check() && auth()->user()->role === 'admin')
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}"
                        href="{{ route('settings.index') }}">
                        <i class="fas fa-cogs"></i> Pengaturan Toko
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                        href="{{ route('users.index') }}">
                        <i class="fas fa-users"></i> Pengaturan User
                    </a>
                </li>
                <li class="nav-item mb-1 mt-2 border-top pt-2">
                    <a class="nav-link {{ request()->routeIs('backup.index') ? 'active' : '' }}"
                        href="{{ route('backup.index') }}">
                        <i class="fas fa-database"></i> Backup & Restore
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <nav class="navbar d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 text-muted">@yield('title')</h5>
                </div>
                <div>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                            id="dropdownUser" data-bs-toggle="dropdown">
                            @if(Auth::user()->photo)
                                <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="User Photo" class="rounded-circle me-2" style="width: 35px; height: 35px; object-fit: cover;">
                            @else
                                <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                            @endif
                            <strong>{{ Auth::user()->name }}</strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-cog me-2"></i> Profil Saya
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item" type="submit"><i class="fas fa-sign-out-alt me-2"></i>
                                        Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>



            @yield('content')

            <!-- App Footer -->
            <footer class="footer mt-auto py-3 text-center text-muted" style="font-size: 0.85rem;">
                <span>&copy; {{ date('Y') }} <strong>Mitra digti | Andi Dev</strong>. All rights reserved.</span>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function () {
            document.body.classList.toggle('sidebar-collapsed');
        });

        // Global SweetAlert Toast Setup
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        @if(session('success'))
            Toast.fire({ icon: 'success', title: '{{ session("success") }}' });
        @endif

        @if(session('error'))
            Toast.fire({ icon: 'error', title: '{{ session("error") }}' });
        @endif

            @if($errors->any())
                let errorMsg = '';
                @foreach($errors->all() as $error)
                    errorMsg += '{{ $error }}<br>';
                @endforeach
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: errorMsg
                });
            @endif

        // Form Delete/Cancel Confirmations Global Handler
        $(document).on('submit', '.form-confirm', function (e) {
            e.preventDefault();
            let form = $(this);
            let message = form.data('confirm-message') || 'Anda yakin ingin melanjutkan?';

            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form[0].submit();
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>