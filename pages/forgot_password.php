<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #E0F7FA;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            background: #fff;
            border: 2px solid #45B7D1;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 35px;
            animation: fadeIn 0.8s ease;
            max-width: 420px;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .title {
            font-weight: 700;
            color: #0077b6;
        }
        .btn-primary {
            width: 100%;
            background-color: #0077b6;
            border: none;
            padding: 10px;
            border-radius: 10px;
        }
        .btn-primary:hover {
            background-color: #005f87;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
        }
        .footer-text {
            margin-top: 15px;
            font-size: 14px;
            color: #444;
        }
        a {
            color: #0077b6;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="card col-md-4 col-10 text-center">
        <h3 class="title mb-3">Lupa Password</h3>
        <?php if(!empty($message)): ?>
            <div class="alert alert-info">{{message}}</div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" class="form-control mb-3" placeholder="Masukkan email Anda" required>
            <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
        </form>

        <div class="footer-text">Ingat password? <a href="login.php">Login</a></div>
    </div>
</body>
</html>
