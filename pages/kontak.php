<?php
include '../includes/header.php';
?>
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(180deg, #f9fcff, #e7f1ff);
        margin: 0;
    }

    h1, h3, label {
        font-family: 'Poppins', sans-serif;
    }

    .contact-container {
        max-width: 1100px;
        margin: auto;
        padding: 40px 20px;
    }

    /* Card Glass iOS */
    .glass-card {
        background: rgba(255, 255, 255, 0.35);
        border-radius: 20px;
        padding: 35px;
        border: 1.5px solid rgba(255, 255, 255, 0.55);
        backdrop-filter: blur(25px) saturate(180%);
        -webkit-backdrop-filter: blur(25px) saturate(180%);
        box-shadow: 0 20px 45px rgba(0,0,0,0.12);
        animation: fadeIn 0.7s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(25px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .section-title {
        font-size: 32px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 40px;
        color: #0f1d55;
    }

    /* Input field modern */
    .icon-input {
        position: absolute;
        left: 15px;
        top: 10px;
        font-size: 20px;
        color: #3f5ea1;
    }

    .form-control {
        padding-left: 45px;
        height: 48px;
        border-radius: 14px;
        border: 1px solid rgba(0,0,0,0.15);
        background: rgba(255,255,255,0.55);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    .form-control:focus {
        border: 1px solid #0066ff;
        background: rgba(255,255,255,0.8);
        box-shadow: 0 0 6px rgba(0,110,255,0.3);
    }

    textarea.form-control {
        height: auto;
        padding-top: 14px;
        padding-bottom: 14px;
    }

    /* Tombol */
    .btn-send {
        width: 100%;
        height: 48px;
        border-radius: 14px;
        background: #005BBB;
        color: white;
        font-weight: 600;
        font-size: 17px;
        border: none;
        transition: 0.3s;
    }

    .btn-send:hover {
        background: #004099;
        transform: scale(1.03);
    }

    .contact-info p {
        font-size: 15px;
        margin-bottom: 7px;
        color: #333;
    }

    .contact-icon {
        color: #005BBB;
        margin-right: 8px;
        font-size: 20px;
    }
</style>

<main class="contact-container">

    <h1 class="section-title">Kontak Kami</h1>

    <div class="row g-4">

        <!-- Info Kontak -->
        <div class="col-md-5">
            <div class="glass-card">
                <h3 class="text-primary mb-3 text-center">Info Kontak</h3>

                <div class="contact-info">
                    <p><i class="bi bi-geo-alt contact-icon"></i>Jl. WR Supratman No.6, Jember</p>
                    <p><i class="bi bi-telephone-forward contact-icon"></i>022-1234567</p>
                    <p><i class="bi bi-envelope contact-icon"></i>tkpertiwi@gmail.com</p>
                    <p><i class="bi bi-clock contact-icon"></i>Senin - Jumat, 08.00 - 15.00 WIB</p>
                </div>

                <!-- Map -->
                <div class="mt-4">
                    <iframe 
                        
                        width="100%" height="250" style="border-radius:14px; border:0;" allowfullscreen loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>

        <!-- Form Kontak -->
        <div class="col-md-7">
            <div class="glass-card">
                <h3 class="text-primary mb-4 text-center">Form Kontak</h3>

                <form>

                    <label class="mb-1">Nama</label>
                    <div class="position-relative mb-3">
                        <i class="bi bi-person icon-input"></i>
                        <input type="text" id="nama" class="form-control" placeholder="Masukkan nama">
                    </div>

                    <label class="mb-1">Email</label>
                    <div class="position-relative mb-3">
                        <i class="bi bi-envelope-at icon-input"></i>
                        <input type="email" id="email" class="form-control" placeholder="Masukkan email">
                    </div>

                    <label class="mb-1">Pesan</label>
                    <div class="position-relative mb-4">
                        <i class="bi bi-chat-dots icon-input"></i>
                        <textarea id="pesan" rows="4" class="form-control" placeholder="Tulis pesan..."></textarea>
                    </div>

                    <button type="submit" class="btn-send">
                        Kirim Pesan
                    </button>

                </form>

            </div>
        </div>

    </div>

</main>

<?php
$footerPath = $_SERVER['DOCUMENT_ROOT'] . '/project-semester-3-/includes/footer.php';
if (file_exists($footerPath)) {
    include $footerPath;
}
?>
