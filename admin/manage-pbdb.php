<?php
include '../includes/config.php';
include 'sidebar.php';

// 🔹 Proses perubahan status PPDB oleh admin
if (isset($_POST['ubah_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE pendaftaran SET status_ppdb=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    echo "<script>window.location='manage-ppdb.php';</script>";
    exit;
}

// 🔹 Ambil data pendaftar
$result = $conn->query("SELECT * FROM pendaftaran ORDER BY tanggal_daftar DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola PPDB - TK Pertiwi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
  background: #eaf6ff;
  font-family: 'Poppins', sans-serif;
}
.main-content {
  margin-left: 250px;
  padding: 90px 30px;
}
.table-container {
  background: #fff;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}
.table th {
  background: #007bff;
  color: #fff;
  font-weight: 600;
}
.table td, .table th {
  vertical-align: middle;
}
.badge {
  font-size: 0.9rem;
  padding: 6px 10px;
  border-radius: 8px;
}
.btn-action {
  font-size: 0.8rem;
  border-radius: 6px;
}
@media (max-width: 768px) {
  .main-content { margin-left: 0; padding: 80px 15px; }
}
</style>
</head>
<body>

<!-- Header -->
<div class="header bg-primary text-white p-3 fixed-top">
  <h4 class="m-0"><i class="bi bi-people-fill me-2"></i>Kelola Pendaftaran Siswa Baru (PPDB)</h4>
</div>

<main class="main-content">
  <div class="table-container">
    <h5 class="fw-bold mb-4">Data Pendaftar</h5>

    <table class="table table-bordered align-middle text-center">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Anak</th>
          <th>Nama Orang Tua</th>
          <th>Tanggal Daftar</th>
          <th>Kartu Keluarga</th>
          <th>Akta Kelahiran</th>
          <th>Pas Foto</th>
          <th>Surat Sehat</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): $no = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama_anak']); ?></td>
            <td><?= htmlspecialchars($row['nama_ortu']); ?></td>
            <td><?= date('d M Y', strtotime($row['tanggal_daftar'])); ?></td>
            
            <!-- Dokumen -->
            <td>
              <?php if ($row['kartu_keluarga']): ?>
                <a href="../uploads/<?= $row['kartu_keluarga']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-eye"></i> Lihat
                </a>
              <?php else: ?>
                <span class="text-muted">Belum ada</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($row['akta_kelahiran']): ?>
                <a href="../uploads/<?= $row['akta_kelahiran']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-eye"></i> Lihat
                </a>
              <?php else: ?>
                <span class="text-muted">Belum ada</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($row['pas_foto']): ?>
                <a href="../uploads/<?= $row['pas_foto']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-eye"></i> Lihat
                </a>
              <?php else: ?>
                <span class="text-muted">Belum ada</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($row['surat_sehat']): ?>
                <a href="../uploads/<?= $row['surat_sehat']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-eye"></i> Lihat
                </a>
              <?php else: ?>
                <span class="text-muted">Belum ada</span>
              <?php endif; ?>
            </td>

            <!-- Status PPDB -->
            <td>
              <span class="badge 
                <?= $row['status_ppdb'] == 'diterima' ? 'bg-success' : 
                   ($row['status_ppdb'] == 'pending' ? 'bg-warning text-dark' : 'bg-danger'); ?>">
                <?= ucfirst($row['status_ppdb']); ?>
              </span>
            </td>

            <!-- Aksi -->
            <td>
              <form method="POST" class="d-inline">
                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                <select name="status" class="form-select form-select-sm mb-2">
                  <option value="pending" <?= $row['status_ppdb']=='pending'?'selected':''; ?>>Pending</option>
                  <option value="diterima" <?= $row['status_ppdb']=='diterima'?'selected':''; ?>>Diterima</option>
                  
                </select>
                <button type="submit" name="ubah_status" class="btn btn-primary btn-action w-100">
                  <i class="bi bi-check2-circle"></i> Simpan
                </button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="10" class="text-muted">Belum ada data pendaftar.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
