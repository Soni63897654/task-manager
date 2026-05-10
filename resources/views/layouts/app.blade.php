<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow - Manager</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; font-size: 0.95rem; }
        .navbar { background-color: #111827 !important; padding: 0.8rem 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-link { transition: color 0.3s ease; cursor: pointer; }
        .nav-link.active { color: #10b981 !important; font-weight: 600; }
        .main-wrapper { min-height: calc(100vh - 72px); padding: 40px 0; }
        .auth-card { border: none; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); background: #ffffff; }
        .error { color: #ef4444; font-size: 0.85rem; margin-top: 5px; display: block; } 
        
        /* Dropzone/Upload Styling (Existing styles maintained) */
        .upload-area { border: 2px dashed #cbd5e1; border-radius: 12px; padding: 30px; text-align: center; background: #fdfdfd; transition: 0.3s; }
        .upload-area:hover { border-color: #10b981; background: #f0fdf4; }
        .preview-img { width: 100%; height: 100px; object-fit: cover; border-radius: 8px; }
        .file-card { position: relative; transition: 0.3s; }
        .file-card:hover { transform: translateY(-3px); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="/"><i class="bi bi-layers-half me-2"></i>TaskFlow</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="/">Home</a></li>
                    
                    @auth
                        {{-- Dashboard Route --}}
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="/dashboard">Dashboard</a>
                        </li>
                        
                        {{-- Tasks Route --}}
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('tasks*') ? 'active' : '' }}" href="/tasks">Tasks</a>
                        </li>
                        
                        {{-- Documents/Upload Route (Existing) --}}
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('upload-files*') ? 'active' : '' }}" href="/upload-files">Documents</a>
                        </li>
                        
                        {{-- Logout --}}
                        <li class="nav-item ms-lg-3">
                            <form id="logout-form" action="/logout" method="POST" style="display: none;">@csrf</form>
                            <a class="nav-link text-danger" href="javascript:void(0)" onclick="handleLogout()">
                                 Logout
                            </a>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link {{ Request::is('login') ? 'active' : '' }}" href="/login">Login</a></li>
                        <li class="nav-item"><a class="nav-link {{ Request::is('register') ? 'active' : '' }}" href="/register">Register</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-wrapper">
        <div class="container">
            @yield('content')
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Logout Alert
        function handleLogout() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Your session will be closed!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#111827',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Logout!'
            }).then((result) => {
                if (result.isConfirmed) { 
                    document.getElementById('logout-form').submit(); 
                }
            })
        }
    </script>

    @stack('scripts')
</body>
</html>