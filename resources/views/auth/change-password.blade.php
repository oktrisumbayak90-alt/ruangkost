<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Kata Sandi - KostKu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            color: #0f172a;
        }
        .card {
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px -2px rgba(16, 24, 40, 0.06);
            max-width: 420px;
            width: 100%;
            background: #ffffff;
            padding: 36px 32px;
        }
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 10px 12px;
            font-size: 13.5px;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }
        .btn-primary {
            background: #2563eb;
            border: 1px solid #2563eb;
            border-radius: 8px;
            padding: 10px 16px;
            font-weight: 600;
            font-size: 14px;
        }
        .btn-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }
        .icon-badge {
            width: 50px;
            height: 50px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #2563eb;
            margin: 0 auto 12px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="text-center mb-4">
            <div class="icon-badge">
                <i class="bi bi-key"></i>
            </div>
            <h2 style="font-weight:700;font-size:18px;color:#0f172a;margin-bottom:4px;">Ganti Kata Sandi</h2>
            <p class="text-muted mb-0" style="font-size:13px;">Untuk keamanan akun, silakan buat kata sandi baru Anda.</p>
        </div>

        @if($errors->any())
        <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:12.5px;border-radius:8px;">
            @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('password.update.forced') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Kata Sandi Baru</label>
                <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" minlength="8" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label">Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-check-circle me-1"></i> Simpan Kata Sandi
            </button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
