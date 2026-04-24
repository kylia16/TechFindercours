<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFinder</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main {
            flex: 1; /* ← pousse le footer en bas */
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fa fa-search me-2"></i>TechFinder
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="/">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/web/competences">Compétences</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/web/users">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/web/interventions">Interventions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/web/user-competences">User Compétences</a>
                    </li>
                    @if(session('user'))
                    <li class="nav-item ms-2 dropdown">
                        <a class="btn btn-light text-primary fw-bold px-3 dropdown-toggle" href="#"
                        data-bs-toggle="dropdown">
                            <i class="fa fa-user me-1"></i>{{ session('user')['prenom_user'] }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item text-muted">{{ session('user')['role_user'] }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="/web/deconnexion">
                                    <i class="fa fa-sign-out-alt me-1"></i>Déconnexion
                                </a>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item ms-2">
                        <a class="btn btn-light text-primary fw-bold px-3" href="/web/connexion">
                            <i class="fa fa-sign-in-alt me-1"></i>Connexion
                        </a>
                    </li>
                @endif
                </ul>
            </div>
        </div>
    </nav>
{{-- TOAST NOTIFICATIONS --}}
@if(session('success'))
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="toast-success" class="toast align-items-center text-white bg-success border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="toast-error" class="toast align-items-center text-white bg-danger border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fa fa-times-circle me-2"></i>{{ session('error') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif
    <div class="main">
        @yield('main')
    </div>

    <footer class="bg-primary text-white text-center py-3">
        <p class="mb-0">&copy; 2026 TechFinder. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toasts = document.querySelectorAll('.toast');
        toasts.forEach(function (toastEl) {
            const toast = new bootstrap.Toast(toastEl, {
                delay: 3000,
                autohide: true
            });
            toast.show();
        });
    });
</script>
</html>
