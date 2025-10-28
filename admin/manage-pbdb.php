<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <title>Manajemen PPDB</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background-color: #eaf6ff;
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
    }

    .header .menu-toggle {
      font-size: 24px;
      cursor: pointer;
      margin-right: 20px;
    }

    .header .title {
      font-size: 18px;
      font-weight: 600;
    }

    .header .icons {
      margin-left: auto;
      display: flex;
      gap: 20px;
      font-size: 18px;
      cursor: pointer;
    }

    /* MAIN CONTENT */
    .main-content {
      margin-left: 250px;
      padding: 90px 30px 30px;
      transition: margin-left 0.3s;
    }

    .main-content.collapsed {
      margin-left: 60px;
    }

    h1 {
      font-size: 22px;
      margin-bottom: 20px;
    }

    /* FILTER SECTION */
    .filter-bar {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    select, input[type="text"] {
      padding: 8px 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }

    input[type="text"] {
      margin-left: auto;
    }

    /* TABLE SECTION */
    .table-container {
      background-color: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    th, td {
      padding: 12px 16px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #fff;
      font-weight: 600;
      border-bottom: 2px solid #ccc;
    }

    tr:nth-child(even) {
      background-color: #f4f4f4;
    }

    tr:hover {
      background-color: #e8f0ff;
    }

    /* STATUS COLORS */
    .status-pending {
      color: #ff9800;
      font-weight: 600;
    }
    .status-diterima {
      color: #4caf50;
      font-weight: 600;
    }
    .status-ditolak {
      color: #f44336;
      font-weight: 600;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
      }
      .filter-bar {
        flex-direction: column;
        align-items: flex-start;
      }
      input[type="text"] {
        width: 100%;
        margin-left: 0;
      }
    }
  </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="logo d-flex align-items-center">
            <i class="bi bi-list me-3 fs-4"></i> MANAGE PPDB
        </div>
        <div class="icons">
            <i class="bi bi-bell"></i>
            <i class="bi bi-envelope"></i>
            <i class="bi bi-person-circle"></i>
        </div>
    </div>

  <!-- SIDEBAR INCLUDE -->
  <?php include 'sidebar.php'; ?>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <h1>Tabel Data Pendaftar</h1>

    <div class="filter-bar">
      <select name="status">
        <option>Status</option>
        <option>Pending</option>
        <option>Diterima</option>
        <option>Ditolak</option>
      </select>

      <select name="tanggal">
        <option>Tanggal Daftar</option>
        <option>Terbaru</option>
        <option>Terlama</option>
      </select>

      <input type="text" placeholder="Search">
    </div>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Tanggal Pendaftaran</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Aisyoh Putri</td>
            <td>#2024001<br>12 Jan 2025</td>
            <td class="status-pending">Pending</td>
          </tr>
          <tr>
            <td>2</td>
            <td>Aisyoh Putri</td>
            <td>#2024001<br>12 Jan 2025</td>
            <td class="status-diterima">Diterima</td>
          </tr>
          <tr>
            <td>3</td>
            <td>Aisyoh Putri</td>
            <td>#2024001<br>12 Jan 2025</td>
            <td class="status-diterima">Diterima</td>
          </tr>
          <tr>
            <td>4</td>
            <td>Aisyoh Putri</td>
            <td>#2024001<br>12 Jan 2025</td>
            <td class="status-diterima">Diterima</td>
          </tr>
          <tr>
            <td>5</td>
            <td>Aisyoh Putri</td>
            <td>#2024001<br>12 Jan 2025</td>
            <td class="status-pending">Pending</td>
          </tr>
          <tr>
            <td>6</td>
            <td>Aisyoh Putri</td>
            <td>#2024001<br>12 Jan 2025</td>
            <td class="status-ditolak">Ditolak</td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>

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
