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

// Tentukan foto profil (cek di folder uploads/profil/)
$profile_img = null;
$has_photo = false;

if ($profile_file) {
    // Coba beberapa path alternatif
    $possible_paths = [
        __DIR__ . '/../uploads/profil/' . $profile_file,
        $_SERVER['DOCUMENT_ROOT'] . '/project-semester-3-/uploads/profil/' . $profile_file,
    ];

    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            $profile_img = '/project-semester-3-/uploads/profil/' . $profile_file; // Hapus strip ekstra
            $has_photo = true;
            break;
        }
    }

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
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
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
        .btn-login,
        .btn-signup {
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

        /* ===== Profile Picture di Navbar (kecil) ===== */
        .navbar-profile-pic {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #4682B4;
            margin-right: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }

        /* Default Avatar di Navbar */
        .navbar-profile-default {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4682B4, #315f86);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #4682B4;
            margin-right: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }

        .navbar-profile-default i {
            color: white;
            font-size: 20px;
        }

        .navbar-username {
            font-weight: 600;
            color: #333;
        }

        /* ===== Dropdown Profile Header ===== */
        .dropdown-profile-header {
            padding: 15px;
            background: linear-gradient(135deg, #4682B4, #315f86);
            color: white;
            border-radius: 8px 8px 0 0;
            margin: -8px -8px 12px -8px;
        }

        .dropdown-profile-pic-large {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }

        .dropdown-profile-default-large {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }

        .dropdown-profile-default-large i {
            color: white;
            font-size: 28px;
        }

        .dropdown-profile-info {
            margin-left: 15px;
            flex: 1;
            min-width: 0;
        }

        .dropdown-profile-name {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 5px;
            line-height: 1.2;
            word-break: break-word;
        }

        .dropdown-profile-role {
            font-size: 12px;
            opacity: 0.95;
            text-transform: capitalize;
            background: rgba(255, 255, 255, 0.25);
            padding: 3px 10px;
            border-radius: 12px;
            display: inline-block;
            font-weight: 600;
        }

        /* ===== Dropdown Menu Styling ===== */
        .dropdown-menu {
            min-width: 280px;
            padding: 8px;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
            border: none;
            margin-top: 10px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .dropdown-item:hover {
            background-color: #e3f2fd;
            transform: translateX(5px);
        }

        .dropdown-item i {
            width: 22px;
            font-size: 16px;
            color: #4682B4;
        }

        .dropdown-item.text-danger i {
            color: #dc3545;
        }

        .dropdown-item.text-danger:hover {
            background-color: #ffe6e6;
        }

        .dropdown-divider {
            margin: 10px 0;
            opacity: 0.15;
        }

        /* ===== Profil Dropdown Toggle ===== */
        .nav-profile {
            display: flex;
            align-items: center;
            font-weight: 500;
            color: #333;
            cursor: pointer;
        }

        @media (max-width: 992px) {
            .navbar-collapse {
                background: #fff;
                border-radius: 10px;
                padding: 10px;
            }

            .navbar-username {
                font-size: 14px;
            }

            .dropdown-menu {
                min-width: 260px;
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

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="/project-semester-3-/pages/index.php">Beranda</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="/project-semester-3-/pages/profil.php">Profil
                            Sekolah</a></li>
                    <li class="nav-item"><a class="nav-link" href="/project-semester-3-/pages/agenda.php">Agenda</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="/project-semester-3-/pages/info_pbdb.php">Info
                            PBDB</a></li>
                    <li class="nav-item"><a class="nav-link" href="/project-semester-3-/pages/kontak.php">Kontak</a>
                    </li>

                    <?php if ($username): ?>
                        <!-- ===== Jika user sudah login ===== -->
                        <li class="nav-item dropdown ms-lg-3 mt-2 mt-lg-0">
                            <a class="nav-link dropdown-toggle nav-profile d-flex align-items-center" href="#"
                                id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                                <!-- FOTO PROFIL atau DEFAULT AVATAR -->
                                <?php if ($has_photo): ?>
                                    <img src="<?= htmlspecialchars($profile_img) ?>" alt="Profil" class="navbar-profile-pic">
                                <?php else: ?>
                                    <span class="navbar-profile-default">
                                        <i class="bi bi-person-fill"></i>
                                    </span>
                                <?php endif; ?>

                                <!-- USERNAME -->
                                <span class="navbar-username"><?= htmlspecialchars($username) ?></span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">

                                <!-- Header Dropdown dengan Profile Picture -->
                                <div class="dropdown-profile-header">
                                    <div class="d-flex align-items-center">
                                        <?php if ($has_photo): ?>
                                            <img src="<?= htmlspecialchars($profile_img) ?>" alt="Profil"
                                                class="dropdown-profile-pic-large">
                                        <?php else: ?>
                                            <span class="dropdown-profile-default-large">
                                                <i class="bi bi-person-fill"></i>
                                            </span>
                                        <?php endif; ?>

                                        <div class="dropdown-profile-info">
                                            <div class="dropdown-profile-name">
                                                <?= htmlspecialchars($username) ?>
                                            </div>
                                            <div class="dropdown-profile-role">
                                                <?= htmlspecialchars($role) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($role === 'admin'): ?>
                                    <!-- Menu khusus ADMIN -->
                                    <li>
                                        <a class="dropdown-item" href="/project-semester-3-/admin/admin_dashboard.php">
                                            <i class="bi bi-speedometer2 me-2"></i>Dashboard Admin
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="/project-semester-3-/pages/edit_profil.php">
                                            <i class="bi bi-person-circle me-2"></i>Profil Saya
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <!-- Menu khusus USER -->
                                    <li>
                                        <a class="dropdown-item" href="/project-semester-3-/pages/edit_profil.php">
                                            <i class="bi bi-person-circle me-2"></i>Profil Saya
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

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