<?php
include '../includes/header.php';
?>

<main class="container-fluid p-0">
    <section class="kontak-section py-5" style="background: #FFF3E0;">
        <div class="container">
            <h1 class="text-center mb-5 text-primary animate__animated animate__fadeIn">Kontak Kami</h1>
            <div class="row">
                <div class="col-md-6">
                    <h3 class="text-center mb-4 text-success">Info Kontak</h3>
                    <p><strong>Alamat:</strong> Jl. Raya Kopo No.1, Kec. Bojongloa Kidul, Kota Bandung</p>
                    <p><strong>Telepon:</strong> 022-1234567</p>
                    <p><strong>Email:</strong> tkpertiwi@gmail.com</p>
                    <p><strong>Jam Operasional:</strong> Senin-Jumat, 08:00 - 15:00 WIB</p>
                </div>
                <div class="col-md-6">
                    <h3 class="text-center mb-4 text-success">Form Kontak</h3>
                    <form>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email">
                        </div>
                        <div class="mb-3">
                            <label for="pesan" class="form-label">Pesan</label>
                            <textarea class="form-control" id="pesan" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
include '../includes/footer.php';
?>