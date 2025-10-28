<?php
session_start();

// === KONEKSI DATABASE ===
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// ======== PROSES TAMBAH ADMIN ========
if (isset($_POST['add_admin'])) {
    $new_username = trim($_POST['username']);
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $new_email = trim($_POST['email']);

    // Cek apakah username sudah ada
    $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $check->execute([$new_username]);
    if ($check->fetchColumn() > 0) {
        echo "<script>alert('Username sudah digunakan! Silakan pilih username lain.'); window.history.back();</script>";
        exit;
    }

    // Insert admin baru
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, nama_ortu, role) VALUES (?, ?, ?, '', 'admin')");
    $stmt->execute([$new_username, $new_password, $new_email]);
    header("Location: manage_admin.php");
    exit;
}

// === PROSES HAPUS ADMIN ===
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
    $stmt->execute([$id]);
    header("Location: manage_admin.php");
    exit;
}

// === PROSES UPDATE ADMIN ===
if (isset($_POST['update_admin'])) {
    $id = $_POST['id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=? AND role='admin'");
        $stmt->execute([$username, $email, $password, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET username=?, email=? WHERE id=? AND role='admin'");
        $stmt->execute([$username, $email, $id]);
    }
    header("Location: manage_admin.php");
    exit;
}

// === AMBIL DATA ADMIN ===
$stmt = $pdo->query("SELECT id, username, email, created_at FROM users WHERE role='admin' ORDER BY id DESC");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admin - TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #eaf6ff;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 60px;
            background-color: #007bff;
            display: flex;
            align-items: center;
            padding: 0 20px;
            color: #fff;
            z-index: 1000;
        }
        .header .menu-toggle {
            font-size: 24px;
            cursor: pointer;
            margin-right: 20px;
        }
        .header .title {
            font-size: 18px;
            font-weight: 600;
        }
        .header .icons {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Main Layout */
        .main-content {
            margin-left: 250px;
            padding: 90px 30px 30px;
            transition: margin-left 0.3s;
        }
        .main-content.collapsed {
            margin-left: 60px;
        }

        /* Box */
        .content-box {
            background-color: #dff4ff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Table */
        .table-container {
            background-color: #fff;
            border-radius: 10px;
            overflow-x: auto;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        thead {
            background-color: #f5faff;
        }

        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        th {
            color: #333;
            font-weight: 600;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-danger {
            background-color: #d62828;
            border: none;
        }

        .btn-warning {
            background-color: #fcbf49;
            border: none;
        }

        .button-group {
            display: flex;
            justify-content: end;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 80px 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="menu-toggle"><i class="bi bi-list"></i></div>
        <div class="title">KELOLA ADMIN</div>
        <div class="icons">
            <i class="bi bi-bell"></i>
            <i class="bi bi-envelope"></i>
            <i class="bi bi-person-circle"></i>
        </div>
    </div>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <h1 class="mb-4">Tabel Admin</h1>

        <div class="content-box">
            <div class="button-group">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">+ Tambah Admin</button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($admins)): ?>
                            <tr><td colspan="5" class="text-center">Belum ada admin</td></tr>
                        <?php else: ?>
                            <?php foreach ($admins as $i => $a): ?>
                            <tr>
                                <td><?= $i + 1; ?></td>
                                <td><?= htmlspecialchars($a['username']); ?></td>
                                <td><?= htmlspecialchars($a['email']); ?></td>
                                <td><?= htmlspecialchars($a['created_at']); ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editAdminModal<?= $a['id'] ?>">Edit</button>
                                    <a href="?delete=<?= $a['id'] ?>" onclick="return confirm('Yakin ingin hapus admin ini?')" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>

                            <!-- Modal Edit Admin -->
                            <div class="modal fade" id="editAdminModal<?= $a['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <form method="POST" class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Admin</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Username</label>
                                                <input type="text" name="username" value="<?= htmlspecialchars($a['username']); ?>" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" value="<?= htmlspecialchars($a['email']); ?>" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Password (kosongkan jika tidak diubah)</label>
                                                <input type="password" name="password" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="update_admin" class="btn btn-success">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Tambah Admin -->
    <div class="modal fade" id="addAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Admin Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_admin" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        });
    </script>
</body>
</html>
