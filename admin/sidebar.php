<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Tentukan halaman-halaman yang termasuk dalam submenu "Dashboards"
$dashboard_pages = ['manage-pbdb.php', 'galeri.php', 'manage_agenda.php', 'manage_payments.php', 'manage_teachers.php', 'manage_admin.php', 'manage_pbdb_info.php'];
$is_dashboard_active = in_array($current_page, $dashboard_pages);
?>

<style>
    /* SIDEBAR */
    .sidebar {
        width: 250px;
        background-color: #ffffff;
        border-right: 1px solid #e0e0e0;
        padding: 20px;
        height: calc(100vh - 60px);
        position: fixed;
        top: 60px;
        left: 0;
        overflow-y: auto;
        transition: width 0.3s ease;
        z-index: 999;
    }

    .sidebar.collapsed {
        width: 60px;
        padding: 20px 10px;
    }

    .sidebar ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .sidebar li {
        margin-bottom: 10px;
    }

    .sidebar a {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #333;
        padding: 10px 12px;
        border-radius: 6px;
        transition: all 0.2s ease;
        font-size: 15px;
    }

    .sidebar a:hover {
        background-color: #e6f0ff;
        color: #007bff;
    }

    .sidebar a.active {
        background-color: #007bff;
        color: #fff;
    }

    .sidebar i {
        font-size: 18px;
        margin-right: 12px;
        width: 20px;
        text-align: center;
    }

    .sidebar.collapsed i {
        margin-right: 0;
    }

    .sidebar.collapsed a span {
        display: none;
    }

    /* SUBMENU */
    .submenu {
        list-style: none;
        padding-left: 30px;
        margin-top: 5px;
        display: none;
        transition: all 0.3s ease;
    }

    .menu-item.active .submenu {
        display: block;
    }

    .submenu a {
        font-size: 14px;
        padding: 8px 10px;
        color: #444;
    }

    .submenu a:hover {
        background-color: #f0f6ff;
        color: #007bff;
    }

    .arrow {
        margin-left: auto;
        font-size: 12px;
        transition: transform 0.3s ease;
    }

    .menu-item.active .arrow {
        transform: rotate(180deg);
    }

    /* Scrollbar */
    .sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 3px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background-color: #999;
    }
    /* Responsif: Sidebar sembunyi di layar kecil */
@media (max-width: 768px) {
    .sidebar {
        left: -250px; /* sembunyikan di mobile */
    }

    .sidebar.active {
        left: 0; /* tampilkan ketika diaktifkan */
    }
}


</style>

<nav class="sidebar">
    <ul>
        <!-- Overview -->
        <li>
            <a href="admin_dashboard.php" class="<?= $current_page == 'admin_dashboard.php' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i><span>Overview</span>
            </a>
        </li>

        <!-- Dashboards Section -->
        <li class="menu-item <?= $is_dashboard_active ? 'active' : '' ?>">
            <a href="#" class="toggle-submenu">
                <i class="bi bi-grid"></i>
                <span>Dashboards</span>
                <i class="bi bi-chevron-down arrow"></i>
            </a>
            <ul class="submenu">
                <li>
                    <a href="manage-pbdb.php" class="<?= $current_page == 'manage-pbdb.php' ? 'active' : '' ?>">
                        <i class="bi bi-person-plus"></i><span>Pendaftaran Siswa Baru</span>
                    </a>
                </li>
                <li>
                    <a href="galeri.php" class="<?= $current_page == 'galeri.php' ? 'active' : '' ?>">
                        <i class="bi bi-image"></i><span>Galeri Foto</span>
                    </a>
                </li>
                
                    <a href="manage_pbdb_info.php" class="<?= $current_page == 'manage_pbdb_info.php' ? 'active' : '' ?>">
                      <i class="bi bi-info-circle"></i> Kelola Info PPDB
                    </a>
                </li>
                <li>
                    <a href="manage_agenda.php" class="<?= $current_page == 'manage_agenda.php' ? 'active' : '' ?>">
                        <i class="bi bi-calendar-event"></i><span>Manajemen Acara</span>
                    </a>
                </li>
                <li>
                    <a href="manage_payments.php" class="<?= $current_page == 'manage_payments.php' ? 'active' : '' ?>">
                        <i class="bi bi-credit-card"></i><span>Pembayaran</span>
                    </a>
                </li>
                <li>
                    <a href="manage_teachers.php" class="<?= $current_page == 'manage_teachers.php' ? 'active' : '' ?>">
                        <i class="bi bi-people"></i><span>Manajemen Absen Guru</span>
                    </a>
                </li>
                <li>
                    <a href="manage_admin.php" class="<?= $current_page == 'manage_admin.php' ? 'active' : '' ?>">
                        <i class="bi bi-person-gear"></i><span>Manajemen Admin</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Logout -->
        <li>
            <a href="../pages/index.php">
                <i class="bi bi-box-arrow-right"></i><span>Logout</span>
            </a>
        </li>
    </ul>
</nav>

<script>
    // Toggle submenu saat diklik
    document.querySelectorAll('.toggle-submenu').forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            const parent = toggle.closest('.menu-item');
            parent.classList.toggle('active');
        });
    });
</script>
