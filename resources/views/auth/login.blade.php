<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Management Catering</title>
    <link href="{{ asset('sbadmin') }}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('sbadmin') }}/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary d-flex align-items-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <div class="text-center">
                                <div class="mb-3 text-center">
                                    <img src="{{ asset('logologin.png') }}" alt="Logo" class="img-fluid"
                                        style="max-height: 80px; width: auto;">
                                </div>
                                <h1 class="h4 text-gray-900 font-weight-bold mb-4">Management Catering</h1>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger py-2">
                                    <small>{{ $errors->first() }}</small>
                                </div>
                            @endif

                            <form action="{{ route('login.post') }}" method="POST" class="user">
                                @csrf
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control form-control-user"
                                        placeholder="Masukkan Email..." value="{{ old('email') }}" required autofocus>
                                </div>
                                <div class="form-group">
                                    <div class="position-relative">
                                        <input type="password" name="password" id="password"
                                            class="form-control form-control-user" placeholder="Password" required
                                            style="padding-right: 3rem;">
                                        <span id="togglePassword" role="button" aria-label="Tampilkan password"
                                            style="position: absolute; top: 50%; right: 20px; transform: translateY(-50%); cursor: pointer; color: #858796;">
                                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" name="remember" class="custom-control-input"
                                            id="customCheck">
                                        <label class="custom-control-label" for="customCheck">Ingat Saya</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block font-weight-bold">
                                    Login
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const passwordInput = document.getElementById('password');
        const toggleBtn = document.getElementById('togglePassword');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        toggleBtn.addEventListener('click', function() {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            toggleIcon.classList.toggle('fa-eye', !isHidden);
            toggleIcon.classList.toggle('fa-eye-slash', isHidden);
            toggleBtn.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
        });
    </script>
</body>

</html>
