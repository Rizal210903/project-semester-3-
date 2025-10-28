<?php
session_start();
ob_start(); // Mulai output buffering
include '../includes/header.php';

// Konfigurasi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Koneksi gagal: " . $e->getMessage());
    die("Koneksi ke database gagal!");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Ambil data user berdasarkan username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Simpan data sesi pengguna
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        ob_end_clean(); // Bersihkan buffer sebelum redirect

        // Arahkan berdasarkan role
        if ($user['role'] === 'admin') {
            header("Location: /project-semester-3-/admin/admin_dashboard.php");
        } else {
            header("Location: /project-semester-3-/pages/index.php");
        }
        exit;
    } else {
        $error = "Username atau password salah!";
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #E0F7FA;
        }
        .login-section {
            background: #fff;
            border: 2px solid #45B7D1;
            border-radius: 10px;
            padding: 30px;
            margin-top: 40px;
            max-width: 450px;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .login-section h1 {
            color: #0077b6;
            text-align: center;
            margin-bottom: 25px;
            font-weight: 700;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-primary {
            background-color: #0077b6;
            border: none;
        }
        .btn-primary:hover {
            background-color: #005f87;
        }
        .error {
            color: red;
            font-size: 0.9rem;
            text-align: center;
        }
    </style>
</head>
<body>

<main class="container-fluid p-0">
    <section class="py-5">
        <div class="container">
            <div class="login-section animate__animated animate__fadeInUp">
                <h1>Login</h1>
                <?php if ($error): ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                    <p class="mt-3 text-center">Belum punya akun? <a href="/project-semester-3-/pages/register.php">Daftar di sini</a></p>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
<?php ob_end_flush(); ?>
</body>
</html>
