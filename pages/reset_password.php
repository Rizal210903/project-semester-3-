<?php
session_start();
require '../includes/config.php';

$message = "";
$showForm = false;
$token = $_GET['token'] ?? '';

if ($token) {
    $stmt = $pdo->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && strtotime($user['reset_expires']) > time()) {
        $showForm = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $password2 = $_POST['password2'] ?? '';

            if ($password === $password2) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
                $stmt->execute([$hash, $user['id']]);
                $message = "Password berhasil diubah. Silakan <a href='login.php'>login</a>.";
                $showForm = false;
            } else {
                $message = "Password dan konfirmasi tidak sama!";
            }
        }
    } else {
        $message = "Token tidak valid atau sudah kadaluarsa!";
    }
} else {
    $message = "Token tidak ditemukan!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Reset Password</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-3">Reset Password</h3>
    <?php if($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <?php if($showForm): ?>
    <form method="POST">
        <input type="password" name="password" class="form-control mb-2" placeholder="Password baru" required>
        <input type="password" name="password2" class="form-control mb-2" placeholder="Konfirmasi password" required>
        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
