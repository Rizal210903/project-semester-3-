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
<?php
// Pastikan sudah ambil data profil admin
// $admin['username'], $admin['email'], $admin['foto']
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Saya</title>

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

    .profile-card {
        width: 500px;
        padding: 40px;
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(30px);
        -webkit-backdrop-filter: blur(30px);
        border: 1.5px solid rgba(255, 255, 255, 0.55);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18);
        transition: .3s ease;
    }

    .profile-card:hover {
        background: rgba(255, 255, 255, 0.32);
        border: 1.5px solid rgba(255, 255, 255, 0.75);
        transform: scale(1.015);
    }

    .profile-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid rgba(255,255,255,0.8);
        margin-bottom: 20px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
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

    .btn-save {
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

    .btn-save:hover {
        background: #004099;
        transform: scale(1.03);
    }

    .btn-back {
        width: 100%;
        height: 45px;
        border-radius: 10px;
        background: #cccccc;
        font-weight: 600;
        transition: .25s ease;
    }

    .btn-back:hover {
        background: #b9b9b9;
    }

</style>
</head>

<body>

<div class="profile-card text-center">
    <h2 class="fw-bold mb-3" style="color:#003366;">Profil Saya</h2>

    <?php if ($error ?? false): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success ?? false): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <img src="<?= htmlspecialchars($admin['foto'] ?? 'https://via.placeholder.com/120') ?>"
         class="profile-img" alt="Foto Profil">

    <form method="POST" enctype="multipart/form-data">

        <!-- USERNAME -->
        <label class="fw-semibold">Username</label>
        <div class="position-relative mb-3">
            <i class="bi bi-person icon-input"></i>
            <input type="text" name="username" class="form-control"
                   value="<?= htmlspecialchars($admin['username']) ?>" required>
        </div>

        <!-- EMAIL -->
        <label class="fw-semibold">Email</label>
        <div class="position-relative mb-3">
            <i class="bi bi-envelope icon-input"></i>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($admin['email']) ?>" required>
        </div>

        <!-- FOTO -->
        <label class="fw-semibold">Foto Profil Baru</label>
        <div class="position-relative mb-3">
            <i class="bi bi-image icon-input"></i>
            <input type="file" name="foto" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn-save mt-2">Simpan Perubahan</button>

        <button type="button" onclick="window.location.href='admin_dashboard.php'" 
                class="btn btn-back mt-3">Kembali</button>

    </form>
</div>

</body>
</html>

