<?php
ob_start(); // Mulai output buffering
include '../includes/header.php';


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Koneksi gagal: " . $e->getMessage());
    die("Koneksi gagal: " . $e->getMessage());
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        ob_end_clean(); // Hapus output sebelum redirect
        header("Location: /project-semester-3-/pages/pendaftaran.php");
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
        body { font-family: 'Poppins', sans-serif; background: #E0F7FA; }
        .login-section { background: #fff; border: 2px solid #45B7D1; border-radius: 10px; padding: 20px; margin-top: 20px; }
        .error { color: red; font-size: 0.9rem; }
    </style>
</head>
<body>
    <main class="container-fluid p-0">
        <section class="py-5">
            <div class="container">
                <h1 class=" color: text-center mb-5 text-primary animate__animated animate__fadeIn" style="font-family: #000080;'Poppins', sans-serif;">Login</h1>
                <div class="login-section animate__animated animate__fadeInUp">
                    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                        <p class="mt-3">Belum punya akun? <a href="/project-semester-3-/pages/register.php">Register di sini</a></p>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>
    <?php ob_end_flush(); // Akhiri buffering dan kirim output ?>
</body>
</html>