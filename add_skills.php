<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include "config_db.php";

    $user_id = $_SESSION['user_id'];

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $type = mysqli_real_escape_string($conn, $_POST['type']); 

    $sql = "INSERT INTO skills (user_id, title, description, type) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isss", $user_id, $title, $description, $type);

    if (mysqli_stmt_execute($stmt)) {
        
        $target_type = ($type == 'ogret') ? 'ogren' : 'ogret';

        $check_match = "SELECT user_id FROM skills WHERE title = ? AND type = ? AND user_id != ?";
        $stmt_match = mysqli_prepare($conn, $check_match);
        mysqli_stmt_bind_param($stmt_match, "ssi", $title, $target_type, $user_id);
        mysqli_stmt_execute($stmt_match);
        $result_match = mysqli_stmt_get_result($stmt_match);

ü        $my_name = $_SESSION['username'] ?? "Bir kullanıcı";
        if ($type == 'ogret') {
            $msg = "Müjde! " . $my_name . " adlı kullanıcı aradığın '" . $title . "' konusunu öğretebileceğini belirtti!";
        } else {
            $msg = $my_name . " adlı kullanıcı senin uzmanlığın olan '" . $title . "' konusunu öğrenmek istiyor!";
        }

        while ($row = mysqli_fetch_assoc($result_match)) {
            $target_user = $row['user_id'];
            
            $notif_sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
            $stmt_notif = mysqli_prepare($conn, $notif_sql);
            mysqli_stmt_bind_param($stmt_notif, "is", $target_user, $msg);
            mysqli_stmt_execute($stmt_notif);
        }

        header("Location: profile.php"); 
        exit();
    } else {
        $error = "Bir hata oluştu: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Beceri Ekle | Beceri Paylaşım</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

<header>
    <h1>Beceri Paylaş</h1>
    <nav>
        <a href="index.php">Ana Sayfa</a>
        <a href="profile.php">Profil</a>
        <a href="logout.php">Çıkış Yap</a>
    </nav>
</header>

<div class="container">
    <div class="form-container" style="margin: 0 auto; width: 100%; max-width: 500px; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        
        <h2>Yeni Bir Beceri Paylaş</h2>
        <p>Yeteneklerinizi paylaşın ve topluluğa katkıda bulunun.</p>
        <hr>

        <?php if(isset($error)): ?>
            <div class="error-msg" style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-row">
                <label><b>Beceri Başlığı</b></label>
                <input type="text" name="title" placeholder="Örn: PHP, Gitar, İspanyolca..." required style="width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ccc; border-radius: 8px;">
            </div>

            <div class="form-row">
                <label><b>İlan Türü</b></label>
                <select name="type" required style="width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ccc; border-radius: 8px;">
                    <option value="ogret">Bu beceriyi öğretebilirim</option>
                    <option value="ogren">Bu beceriyi öğrenmek istiyorum</option>
                </select>
            </div>

            <div class="form-row">
                <label><b>Beceri Açıklaması</b></label>
                <textarea name="description" placeholder="Kendinizi tanıtın veya öğrenme hedefinizi yazın..." required style="width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ccc; border-radius: 8px; height: 100px;"></textarea>
            </div>

            <div class="form-row" style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="button" style="flex: 2; background: #6a1b9a; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: bold;">Kaydet ve Yayınla</button>
                <a href="profile.php" class="button btn-cancel" style="flex: 1; text-align: center; background: #eee; color: #333; text-decoration: none; padding: 12px; border-radius: 8px;">İptal</a>
            </div>
        </form>
    </div>
</div>

</body>
</html> 
