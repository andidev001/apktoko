@extends('layouts.guest')
@section('content')
    <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
        <div class="card p-4">
            <div class="text-center mb-4">
                <h3 class="mb-2" style="color:#7367f0; font-weight:700;"><i class="fas fa-store"></i> AppToko</h3>
                <p class="text-muted">Silahkan login ke akun anda</p>
            </div>
            @if($errors->any())
                <div class="alert alert-danger p-2 fs-6">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                        placeholder="admin@toko.com" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password"
                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required>
                </div>
                <button class="btn btn-primary w-100 mb-3" type="submit">Sign in</button>
            </form>
        </div>
    </div>
@endsection