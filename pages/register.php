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
    $nama_ortu = $_POST['nama_ortu'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validasi
    if (!$username || !$nama_ortu || !$email || !$password || !$confirm_password) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email tidak valid!";
    } else {
        // Cek apakah email sudah ada
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetchColumn() > 0) {
            $error = "Email '$email' sudah digunakan! Silakan gunakan email lain.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, nama_ortu, email, password) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $nama_ortu, $email, $hashed_password])) {
                ob_end_clean(); // Hapus output sebelum redirect
                header("Location: /project-semester-3-/pages/login.php");
                exit;
            } else {
                $error = "Registrasi gagal! Hubungi admin.";
                $errorInfo = $stmt->errorInfo();
                error_log("SQL Error: " . print_r($errorInfo, true));
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
    <title>Register - TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #E0F7FA; }
        .register-section { background: #fff; border: 2px solid #45B7D1; border-radius: 10px; padding: 20px; margin-top: 20px; }
        .error { color: red; font-size: 0.9rem; }
    </style>
</head>
<body>
    <main class="container-fluid p-0">
        <section class="py-5">
            <div class="container">
                <h1 class="text-center mb-5 text-primary animate__animated animate__fadeIn" style="font-family: 'Poppins', sans-serif;">Register</h1>
                <div class="register-section animate__animated animate__fadeInUp">
                    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama_ortu" class="form-label">Nama Lengkap Orang Tua</label>
                            <input type="text" class="form-control" id="nama_ortu" name="nama_ortu" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                        <p class="mt-3">Sudah punya akun? <a href="/project-semester-3-/pages/login.php">Login di sini</a></p>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>
    <?php ob_end_flush();  ?>
</body>
</html>