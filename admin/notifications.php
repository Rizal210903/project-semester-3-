<?php
session_start();
include '../includes/config.php';

// Pastikan admin login
if (!isset($_SESSION['admin_id'])) {
    die("Harus login sebagai admin!");
}

// Ambil semua notifikasi
$stmt = $conn->prepare("
    SELECT n.id, n.message, n.type, n.created_at, p.nama_anak, p.nama_ortu
    FROM notifications n
    LEFT JOIN pendaftaran p ON n.pendaftaran_id = p.id
    ORDER BY n.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Notifikasi Admin - TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #eef2f7;
            font-family: 'Poppins', sans-serif;
        }

        .notif-container {
            max-width: 900px;
            margin: 40px auto;
        }

        .notif-card {
            background: #fff;
            border-radius: 14px;
            padding: 20px;
            display: flex;
            gap: 15px;
            align-items: flex-start;
            border: 1px solid #e3e8ef;
            transition: 0.2s ease-in-out;
        }

        .notif-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .notif-icon {
            font-size: 35px;
            padding: 12px;
            border-radius: 12px;
            color: #fff;
            flex-shrink: 0;
        }

        .icon-pendaftaran {
            background: #0d6efd;
        }

        .icon-pembayaran {
            background: #28a745;
        }

        .notif-content {
            flex: 1;
        }

        .notif-content p {
            margin: 0;
            font-size: 1rem;
            font-weight: 500;
            color: #333;
            word-break: break-word;
        }

        .notif-meta {
            font-size: 0.85rem;
            margin-top: 5px;
            color: #6c757d;
        }

        .notif-tag {
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .tag-pendaftaran {
            background: #e3f0ff;
            color: #0d6efd;
        }

        .tag-pembayaran {
            background: #e8f9ee;
            color: #198754;
        }

        .notif-time {
            text-align: right;
            font-size: 0.75rem;
            color: #6c757d;
            white-space: nowrap;
        }

        @media (max-width: 576px) {
            .notif-card {
                flex-direction: column;
            }

            .notif-time {
                text-align: left;
            }
        }
    </style>
</head>

<body>

    <div class="notif-container">
        <h2 class="mb-4 fw-bold text-center">📢 Notifikasi Admin</h2>

        <?php if (count($notifications) === 0): ?>
            <div class="alert alert-info text-center">Belum ada notifikasi.</div>
        <?php else: ?>
            <?php foreach ($notifications as $notif): ?>

                <div class="notif-card shadow-sm">

                    <!-- Icon -->
                    <div class="notif-icon <?= $notif['type'] === 'pendaftaran' ? 'icon-pendaftaran' : 'icon-pembayaran' ?>">
                        <?= $notif['type'] === 'pendaftaran' ? '📘' : '💰' ?>
                    </div>

                    <!-- Isi -->
                    <div class="notif-content">
                        <p><?= htmlspecialchars($notif['message'] ?? '(Tidak ada pesan)') ?></p>

                        <div class="notif-meta">
                            Anak: <strong><?= htmlspecialchars($notif['nama_anak'] ?? '-') ?></strong> |
                            Orang Tua: <strong><?= htmlspecialchars($notif['nama_ortu'] ?? '-') ?></strong>
                        </div>

                        <span class="notif-tag <?= $notif['type'] === 'pendaftaran' ? 'tag-pendaftaran' : 'tag-pembayaran' ?>">
                            <?= ucfirst($notif['type']) ?>
                        </span>
                    </div>

                    <!-- Waktu -->
                    <div class="notif-time">
                        <?= date('d M Y H:i', strtotime($notif['created_at'])) ?>
                    </div>

                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>

</html>
                