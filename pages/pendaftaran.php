<?php
// Inisialisasi variabel
$name = $email = $birthdate = $kk_number = $address = $whatsapp = "";
$errors = [];
$registration_id = uniqid(); // ID unik buat pendaftaran
$photo_path = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $birthdate = htmlspecialchars($_POST['birthdate'] ?? '');
    $kk_number = htmlspecialchars($_POST['kk_number'] ?? '');
    $address = htmlspecialchars($_POST['address'] ?? '');
    $whatsapp = htmlspecialchars($_POST['whatsapp'] ?? '');

    // Handle upload foto
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $new_name = $registration_id . "_photo." . $ext;
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/tk-pertiwi/uploads/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            $photo_path = $upload_dir . $new_name;
            move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
            $photo_path = '/tk-pertiwi/uploads/' . $new_name; // Path relatif
        } else {
            $errors[] = "Format foto harus JPG, JPEG, atau PNG.";
        }
    } else {
        $errors[] = "Foto wajib diunggah.";
    }

    // Validasi sederhana
    if (empty($name)) $errors[] = "Nama harus diisi.";
    if (empty($email)) $errors[] = "Email harus diisi.";
    if (empty($birthdate)) $errors[] = "Tanggal lahir harus diisi.";
    if (empty($kk_number)) $errors[] = "Nomor KK harus diisi.";
    if (empty($address)) $errors[] = "Alamat harus diisi.";
    if (empty($whatsapp)) $errors[] = "Nomor WhatsApp harus diisi.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid.";
    if (!preg_match('/^\+?([0-9]{10,13})$/', $whatsapp)) $errors[] = "Nomor WhatsApp tidak valid (gunakan 10-13 digit, bisa dengan +).";

    // Kalo ga ada error, proses pendaftaran
    if (empty($errors)) {
        // Simpan ke file (placeholder, nanti ke database)
        $data = "ID: $registration_id, Nama: $name, Email: $email, Tanggal Lahir: $birthdate, Nomor KK: $kk_number, Alamat: $address, Nomor WhatsApp: $whatsapp, Foto: $photo_path, Tanggal Pendaftaran: " . date('Y-m-d H:i:s') . "\n";
        file_put_contents('registrations.txt', $data, FILE_APPEND);
        // Kirim nama ke thank-you
        header("Location: thank-you.php?success=1&name=" . urlencode($name));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #E0F7FA;
            font-family: 'Arial', sans-serif;
        }
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-container h2 {
            color: #1E90FF;
            margin-bottom: 20px;
        }
        .error {
            color: red;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        .btn-primary {
            background-color: #1E90FF;
            border: none;
            padding: 10px 20px;
        }
        .btn-primary:hover {
            background-color: #104E8B;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 class="text-center">Form Pendaftaran TK Pertiwi</h2>
        <?php
        if (!empty($errors)) {
            echo '<div class="alert alert-danger">';
            foreach ($errors as $error) {
                echo "<p class='error'>$error</p>";
            }
            echo '</div>';
        }
        ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
            </div>
            <div class="mb-3">
                <label for="birthdate" class="form-label">Tanggal Lahir</label>
                <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo $birthdate; ?>" required>
            </div>
            <div class="mb-3">
                <label for="kk_number" class="form-label">Nomor Kartu Keluarga</label>
                <input type="text" class="form-control" id="kk_number" name="kk_number" value="<?php echo $kk_number; ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Alamat</label>
                <textarea class="form-control" id="address" name="address" required><?php echo $address; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="whatsapp" class="form-label">Nomor WhatsApp</label>
                <input type="tel" class="form-control" id="whatsapp" name="whatsapp" value="<?php echo $whatsapp; ?>" placeholder="+6281234567890" required>
            </div>
            <div class="mb-3">
                <label for="photo" class="form-label">Foto Diri (JPG, JPEG, PNG)</label>
                <input type="file" class="form-control" id="photo" name="photo" accept=".jpg,.jpeg,.png" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>