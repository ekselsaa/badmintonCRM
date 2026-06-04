<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Habis - Anbiyaa Sport</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%230ea5e9%22 stroke-width=%222.8%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><path d=%22M9 16c0 1.66 1.34 3 3 3s3-1.34 3-3%22 fill=%22%230ea5e9%22 stroke=%22%230ea5e9%22 stroke-width=%222.8%22/><path d=%22M8 14.5h8%22 stroke=%22%232563eb%22 stroke-width=%223%22/><path d=%22M7.5 13.5L5 5%22 stroke-width=%222.8%22/><path d=%22M12 13.5V4%22 stroke-width=%222.8%22/><path d=%22M16.5 13.5L19 5%22 stroke-width=%222.8%22/><path d=%22M6 9.5h12%22 stroke-width=%221.8%22 opacity=%220.75%22/></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card-419 { background: #fff; border-radius: 20px; padding: 2.5rem; text-align: center; max-width: 420px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .icon-419 { font-size: 4rem; margin-bottom: 1rem; }
        .countdown { font-size: 1.5rem; font-weight: 700; color: #1a56db; }
    </style>
</head>
<body>
    <div class="card-419">
        <div class="icon-419">⏱️</div>
        <h4 class="fw-bold mb-2" style="color:#1e293b">Sesi Habis</h4>
        <p class="text-muted mb-3">Halaman ini telah kedaluwarsa karena tidak ada aktivitas. Anda akan diarahkan ke halaman login dalam <span class="countdown" id="count">3</span> detik.</p>
        <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4">
            Login Sekarang
        </a>
    </div>
    <script>
        var seconds = 3;
        var interval = setInterval(function() {
            seconds--;
            document.getElementById('count').textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = '{{ route("login") }}';
            }
        }, 1000);
    </script>
</body>
</html>
