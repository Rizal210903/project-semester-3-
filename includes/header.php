<?php
// Pastikan session aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ========================
// Ambil data session login
// ========================
$username = $_SESSION['username'] ?? $_SESSION['admin_username'] ?? null;
$profile_file = $_SESSION['foto'] ?? $_SESSION['admin_foto'] ?? null;
$role = $_SESSION['role'] ?? (isset($_SESSION['admin_username']) ? 'admin' : 'user');

// Tentukan foto profil (pakai default kalau tidak ada)
$profile_img = '/project-semester-3-/img/profile_default.png';
if ($profile_file && file_exists($_SERVER['DOCUMENT_ROOT'] . '/project-semester-3-/uploads/' . $profile_file)) {
    $profile_img = '/project-semester-3-/uploads/' . $profile_file;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TK Pertiwi</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #E0F6FF;
            margin: 0;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            background: #ffffff;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            color: #4682B4 !important;
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            max-height: 40px;
            margin-right: 10px;
        }

        .nav-link {
            font-weight: 500;
            color: #333 !important;
            margin: 0 10px;
            transition: 0.3s;
        }

        .nav-link:hover {
            color: #4682B4 !important;
        }

        /* ===== Tombol Login / Daftar ===== */
        .btn-login, .btn-signup {
            border-radius: 8px;
            padding: 6px 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-login {
            background-color: #ffffff;
            border: 2px solid #4682B4;
            color: #4682B4;
        }

        .btn-login:hover {
            background-color: #4682B4;
            color: white;
        }

        .btn-signup {
            background-color: #4682B4;
            border: 2px solid #4682B4;
            color: white;
        }

        .btn-signup:hover {
            background-color: #315f86;
            border-color: #315f86;
        }

        /* ===== Profil Dropdown ===== */
        .nav-profile {
            display: flex;
            align-items: center;
            font-weight: 500;
            color: #333;
        }

        .nav-profile img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 8px;
            border: 2px solid #ddd;
            object-fit: cover;
        }

        .nav-profile i {
            font-size: 1.5rem;
            color: #4682B4;
            margin-right: 8px;
        }

        @media (max-width: 992px) {
            .navbar-collapse {
                background: #fff;
                border-radius: 10px;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- ====== NAVBAR ====== -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="/project-semester-3-/pages/index.php">
                <img src="/project-semester-3-/img/logo_tk.png" alt="Logo"> TK Pertiwi
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="/project-semester-3-/pages/index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="/project-semester-3-/pages/profil.php">Profil Sekolah</a></li>
                    <li class="nav-item"><a class="nav-link" href="/project-semester-3-/pages/agenda.php">Agenda</a></li>
                    <li class="nav-item"><a class="nav-link" href="/project-semester-3-/pages/info_pbdb.php">Info PBDB</a></li>
                    <li class="nav-item"><a class="nav-link" href="/project-semester-3-/pages/kontak.php">Kontak</a></li>

                    <?php if ($username): ?>
                        <!-- ===== Jika user sudah login ===== -->
                        <li class="nav-item dropdown ms-lg-3 mt-2 mt-lg-0">
                            <a class="nav-link dropdown-toggle nav-profile" href="#" id="userDropdown"
                               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if ($profile_file): ?>
                                    <img src="<?= htmlspecialchars($profile_img) ?>" alt="Profil">
                                <?php else: ?>
                                    <i class="bi bi-person-circle"></i>
                                <?php endif; ?>
                                <?= htmlspecialchars($username) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <?php if ($role === 'admin'): ?>
                                    <!-- Menu khusus ADMIN -->
                                    <li>
                                        <a class="dropdown-item" href="/project-semester-3-/admin/admin_dashboard.php">
                                            <i class="bi bi-speedometer2 me-2"></i>Dashboard Admin
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <!-- Menu khusus USER -->
                                    <li>
                                        <a class="dropdown-item" href="/project-semester-3-/pages/edit_profil.php">
                                            <i class="bi bi-person-lines-fill me-2"></i>Profil Saya
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <li><hr class="dropdown-divider"></li>

                                    <li>
                                        <a class="dropdown-item text-danger" href="/project-semester-3-/pages/logout.php">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </a>
                                    </li>

                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- ===== Jika user belum login ===== -->
                        <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                            <a href="/project-semester-3-/pages/login.php" class="btn btn-login btn-sm">Login</a>
                        </li>
                        <li class="nav-item ms-2 mt-2 mt-lg-0">
                            <a href="/project-semester-3-/pages/register.php" class="btn btn-signup btn-sm">Sign Up</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
