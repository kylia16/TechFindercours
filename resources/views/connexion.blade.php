@extends('template')

@section('main')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="fa fa-sign-in-alt me-2"></i>Connexion</h4>
                </div>
                <div class="card-body p-4">

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="/web/connexion" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Login</label>
                            <input type="text" name="login_user" class="form-control"
                                   placeholder="Votre login" required
                                   value="{{ old('login_user') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mot de passe</label>
                            <input type="password" name="password_user" class="form-control"
                                   placeholder="Votre mot de passe" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-sign-in-alt me-2"></i>Se connecter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
