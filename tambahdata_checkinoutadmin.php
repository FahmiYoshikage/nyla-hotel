<?php
include 'koneksi.php';

// Query untuk mendapatkan daftar ID Pemesanan dan data terkait
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Data Check-In & Check-Out | Admin Hotel</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <?php include 'header.php'; // Asumsi file ini ada ?>
  <?php include 'navbar.php'; // Asumsi file ini ada ?>

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>TAMBAH DATA CHECK-IN & CHECK-OUT</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Tambah Data Check-In & Check-Out</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="card card-success">
        <div class="card-header">
          <h3 class="card-title">Form Tambah Data</h3>
        </div>
        <form method="post" action="proses_tambah_checkinout.php"> 
          <div class="card-body">
            
            <div class="form-group">
              <label for="tgl_pemesanan">Tanggal Pemesanan / ID Pemesanan</label>
              <select class="form-control" id="tgl_pemesanan" name="id_pemesanan" required>
                <option value="">Pilih ID / Tanggal Pemesanan</option>
                <?php
                if ($result_pemesanan && $result_pemesanan->num_rows > 0) {
                    while ($row = $result_pemesanan->fetch_assoc()) {
                        $display_text = "ID: {$row['ID_PEMESANAN']} | Tgl: {$row['TGL_PESAN']} | Pelanggan: {$row['NAMA_PELANGGAN']} | Kamar: {$row['JENIS_KAMAR']}";
                        echo "<option value='{$row['ID_PEMESANAN']}'>{$display_text}</option>";
                    }
                }
                ?>
              </select>
            </div>

            <input type="hidden" id="id_pelanggan_hidden" name="id_pelanggan">
            <input type="hidden" id="id_jenis_kamar_hidden" name="id_jenis_kamar">

            <div class="form-group">
              <label for="pelanggan">Pelanggan</label>
              <input type="text" class="form-control" id="nama_pelanggan" disabled placeholder="Nama Pelanggan Otomatis Terisi">
            </div>

            <div class="form-group">
              <label for="jenis_kamar">Jenis Kamar</label>
              <input type="text" class="form-control" id="jenis_kamar_nama" disabled placeholder="Jenis Kamar Otomatis Terisi">
            </div>

            <div class="form-group">
              <label for="kamar">Kamar</label>
              <select class="form-control" id="kamar" name="id_kamar" required>
                <option value="">Pilih Kamar (Terisi Otomatis)</option>
              </select>
            </div>

            <div class="form-group">
              <label for="tgl_check_in">Tanggal Check-In</label>
              <input type="date" class="form-control" id="tgl_check_in" name="tgl_check_in" required readonly>
            </div>

            <div class="form-group">
              <label for="jam_check_in">Jam Check-In</label>
              <input type="time" class="form-control" id="jam_check_in" name="jam_check_in" required>
            </div>

            <div class="form-group">
              <label for="lama_inap">Lama Inap (Hari)</label>
              <input type="number" class="form-control" id="lama_inap" name="lama_inap" required min="1">
            </div>

            <div class="form-group">
              <label for="tgl_check_out">Tanggal Check-Out</label>
              <input type="date" class="form-control" id="tgl_check_out" name="tgl_check_out" required readonly>
            </div>

            <div class="form-group">
              <label for="status_pembayaran">Status Pembayaran</label>
              <select class="form-control" id="status_pembayaran" name="status_pembayaran" required>
                <option value="Lunas">Lunas</option>
                <option value="Belum Lunas">Belum Lunas</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="status_checkinout">Status Check-In/Check-Out</label>
              <select class="form-control" id="status_checkinout" name="status_checkinout" required>
                <option value="Check-In">Check-In</option>
                <option value="Check-Out">Check-Out</option>
                <option value="Batal">Batal</option>
              </select>
            </div>

          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-success">Simpan Data</button>
            <a href="data_checkinoutadmin.php" class="btn btn-default">Kembali</a>
          </div>
        </form>
        </div>
    </section>
  </div>

  <?php include 'footer.php'; // Asumsi file ini ada ?>
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
            loadAvailableRooms(data.ID_JENIS_KAMAR);

          } else {
            alert(response.message);
            // Reset fields
            $('#tgl_pemesanan').val("");
          }
        },
        error: function() {
          alert('Terjadi kesalahan saat mengambil data pemesanan.');
        }
      });
    });

    // --- FUNGSI BANTUAN: MENDAPATKAN KAMAR TERSEDIA ---
    function loadAvailableRooms(id_jenis_kamar) {
        // AJAX: Ambil Kamar Tersedia dari server
        $.ajax({
            url: 'get_available_rooms.php', // Endpoint untuk kamar tersedia
            type: 'POST',
            data: { id_jenis_kamar: id_jenis_kamar },
            dataType: 'json',
            success: function(response) {
                var room_select = $('#kamar');
                room_select.html('<option value="">Pilih Kamar</option>'); // Reset dropdown

                if (response.success && response.rooms.length > 0) {
                    $.each(response.rooms, function(index, room) {
                        room_select.append('<option value="' + room.ID_KAMAR + '">' + room.NO_KAMAR + '</option>');
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

    // Panggil fungsi perhitungan saat Lama Inap atau Tgl Check-In berubah
    $('#lama_inap').on('input', calculateCheckOutDate);
    $('#tgl_check_in').on('change', calculateCheckOutDate);
    
  });
</script>
</body>
</html>