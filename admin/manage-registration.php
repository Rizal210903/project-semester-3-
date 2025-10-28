<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /project-semester-3-/pages/login.php');
    exit;
}

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

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status_pendaftaran'];
    try {
        $stmt = $pdo->prepare("UPDATE pendaftaran SET status_pendaftaran = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        $_SESSION['success'] = "Status pendaftaran berhasil diperbarui!";
        header('Location: /project-semester-3-/admin/manage_registrations.php');
        exit;
    } catch(PDOException $e) {
        $error = "Gagal memperbarui status: " . $e->getMessage();
    }
}

// Ambil data pendaftaran
$stmt = $pdo->query("SELECT * FROM pendaftaran ORDER BY tanggal_daftar DESC");
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pendaftaran - TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #E0F7FA; }
        .content { margin-left: 250px; padding: 20px; }
        .card { border-radius: 15px; }
        @media (max-width: 768px) { .content { margin-left: 0; } }
    </style>
</head>
<body>
    <div class="content">
        <h1 class="text-center mb-4 text-primary">Kelola Pendaftaran</h1>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Daftar Pendaftaran</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama Anak</th>
                            <th>Nama Orang Tua</th>
                            <th>Tanggal Daftar</th>
                            <th>Status Pendaftaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registrations as $registration): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($registration['nama_anak']); ?></td>
                                <td><?php echo htmlspecialchars($registration['nama_ortu']); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($registration['tanggal_daftar'])); ?></td>
                                <td>
                                    <span class="badge <?php echo $registration['status_pendaftaran'] === 'diterima' ? 'bg-success' : ($registration['status_pendaftaran'] === 'ditolak' ? 'bg-danger' : 'bg-warning'); ?>">
                                        <?php echo htmlspecialchars(ucfirst($registration['status_pendaftaran'] ?? 'Menunggu')); ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="id" value="<?php echo $registration['id']; ?>">
                                        <select name="status_pendaftaran" class="form-select form-select-sm d-inline w-auto">
                                            <option value="menunggu" <?php echo ($registration['status_pendaftaran'] ?? 'menunggu') === 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                            <option value="diterima" <?php echo ($registration['status_pendaftaran'] ?? '') === 'diterima' ? 'selected' : ''; ?>>Diterima</option>
                                            <option value="ditolak" <?php echo ($registration['status_pendaftaran'] ?? '') === 'ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-primary btn-sm">Perbarui</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>