<?php
session_start();

// Menghapus semua variabel sesi
$_SESSION = [];

// Menghancurkan sesi
session_destroy();

// Mengalihkan pengguna kembali ke halaman login
header("Location: login.php");
exit;
