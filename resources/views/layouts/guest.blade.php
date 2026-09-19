<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - App Toko</title>
    @php
        $faviconSettings = \App\Models\Setting::first();
        $faviconFile = ($faviconSettings && $faviconSettings->shop_logo) ? asset('storage/' . $faviconSettings->shop_logo) : asset('favicon.ico');
    @endphp
    <link rel="icon" type="image/png" href="{{ $faviconFile }}">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background-color: #f8f8f9;
        }

        .card {
            border: 0;
            box-shadow: 0 0.25rem 1.125rem rgba(75, 70, 92, 0.1);
            border-radius: 0.5rem;
        }

        .btn-primary {
            background-color: #7367f0;
            border-color: #7367f0;
        }

        .btn-primary:hover {
            background-color: #685dd8;
            border-color: #685dd8;
        }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>