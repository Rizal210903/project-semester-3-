<?php
include '../includes/config.php';
include 'sidebar.php';

// Ambil data pendaftar
$result = $conn->query("SELECT * FROM pendaftaran ORDER BY tanggal_daftar DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola PPDB - TK Pertiwi</title>

<!-- Bootstrap & Icon -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body { background: #eaf6ff; font-family: 'Poppins', sans-serif; }
.main-content { margin-left: 250px; padding: 90px 30px; }
.table-container { background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 3px 8px rgba(0,0,0,0.1); }
.table th { background: #007bff; color: #fff; font-weight: 600; }
.table td, .table th { vertical-align: middle; }
.badge { font-size: 0.9rem; padding: 6px 10px; border-radius: 8px; }
.btn-action { font-size: 0.8rem; border-radius: 6px; }
@media (max-width: 768px) { .main-content { margin-left: 0; padding: 80px 15px; } }
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
          <tr id="row-<?= $row['id']; ?>">
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama_anak']); ?></td>
            <td><?= htmlspecialchars($row['nama_ortu']); ?></td>
            <td><?= date('d M Y', strtotime($row['tanggal_daftar'])); ?></td>

            <!-- Dokumen -->
            <?php foreach(['kartu_keluarga','akta_kelahiran','pas_foto','surat_sehat'] as $dok): ?>
            <td>
              <?php if ($row[$dok]): ?>
                <a href="../uploads/<?= $row[$dok]; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-eye"></i> Lihat
                </a>
              <?php else: ?>
                <span class="text-muted">Belum ada</span>
              <?php endif; ?>
            </td>
            <?php endforeach; ?>

            <!-- Status PPDB -->
            <td class="status-td">
              <span class="badge 
                <?= $row['status_ppdb'] == 'diterima' ? 'bg-success' : 
                   ($row['status_ppdb'] == 'pending' ? 'bg-warning text-dark' : 'bg-danger'); ?>">
                <?= ucfirst($row['status_ppdb']); ?>
              </span>
            </td>

            <!-- Aksi -->
            <td>
              <form class="ubahStatusForm" data-id="<?= $row['id']; ?>">
                <select name="status" class="form-select form-select-sm mb-2">
                  <option value="pending" <?= $row['status_ppdb']=='pending'?'selected':''; ?>>Pending</option>
                  <option value="diterima" <?= $row['status_ppdb']=='diterima'?'selected':''; ?>>Diterima</option>
                  <option value="ditolak" <?= $row['status_ppdb']=='ditolak'?'selected':''; ?>>Ditolak</option>
                </select>
                <button type="submit" class="btn btn-primary btn-action w-100">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
  $(".ubahStatusForm").on("submit", function(e) {
    e.preventDefault();
    let form = $(this);
    let id = form.data("id");
    let status = form.find("select[name='status']").val();

    $.ajax({
      url: "update_status.php",
      method: "POST",
      data: { id: id, status: status, ubah_status: true },
      dataType: "json",
      success: function(response) {
        if(response.success) {
          let badgeClass = status === "diterima" ? "bg-success" :
                           status === "pending" ? "bg-warning text-dark" : "bg-danger";
          form.closest("tr").find(".status-td").html(
            `<span class="badge ${badgeClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`
          );
          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: response.message,
            timer: 1500,
            showConfirmButton: false
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: response.message
          });
        }
      },
      error: function() {
        Swal.fire({
          icon: "error",
          title: "Gagal!",
          text: "Terjadi kesalahan server."
        });
      }
    });
  });
});
</script>

</body>
</html>
