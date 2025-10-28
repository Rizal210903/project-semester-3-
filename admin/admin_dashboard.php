<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - TK Pertiwi</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #eaf6ff;
      overflow-x: hidden;
    }

    /* SIDEBAR */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      height: 100vh;
      background-color: #fff;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
      transition: width 0.3s ease, left 0.3s ease;
      z-index: 99;
      overflow-y: auto;
    }

    .sidebar.collapsed {
      width: 70px;
    }

    .sidebar .logo {
      font-size: 20px;
      font-weight: 700;
      color: #007bff;
      margin-bottom: 30px;
      text-align: center;
    }

    .sidebar.collapsed .logo span,
    .sidebar.collapsed .nav-link span,
    .sidebar.collapsed .dropdown-menu {
      display: none;
    }

    .sidebar .nav-link {
      display: flex;
      align-items: center;
      padding: 12px 15px;
      color: #333;
      border-radius: 8px;
      margin-bottom: 8px;
      transition: 0.3s;
      text-decoration: none;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      background-color: #e6f0ff;
      color: #007bff;
    }

    .sidebar .nav-link i {
      font-size: 18px;
      margin-right: 12px;
      width: 25px;
      text-align: center;
    }

    .dropdown-toggle::after {
      margin-left: auto;
      transition: transform 0.3s;
    }

    .dropdown-menu {
      padding-left: 40px;
      background: transparent;
      border: none;
      box-shadow: none;
    }

    .dropdown-menu .nav-link {
      padding: 8px 15px;
      font-size: 14px;
    }

    .show .dropdown-toggle::after {
      transform: rotate(180deg);
    }

    /* HEADER */
    .header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 60px;
      background-color: #007bff;
      display: flex;
      align-items: center;
      padding: 0 20px;
      color: #fff;
      z-index: 1000;
      justify-content: space-between;
    }

    .menu-toggle {
      font-size: 24px;
      cursor: pointer;
    }

    .header .title {
      font-size: 18px;
      font-weight: 600;
    }

    .header .icons {
      display: flex;
      gap: 20px;
      font-size: 18px;
      cursor: pointer;
    }

    /* MAIN CONTENT */
    .main-content {
      margin-left: 250px;
      padding: 90px 25px 30px;
      transition: margin-left 0.3s ease;
    }

    .main-content.collapsed {
      margin-left: 70px;
    }

    h1 {
      font-size: 22px;
      margin-bottom: 20px;
      font-weight: 600;
      color: #333;
    }

    /* CARD */
    .card-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 25px;
    }

    .card-box {
      flex: 1 1 250px;
      background-color: #fff;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      position: relative;
    }

    .card-box::before {
      content: "";
      position: absolute;
      left: 0;
      top: 0;
      width: 5px;
      height: 100%;
      background-color: #007bff;
      border-radius: 10px 0 0 10px;
    }

    .card-box .number {
      font-size: 26px;
      font-weight: 700;
      color: #007bff;
    }

    .card-box .label {
      font-size: 14px;
      color: #555;
    }

    /* BLUE CARD */
    .blue-card {
      background-color: #007bff;
      color: white;
      border-radius: 12px;
      padding: 25px;
      margin-bottom: 25px;
      text-align: center;
    }

    .blue-card h2 {
      font-size: 18px;
      margin-bottom: 20px;
      font-weight: 600;
    }

    .blue-card .stats {
      display: flex;
      justify-content: center;
      gap: 60px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .blue-card .stats > div {
      text-align: center;
    }

    .blue-card .number {
      font-size: 28px;
      font-weight: bold;
    }

    .blue-card .label {
      font-size: 14px;
      opacity: 0.9;
    }

    .blue-card .btn-kelola {
      background-color: #339dff;
      border: none;
      padding: 10px 30px;
      border-radius: 8px;
      color: white;
      font-weight: 600;
      font-size: 15px;
      cursor: pointer;
      transition: 0.3s;
      text-decoration: none;
      display: inline-block;
    }

    .blue-card .btn-kelola:hover {
      background-color: #006ae0;
    }

    /* GRAPH */
    .graph-container {
      background-color: #fff;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .graph-container h2 {
      font-size: 18px;
      margin-bottom: 15px;
      color: #333;
    }

    @media (max-width: 768px) {
      .sidebar {
        left: -250px;
      }

      .sidebar.active {
        left: 0;
      }

      .main-content {
        margin-left: 0;
        padding: 90px 15px;
      }
    }
  </style>
</head>
<body>

  <!-- HEADER -->
  <div class="header">
    <i class="bi bi-list menu-toggle"></i>
    <div class="title">ADMIN DASHBOARD</div>
    <div class="icons">
      <i class="bi bi-bell"></i>
      <i class="bi bi-envelope"></i>
      <i class="bi bi-person-circle"></i>
    </div>
  </div>

  <!-- SIDEBAR -->
  <?php include 'sidebar.php'; ?>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <h1>Dashboard</h1>

    <div class="card-container">
      <div class="card-box">
        <div class="number">25</div>
        <div class="label">Menunggu Verifikasi</div>
      </div>
      <div class="card-box">
        <div class="number">10</div>
        <div class="label">Diterima</div>
      </div>
    </div>

    <div class="blue-card">
      <h2>Pendaftar Baru Tahun Ini</h2>
      <div class="stats">
        <div>
          <div class="number">25</div>
          <div class="label">Menunggu Verifikasi</div>
        </div>
        <div>
          <div class="number">10</div>
          <div class="label">Diterima</div>
        </div>
      </div>
      <a href="manage-pbdb.php" class="btn-kelola">Kelola Pendaftaran</a>
    </div>

    <div class="graph-container">
      <h2>Grafik Pendaftaran</h2>
      <canvas id="myChart" height="100"></canvas>
    </div>
  </main>

  <!-- Bootstrap & Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Sidebar Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    if (menuToggle) {
      menuToggle.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
          sidebar.classList.toggle('active');
        } else {
          sidebar.classList.toggle('collapsed');
          mainContent.classList.toggle('collapsed');
        }
      });
    }

    // Sidebar dropdown (tidak auto tertutup)
    document.querySelectorAll('.dropdown-toggle').forEach(item => {
      item.addEventListener('click', e => {
        e.preventDefault();
        const menu = item.nextElementSibling;
        menu.classList.toggle('show');
      });
    });

    // Chart.js
    const ctx = document.getElementById('myChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['2020', '2021', '2022', '2023', '2024'],
        datasets: [{
          label: 'Pendaftaran',
          data: [20, 55, 35, 45, 60],
          fill: true,
          backgroundColor: 'rgba(0, 123, 255, 0.15)',
          borderColor: '#007bff',
          borderWidth: 2,
          tension: 0.4,
          pointBackgroundColor: '#007bff',
          pointRadius: 4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            max: 70,
            ticks: { stepSize: 10 }
          }
        }
      }
    });
  </script>
</body>
</html>
