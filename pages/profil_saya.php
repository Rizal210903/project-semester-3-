<?php
include '../includes/header.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Ambil data user berdasarkan session
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<div class='alert alert-danger text-center mt-4'>Data pengguna tidak ditemukan.</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #E0F6FF;
        }
        .profile-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
            max-width: 600px;
            margin: 50px auto;
        }
        .profile-header {
            text-align: center;
        }
        .profile-header img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #4682B4;
            object-fit: cover;
        }
        .profile-header h3 {
            margin-top: 15px;
            color: #4682B4;
        }
        .profile-info {
            margin-top: 30px;
        }
        .profile-info .info-item {
            margin-bottom: 15px;
        }
        .profile-info label {
            font-weight: 600;
            color: #555;
        }
        .btn-edit {
            background-color: #4682B4;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
        }
        .btn-edit:hover {
            background-color: #315f86;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="profile-card">
        <div class="profile-header">
            <img src="<?= !empty($user['foto']) ? '../uploads/' . htmlspecialchars($user['foto']) : '../img/default_user.png' ?>" alt="Foto Profil">
            <h3><?= htmlspecialchars($user['username']) ?></h3>
            <p class="text-muted"><?= ucfirst(htmlspecialchars($user['role'])) ?></p>
        </div>

        <div class="profile-info">
            <div class="info-item">
                <label>Nama Orang Tua:</label>
                <p><?= isset($user['nama_ortu']) ? htmlspecialchars($user['nama_ortu']) : '-' ?></p>
            </div>
            <div class="info-item">
                <label>Email:</label>
                <p><?= isset($user['email']) ? htmlspecialchars($user['email']) : '-' ?></p>
            </div>
            <div class="info-item">
                <label>Tanggal Daftar:</label>
                <p><?= isset($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : '-' ?></p>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="edit_profil.php" class="btn btn-edit"><i class="bi bi-pencil"></i> Edit Profil</a>
        </div>
    </div>
</div>

</body>
</html>
