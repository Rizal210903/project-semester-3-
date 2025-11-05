<?php
include '../includes/config.php';
include 'sidebar.php';

// Update otomatis: jika bukti pembayaran ada, ubah status jadi 'dibayar'
$conn->query("UPDATE pendaftaran 
              SET status_pembayaran='dibayar' 
              WHERE bukti_pembayaran IS NOT NULL 
              AND status_pembayaran!='dibayar'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Pembayaran - TK Pertiwi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    background-color: #f1f7fd;
    font-family: 'Poppins', sans-serif;
}
.header {
    position: fixed;
    top: 0; left: 0; right: 0;
    height: 60px;
    background-color: #007bff;
    color: #fff;
    display: flex;
    align-items: center;
    padding: 0 25px;
    z-index: 1000;
    font-weight: 600;
}
.main-content {
    margin-left: 250px;
    padding: 100px 30px;
}
@media (max-width: 768px) {
    .main-content { margin-left: 0; padding: 90px 20px; }
}

/* Container */
.card-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    padding: 25px;
}

/* Table */
.table thead {
    background-color: #e9f2ff;
}
.table th {
    font-weight: 600;
    color: #333;
}
.table td {
    vertical-align: middle;
}
.table img {
    border-radius: 8px;
    cursor: pointer;
    width: 80px;
    height: 60px;
    object-fit: cover;
    transition: transform 0.2s;
}
.table img:hover {
    transform: scale(1.1);
}

/* Badges */
.badge-status {
    font-weight: 600;
    border-radius: 10px;
    padding: 6px 10px;
    display: inline-block;
}
.status-belum_bayar {
    background-color: #fff3cd;
    color: #856404;
}
.status-menunggu_verifikasi {
    background-color: #d1ecf1;
    color: #0c5460;
}
.status-dibayar {
    background-color: #d4edda;
    color: #155724;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 30px;
    color: #6c757d;
}

/* Modal */
.modal-img {
    width: 100%;
    border-radius: 10px;
}
</style>
</head>
<body>

<div class="header">
    <i class="bi bi-wallet2 me-2 fs-4"></i> Kelola Pembayaran Siswa Baru
</div>

<main class="main-content">
    <div class="card-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-primary m-0">Data Pembayaran</h5>
            <small class="text-muted"><i class="bi bi-clock"></i> Auto refresh setiap 30 detik</small>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-bordered text-center">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Anak</th>
                        <th>Nama Orang Tua</th>
                        <th>Metode Pembayaran</th>
                        <th>Tanggal Pembayaran</th>
                        <th>Bukti Pembayaran</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM pendaftaran ORDER BY tanggal_pembayaran DESC");
                    if ($result && $result->num_rows > 0):
                        $no = 1;
                        while ($row = $result->fetch_assoc()):
                            $statusClass = "status-" . $row['status_pembayaran'];
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama_anak']); ?></td>
                        <td><?= htmlspecialchars($row['nama_ortu']); ?></td>
                        <td><?= $row['metode_pembayaran'] ?: '-'; ?></td>
                        <td><?= $row['tanggal_pembayaran'] ? date('d M Y', strtotime($row['tanggal_pembayaran'])) : '-'; ?></td>
                        <td>
                            <?php if ($row['bukti_pembayaran']): ?>
                                <img src="../uploads/<?= $row['bukti_pembayaran']; ?>" 
                                     alt="Bukti" 
                                     onclick="showModal('../uploads/<?= $row['bukti_pembayaran']; ?>')">
                            <?php else: ?>
                                <span class="text-muted">Belum ada</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge-status <?= $statusClass; ?>">
                            <?= ucfirst(str_replace('_',' ', $row['status_pembayaran'])); ?>
                        </span></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="bi bi-folder-x fs-3 d-block mb-2"></i>
                            Belum ada data pembayaran.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Preview Gambar -->
<div class="modal fade" id="imageModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img src="" id="modalImage" class="modal-img" alt="Bukti Pembayaran">
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Preview bukti pembayaran dalam modal
function showModal(src) {
    document.getElementById('modalImage').src = src;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

// Auto-refresh tiap 30 detik
setInterval(() => location.reload(), 30000);
</script>

</body>
</html>
    