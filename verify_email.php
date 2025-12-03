<?php
include "koneksi.php";
session_start();
if(isset($_GET['token'])){
    $token = $_GET['token'];
    $verify_query = "SELECT verify_token, verify_status FROM pelanggan WHERE verify_token = '$token' LIMIT 1";
    $verify_sql = mysqli_query($koneksi, $verify_query);
    $result = mysqli_fetch_assoc($verify_sql);

    if($result){
        if($result['verify_status'] == '0'){
            $clicked_token = $result['verify_token'];
            $update_query = "UPDATE pelanggan SET verify_status = '1' WHERE verify_token = '$clicked_token'";
            $update_sql = mysqli_query($koneksi, $update_query);
            if($update_sql){
                $_SESSION['status'] = "succes, email sudah diverifikasi sebelumnya!";
                header('location: login.php');
            }
            else{
                $_SESSION['status'] = "succes, email sudah diverifikasi sebelumnya!";
                header('location: login.php');
            }
        } else{
                $_SESSION['status'] = "danger, token tidak berlaku";
                header('location: login.php');
    }
}
}
?>