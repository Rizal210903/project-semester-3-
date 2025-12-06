<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    die("Harus login sebagai admin!");
}

// Handle Mark All Read
if (isset($_GET['mark_all_read'])) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
    $stmt->execute();
    $stmt->close();
    echo "success";
    exit();
}

// Handle Clear All
if (isset($_GET['clear_all'])) {
    $stmt = $conn->prepare("DELETE FROM notifications");
    $stmt->execute();
    $stmt->close();
    echo "success";
    exit();
}

// Query mengambil data dari tabel pendaftaran
$stmt = $conn->prepare("
    SELECT 
        n.id, 
        n.message, 
        n.type, 
        n.created_at, 
        p.nama_anak,
        p.nama_ortu,
        n.is_read
    FROM notifications n
    LEFT JOIN pendaftaran p ON n.pendaftaran_id = p.id
    ORDER BY n.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Hitung total unread
$unread_count = 0;
foreach ($notifications as $n) {
    if (($n['is_read'] ?? 1) == 0) $unread_count++;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Admin - TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #eaf6ff;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            padding: 20px 0;
            position: relative;
            overflow-x: hidden;
        }

        .notif-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        /* Back Button */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: white;
            border: 2px solid #d6eaff;
            border-radius: 10px;
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.3s;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .back-button:hover {
            background: #007bff;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }

        /* Header */
        .header-section {
            background: #ffffff;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            animation: slideDown 0.6s ease-out;
            border: 2px solid #d6eaff;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: #007bff;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .section-subtitle {
            color: #666;
            font-size: 1rem;
            font-weight: 500;
        }

        .stats-row {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .stat-card {
            flex: 1;
            min-width: 200px;
            background: #007bff;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
            transition: transform 0.3s;
            position: relative;
        }

        .stat-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 5px;
            height: 100%;
            background-color: #0056b3;
            border-radius: 10px 0 0 10px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 5px;
        }

        /* Filter Tabs */
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 12px 25px;
            border: none;
            background: #ffffff;
            color: #333;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            border: 2px solid #d6eaff;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-color: #007bff;
        }

        .filter-btn.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        /* Notification Cards */
        .notif-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            border: 2px solid #d6eaff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out;
            animation-fill-mode: both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notif-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
            border-color: #007bff;
        }

        /* Unread Style */
        .notif-card.unread {
            border-color: #007bff;
            background: #eaf6ff;
        }

        .notif-card.unread::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: #007bff;
        }

        .unread-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #dc3545;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .notif-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 15px;
        }

        .notif-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            flex-shrink: 0;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .notif-card:hover .notif-icon {
            transform: scale(1.05);
        }

        .icon-pendaftaran {
            background: #007bff;
        }

        .icon-pembayaran {
            background: #28a745;
        }

        .notif-content {
            flex: 1;
        }

        .notif-message {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .notif-meta {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
            color: #666;
        }

        .meta-item i {
            color: #007bff;
        }

        .meta-value {
            font-weight: 600;
            color: #333;
        }

        /* Tags */
        .notif-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
        }

        .notif-tag {
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .tag-pendaftaran {
            background: #e7f0ff;
            color: #0056b3;
            border: 1px solid #d6eaff;
        }

        .tag-pembayaran {
            background: #e9ffe9;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .notif-time {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            color: #999;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 14px 28px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
        }

        .btn-mark-all {
            background: #007bff;
            color: white;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
        }

        .btn-mark-all:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
            background: #0056b3;
        }

        .btn-mark-all:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-clear-all {
            background: #ffffff;
            color: #dc3545;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);
            border: 2px solid #d6eaff;
        }

        .btn-clear-all:hover:not(:disabled) {
            background: #dc3545;
            color: white;
            transform: translateY(-2px);
            border-color: #dc3545;
        }

        .btn-clear-all:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Empty State */
        .empty-state {
            background: #ffffff;
            border-radius: 12px;
            padding: 60px 30px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: 2px solid #d6eaff;
        }

        .empty-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.7;
        }

        .empty-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .empty-text {
            color: #666;
            font-size: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .section-title {
                font-size: 1.8rem;
            }

            .notif-header {
                flex-direction: column;
                text-align: center;
            }

            .notif-footer {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .stat-card {
                min-width: 100%;
            }
        }

        /* Stagger animation for cards */
        .notif-card:nth-child(1) { animation-delay: 0.1s; }
        .notif-card:nth-child(2) { animation-delay: 0.2s; }
        .notif-card:nth-child(3) { animation-delay: 0.3s; }
        .notif-card:nth-child(4) { animation-delay: 0.4s; }
        .notif-card:nth-child(5) { animation-delay: 0.5s; }
    </style>
</head>

<body>

    <div class="notif-container">

        <!-- Back Button -->
        <a href="admin_dashboard.php" class="back-button">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Dashboard
        </a>

        <!-- Header Section -->
        <div class="header-section">
            <h1 class="section-title">
                <i class="bi bi-bell-fill"></i>
                Notifikasi Admin
            </h1>
            <p class="section-subtitle">Kelola semua notifikasi pendaftaran dan pembayaran</p>

            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-number" id="totalCount"><?= count($notifications) ?></div>
                    <div class="stat-label">Total Notifikasi</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="unreadCount"><?= $unread_count ?></div>
                    <div class="stat-label">Belum Dibaca</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button id="markAllReadBtn" class="btn-action btn-mark-all" <?= $unread_count == 0 ? 'disabled' : '' ?>>
                <i class="bi bi-check-all"></i>
                Tandai Semua Sudah Dibaca
            </button>
            <button id="clearAllBtn" class="btn-action btn-clear-all" <?= count($notifications) == 0 ? 'disabled' : '' ?>>
                <i class="bi bi-trash"></i>
                Hapus Semua
            </button>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <button class="filter-btn active" data-filter="all">
                <i class="bi bi-grid-fill"></i> Semua
            </button>
            <button class="filter-btn" data-filter="pendaftaran">
                <i class="bi bi-person-plus-fill"></i> Pendaftaran
            </button>
            <button class="filter-btn" data-filter="pembayaran">
                <i class="bi bi-cash-coin"></i> Pembayaran
            </button>
            <button class="filter-btn" data-filter="unread">
                <i class="bi bi-envelope-fill"></i> Belum Dibaca
            </button>
        </div>

        <!-- Notifications List -->
        <div id="notificationsList">
            <?php if (count($notifications) === 0): ?>
                <div class="empty-state">
                    <div class="empty-icon">🔔</div>
                    <h3 class="empty-title">Belum Ada Notifikasi</h3>
                    <p class="empty-text">Notifikasi baru akan muncul di sini</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $notif): ?>
                    <?php 
                        $isUnread = ($notif['is_read'] ?? 1) == 0;
                        $type = $notif['type'];
                        $iconClass = $type === 'pendaftaran' ? 'icon-pendaftaran' : 'icon-pembayaran';
                        $tagClass = $type === 'pendaftaran' ? 'tag-pendaftaran' : 'tag-pembayaran';
                        $icon = $type === 'pendaftaran' ? '📝' : '💳';
                    ?>

                    <div class="notif-card <?= $isUnread ? 'unread' : '' ?>" data-type="<?= $type ?>" data-read="<?= $isUnread ? '0' : '1' ?>">
                        
                        <?php if ($isUnread): ?>
                            <span class="unread-badge">BARU</span>
                        <?php endif; ?>

                        <div class="notif-header">
                            <div class="notif-icon <?= $iconClass ?>">
                                <?= $icon ?>
                            </div>

                            <div class="notif-content">
                                <div class="notif-message">
                                    <?= htmlspecialchars($notif['message'] ?? '(Tidak ada pesan)') ?>
                                </div>

                                <div class="notif-meta">
                                    <div class="meta-item">
                                        <i class="bi bi-person-fill"></i>
                                        Anak: <span class="meta-value"><?= htmlspecialchars($notif['nama_anak'] ?? '-') ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="bi bi-people-fill"></i>
                                        Orang Tua: <span class="meta-value"><?= htmlspecialchars($notif['nama_ortu'] ?? '-') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="notif-footer">
                            <span class="notif-tag <?= $tagClass ?>">
                                <i class="bi bi-<?= $type === 'pendaftaran' ? 'person-plus' : 'cash' ?>"></i>
                                <?= ucfirst($type) ?>
                            </span>
                            <div class="notif-time">
                                <i class="bi bi-clock"></i>
                                <?= date('d M Y, H:i', strtotime($notif['created_at'])) ?>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mark all as read
        document.getElementById("markAllReadBtn").addEventListener("click", function() {
            if (this.disabled) return;
            
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';

            fetch("notifications.php?mark_all_read=1")
                .then(response => response.text())
                .then(data => {
                    if (data.includes('success')) {
                        // Remove unread class and badge
                        document.querySelectorAll(".notif-card.unread").forEach(card => {
                            card.classList.remove("unread");
                            card.dataset.read = "1";
                            const badge = card.querySelector(".unread-badge");
                            if (badge) badge.remove();
                        });

                        // Update unread count
                        document.getElementById("unreadCount").textContent = "0";

                        // Show success message
                        showToast("Semua notifikasi telah ditandai sebagai dibaca", "success");
                    }
                    
                    // Keep button disabled
                    this.innerHTML = '<i class="bi bi-check-all"></i> Tandai Semua Sudah Dibaca';
                })
                .catch(error => {
                    console.error("Error:", error);
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-check-all"></i> Tandai Semua Sudah Dibaca';
                    showToast("Terjadi kesalahan", "error");
                });
        });

        // Clear all notifications
        document.getElementById("clearAllBtn").addEventListener("click", function() {
            if (this.disabled) return;
            
            if (confirm("Apakah Anda yakin ingin menghapus semua notifikasi?")) {
                this.disabled = true;
                this.innerHTML = '<i class="bi bi-hourglass-split"></i> Menghapus...';
                
                fetch("notifications.php?clear_all=1")
                    .then(response => response.text())
                    .then(data => {
                        if (data.includes('success')) {
                            document.getElementById("notificationsList").innerHTML = `
                                <div class="empty-state">
                                    <div class="empty-icon">🔔</div>
                                    <h3 class="empty-title">Belum Ada Notifikasi</h3>
                                    <p class="empty-text">Notifikasi baru akan muncul di sini</p>
                                </div>
                            `;
                            
                            document.getElementById("totalCount").textContent = "0";
                            document.getElementById("unreadCount").textContent = "0";
                            
                            // Disable both buttons
                            document.getElementById("markAllReadBtn").disabled = true;
                            
                            showToast("Semua notifikasi telah dihapus", "success");
                        }
                        
                        this.innerHTML = '<i class="bi bi-trash"></i> Hapus Semua';
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        this.disabled = false;
                        this.innerHTML = '<i class="bi bi-trash"></i> Hapus Semua';
                        showToast("Terjadi kesalahan", "error");
                    });
            }
        });

        // Filter functionality
        document.querySelectorAll(".filter-btn").forEach(btn => {
            btn.addEventListener("click", function() {
                // Update active state
                document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
                this.classList.add("active");

                const filter = this.dataset.filter;
                const cards = document.querySelectorAll(".notif-card");

                cards.forEach(card => {
                    const type = card.dataset.type;
                    const isUnread = card.dataset.read === "0";

                    if (filter === "all") {
                        card.style.display = "block";
                    } else if (filter === "unread") {
                        card.style.display = isUnread ? "block" : "none";
                    } else {
                        card.style.display = type === filter ? "block" : "none";
                    }
                });
            });
        });

        // Toast notification
        function showToast(message, type) {
            const toast = document.createElement("div");
            toast.style.cssText = `
                position: fixed;
                bottom: 30px;
                right: 30px;
                background: ${type === 'success' ? 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)' : 'linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%)'};
                color: white;
                padding: 15px 25px;
                border-radius: 50px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                z-index: 10000;
                font-weight: 600;
                animation: slideInRight 0.5s ease-out;
            `;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = "slideOutRight 0.5s ease-out";
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }

        // Auto refresh notifikasi setiap 30 detik (opsional)
        setInterval(() => {
            fetch('notifications.php')
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTotal = doc.getElementById('totalCount').textContent;
                    const newUnread = doc.getElementById('unreadCount').textContent;
                    
                    const currentTotal = document.getElementById('totalCount').textContent;
                    const currentUnread = document.getElementById('unreadCount').textContent;
                    
                    // Update jika ada perubahan
                    if (newTotal !== currentTotal || newUnread !== currentUnread) {
                        document.getElementById('totalCount').textContent = newTotal;
                        document.getElementById('unreadCount').textContent = newUnread;
                        
                        // Show notification jika ada notifikasi baru
                        if (parseInt(newTotal) > parseInt(currentTotal)) {
                            showToast("Ada notifikasi baru!", "success");
                        }
                    }
                })
                .catch(error => console.error('Auto refresh error:', error));
        }, 30000); // 30 detik
    </script>

</body>

</html>