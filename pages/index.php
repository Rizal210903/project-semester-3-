<?php
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <main class="container-fluid p-0">
        <!-- Hero Section Slider -->
        <section class="hero-section">
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" style="height: 100vh;">
                <div class="carousel-inner">
                    <div class="carousel-item active" style="background: #FFFFFF; height: 100%; background-image: url('<?php echo file_exists('../img/foto.png') ? '../img/foto.png' : 'https://via.placeholder.com/1200x800'; ?>'); background-size: cover; background-position: center;">
                        <div class="carousel-caption d-none d-md-block" style="top: 50%; transform: translateY(-50%);">
                            <h1 class="display-3 fw-bold text-white fade-text" style="font-family: 'Poppins', sans-serif;">Membangun Masa Depan Anak</h1>
                            <p class="lead text-white fade-text" style="font-family: 'Poppins', sans-serif;">Pendidikan Berkualitas untuk Generasi Muda</p>
                        </div>
                    </div>
                    <div class="carousel-item" style="background: #FFFFFF; height: 100%; background-image: url('<?php echo file_exists('../img/foto3.png') ? '../img/foto3.png' : 'https://via.placeholder.com/1200x800'; ?>'); background-size: cover; background-position: center;">
                        <div class="carousel-caption d-none d-md-block" style="top: 50%; transform: translateY(-50%);">
                            <h1 class="display-3 fw-bold text-white fade-text" style="font-family: 'Poppins', sans-serif;">Ceria Belajar Bersama</h1>
                            <p class="lead text-white fade-text" style="font-family: 'Poppins', sans-serif;">Lingkungan Ramah untuk Anak-Anak</p>
                        </div>
                    </div>
                    <div class="carousel-item" style="background: #FFFFFF; height: 100%; background-image: url('<?php echo file_exists('../img/fototi.png') ? '../img/fototi.png' : 'https://via.placeholder.com/1200x800'; ?>'); background-size: cover; background-position: center;">
                        <div class="carousel-caption d-none d-md-block" style="top: 50%; transform: translateY(-50%);">
                            <h1 class="display-3 fw-bold text-white fade-text" style="font-family: 'Poppins', sans-serif;">Inspirasi dari Awal</h1>
                            <p class="lead text-white fade-text" style="font-family: 'Poppins', sans-serif;">Fondasi Pendidikan Terbaik untuk Anak</p>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                <!-- Indikator Slider -->
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
            </div>
            <svg class="star-decoration" width="60" height="60" viewBox="0 0 24 24" fill="#FFF" style="position: absolute; top: 20px; left: 20px; animation: float 3s infinite; opacity: 0.7;">
                <path d="M12 .587l3.668 7.431 8.332 1.151-6 5.873 1.417 8.278L12 18.804l-7.417 3.516L6 15.042l-6-5.873 8.332-1.151z"/>
            </svg>
            <svg class="star-decoration" width="50" height="50" viewBox="0 0 24 24" fill="#FFF" style="position: absolute; bottom: 20px; right: 20px; animation: float 3s infinite; opacity: 0.7;">
                <path d="M12 .587l3.668 7.431 8.332 1.151-6 5.873 1.417 8.278L12 18.804l-7.417 3.516L6 15.042l-6-5.873 8.332-1.151z"/>
            </svg>
        </section>

        <!-- Gallery Foto Ekstra Kurikuler -->
        <section class="gallery-section py-5">
            <div class="container">
                <h2 class="text-center mb-5" style="color: #000080; font-family: 'Poppins', sans-serif; animation: bounce 1s;">Galeri Kegiatan</h2>
                <div class="text-center mb-4">
                    <button class="btn btn-outline-info mx-2 animate__animated animate__fadeIn" onclick="showCategory('all')">Semua</button>
                    <button class="btn btn-outline-info mx-2 animate__animated animate__fadeIn" onclick="showCategory('kegiatan')" style="animation-delay: 0.2s;">Foto Kegiatan Siswa</button>
                    <button class="btn btn-outline-info mx-2 animate__animated animate__fadeIn" onclick="showCategory('prestasi')" style="animation-delay: 0.4s;">Prestasi</button>
                    <button class="btn btn-outline-info mx-2 animate__animated animate__fadeIn" onclick="showCategory('ekstrakurikuler')" style="animation-delay: 0.6s;">Ekstrakurikuler</button>
                </div>
                <div id="gallery-content" class="row g-4 justify-content-center">
                    <!-- Default: Semua -->
                    <div class="col-md-3 gallery-item all kegiatan"><img src="<?php echo file_exists('../Uploads/ekstra1_edited.jpg') ? '../Uploads/ekstra1_edited.jpg' : 'https://via.placeholder.com/300x200'; ?>" alt="Ekstra 1" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="animation-delay: 0.2s;"></div>
                    <div class="col-md-3 gallery-item all prestasi"><img src="<?php echo file_exists('../img/placeholder2.jpg') ? '../img/placeholder2.jpg' : 'https://via.placeholder.com/300x200'; ?>" alt="Ekstra 2" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="animation-delay: 0.4s;"></div>
                    <div class="col-md-3 gallery-item all kegiatan"><img src="<?php echo file_exists('../img/placeholder3.jpg') ? '../img/placeholder3.jpg' : 'https://via.placeholder.com/300x200'; ?>" alt="Ekstra 3" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="animation-delay: 0.6s;"></div>
                    <div class="col-md-3 gallery-item all prestasi"><img src="<?php echo file_exists('../img/placeholder4.jpg') ? '../img/placeholder4.jpg' : 'https://via.placeholder.com/300x200'; ?>" alt="Ekstra 4" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="animation-delay: 0.8s;"></div>
                    <div class="col-md-3 gallery-item all ekstrakurikuler"><img src="<?php echo file_exists('../img/placeholder5.jpg') ? '../img/placeholder5.jpg' : 'https://via.placeholder.com/300x200'; ?>" alt="Ekstra 5" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="animation-delay: 1.0s;"></div>
                    <div class="col-md-3 gallery-item all ekstrakurikuler"><img src="<?php echo file_exists('../img/placeholder6.jpg') ? '../img/placeholder6.jpg' : 'https://via.placeholder.com/300x200'; ?>" alt="Ekstra 6" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="animation-delay: 1.2s;"></div>
                </div>
            </div>
        </section>

        <!-- Peta Lokasi -->
        <section class="map-section py-5">
            <div class="container">
                <h2 class="text-center mb-5" style="color: #000080; font-family: 'Poppins', sans-serif; animation: bounce 1s;">Lokasi Kami</h2>
                <div class="ratio ratio-16x9">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.2639494126097!2d113.69934897432888!3d-8.17614258195306!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd69425c12acb8b%3A0x86fdf63c580222fc!2sTK%20Pertiwi!5e0!3m2!1sid!2sid!4v1759136704596!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </section>
    </main>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .hero-section {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            background: #FFFFFF;
        }

        .carousel-inner {
            height: 100%;
        }

        .carousel-item {
            height: 100%;
            background-size: cover;
            background-position: center;
        }

        .carousel-caption {
            text-align: center !important;
            text-shadow: 2px 2px #4682B4;
        }

        .carousel-caption h1 {
            font-family: 'Poppins', sans-serif;
        }

        .carousel-caption p {
            font-family: 'Poppins', sans-serif;
            text-shadow: 1px 1px #4682B4;
        }

        .sambutan-section {
            background: #FFFFFF;
        }

        .sambutan-box {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #87CEEB;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .sambutan-section img {
            max-width: 200px;
            border: 5px solid #87CEEB;
            filter: drop-shadow(0 0 10px #87CEEB);
            position: relative;
            z-index: 1;
        }

        .gallery-section {
            background: #FFFFFF;
        }

        .gallery-item {
            display: none;
        }

        .gallery-item.all {
            display: block;
        }

        .gallery-item.kegiatan,
        .gallery-item.prestasi,
        .gallery-item.ekstrakurikuler {
            display: none;
        }

        .gallery-section button {
            background: rgba(135, 206, 235, 0.2);
            transition: transform 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .gallery-section button:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .map-section {
            background: #FFFFFF;
        }

        .star-decoration {
            opacity: 0.7;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        /* Animasi Fade Out dengan Scale Down */
        .fade-text {
            opacity: 1;
            transform: scale(1);
            transition: opacity 1.5s ease-out, transform 1.5s ease-out;
        }

        .fade-text.fade-out {
            opacity: 0;
            transform: scale(0.8);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const carousel = new bootstrap.Carousel(document.getElementById('heroCarousel'), {
            interval: 5000,
            ride: 'carousel'
        });

        function fadeOutText() {
            const fadeTexts = document.querySelectorAll('.fade-text');
            fadeTexts.forEach(text => {
                setTimeout(() => {
                    text.classList.add('fade-out');
                }, 3000);
            });
        }

        carousel._element.addEventListener('slid.bs.carousel', function (event) {
            const activeSlide = document.querySelector('.carousel-item.active .carousel-caption');
            if (activeSlide) {
                const fadeTexts = activeSlide.querySelectorAll('.fade-text');
                fadeTexts.forEach(text => {
                    text.classList.remove('fade-out');
                    fadeOutText();
                });
            }
        });

        fadeOutText();
    });

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

    <?php
    $footerPath = $_SERVER['DOCUMENT_ROOT'] . '/project-semester-3-/includes/footer.php';
    if (!file_exists($footerPath)) {
        echo "<p style='color:red;'>Footer ga ketemu di: $footerPath</p>";
        exit;
    }
    include $footerPath;
    ?>
</body>
</html>