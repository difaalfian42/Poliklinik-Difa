<?php
session_start(); // Mulai session

// Hapus semua session
session_destroy();

// Redirect kembali ke halaman login atau halaman lainnya
header("Location: login.php");
exit;
?>
