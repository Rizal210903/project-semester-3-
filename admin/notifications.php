<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    die("Harus login sebagai admin!");
}



$stmt = $conn->prepare("
    SELECT n.id, n.message, n.type, n.created_at, p.nama_anak, p.nama_ortu, n.is_read
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
    <title>Notifikasi Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6fb;
            font-family: 'Poppins', sans-serif;
        }

        .notif-container {
            max-width: 900px;
            margin: 40px auto;
        }

        .section-title {
            font-size: 32px;
            font-weight: 700;
            color: #2b2b2b;
            text-align: center;
            margin-bottom: 30px;
        }

        /* ====== DESAIN BARU TOTAL ====== */
        .notif-card {
            display: flex;
            gap: 20px;
            background: #ffffff;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 22px;
            border: 1px solid #e0e6f0;
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.06);
            transition: 0.25s ease-in-out;
        }

        .notif-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 32px rgba(0, 0, 0, 0.12);
        }

        /* UNREAD */
        .notif-card.unread {
            border-left: 8px solid #0d6efd !important;
            background: #eaf2ff;
        }

        /* Icon */
        .notif-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: bold;
            color: #fff;
            flex-shrink: 0;
        }

        .icon-pendaftaran {
            background: linear-gradient(135deg, #0d6efd, #5f9cff);
        }

        .icon-pembayaran {
            background: linear-gradient(135deg, #198754, #62d48c);
        }

        .notif-content {
            flex: 1;
        }

        .notif-content p {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }

        .notif-meta {
            font-size: .9rem;
            margin-top: 6px;
            color: #666;
        }

        /* TAG */
        .notif-tag {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 14px;
            font-size: .8rem;
            font-weight: 700;
            border-radius: 12px;
        }

        .tag-pendaftaran {
            background: #e7f0ff;
            color: #0d6efd;
        }

        .tag-pembayaran {
            background: #e9ffe9;
            color: #198754;
        }

        /* Waktu */
        .notif-time {
            min-width: 130px;
            font-size: .85rem;
            color: #999;
            text-align: right;
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

        /* Button */
        #markAllReadBtn {
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 128, 0, 0.25);
        }

        #markAllReadBtn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 128, 0, 0.35);
        }
    </style>

</head>

<body>

    <div class="notif-container">

        <h2 class="section-title">📢 Notifikasi Admin</h2>

        <div class="text-end mb-3">
            <button id="markAllReadBtn" class="btn btn-success px-4">
                ✔ Tandai Semua Sudah Dibaca
            </button>
        </div>

        <?php if (count($notifications) === 0): ?>
            <div class="alert alert-info text-center">Belum ada notifikasi.</div>
        <?php else: ?>
            <?php foreach ($notifications as $notif): ?>
                <?php $isUnread = ($notif['is_read'] ?? 1) == 0; ?>

                <div class="notif-card <?= $isUnread ? 'unread' : '' ?>">

                    <div class="notif-icon <?= $notif['type'] === 'pendaftaran' ? 'icon-pendaftaran' : 'icon-pembayaran' ?>">
                        <?= $notif['type'] === 'pendaftaran' ? '📘' : '💰' ?>
                    </div>

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

                    <div class="notif-time">
                        <?= date('d M Y H:i', strtotime($notif['created_at'])) ?>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>


    <script>
        document.getElementById("markAllReadBtn").addEventListener("click", function () {
            fetch("notifications.php?mark_all_read=1")
                .then(response => response.text())
                .then(data => {
                    // Hilangkan highlight kartu yang unread
                    document.querySelectorAll(".notif-card.unread").forEach(card => {
                        card.classList.remove("unread");
                    });

                    // Update badge notifikasi jadi nol
                    let badge = document.getElementById("notifCount");
                    if (badge) {
                        badge.innerText = "0";
                        badge.style.display = "none"; // sembunyikan badge
                    }
                })
                .catch(error => console.error("Error:", error));
        });
    </script>


</body>

</html>