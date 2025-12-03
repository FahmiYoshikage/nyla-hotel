<?php
include "koneksi.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM pelanggan WHERE EMAIL='$email'";
    $sql = mysqli_query($koneksi, $query);
    $result = mysqli_fetch_assoc($sql);

    if ($result) {

        // Jika admin
        if ($result['ROLE'] == "admin") {

            if ($password == $result['PASSWORD']) {

                $_SESSION['email'] = $email;
                $_SESSION['role'] = "admin";
                $_SESSION['ID_PELANGGAN'] = $result['ID_PELANGGAN']; // FIX
                $_SESSION['NAMA_PELANGGAN'] = $result['NAMA_PELANGGAN'];

                header("Location: index.php");
                exit;

            } else {
                echo "<script>alert('Password admin salah');window.location='login.php';</script>";
                exit;
            }
        }

        // Jika pelanggan
        if ($result['verify_status'] == '1') {

            if (password_verify($password, $result['PASSWORD'])) {

                $_SESSION['email'] = $email;
                $_SESSION['role'] = "pelanggan";
                $_SESSION['ID_PELANGGAN'] = $result['ID_PELANGGAN']; // FIX PENTING
                $_SESSION['NAMA_PELANGGAN'] = $result['NAMA_PELANGGAN'];

                header("Location: indexp.php");
                exit;

            } else {
                echo "<script>alert('❌ Password tidak sesuai!');window.location='login.php';</script>";
                exit;
            }

        } else {
            $_SESSION['status'] = "danger, Silahkan verifikasi email";
            header('location: login.php');
            exit;
        }

    } else {
        echo "<script>alert('Email tidak ditemukan!');window.location='login.php';</script>";
        exit;
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Log in (v2)</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="index2.html" class="h1">Aston Hotel & Resorts</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Login Terlebih dahulu</p>

      <form action="" method="post">
       <?php       
if
(isset($_SESSION['status'])){
  $split = explode(',', $_SESSION['status']);
?>
    <div class="alert alert-<?php echo $split[0]; ?> alert-dismissible fade show" role="alert">
        <?php echo $split[1]; ?> 
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php
    session_unset();
}
?>


        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Ingatkan saya
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Masuk</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <div class="social-auth-links text-center mt-2 mb-3">
        <a href="#" class="btn btn-block btn-danger">
          <i class="fab fa-google-plus mr-2"></i> Masuk dengan Google
        </a>
      </div>
      <!-- /.social-auth-links -->

      <!--<p class="mb-1">
        <a href="forgot-password.html">I forgot my password</a>
      </p>-->
      <p class="mb-0">
        <a href="register.php" class="text-center">Registrasi Terlebih dahulu</a>
      </p>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
