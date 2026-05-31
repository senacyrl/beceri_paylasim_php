<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "config_db.php";
session_start();

$mode = $_GET['mode'] ?? null;

if ($mode == "register") {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $username = isset($_POST['name']) ? trim($_POST['name']) : null;
        $email    = isset($_POST['email']) ? trim($_POST['email']) : null;
        $password = isset($_POST['password']) ? $_POST['password'] : null;

        if (empty($username) || empty($email) || empty($password)) {
            die("Lütfen tüm alanları doldurun!");
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password)
                VALUES ('$username', '$email', '$hashed_password')";

        if (mysqli_query($conn, $sql)) {

            echo "<h2 style='color:green;text-align:center;'>Kayıt Başarılı!</h2>";
            echo "<p style='text-align:center;'>Login sayfasına yönlendiriliyorsunuz...</p>";

            header("Location: login_register.php");
            exit();

        } else {
            echo "SQL Hata: " . mysqli_error($conn);
        }
    }
    exit();
}


if ($mode == "login") {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $email    = isset($_POST['email']) ? trim($_POST['email']) : null;
        $password = isset($_POST['password']) ? $_POST['password'] : null;

        if (!$email || !$password) {
            die("Lütfen tüm alanları doldurun.");
        }

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user) {

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                echo "Giriş başarılı! Yönlendiriliyorsunuz...";
                header("Location: index.php");
                exit();

            } else {
                echo "Şifre yanlış!";
            }

        } else {
            echo "Kullanıcı bulunamadı!";
        }
    }

    exit();
}
echo "Geçersiz işlem!";
?>
