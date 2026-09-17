<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['siswa_id']);
$siswaNama  = $isLoggedIn ? $_SESSION['siswa_nama'] : '';

// Fungsi untuk menampilkan pesan flash (sekali pakai)
function setFlash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

function getFlash($key) {
function requireLogin() {
    if (!isset($_SESSION['siswa_id'])) {
        setFlash('error', 'Silakan masuk terlebih dahulu untuk mengakses halaman ini.');
        header("Location: login.php");
        exit;
    }
}
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}
?>