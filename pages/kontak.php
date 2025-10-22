<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TK Pertiwi - Beranda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/project-semester-3-/assets/css/style.css?v=1.6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <style>
        body {
            font-family: 'Comic Sans MS', sans-serif;
            margin: 0;
            background: #E0F6FF; /* Biru muda lembut */
        }
        .navbar {
            background: #ffffffff;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .navbar-brand img {
            max-height: 40px;
        }
        .navbar-brand, .nav-link {
            font-family: 'Poppins', sans-serif; /* Font navbar Poppins */
            font-weight: 500; /* Sedang */
            color: #333 !important; /* Teks hitam lembut */
            margin: 0 10px;
        }
        .nav-link:hover {
            color: #4682B4 !important; /* Biru steel saat hover */
            font-weight: 600; /* Lebih tebal saat hover */
        }
        .hero-section {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
        }
        .sambutan-section {
            background: linear-gradient(135deg, #D1F2EB, #E0F7FA);
            padding: 50px 0;
        }
        .gallery-section {
            background: linear-gradient(135deg, #B0E0E6, #D1F2EB);
            padding: 50px 0;
        }
        .map-section {
            background: linear-gradient(135deg, #D1F2EB, #E0F7FA);
            padding: 50px 0;
        }
        .text-4682B4 { color: #4682B4; }
        .text-333 { color: #333; }
        .text-555 { color: #555; }
        .sambutan-box {
            background: rgba(209, 242, 235, 0.95);
            padding: 20px;
            border-radius: 10px;
            border: 3px dashed #87CEEB;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-4682B4" href="/project-semester-3-/pages/index.php">
                <img src="/project-semester-3-/img/logo_tk.png" alt="Logo" style="width: 40px; height: auto;"> TK Pertiwi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link animate__animated animate__fadeIn" href="/project-semester-3-/pages/index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link animate__animated animate__fadeIn" href="/project-semester-3-/pages/profil.php" style="animation-delay:  0.1s;">Profil Sekolah</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link animate__animated animate__fadeIn" href="/project-semester-3-/pages/agenda.php" style="animation-delay: 0.2s;">Agenda Kegiatan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link animate__animated animate__fadeIn" href="/project-semester-3-/pages/info_pbdb.php" style="animation-delay: 0.3s;">Info PBDB</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link animate__animated animate__fadeIn" href="/project-semester-3-/pages/kontak.php" style="animation-delay: 0.4s;">Kontak</a>
                    </li>
                    
                </ul>
            </div>
        </div>
    </nav>
<main class="container-fluid p-0">
    <section class="kontak-section py-5" style="background: #E0F6FF;">
        <div class="container">
            <h1 class="text-center mb-5 text-primary animate__animated animate__fadeIn" style="font-family: 'Poppins', sans-serif;">Kontak Kami</h1>
            <div class="row">
                <div class="col-md-6">
                    <h3 class="text-center mb-4 text-success" style="font-family: 'Poppins', sans-serif;">Info Kontak</h3>
                    <p style="font-family: 'Poppins', sans-serif;"><strong>Alamat:</strong> Jl. WR Supratman No.6, Kauman, Kepatihan, Kec. Kaliwates, Kabupaten Jember, Jawa Timur 68131</p>
                    <p style="font-family: 'Poppins', sans-serif;"><strong>Telepon:</strong> 022-1234567</p>
                    <p style="font-family: 'Poppins', sans-serif;"><strong>Email:</strong> tkpertiwi@gmail.com</p>
                    <p style="font-family: 'Poppins', sans-serif;"><strong>Jam Operasional:</strong> Senin-Jumat, 08:00 - 15:00 WIB</p>
                </div>
                <div class="col-md-6">
                    <h3 class="text-center mb-4 text-success" style="font-family: 'Poppins', sans-serif;">Form Kontak</h3>
                    <form>
                        <div class="mb-3">
                            <label for="nama" class="form-label" style="font-family: 'Poppins', sans-serif;">Nama</label>
                            <input type="text" class="form-control" id="nama">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label" style="font-family: 'Poppins', sans-serif;">Email</label>
                            <input type="email" class="form-control" id="email">
                        </div>
                        <div class="mb-3">
                            <label for="pesan" class="form-label" style="font-family: 'Poppins', sans-serif;">Pesan</label>
                            <textarea class="form-control" id="pesan" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="font-family: 'Poppins', sans-serif;">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"></script>
    <script>
    function showCategory(category) {
        const items = document.querySelectorAll('.gallery-item');
        items.forEach(item => {
            item.style.display = 'none';
            if (category === 'all' || item.classList.contains(category)) {
                item.style.display = 'block';
            }
        });
    }
    </script>
    <footer class="bg-light text-center p-3 mt-5" style="background: #E0F6FF; color: #333;">
        <p class="mb-0">&copy; 2025 TK Pertiwi. Semua hak cipta dilindungi.</p>
    </footer>
</body>
</html>