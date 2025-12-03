

<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="index3.html" class="brand-link">
    <img src="img/aston1.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">Aston Hotel & Resorts</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- User Panel -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block">Nyla Anidia Astri</a>
      </div>
    </div>

    <!-- Sidebar Search -->
    <div class="form-inline">
      <div class="input-group" data-widget="sidebar-search">
        <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-sidebar">
            <i class="fas fa-search fa-fw"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" role="menu">

        <!-- DASHBOARD -->
        <li class="nav-item">
          <a href="#" class="nav-link" onclick="toggleDropdown(event, 'dashboardMenu')">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Dashboard
              <i class="fas fa-angle-down right"></i>
            </p>
          </a>
          <ul id="dashboardMenu" class="nav flex-column dropdown-content" style="display: none; margin-left: 20px;">
            <li class="nav-item">
              <a href="dashboard.php" class="nav-link">
                <p>Dashboard v1</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- MASTER -->
        <li class="nav-item">
          <a href="#" class="nav-link" onclick="toggleDropdown(event, 'masterMenu')">
            <i class="nav-icon fas fa-folder"></i>
            <p>
              Master
              <i class="fas fa-angle-down right"></i>
            </p>
          </a>
          <ul id="masterMenu" class="nav flex-column dropdown-content" style="display: none; margin-left: 20px;">
            <li class="nav-item">
              <a href="pelanggan.php" class="nav-link"><p>Pelanggan</p></a>
            </li>
            <li class="nav-item">
              <a href="jenis-kamaradmin.php" class="nav-link"><p>Jenis Kamar</p></a>
            </li>
            <li class="nav-item">
              <a href="kamaradmin.php" class="nav-link"><p>Kamar</p></a>
            </li>
          </ul>
        </li>

        <!-- TRANSAKSI -->

        <li class="nav-item">
          <a href="#" class="nav-link" onclick="toggleDropdown(event, 'transaksiMenu')">
            <i class="nav-icon fas fa-exchange-alt"></i>
            <p>
              Transaksi
              <i class="fas fa-angle-down right"></i>
            </p>
          </a>
          <ul id="transaksiMenu" class="nav flex-column dropdown-content" style="display: none; margin-left: 25px;">
            <li class="nav-item"><a href="pemesanan.php" class="nav-link"><p>Pemesanan</p></a></li>
            <li class="nav-item"><a href="laundry.php" class="nav-link"><p>Laundry</p></a></li>
            <li class="nav-item"><a href="food_n_beverage.php" class="nav-link"><p>Food Beverage</p></a></li>
            <li class="nav-item"><a href="checkinout_admin.php" class="nav-link"><p>Check In Out</p></a></li>
            <li class="nav-item"><a href="pembayaran.php" class="nav-link"><p>Pembayaran</p></a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" onclick="toggleDropdown(event, 'laporanMenu')">
            <i class="nav-icon fas fa-file-alt"></i>
            <p>
              Laporan
              <i class="fas fa-angle-down right"></i>
            </p>
          </a>
          <ul id="laporanMenu" class="nav flex-column dropdown-content" style="display: none; margin-left: 25px;">
            <li class="nav-item"><a href="pemesanan.php" class="nav-link"><p>Pemesanan</p></a></li>
            <li class="nav-item"><a href="detail_laundry.php" class="nav-link"><p>Laundry</p></a></li>
            <li class="nav-item"><a href="detail_food_n_beverage.php" class="nav-link"><p>Food Beverage</p></a></li>
            <li class="nav-item"><a href="checkinout.php" class="nav-link"><p>Check In Out</p></a></li>
            <li class="nav-item"><a href="pembayaran.php" class="nav-link"><p>Pembayaran</p></a></li>
          </ul>
        </li>
         <li class="nav-item"><a href="data_feedback.php" class="nav-link"><p>Data Feedback</p></a></li>
       <a href="logout.php" class="btn btn-danger">Logout</a>
      </ul>
    </nav>
  </div>
</aside>

<!-- JavaScript -->
<script>
  function toggleDropdown(event, menuId) {
    event.preventDefault();
    const menu = document.getElementById(menuId);
    menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
  }
</script>

<style>
  .nav-link {
    cursor: pointer;
  }
  .dropdown-content {
    transition: all 0.3s ease;
  }
</style>
