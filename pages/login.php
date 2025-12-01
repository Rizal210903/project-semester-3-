<?php
session_start();
ob_start();

// ---------------------------
// Konfigurasi DB
// ---------------------------
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi ke database gagal!");
}

$error = '';
$loginValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $loginInput = trim($_POST['login'] ?? '');
    $passwordInput = $_POST['password'] ?? '';

    $loginValue = htmlspecialchars($loginInput, ENT_QUOTES, 'UTF-8');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :login OR email = :login LIMIT 1");
    $stmt->execute(['login' => $loginInput]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($passwordInput, $user['password'])) {

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        ob_end_clean();

        if ($user['role'] === 'admin') {
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_id'] = $user['id'];
            header("Location: /project-semester-3-/admin/admin_dashboard.php");
            exit;
        }

        header("Location: /project-semester-3-/pages/index.php");
        exit;

    } else {
        $error = "Username/email atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - TK Pertiwi</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    body {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: linear-gradient(180deg, #f9fcff 0%, #e7f1ff 100%);
        font-family: 'Poppins', sans-serif;
    }

    .login-card {
        width: 450px;
        padding: 40px;
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(30px);
        -webkit-backdrop-filter: blur(30px);
        border: 1.5px solid rgba(255, 255, 255, 0.55);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18);
        transition: .3s ease;
    }

    .login-card:hover {
        background: rgba(255, 255, 255, 0.32);
        border: 1.5px solid rgba(255, 255, 255, 0.75);
        transform: scale(1.015);
    }

    .login-card h2 {
        font-weight: 700;
        text-align: center;
        margin-bottom: 25px;
        color: #003366;
    }

    .form-control {
        padding-left: 45px;
        height: 45px;
        border-radius: 12px;
        border: 1px solid #ccc;
        font-size: 16px;
        background: rgba(255, 255, 255, 0.55);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    .form-control:focus {
        border: 1px solid #0066ff;
        background: rgba(255, 255, 255, 0.75);
        box-shadow: 0 0 8px rgba(0, 110, 255, 0.3);
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

    .btn-login {
        width: 100%;
        height: 50px;
        border-radius: 12px;
        background: #005BBB;
        color: white;
        font-size: 18px;
        font-weight: 600;
        border: none;
        transition: .25s ease;
    }

    .btn-login:hover {
        background: #004099;
        transform: scale(1.03);
    }

    .error {
        text-align: center;
        color: red;
        margin-bottom: 10px;
    }
</style>
</head>

<body>

<div class="login-card">
    <h2>Login Akun</h2>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Username / Email</label>
        <div class="position-relative mb-3">
            <i class="bi bi-person icon-input"></i>
            <input type="text" name="login" class="form-control" required
                   value="<?= $loginValue ?>">
        </div>

        <label>Password</label>
        <div class="position-relative mb-4">
            <i class="bi bi-lock icon-input"></i>

            <input type="password" name="password" id="passwordField" class="form-control" required>

            <!-- Show / Hide Password -->
            <i class="bi bi-eye toggle-eye" id="togglePassword"></i>
        </div>

        <button class="btn-login" type="submit">Login</button>

        <p class="text-center mt-3">
            Lupa password? <a href="/project-semester-3-/pages/forgot_password.php">Klik di sini</a>
        </p>

        <p class="text-center">
            Belum punya akun? <a href="register.php" class="fw-bold">Daftar di sini</a>
        </p>

    </form>
</div>

<!-- SCRIPT SHOW/HIDE PASSWORD -->
<script>
document.getElementById("togglePassword").addEventListener("click", function () {
    const passwordField = document.getElementById("passwordField");

    const type = passwordField.type === "password" ? "text" : "password";
    passwordField.type = type;

    this.classList.toggle("bi-eye");
    this.classList.toggle("bi-eye-slash");
});
</script>

<?php ob_end_flush(); ?>
</body>
</html>
