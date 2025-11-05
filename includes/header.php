<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #E0F6FF;
            margin: 0;
        }

        /* ===== NAVBAR STYLE ===== */
        .navbar {
            background: #ffffff;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand img {
            max-height: 40px;
        }

        .navbar-brand, .nav-link {
            font-weight: 500;
            color: #333 !important;
            margin: 0 10px;
        }

        .nav-link:hover {
            color: #4682B4 !important;
            font-weight: 600;
        }

        .btn-login {
            border: 2px solid #4682B4;
            color: #4682B4;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            background-color: #4682B4;
            color: white;
        }

        .btn-signup {
            background-color: #4682B4;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-signup:hover {
            background-color: #315f86;
            color: white;
        }

        /* Responsive fix */
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
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand fw-bold text-4682B4" href="/project-semester-3-/pages/index.php">
                <img src="/project-semester-3-/img/logo_tk.png" alt="Logo"> TK Pertiwi
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="/project-semester-3-/pages/index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="/project-semester-3-/pages/profil.php">Profil Sekolah</a></li>
                    <li class="nav-item"><a class="nav-link" href="/project-semester-3-/pages/agenda.php">Agenda Kegiatan</a></li>
                    <li class="nav-item"><a class="nav-link" href="/project-semester-3-/pages/info_pbdb.php">Info PBDB</a></li>
                    <li class="nav-item"><a class="nav-link" href="/project-semester-3-/pages/kontak.php">Kontak</a></li>

                    <!-- Tombol Login dan Sign Up -->
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a href="/project-semester-3-/pages/login.php" class="btn btn-login btn-sm">Login</a>
                    </li>
                    <li class="nav-item ms-2 mt-2 mt-lg-0">
                        <a href="/project-semester-3-/pages/register.php" class="btn btn-signup btn-sm">Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Load Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
