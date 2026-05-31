<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "beceri_paylasimi";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("DB hatası: " . mysqli_connect_error());
}

$sql = "INSERT INTO users (username, email, password)
VALUES ('testuser', 'test@test.com', '123456')";

if (mysqli_query($conn, $sql)) {
    echo "TEST INSERT BAŞARILI";
} else {
    echo "HATA: " . mysqli_error($conn);
}
?>
