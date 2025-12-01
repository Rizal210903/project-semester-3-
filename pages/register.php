<?php
ob_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) { die("Koneksi gagal: " . mysqli_connect_error()); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $nama_ortu = $_POST['nama_ortu'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$username || !$nama_ortu || !$email || !$password || !$confirm_password) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Password tidak cocok!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email tidak valid!";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $count);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($count > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, nama_ortu, email, password, role) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssss", $username, $nama_ortu, $email, $hashed_password, $role);

            if (mysqli_stmt_execute($stmt)) {
                ob_end_clean();
                header("Location: login.php");
                exit;
            } else {
                $error = "Pendaftaran gagal!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Akun</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: linear-gradient(180deg, #f9fcff 0%, #e7f1ff 100%);
        font-family: 'Poppins', sans-serif;
    }

    .register-card {
        width: 450px;
        padding: 40px;
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(35px) saturate(180%);
        border: 1.5px solid rgba(255, 255, 255, 0.55);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18);
        transition: all 0.3s ease;
    }

    .register-card:hover {
        background: rgba(255, 255, 255, 0.32);
        border: 1.5px solid rgba(255, 255, 255, 0.7);
        transform: scale(1.015);
    }

    .form-control {
        padding-left: 45px;
        padding-right: 45px;
        height: 45px;
        border-radius: 12px;
    }

    .icon-input {
        position: absolute;
        left: 15px;
        top: 10px;
        font-size: 20px;
        color: #324b81;
    }

    .toggle-eye {
        position: absolute;
        right: 15px;
        top: 10px;
        font-size: 20px;
        cursor: pointer;
        color: #324b81;
    }

    .btn-primary {
        border-radius: 12px;
        height: 50px;
        font-size: 18px;
        background: #005BBB;
        transition: 0.3s;
    }

    .btn-primary:hover {
        background: #004099;
        transform: scale(1.03);
    }

    .title {
        font-size: 32px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 25px;
        color: #0f1d55;
    }

    .error {
        color: red;
        font-size: 0.9rem;
        text-align: center;
    }
</style>
</head>

<body>

<div class="register-card">
    <div class="title">Daftar Akun</div>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Username</label>
        <div class="position-relative mb-3">
            <i class="bi bi-person icon-input"></i>
            <input type="text" name="username" class="form-control" required>
        </div>

        <label>Nama Lengkap Orang Tua</label>
        <div class="position-relative mb-3">
            <i class="bi bi-people icon-input"></i>
            <input type="text" name="nama_ortu" class="form-control" required>
        </div>

        <label>Email</label>
        <div class="position-relative mb-3">
            <i class="bi bi-envelope icon-input"></i>
            <input type="email" name="email" class="form-control" required>
        </div>

        <!-- PASSWORD DENGAN SHOW/HIDE -->
        <label>Password</label>
        <div class="position-relative mb-3">
            <i class="bi bi-lock icon-input"></i>
            <input type="password" id="password" name="password" class="form-control" required>
            <i class="bi bi-eye toggle-eye" onclick="togglePassword('password', this)"></i>
        </div>

        <!-- CONFIRM PASSWORD DENGAN SHOW/HIDE -->
        <label>Konfirmasi Password</label>
        <div class="position-relative mb-4">
            <i class="bi bi-check-circle icon-input"></i>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            <i class="bi bi-eye toggle-eye" onclick="togglePassword('confirm_password', this)"></i>
        </div>

        <button class="btn btn-primary w-100" type="submit">Daftar Sekarang</button>

        <p class="text-center mt-3">
            Sudah punya akun? <a href="login.php" class="fw-bold">Login di sini</a>
        </p>
    </form>
</div>

<!-- JS SHOW/HIDE PASSWORD -->
<script>
function togglePassword(id, icon) {
    const field = document.getElementById(id);
    if (field.type === "password") {
        field.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    } else {
        field.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
}
</script>

</body>
</html>
