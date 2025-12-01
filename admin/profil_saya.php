<?php
session_start();
$error = "";
$success = "";

// --- Pastikan admin login ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

include __DIR__ . "/../includes/config.php";

$admin_id = $_SESSION['user_id'];

// --- Ambil data admin ---
$stmt = $conn->prepare("SELECT username, email, foto FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

if (!$admin) {
    die("Data admin tidak ditemukan.");
}

// --- Proses update profil ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $foto     = $admin['foto']; // default foto saat ini

    // Upload foto baru jika ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . "/../uploads/admin/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $filename = "admin_" . time() . "_" . uniqid() . "." . $ext;
        $target = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
            $foto = $filename;
        } else {
            $error = "Gagal mengunggah foto baru.";
        }
    }

    if (!$error) {
        $update = $conn->prepare("UPDATE users SET username = ?, email = ?, foto = ? WHERE id = ?");
        $update->bind_param("sssi", $username, $email, $foto, $admin_id);

        if ($update->execute()) {
            $success = "Profil berhasil diperbarui.";
            $admin['username'] = $username;
            $admin['email'] = $email;
            $admin['foto'] = $foto;
        } else {
            $error = "Gagal memperbarui profil: " . $update->error;
        }

        $update->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; background: #f5f6fa; }
.profile-container { max-width: 500px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
.profile-img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin-bottom: 20px; }
</style>
</head>
<body>

<div class="profile-container text-center">
    <h3 class="mb-4">Profil Admin</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <img src="<?= htmlspecialchars($admin['foto'] ?? 'https://via.placeholder.com/120') ?>" alt="Foto Admin" class="profile-img">

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3 text-start">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($admin['username'] ?? '') ?>" required>
        </div>

        <div class="mb-3 text-start">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required>
        </div>

        <div class="mb-3 text-start">
            <label class="form-label">Foto Profil</label>
            <input type="file" name="foto" class="form-control" accept="image/*">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti foto</small>
        </div>

        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
