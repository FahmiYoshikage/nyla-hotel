<?php
include 'koneksi.php';

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi Database Gagal: " . $koneksi->connect_error);
}

// Query untuk statistik kamar
$sql_stats = "SELECT 
                COUNT(*) as total_kamar,
                SUM(CASE WHEN STATUS = 'Tersedia' THEN 1 ELSE 0 END) as tersedia,
                SUM(CASE WHEN STATUS = 'Terisi' THEN 1 ELSE 0 END) as terisi,
                SUM(CASE WHEN STATUS = 'Maintenance' THEN 1 ELSE 0 END) as maintenance
              FROM kamar";
$result_stats = $koneksi->query($sql_stats);
$stats = $result_stats->fetch_assoc();

// Query untuk statistik per jenis kamar
$sql_jenis = "SELECT 
                jk.JENIS_KAMAR,
                COUNT(k.ID_KAMAR) as jumlah,
                SUM(CASE WHEN k.STATUS = 'Tersedia' THEN 1 ELSE 0 END) as tersedia,
                SUM(CASE WHEN k.STATUS = 'Terisi' THEN 1 ELSE 0 END) as terisi
              FROM jenis_kamar jk
              LEFT JOIN kamar k ON jk.ID_JENIS_KAMAR = k.ID_JENIS_KAMAR
              GROUP BY jk.ID_JENIS_KAMAR, jk.JENIS_KAMAR
              ORDER BY jk.JENIS_KAMAR";
$result_jenis = $koneksi->query($sql_jenis);

// Siapkan data untuk chart
$jenis_kamar_labels = [];
$jenis_kamar_data = [];
$jenis_kamar_tersedia = [];
$jenis_kamar_terisi = [];

while ($row = $result_jenis->fetch_assoc()) {
    $jenis_kamar_labels[] = $row['JENIS_KAMAR'];
    $jenis_kamar_data[] = $row['jumlah'];
    $jenis_kamar_tersedia[] = $row['tersedia'];
    $jenis_kamar_terisi[] = $row['terisi'];
}

// Query untuk data tabel
$sql_table = "SELECT 
                kamar.ID_KAMAR,
                kamar.NO_KAMAR,
                kamar.STATUS,
                jenis_kamar.JENIS_KAMAR,
                jenis_kamar.TARIF,
                jenis_kamar.FASILITAS
              FROM kamar
              INNER JOIN jenis_kamar 
                ON kamar.ID_JENIS_KAMAR = jenis_kamar.ID_JENIS_KAMAR
              ORDER BY kamar.NO_KAMAR";

$result_table = $koneksi->query($sql_table);

// Hitung persentase
$persentase_tersedia = ($stats['total_kamar'] > 0) ? round(($stats['tersedia'] / $stats['total_kamar']) * 100, 1) : 0;
$persentase_terisi = ($stats['total_kamar'] > 0) ? round(($stats['terisi'] / $stats['total_kamar']) * 100, 1) : 0;
$persentase_maintenance = ($stats['total_kamar'] > 0) ? round(($stats['maintenance'] / $stats['total_kamar']) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard | Admin Hotel</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  
  <!-- Custom CSS untuk Dashboard Kamar -->
  <style>
    /* Info Box Custom Styles */
    .info-box {
      min-height: 90px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      border-radius: 10px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .info-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .info-box-icon {
      border-radius: 10px 0 0 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .info-box-icon > i {
      font-size: 3rem;
    }
    
    .info-box-number {
      font-size: 2rem;
      font-weight: bold;
    }
    
    .info-box-text {
      text-transform: uppercase;
      font-weight: 600;
      font-size: 0.85rem;
    }
    
    /* Card Styles */
    .card {
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    .card-header {
      border-radius: 10px 10px 0 0;
      font-weight: bold;
    }
    
    /* Progress Bar Styles */
    .progress {
      height: 25px;
      border-radius: 10px;
      font-weight: bold;
    }
    
    .progress-bar {
      line-height: 25px;
      font-size: 0.9rem;
    }
    
    /* Chart Container */
    .chart-container {
      position: relative;
      height: 300px;
      margin: 20px 0;
    }
    
    /* Status Badge */
    .status-badge {
      padding: 5px 15px;
      border-radius: 20px;
      font-weight: bold;
      font-size: 0.85rem;
    }
    
    .status-tersedia {
      background-color: #28a745;
      color: white;
    }
    
    .status-terisi {
      background-color: #dc3545;
      color: white;
    }
    
    .status-maintenance {
      background-color: #ffc107;
      color: #000;
    }
    
    /* Button Styles */
    .btn-action {
      border-radius: 5px;
      padding: 5px 15px;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .btn-action:hover {
      transform: scale(1.05);
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    }
    
    /* DataTable Custom */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
      background: linear-gradient(to bottom, #007bff 0%, #0056b3 100%);
      color: white !important;
      border-radius: 5px;
    }
    
    /* Room Card Grid */
    .room-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      gap: 15px;
      margin: 20px 0;
    }
    
    .room-card {
      border: 2px solid #ddd;
      border-radius: 10px;
      padding: 15px;
      text-align: center;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    
    .room-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .room-card.tersedia {
      background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
      border-color: #28a745;
    }
    
    .room-card.terisi {
      background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
      border-color: #dc3545;
    }
    
    .room-card.maintenance {
      background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
      border-color: #ffc107;
    }
    
    .room-number {
      font-size: 1.5rem;
      font-weight: bold;
      margin-bottom: 5px;
    }
    
    .room-type {
      font-size: 0.85rem;
      color: #666;
      margin-bottom: 10px;
    }
    
    .room-status {
      font-size: 0.8rem;
      font-weight: bold;
      padding: 3px 10px;
      border-radius: 15px;
      display: inline-block;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .room-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
      }
      
      .info-box-number {
        font-size: 1.5rem;
      }
    }
  </style>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include 'header.php'; ?>
  <?php include 'navbar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><i class="fas fa-chart-pie"></i> DASHBOARD</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        
        <!-- Info Boxes Row -->
        <div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1">
                <i class="fas fa-door-open"></i>
              </span>
              <div class="info-box-content">
                <span class="info-box-text">Total Kamar</span>
                <span class="info-box-number"><?= $stats['total_kamar']; ?></span>
              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-success elevation-1">
                <i class="fas fa-check-circle"></i>
              </span>
              <div class="info-box-content">
                <span class="info-box-text">Kamar Tersedia</span>
                <span class="info-box-number"><?= $stats['tersedia']; ?></span>
              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-danger elevation-1">
                <i class="fas fa-user-check"></i>
              </span>
              <div class="info-box-content">
                <span class="info-box-text">Kamar Terisi</span>
                <span class="info-box-number"><?= $stats['terisi']; ?></span>
              </div>
            </div>
          </div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-warning elevation-1">
                <i class="fas fa-tools"></i>
              </span>
              <div class="info-box-content">
                <span class="info-box-text">Maintenance</span>
                <span class="info-box-number"><?= $stats['maintenance']; ?></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Progress Bars Row -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header bg-gradient-info">
                <h3 class="card-title"><i class="fas fa-chart-bar"></i> Status Kamar Overview</h3>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <small class="text-muted">Kamar Tersedia</small>
                  <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: <?= $persentase_tersedia; ?>%" 
                         aria-valuenow="<?= $persentase_tersedia; ?>" 
                         aria-valuemin="0" aria-valuemax="100">
                      <?= $stats['tersedia']; ?> Kamar (<?= $persentase_tersedia; ?>%)
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <small class="text-muted">Kamar Terisi</small>
                  <div class="progress">
                    <div class="progress-bar bg-danger" role="progressbar" 
                         style="width: <?= $persentase_terisi; ?>%" 
                         aria-valuenow="<?= $persentase_terisi; ?>" 
                         aria-valuemin="0" aria-valuemax="100">
                      <?= $stats['terisi']; ?> Kamar (<?= $persentase_terisi; ?>%)
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <small class="text-muted">Maintenance</small>
                  <div class="progress">
                    <div class="progress-bar bg-warning" role="progressbar" 
                         style="width: <?= $persentase_maintenance; ?>%" 
                         aria-valuenow="<?= $persentase_maintenance; ?>" 
                         aria-valuemin="0" aria-valuemax="100">
                      <?= $stats['maintenance']; ?> Kamar (<?= $persentase_maintenance; ?>%)
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header bg-gradient-primary">
                <h3 class="card-title"><i class="fas fa-chart-pie"></i> Distribusi Status Kamar</h3>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <canvas id="pieChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card">
              <div class="card-header bg-gradient-success">
                <h3 class="card-title"><i class="fas fa-chart-bar"></i> Kamar per Jenis</h3>
              </div>
              <div class="card-body">
                <div class="chart-container">
                  <canvas id="barChart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Room Grid Visualization -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header bg-gradient-secondary">
                <h3 class="card-title"><i class="fas fa-th"></i> Visualisasi Kamar</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="room-grid">
                  <?php
          $result_table->data_seek(0);
          while ($row = $result_table->fetch_assoc()) {

              $status_class = strtolower($row['STATUS']);
              $status_text = $row['STATUS'];

              if ($status_class == 'tersedia') {
                  $status_class = 'tersedia';
              } elseif ($status_class == 'terisi') {
                  $status_class = 'terisi';
              } else {
                  $status_class = 'maintenance';
              }
               $tarif_format = "Rp " . number_format($row['TARIF'], 0, ',', '.');

              echo "
              <div class='room-card {$status_class}' 
                   data-toggle='tooltip' 
                   title='{$row['JENIS_KAMAR']} - {$row['STATUS']}'>
                   
                <div class='room-number'>{$row['NO_KAMAR']}</div>
                
                <div class='room-type'>{$row['JENIS_KAMAR']}</div>

                <div class='room-fasilitas'>
                    <b>Fasilitas:</b> {$row['FASILITAS']}
                </div>

                <div class='room-tarif'>
                    <b>Tarif:</b> {$tarif_format}
                </div>

                <span class='room-status'>{$status_text}</span>

              </div>";
          }
          ?>
                </div>
              </div>
            </div>
          </div>
        </div>


      </div>
    </section>
  </div>

  <?php include 'footer.php'; ?>
</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
$(function () {
  // Initialize DataTable
  $("#example1").DataTable({
    "responsive": true,
    "lengthChange": true,
    "autoWidth": false,
    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
    "language": {
      "search": "Cari:",
      "lengthMenu": "Tampilkan _MENU_ data per halaman",
      "zeroRecords": "Data tidak ditemukan",
      "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
      "infoEmpty": "Tidak ada data tersedia",
      "infoFiltered": "(difilter dari _MAX_ total data)",
      "paginate": {
        "first": "Pertama",
        "last": "Terakhir",
        "next": "Selanjutnya",
        "previous": "Sebelumnya"
      }
    }
  }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

  // Initialize tooltips
  $('[data-toggle="tooltip"]').tooltip();

  // Pie Chart - Status Kamar
  var ctxPie = document.getElementById('pieChart').getContext('2d');
  var pieChart = new Chart(ctxPie, {
    type: 'pie',
    data: {
      labels: ['Tersedia', 'Terisi', 'Maintenance'],
      datasets: [{
        data: [<?= $stats['tersedia']; ?>, <?= $stats['terisi']; ?>, <?= $stats['maintenance']; ?>],
        backgroundColor: [
          'rgba(40, 167, 69, 0.8)',
          'rgba(220, 53, 69, 0.8)',
          'rgba(255, 193, 7, 0.8)'
        ],
        borderColor: [
          'rgba(40, 167, 69, 1)',
          'rgba(220, 53, 69, 1)',
          'rgba(255, 193, 7, 1)'
        ],
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            font: {
              size: 14
            },
            padding: 20
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              var label = context.label || '';
              var value = context.parsed || 0;
              var total = <?= $stats['total_kamar']; ?>;
              var percentage = ((value / total) * 100).toFixed(1);
              return label + ': ' + value + ' (' + percentage + '%)';
            }
          }
        }
      }
    }
  });

  // Bar Chart - Kamar per Jenis
  var ctxBar = document.getElementById('barChart').getContext('2d');
  var barChart = new Chart(ctxBar, {
    type: 'bar',
    data: {
      labels: <?= json_encode($jenis_kamar_labels); ?>,
      datasets: [
        {
          label: 'Tersedia',
          data: <?= json_encode($jenis_kamar_tersedia); ?>,
          backgroundColor: 'rgba(40, 167, 69, 0.7)',
          borderColor: 'rgba(40, 167, 69, 1)',
          borderWidth: 2
        },
        {
          label: 'Terisi',
          data: <?= json_encode($jenis_kamar_terisi); ?>,
          backgroundColor: 'rgba(220, 53, 69, 0.7)',
          borderColor: 'rgba(220, 53, 69, 1)',
          borderWidth: 2
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1
          }
        }
      },
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            font: {
              size: 14
            },
            padding: 20
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return context.dataset.label + ': ' + context.parsed.y + ' kamar';
            }
          }
        }
      }
    }
  });

  // Room card click interaction
  $('.room-card').on('click', function() {
    var roomNumber = $(this).find('.room-number').text();
    var roomType = $(this).find('.room-type').text();
    var roomStatus = $(this).find('.room-status').text();
    
    alert('Kamar: ' + roomNumber + '\nJenis: ' + roomType + '\nStatus: ' + roomStatus);
  });
});
</script>
</body>
</html>