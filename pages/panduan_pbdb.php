<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    body {
        background: linear-gradient(135deg, #A8EFFF, #E0F7FF, #ffffff);
        background-size: 300% 300%;
        animation: gradientBG 8s ease infinite;
        font-family: 'Poppins', sans-serif;
    }

    @keyframes gradientBG {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    .title-box {
        text-align: center;
        margin-bottom: 40px;
    }

    .title-box h1 {
        font-weight: 700;
        font-size: 36px;
        color: #004AAD;
    }

    .guide-card {
        backdrop-filter: blur(12px);
        background: rgba(255, 255, 255, 0.35);
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transition: 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    .guide-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.18);
    }

    .guide-card h3 {
        font-weight: 600;
        font-size: 22px;
        color: #004AAD;
        margin-bottom: 15px;
    }

    .checklist li {
        margin-bottom: 10px;
        font-size: 15px;
    }

    .icon-box {
        font-size: 35px;
        color: #004AAD;
        margin-bottom: 10px;
    }

    .start-btn {
        background: #004AAD;
        color: white;
        padding: 12px 30px;
        border-radius: 12px;
        font-size: 18px;
        text-decoration: none;
        font-weight: 600;
        transition: 0.25s ease;
    }

    .start-btn:hover {
        background: #00357c;
        color: #fff;
    }

    .back-btn {
    background: #ffffffaa;
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,0.8);
    padding: 8px 18px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 500;
    color: #004AAD;
    cursor: pointer;
    transition: 0.25s ease;
}

.back-btn:hover {
    background: #ffffffdd;
    transform: translateX(-3px);
}

</style>

<div class="container py-5">

    <div class="title-box">
        <h1>Panduan PPDB TK Pertiwi</h1>
        <p class="text-muted">Informasi penting yang harus disiapkan sebelum mendaftar</p>
    </div>

    <div class="mb-4">
        <button onclick="history.back()" class="back-btn">
            <i class="bi bi-arrow-left"></i> Kembali
        </button>
    </div>


    <div class="row g-4">

        <!-- Data Anak -->
        <div class="col-md-6">
            <div class="guide-card">
                <div class="icon-box"><i class="bi bi-person-bounding-box"></i></div>
                <h3>Data Anak</h3>
                <ul class="checklist">
                    <li>Nama lengkap anak</li>
                    <li>Tanggal lahir anak</li>
                    <li>Alamat lengkap</li>
                    <li>Pas foto (JPG/PNG)</li>
                    <li>Akta kelahiran (JPG/PNG/PDF)</li>
                    <li>Surat keterangan sehat (JPG/PDF)</li>
                </ul>
            </div>
        </div>

        <!-- Data Orang Tua -->
        <div class="col-md-6">
            <div class="guide-card">
                <div class="icon-box"><i class="bi bi-people"></i></div>
                <h3>Data Orang Tua</h3>
                <ul class="checklist">
                    <li>Nama orang tua/wali</li>
                    <li>Nomor telepon aktif (WA disarankan)</li>
                    <li>Email aktif</li>
                </ul>
            </div>
        </div>

        <!-- Dokumen Keluarga -->
        <div class="col-md-6">
            <div class="guide-card">
                <div class="icon-box"><i class="bi bi-folder-check"></i></div>
                <h3>Dokumen Keluarga</h3>
                <ul class="checklist">
                    <li>Kartu Keluarga (KK) — JPG/PNG/PDF</li>
                </ul>
            </div>
        </div>

        <!-- Pembayaran -->
        <div class="col-md-6">
            <div class="guide-card">
                <div class="icon-box"><i class="bi bi-credit-card"></i></div>
                <h3>Informasi Pembayaran</h3>
                <ul class="checklist">
                    <li>Pilih metode pembayaran (Transfer/Tunai)</li>
                    <li>Siapkan bukti pembayaran (jika transfer)</li>
                </ul>
            </div>
        </div>
    </div>



</div>