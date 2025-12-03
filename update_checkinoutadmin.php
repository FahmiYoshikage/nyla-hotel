<?php
include 'koneksi.php';

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi Database Gagal: " . $koneksi->connect_error);
}

// =======================================================
// === 1. AMBIL DATA AWAL TRANSAKSI BERDASARKAN ID ===
// =======================================================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
            alert('ID check-in/check-out tidak ditemukan!');
            window.location='checkinout_admin.php';
          </script>";
    exit;
}

$id = (int)$_GET['id'];

// Ambil data checkin_checkout berdasarkan ID menggunakan prepared statement
$sql = "SELECT 
          c.*,
          p.TGL_PESAN,
          p.TGL_CHECK_IN AS TGL_CHECK_IN_PESANAN,
          pel.NAMA_PELANGGAN,
          jk.JENIS_KAMAR,
          k.NO_KAMAR
        FROM checkin_checkout c
        LEFT JOIN pemesanan p ON c.ID_PEMESANAN = p.ID_PEMESANAN
        LEFT JOIN pelanggan pel ON c.ID_PELANGGAN = pel.ID_PELANGGAN
        LEFT JOIN jenis_kamar jk ON c.ID_JENIS_KAMAR = jk.ID_JENIS_KAMAR
        LEFT JOIN kamar k ON c.ID_KAMAR = k.ID_KAMAR
        WHERE c.ID_CHECKIN_OUT = ?";

$stmt = $koneksi->prepare($sql);

if (!$stmt) {
    die("Error prepare statement: " . $koneksi->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>
            alert('Data check-in/check-out tidak ditemukan!');
            window.location='checkinout_admin.php';
          </script>";
    exit;
}

$data = $result->fetch_assoc();
$stmt->close();

// Simpan ID Kamar yang lama sebelum potensi update di POST
$current_id_kamar = $data['ID_KAMAR'];
$current_status_checkinout = $data['STATUS_CHECKINOUT'];


// Query untuk mendapatkan daftar ID Pemesanan
$sql_pemesanan = "SELECT 
                      p.ID_PEMESANAN, 
                      p.TGL_PESAN, 
                      l.NAMA_PELANGGAN, 
                      jk.JENIS_KAMAR
                    FROM pemesanan p
                    INNER JOIN pelanggan l ON p.ID_PELANGGAN = l.ID_PELANGGAN
                    INNER JOIN jenis_kamar jk ON p.ID_JENIS_KAMAR = jk.ID_JENIS_KAMAR
                    ORDER BY p.TGL_PESAN DESC";

$result_pemesanan = $koneksi->query($sql_pemesanan);

// =======================================================
// === 2. PROSES UPDATE DATA (POST REQUEST) ===
// =======================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pemesanan = (int)$_POST['id_pemesanan'];
    $id_pelanggan = (int)$_POST['id_pelanggan'];
    $id_jenis_kamar = (int)$_POST['id_jenis_kamar'];
    $id_kamar = (int)$_POST['id_kamar']; // ID Kamar BARU
    $tgl_check_in = $_POST['tgl_check_in'];
    $jam_check_in = $_POST['jam_check_in'];
    $lama_inap = (int)$_POST['lama_inap'];
    $tgl_check_out = $_POST['tgl_check_out'];
    $status_pembayaran = $_POST['status_pembayaran'];
    $status_checkinout = $_POST['status_checkinout']; // Status Check-in/out BARU

    // Validasi input
    if (empty($id_pemesanan) || empty($id_pelanggan) || empty($id_jenis_kamar) || 
        empty($id_kamar) || empty($tgl_check_in) || empty($jam_check_in) || 
        empty($lama_inap) || empty($tgl_check_out) || empty($status_pembayaran) || 
        empty($status_checkinout)) {
        echo "<script>
                alert('❌ Semua field harus diisi!');
                window.history.back();
              </script>";
        exit;
    }

    // A. UPDATE checkin_checkout
    $update = "UPDATE checkin_checkout SET 
                ID_PEMESANAN = ?,
                ID_PELANGGAN = ?,
                ID_JENIS_KAMAR = ?,
                ID_KAMAR = ?,
                TGL_CHECK_IN = ?,
                JAM_CHECK_IN = ?,
                LAMA_INAP = ?,
                TGL_CHECK_OUT = ?,
                STATUS_PEMBAYARAN = ?,
                STATUS_CHECKINOUT = ?
                WHERE ID_CHECKIN_OUT = ?";

    $stmt_update = $koneksi->prepare($update);
    
    if (!$stmt_update) {
        echo "<script>
                alert('❌ Error prepare statement: " . htmlspecialchars($koneksi->error) . "');
                window.history.back();
              </script>";
        exit;
    }

    $stmt_update->bind_param(
        "iiissiisssi",
        $id_pemesanan,
        $id_pelanggan,
        $id_jenis_kamar,
        $id_kamar,
        $tgl_check_in,
        $jam_check_in,
        $lama_inap,
        $tgl_check_out,
        $status_pembayaran,
        $status_checkinout,
        $id
    );

    if ($stmt_update->execute()) {
        $stmt_update->close();
        
        // B. LOGIKA UPDATE STATUS KAMAR
        
        // 1. Logika untuk membebaskan kamar jika Check-out atau Batal
        if ($status_checkinout == "Sudah Checkout" || $status_checkinout == "Batal") {
            
            // Hanya bebaskan kamar LAMA jika status check-in/out sebelumnya BUKAN 'Sudah Checkout' atau 'Batal'
            // untuk menghindari update kamar yang statusnya sudah 'Tersedia'.
            if ($current_status_checkinout != "Sudah Checkout" && $current_status_checkinout != "Batal") {
                
                $update_status_kamar = "UPDATE kamar SET STATUS = 'Tersedia' WHERE ID_KAMAR = ?";
                $stmt_kamar = $koneksi->prepare($update_status_kamar);
                
                if ($stmt_kamar) {
                    $stmt_kamar->bind_param("i", $current_id_kamar);
                    $stmt_kamar->execute();
                    $stmt_kamar->close();
                } else {
                    error_log("Gagal prepare statement update status kamar (Bebaskan Kamar): " . $koneksi->error);
                }
            }
        } 
        
        // 2. Logika untuk mengisi kamar jika ada perubahan kamar saat status 'Check-In'
        // Jika status BARU adalah 'Check-In' dan ID Kamar BERUBAH dari yang lama, 
        // kita perlu: 
        // a) membebaskan kamar lama (sudah dicakup di logika if di atas jika status lama bukan Check-In/Batal)
        // b) mengisi kamar baru.
        if ($status_checkinout == "Check-In") {
            
            // Jika Kamar yang dipilih di form BERBEDA dari Kamar yang lama
            if ($id_kamar != $current_id_kamar) {
                
                // Bebaskan Kamar Lama jika dia masih terisi oleh transaksi ini
                if ($current_status_checkinout == "Check-In") {
                    $up_lama = $koneksi->prepare("UPDATE kamar SET STATUS = 'Tersedia' WHERE ID_KAMAR = ?");
                    $up_lama->bind_param("i", $current_id_kamar);
                    $up_lama->execute();
                    $up_lama->close();
                }
                
                // Isi Kamar Baru
                $up_baru = $koneksi->prepare("UPDATE kamar SET STATUS = 'Terisi' WHERE ID_KAMAR = ?");
                $up_baru->bind_param("i", $id_kamar);
                $up_baru->execute();
                $up_baru->close();

            } else {
                // Jika status baru 'Check-In' dan ID kamar tidak berubah, pastikan kamar tersebut 'Terisi'
                $up_lama = $koneksi->prepare("UPDATE kamar SET STATUS = 'Terisi' WHERE ID_KAMAR = ?");
                $up_lama->bind_param("i", $id_kamar);
                $up_lama->execute();
                $up_lama->close();
            }
        }
        
        // C. REDIRECT SUKSES
        echo "<script>
                alert('✅ Data check-in/check-out berhasil diperbarui!');
                window.location='checkinout_admin.php';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('❌ Gagal memperbarui data: " . htmlspecialchars($stmt_update->error) . "');
                window.history.back();
              </script>";
        $stmt_update->close();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Update Data Check-In & Check-Out | Admin Hotel</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
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
            <h1>UPDATE DATA CHECK-IN & CHECK-OUT</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="data_checkincheckoutadmin.php">Data Check-In & Check-Out</a></li>
              <li class="breadcrumb-item active">Update Data</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="card card-warning">
        <div class="card-header">
          <h3 class="card-title">Form Update Data</h3>
        </div>
        <form method="post" action="" id="updateForm"> 
          <div class="card-body">
            
            <div class="form-group">
              <label for="tgl_pemesanan">Tanggal Pemesanan / ID Pemesanan</label>
              <select class="form-control" id="tgl_pemesanan" name="id_pemesanan" required>
                <option value="">Pilih ID / Tanggal Pemesanan</option>
                <?php
                if ($result_pemesanan && $result_pemesanan->num_rows > 0) {
                    while ($row = $result_pemesanan->fetch_assoc()) {
                        $selected = ($row['ID_PEMESANAN'] == $data['ID_PEMESANAN']) ? 'selected' : '';
                        $display_text = "ID: {$row['ID_PEMESANAN']} | Tgl: {$row['TGL_PESAN']} | Pelanggan: {$row['NAMA_PELANGGAN']} | Kamar: {$row['JENIS_KAMAR']}";
                        echo "<option value='{$row['ID_PEMESANAN']}' $selected>" . htmlspecialchars($display_text) . "</option>";
                    }
                }
                ?>
              </select>
            </div>

            <input type="hidden" id="id_pelanggan_hidden" name="id_pelanggan" value="<?= htmlspecialchars($data['ID_PELANGGAN']); ?>">
            <input type="hidden" id="id_jenis_kamar_hidden" name="id_jenis_kamar" value="<?= htmlspecialchars($data['ID_JENIS_KAMAR']); ?>">

            <div class="form-group">
              <label for="pelanggan">Pelanggan</label>
              <input type="text" class="form-control" id="nama_pelanggan" disabled 
                    value="<?= htmlspecialchars($data['NAMA_PELANGGAN']); ?>" 
                    placeholder="Nama Pelanggan Otomatis Terisi">
            </div>

            <div class="form-group">
              <label for="jenis_kamar">Jenis Kamar</label>
              <input type="text" class="form-control" id="jenis_kamar_nama" disabled 
                    value="<?= htmlspecialchars($data['JENIS_KAMAR']); ?>"
                    placeholder="Jenis Kamar Otomatis Terisi">
            </div>

            <div class="form-group">
              <label for="kamar">Kamar</label>
              <select class="form-control" id="kamar" name="id_kamar" required>
                <option value="">Pilih Kamar</option>
                <?php
                // Ambil semua kamar yang tersedia untuk jenis kamar ini
                $sql_kamar = "SELECT ID_KAMAR, NO_KAMAR FROM kamar 
                              WHERE ID_JENIS_KAMAR = ? 
                              ORDER BY NO_KAMAR ASC";
                $stmt_kamar = $koneksi->prepare($sql_kamar);
                $stmt_kamar->bind_param("i", $data['ID_JENIS_KAMAR']);
                $stmt_kamar->execute();
                $result_kamar = $stmt_kamar->get_result();
                
                while ($row_kamar = $result_kamar->fetch_assoc()) {
                    $selected = ($row_kamar['ID_KAMAR'] == $data['ID_KAMAR']) ? 'selected' : '';
                    echo "<option value='{$row_kamar['ID_KAMAR']}' $selected>" . 
                          htmlspecialchars($row_kamar['NO_KAMAR']) . "</option>";
                }
                $stmt_kamar->close();
                ?>
              </select>
            </div>

            <div class="form-group">
              <label for="tgl_check_in">Tanggal Check-In</label>
              <input type="date" class="form-control" id="tgl_check_in" name="tgl_check_in" 
                    value="<?= htmlspecialchars($data['TGL_CHECK_IN']); ?>" required readonly>
            </div>

            <div class="form-group">
              <label for="jam_check_in">Jam Check-In</label>
              <input type="time" class="form-control" id="jam_check_in" name="jam_check_in" 
                    value="<?php 
                        if (!empty($data['JAM_CHECK_IN'])) {
                            echo htmlspecialchars(date('H:i', strtotime($data['JAM_CHECK_IN'])));
                        }
                    ?>" required>
            </div>

            <div class="form-group">
              <label for="lama_inap">Lama Inap (Hari)</label>
              <input type="number" class="form-control" id="lama_inap" name="lama_inap" 
                    value="<?= htmlspecialchars($data['LAMA_INAP']); ?>" required min="1">
            </div>

            <div class="form-group">
              <label for="tgl_check_out">Tanggal Check-Out</label>
              <input type="date" class="form-control" id="tgl_check_out" name="tgl_check_out" 
                    value="<?= htmlspecialchars($data['TGL_CHECK_OUT']); ?>" required readonly>
            </div>

            <div class="form-group">
              <label for="status_pembayaran">Status Pembayaran</label>
              <select class="form-control" id="status_pembayaran" name="status_pembayaran" required>
                <option value="Lunas" <?= ($data['STATUS_PEMBAYARAN'] == 'Lunas') ? 'selected' : ''; ?>>Lunas</option>
                <option value="Belum Lunas" <?= ($data['STATUS_PEMBAYARAN'] == 'Belum Lunas') ? 'selected' : ''; ?>>Belum Lunas</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="status_checkinout">Status Check-In/Check-Out</label>
              <select class="form-control" id="status_checkinout" name="status_checkinout" required>
                <option value="Check-In" <?= ($data['STATUS_CHECKINOUT'] == 'Check-In') ? 'selected' : ''; ?>>Check-In</option>
                <option value="Sudah Checkout" <?= ($data['STATUS_CHECKINOUT'] == 'Sudah Checkout') ? 'selected' : ''; ?>>Checkout</option>
                <option value="Batal" <?= ($data['STATUS_CHECKINOUT'] == 'Batal') ? 'selected' : ''; ?>>Batal</option>
              </select>
            </div>

          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-warning">
              <i class="fas fa-edit"></i> Update Data
            </button>
            <a href="data_checkincheckoutadmin.php" class="btn btn-default">
              <i class="fas fa-arrow-left"></i> Kembali
            </a>
          </div>
        </form>
        </div>
    </section>
  </div>

  <?php include 'footer.php'; ?>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(document).ready(function() {
    
    // --- FUNGSI UTAMA: MENDAPATKAN DETAIL PEMESANAN ---
    $('#tgl_pemesanan').change(function() {
      var id_pemesanan = $(this).val();
      
      // Reset field lainnya jika tidak ada ID Pemesanan dipilih
      if (id_pemesanan === "") {
        $('#nama_pelanggan').val("");
        $('#jenis_kamar_nama').val("");
        $('#tgl_check_in').val("");
        $('#id_pelanggan_hidden').val("");
        $('#id_jenis_kamar_hidden').val("");
        $('#kamar').html('<option value="">Pilih Kamar (Terisi Otomatis)</option>'); 
        $('#lama_inap').val("");
        $('#tgl_check_out').val("");
        return;
      }

      // 1. AJAX: Ambil detail Pemesanan dari server
      $.ajax({
        url: 'get_pemesanan_data.php', // Endpoint untuk detail pemesanan
        type: 'POST',
        data: { id_pemesanan: id_pemesanan },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            var data = response.data;
            
            // Mengisi field yang otomatis terisi dan hidden input
            $('#nama_pelanggan').val(data.NAMA_PELANGGAN);
            $('#jenis_kamar_nama').val(data.JENIS_KAMAR);
            $('#tgl_check_in').val(data.TGL_CHECK_IN_PESANAN); 
            
            // Isi Hidden Input untuk disubmit
            $('#id_pelanggan_hidden').val(data.ID_PELANGGAN);
            $('#id_jenis_kamar_hidden').val(data.ID_JENIS_KAMAR);

            // 2. Panggil fungsi untuk memuat Kamar Tersedia berdasarkan ID_JENIS_KAMAR
            loadAvailableRooms(data.ID_JENIS_KAMAR, <?= $current_id_kamar ?>);

            // 3. Hitung ulang tanggal checkout jika lama inap sudah terisi
            calculateCheckOutDate();

          } else {
            alert(response.message);
          }
        },
        error: function() {
          alert('Terjadi kesalahan saat mengambil data pemesanan.');
        }
      });
    });

    // --- FUNGSI BANTUAN: MENDAPATKAN KAMAR TERSEDIA ---
    function loadAvailableRooms(id_jenis_kamar, current_kamar_id) {
        // AJAX: Ambil Kamar Tersedia dari server
        $.ajax({
            url: 'get_available_rooms.php', // Endpoint untuk kamar tersedia
            type: 'POST',
            data: { 
                id_jenis_kamar: id_jenis_kamar,
                current_kamar_id: current_kamar_id // Kirim ID kamar yang sedang diedit
            },
            dataType: 'json',
            success: function(response) {
                var room_select = $('#kamar');
                
                room_select.html('<option value="">Pilih Kamar</option>'); // Reset dropdown

                if (response.success && response.rooms.length > 0) {
                    $.each(response.rooms, function(index, room) {
                        var selected = (room.ID_KAMAR == current_kamar_id) ? 'selected' : '';
                        room_select.append('<option value="' + room.ID_KAMAR + '" ' + selected + '>' + room.NO_KAMAR + '</option>');
                    });
                } else {
                    room_select.html('<option value="">Tidak ada kamar tersedia</option>');
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat memuat kamar.');
                $('#kamar').html('<option value="">Gagal memuat kamar</option>');
            }
        });
    }

    // --- FUNGSI BANTUAN: MENGHITUNG TANGGAL CHECK-OUT OTOMATIS ---
    function calculateCheckOutDate() {
        var checkInDate = $('#tgl_check_in').val();
        var lamaInap = parseInt($('#lama_inap').val());

        if (checkInDate && lamaInap > 0) {
            var date = new Date(checkInDate);
            // Tambahkan lama inap (hari) ke tanggal check-in
            date.setDate(date.getDate() + lamaInap); 

            // Format tanggal menjadi YYYY-MM-DD
            var year = date.getFullYear();
            var month = ('0' + (date.getMonth() + 1)).slice(-2);
            var day = ('0' + date.getDate()).slice(-2);
            
            $('#tgl_check_out').val(year + '-' + month + '-' + day);
        } else {
            $('#tgl_check_out').val('');
        }
    }

    // Panggil fungsi perhitungan saat Lama Inap berubah
    $('#lama_inap').on('input', calculateCheckOutDate);
    $('#tgl_check_in').on('change', calculateCheckOutDate);

    // Inisialisasi data current kamar (tidak perlu lagi data attribute karena dikirim via AJAX)
    
    // Validasi form sebelum submit
    $('#updateForm').on('submit', function(e) {
        var checkinDate = new Date($('#tgl_check_in').val());
        var checkoutDate = new Date($('#tgl_check_out').val());
        
        if (checkoutDate <= checkinDate) {
            e.preventDefault();
            alert('❌ Tanggal Check-Out harus lebih besar dari Tanggal Check-In!');
            return false;
        }
        
        var lamaInap = parseInt($('#lama_inap').val());
        if (lamaInap < 1) {
            e.preventDefault();
            alert('❌ Lama Inap minimal 1 hari!');
            return false;
        }
        
        if ($('#kamar').val() === "") {
            e.preventDefault();
            alert('❌ Silakan pilih kamar!');
            return false;
        }
        
        return true;
    });
  });
</script>
</body>
</html>