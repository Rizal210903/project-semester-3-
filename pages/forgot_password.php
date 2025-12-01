<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lupa Password</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;

        /* Background sama seperti register.php */
        background: linear-gradient(180deg, #f9fcff 0%, #e7f1ff 100%);

        font-family: 'Poppins', sans-serif;
    }

    .forget-card {
        width: 420px;
        padding: 40px;
        border-radius: 30px;

        /* Glass effect */
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(35px) saturate(180%);
        -webkit-backdrop-filter: blur(35px) saturate(180%);

        border: 1.5px solid rgba(255, 255, 255, 0.55);

        box-shadow:
            0 20px 50px rgba(0, 0, 0, 0.18),
            0 8px 16px rgba(0, 0, 0, 0.10);

        animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .title {
        text-align: center;
        font-size: 30px;
        font-weight: 700;
        margin-bottom: 25px;
        color: #0f1d55;
    }

    /* Input sama persis seperti register.php */
    .form-control {
        padding-left: 45px;
        height: 45px;
        border-radius: 14px;
        border: 1px solid rgba(0,0,0,0.10);
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        font-size: 15px;
    }

    .form-control:focus {
        background: rgba(255,255,255,0.8);
        border: 1px solid #0066ff;
        box-shadow: 0 0 8px rgba(0,110,255,0.3);
    }

    .icon-input {
        position: absolute;
        left: 15px;
        top: 10px;
        font-size: 20px;
        color: #324b81;
    }

    /* Tombol iOS style */
    .btn-primary {
        width: 100%;
        border-radius: 14px;
        height: 48px;
        font-size: 17px;
        font-weight: 600;
        background: #005BBB;
        border: none;
        transition: 0.25s;
    }

    .btn-primary:hover {
        background: #004099;
        transform: scale(1.03);
    }

    .footer-text {
        text-align: center;
        margin-top: 15px;
        font-size: 14px;
    }

    a {
        color: #005BBB;
        font-weight: 600;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>
</head>

<body>

<div class="forget-card">

    <div class="title">Lupa Password</div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info text-center"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">

        <label>Email Terdaftar</label>
        <div class="position-relative mb-4">
            <i class="bi bi-envelope-at icon-input"></i>
            <input type="email" name="email" class="form-control" placeholder="Masukkan email Anda" required>
        </div>

        <button type="submit" class="btn btn-primary">
            Kirim Link Reset
        </button>

        <p class="footer-text mt-3">
            Ingat password? <a href="login.php">Login di sini</a>
        </p>

    </form>

</div>

</body>
</html>
