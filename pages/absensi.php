<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($_SESSION['role'] != 'guru') { header("Location: dashboard.php"); }
include __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $siswa_id = (int)$_POST['siswa_id'];
    $tanggal = $_POST['tanggal'];
    $status = sanitize($_POST['status']);
    $catatan = sanitize($_POST['catatan']);
    $guru_id = $_SESSION['user_id'];
    
    $sql = "INSERT INTO absensi (siswa_id, tanggal, status, catatan, guru_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $siswa_id, $tanggal, $status, $catatan, $guru_id);
    $stmt->execute();
    echo "Absensi berhasil diinput!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Absensi TK Pertiwi</title>
    <link rel="stylesheet" href="/tk-pertiwi/css/style.css">
</head>
<body>
    <form method="POST">
        <select name="siswa_id">
            <?php
            $sql_siswa = "SELECT id, nama FROM siswa";
            $result = $conn->query($sql_siswa);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['nama'] . "</option>";
            }
            ?>
        </select>
        <input type="date" name="tanggal" required>
        <select name="status">
            <option value="hadir">Hadir</option>
            <option value="sakit">Sakit</option>
            <option value="izin">Izin</option>
            <option value="alfa">Alfa</option>
        </select>
        <textarea name="catatan" placeholder="Catatan"></textarea>
        <button type="submit">Input Absensi</button>
    </form>
</body>
</html>
<?php include __DIR__ . '/../includes/footer.php'; ?>