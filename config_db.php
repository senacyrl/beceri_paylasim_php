<?php

$host = "localhost";
$user = "root";
$password = "06Ad4920";
$database = "beceri_paylasimi";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Veritabanı bağlantı hatası: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>
