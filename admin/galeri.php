<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unggah Foto - TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #E0F7FA;
            margin: 0;
        }

        /* Header */
        .header {
            background-color: #2196F3;
            color: #fff;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .header .logo {
            font-weight: 600;
            font-size: 18px;
        }

        .header .icons i {
            font-size: 20px;
            margin-left: 20px;
            cursor: pointer;
        }

        /* Content */
        .content {
            margin-left: 250px;
            padding: 100px 40px 40px;
        }

        .card-upload {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            padding: 30px;
        }

        .upload-box {
            border: 2px dashed #ccc;
            border-radius: 10px;
            text-align: center;
            padding: 50px 20px;
            transition: border-color 0.3s ease;
        }

        .upload-box:hover {
            border-color: #2196F3;
        }

        .upload-box i {
            font-size: 40px;
            color: #2196F3;
        }

        .upload-box p {
            margin-top: 10px;
            color: #555;
        }

        textarea {
            width: 100%;
            height: 150px;
            border-radius: 10px;
            border: 1px solid #ccc;
            padding: 10px;
            resize: none;
        }

        .btn-upload {
            background-color: #2196F3;
            border: none;
            color: #fff;
            padding: 10px 25px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .btn-upload:hover {
            background-color: #1976D2;
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 0;
                padding: 90px 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="logo d-flex align-items-center">
            <i class="bi bi-list me-3 fs-4"></i> KELOLA GALERI
        </div>
        <div class="icons">
            <i class="bi bi-bell"></i>
            <i class="bi bi-envelope"></i>
            <i class="bi bi-person-circle"></i>
        </div>
    </div>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Content -->
    <div class="content">
        <h4 class="fw-bold mb-4">Unggah Foto</h4>

        <div class="card-upload">
            <div class="row g-4 align-items-start">
                <!-- Box Upload -->
                <div class="col-md-6">
                    <div class="upload-box">
                        <i class="bi bi-cloud-arrow-up"></i>
                        <p>
                            Seret & lepas atau 
                            <a href="#" onclick="document.getElementById('fileInput').click(); return false;">Klik untuk memilih file</a><br>
                            PNG, JPG, GIF hingga 10MB
                        </p>
                        <input type="file" id="fileInput" hidden>
                    </div>
                </div>

                <!-- Caption -->
                <div class="col-md-6">
                    <textarea placeholder="Tambahkan caption untuk foto ini"></textarea>
                    <button class="btn-upload">Unggah</button>
                </div>
            </div>
        </div>
    </div>

        <script>
        // Sidebar toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        });
    </script>

</body>
</html>
