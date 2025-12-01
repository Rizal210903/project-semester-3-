<?php
session_start();
include '../includes/config.php'; // Sesuaikan path

// Pastikan admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../pages/profil_saya.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$error = "";
$success = "";

// --- Ambil data admin ---
$stmt = $conn->prepare("SELECT username, nama, email, foto FROM admins WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Admin tidak ditemukan!");
}

$admin = $result->fetch_assoc();
$stmt->close();

// --- Update profil ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? $admin['nama'];
    $email = $_POST['email'] ?? $admin['email'];

    // Update foto jika ada upload
    $fotoFile = $_FILES['foto'] ?? null;
    $fotoPath = $admin['foto'];

    if ($fotoFile && $fotoFile['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($fotoFile['name'], PATHINFO_EXTENSION);
        $newFoto = "admin_" . time() . "." . $ext;
        $uploadDir = "../../uploads/admins/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        if (move_uploaded_file($fotoFile['tmp_name'], $uploadDir . $newFoto)) {
            $fotoPath = $newFoto;
        } else {
            $error = "Gagal upload foto.";
        }
    }

    if (!$error) {
        $update = $conn->prepare("UPDATE admins SET nama = ?, email = ?, foto = ? WHERE id = ?");
        $update->bind_param("sssi", $nama, $email, $fotoPath, $admin_id);
        if ($update->execute()) {
            $success = "Profil berhasil diperbarui.";
            $admin['nama'] = $nama;
            $admin['email'] = $email;
            $admin['foto'] = $fotoPath;
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; background: #f0f2f5; }
.card-profile { max-width: 600px; margin: 50px auto; padding: 30px; border-radius: 15px; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
.profile-img { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; margin-bottom: 15px; }
</style>
</head>
<body>

<div class="card-profile text-center">
    <h3 class="mb-4">Profil Admin</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <img src="<?= $admin['foto'] ? '../../uploads/admins/' . $admin['foto'] : 'https://via.placeholder.com/120' ?>" alt="Foto Admin" class="profile-img">

        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($admin['nama']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Foto Profil</label>
            <input type="file" name="foto" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
