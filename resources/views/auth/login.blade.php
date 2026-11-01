<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - KostKu</title>
    <meta name="description" content="Masuk ke Sistem Pengelolaan dan Pengaduan Penghuni Kos">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            color: #0f172a;
        }

        .login-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            padding: 40px 36px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 20px -2px rgba(16, 24, 40, 0.06);
        }

        .login-brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-icon-lg {
            width: 52px;
            height: 52px;
            background: #2563eb;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin: 0 auto 14px;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.2);
        }

        .login-brand h1 {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 4px;
            letter-spacing: -0.2px;
        }

        .login-brand p {
            font-size: 13px;
            color: #64748b;
            margin: 0;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            font-size: 13.5px;
            transition: all 0.15s ease;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .input-group-text {
            border-radius: 8px 0 0 8px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-right: 0;
            color: #64748b;
            font-size: 14px;
        }
        .input-group .form-control {
            border-radius: 0 8px 8px 0;
        }

        .btn-login {
            width: 100%;
            background: #2563eb;
            border: 1px solid #2563eb;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            color: white;
            transition: all 0.15s ease;
        }
        .btn-login:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: white;
        }

        .alert {
            border-radius: 8px;
            font-size: 13px;
            padding: 10px 14px;
            border: 1px solid transparent;
        }
        .alert-danger {
            background-color: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }
        .alert-success {
            background-color: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">
            <div class="brand-icon-lg"><i class="bi bi-house-door-fill"></i></div>
            <h1>Masuk ke KostKu</h1>
            <p>Sistem Pengelolaan & Pengaduan Kos</p>
        </div>

        @if($errors->any())
        <div class="alert alert-danger d-flex align-items-start gap-2 mb-3">
            <i class="bi bi-exclamation-circle-fill mt-1"></i>
            <div>
                @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email atau Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="oktri@gmail.com atau oktri"
                           autocomplete="username" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password"
                           placeholder="Masukkan kata sandi"
                           autocomplete="current-password" required>
                    <button type="button" class="btn btn-outline-secondary" id="togglePass" style="border-radius:0 8px 8px 0;border-left:0;border-color:#cbd5e1;color:#64748b;">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="form-check mb-0">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label text-muted" for="remember" style="font-size:12.5px;">Ingat saya</label>
                </div>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePass').addEventListener('click', function () {
            const pw = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pw.type === 'password') {
                pw.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                pw.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    </script>
</body>
</html>
