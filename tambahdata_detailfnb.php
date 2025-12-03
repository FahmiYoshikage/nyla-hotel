<?php
// Panggil file koneksi
include "koneksi.php";

// Cek koneksi di awal
if ($koneksi->connect_error) {
    die("Koneksi Database Gagal Total: " . $koneksi->connect_error);
}

// =======================================================
// === VARIABEL UNTUK MENGISI FORM SECARA OTOMATIS ===
// =======================================================
$selected_id_fnb = '';
$selected_id_pelanggan = '';
$selected_nama_pelanggan = '-- Isi Otomatis --';
$selected_no_kamar = '-- Isi Otomatis --';

// Cek apakah form di-submit untuk mendapatkan ID_FNB (Reload Form)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_fnb']) && !isset($_POST['simpan_data'])) {
    
    $selected_id_fnb = (int)$_POST['id_fnb'];

    if ($selected_id_fnb > 0) {
        
        // Kueri untuk mengambil ID_PELANGGAN, NAMA_PELANGGAN, dan NO_KAMAR
        $sql_lookup = "SELECT 
                            f.ID_PELANGGAN,
                            p.NAMA_PELANGGAN,
                            k.NO_KAMAR
                        FROM food_n_beverage f
                        INNER JOIN pelanggan p ON f.ID_PELANGGAN = p.ID_PELANGGAN
                        INNER JOIN kamar k ON f.ID_KAMAR = k.ID_KAMAR
                        WHERE f.ID_FNB = ? LIMIT 1";

        $stmt_lookup = $koneksi->prepare($sql_lookup);
        
        if ($stmt_lookup) {
            $stmt_lookup->bind_param("i", $selected_id_fnb);
            $stmt_lookup->execute();
            $result_lookup = $stmt_lookup->get_result();

            if ($row = $result_lookup->fetch_assoc()) {
                // Data ditemukan, isi variabel
                $selected_id_pelanggan = $row['ID_PELANGGAN'];
                $selected_nama_pelanggan = $row['NAMA_PELANGGAN'];
                $selected_no_kamar = $row['NO_KAMAR'];
            } else {
                // Data relasi tidak valid (seperti yang ditunjukkan di FNBError atau AJAX Error)
                echo "<script>alert('Kode FNB: Data pelanggan atau kamar terkait tidak valid di database. Periksa ID_PELANGGAN dan ID_KAMAR di tabel fnb.');</script>";
            }
            $stmt_lookup->close();
        } else {
            // Error SQL
            echo "<script>alert('Error: Gagal menyiapkan kueri data otomatis.');</script>";
        }
    }
}


// ------------------------------------------------
//  PROSES SIMPAN DETAIL FNB (INSERT KE DATABASE)
// ------------------------------------------------
// Menggunakan 'simpan_data' untuk membedakan dari submit reload form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan_data'])) {
    
    // Ambil data POST
    $id_fnb          = $_POST['id_fnb'];
    $id_pelanggan    = $_POST['hidden_id_pelanggan']; // AMBIL DARI INPUT HIDDEN
    $tgl_fnb         = $_POST['tgl_fnb'];
    $jam_fnb         = $_POST['jam_fnb'];
    $food            = $_POST['food'];
    $beverage        = $_POST['beverage'];
    $jumlah_fnb      = $_POST['jumlah_fnb'];
    $subtotal_fnb    = $_POST['subtotal_fnb']; 
    
    // Validasi dasar
    if (empty($id_pelanggan) || empty($id_fnb) || (int)$id_pelanggan === 0) {
         echo "<script>
                alert('❌ Gagal menyimpan: ID Pelanggan atau ID FNB kosong/tidak valid. Pilih Kode FNB dan tunggu form terisi otomatis.');
                window.history.back();
              </script>";
        exit;
    }

    // Hitung Total FNB
    $subtotal_val = (int)$subtotal_fnb;
    $jumlah_val = (int)$jumlah_fnb;
    $total_fnb = $subtotal_val * $jumlah_val; 
    
    // Query tambah data ke tabel detail_food_n_beverage
    $sql_insert = "INSERT INTO detail_food_n_beverage 
                   (ID_FNB, ID_PELANGGAN, TGL_FNB, JAM_FNB, FOOD, BEVERAGE, JUMLAH_FNB, SUBTOTAL_FNB, TOTAL_FNB)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $insert_stmt = $koneksi->prepare($sql_insert);
    
    if ($insert_stmt) {
        // Tipe data: iisssisii (sesuaikan dengan tipe data kolom Anda)
        $insert_stmt->bind_param("iissisiii", 
                                $id_fnb, 
                                $id_pelanggan, 
                                $tgl_fnb, 
                                $jam_fnb, 
                                $food, 
                                $beverage, 
                                $jumlah_val, 
                                $subtotal_val, 
                                $total_fnb);

        if ($insert_stmt->execute()) {
            echo "<script>
                    alert('✅ Data Detail FNB berhasil ditambahkan! Total: Rp " . number_format($total_fnb, 0, ',', '.') . "');
                    window.location='detail_food_n_beverage.php'; 
                  </script>";
            exit;
        } else {
            echo "<script>
                    alert('❌ Gagal menambahkan data: " . addslashes($insert_stmt->error) . "');
                    window.history.back();
                  </script>";
            exit;
        }
        $insert_stmt->close();
    } else {
        echo "<script>
                alert('❌ Gagal menyiapkan statement INSERT: " . addslashes($koneksi->error) . "');
                window.history.back();
              </script>";
        exit;
    }
}


// ======================================================
//  QUERY UNTUK SELECT DATA DI DALAM FORM
// ======================================================

// Data Pelanggan (Hanya untuk mapping)
$sql_pelanggan = "SELECT ID_PELANGGAN, NAMA_PELANGGAN FROM pelanggan ORDER BY NAMA_PELANGGAN";
$result_pelanggan = $koneksi->query($sql_pelanggan);

// Data FNB (untuk dropdown Kode FNB)
// TELAH DIPERBAIKI: Mengambil KODE_FNB dari tabel 'fnb', BUKAN 'detail_food_n_beverage'
$sql_fnb_codes = "SELECT ID_FNB, KODE_FNB FROM food_n_beverage ORDER BY ID_FNB DESC"; // <--- BARIS KRUSIAL (dulu baris 148)
$result_fnb_codes = $koneksi->query($sql_fnb_codes);

// Set tanggal dan jam saat ini sebagai default
$tgl_sekarang = date('Y-m-d');
$jam_sekarang = date('H:i');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Data Detail Food & Beverage</title>
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php
  include 'header.php';
  include 'navbar.php';
  ?>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <h1>Tambah Data Detail Food & Beverage (Tanpa AJAX)</h1>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Form Tambah Detail FNB</h3>
                    </div>

                    <form action="" method="POST" id="fnbForm">
                        <div class="card-body">

                            <div class="form-group">
                                <label for="id_fnb">Kode FNB (Transaksi Induk)</label>
                                <select class="form-control" id="id_fnb" name="id_fnb" required 
                                        onchange="document.getElementById('fnbForm').submit();">
                                    <option value="">-- Pilih Kode FNB --</option>
                                    <?php 
                                    if ($result_fnb_codes) {
                                        while ($row = $result_fnb_codes->fetch_assoc()) { 
                                            $selected = ($row['ID_FNB'] == $selected_id_fnb) ? 'selected' : '';
                                        ?>
                                            <option value="<?= $row['ID_FNB']; ?>" <?= $selected; ?>>
                                                <?= htmlspecialchars($row['KODE_FNB']); ?>
                                            </option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="id_pelanggan">Nama Pelanggan</label>
                                <input type="text" class="form-control" id="nama_pelanggan_display" 
                                       value="<?= htmlspecialchars($selected_nama_pelanggan); ?>" required readonly> 
                                
                                <input type="hidden" id="hidden_id_pelanggan" name="hidden_id_pelanggan" 
                                       value="<?= htmlspecialchars($selected_id_pelanggan); ?>">
                            </div>

                            <div class="form-group">
                                <label for="no_kamar">Nomor Kamar</label>
                                <input type="text" class="form-control" id="no_kamar_display" 
                                       value="<?= htmlspecialchars($selected_no_kamar); ?>" required readonly> 
                            </div>
                            
                            <hr>
                            
                            <div class="form-group">
                                <label for="tgl_fnb">Tanggal FNB</label>
                                <input type="date" class="form-control" id="tgl_fnb" name="tgl_fnb" 
                                       value="<?= $tgl_sekarang; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="jam_fnb">Jam FNB</label>
                                <input type="time" class="form-control" id="jam_fnb" name="jam_fnb" 
                                       value="<?= $jam_sekarang; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="food">Food</label>
                                <input type="text" class="form-control" id="food" name="food" 
                                       placeholder="Contoh: Nasi Goreng, Sandwich" required>
                            </div>

                            <div class="form-group">
                                <label for="beverage">Beverage</label>
                                <input type="text" class="form-control" id="beverage" name="beverage" 
                                       placeholder="Contoh: Es Teh, Kopi" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="subtotal_fnb">Harga Satuan / Subtotal (Rp)</label>
                                <input type="number" class="form-control" id="subtotal_fnb" name="subtotal_fnb" 
                                       value="0" min="0" required>
                            </div>

                            <div class="form-group">
                                <label for="jumlah_fnb">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah_fnb" name="jumlah_fnb" 
                                       value="1" min="1" required>
                            </div>

                            <div class="form-group">
                                <label>Total FNB (Rp)</label>
                                <input type="text" class="form-control" id="total_fnb_display" value="Rp 0" readonly>
                            </div>

                        </div>
                        <div class="card-footer">
                            <button type="submit" name="simpan_data" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
<?php include 'footer.php'; ?>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function(){
    function calculateTotal() {
        const subtotal = parseFloat($("#subtotal_fnb").val()) || 0; 
        const jumlah = parseFloat($("#jumlah_fnb").val()) || 0;
        const total = subtotal * jumlah;
        
        $("#total_fnb_display").val('Rp ' + total.toLocaleString('id-ID'));
    }

    $("#subtotal_fnb, #jumlah_fnb").on('input', calculateTotal);
    calculateTotal(); 
});
</script>
</body>
</html>