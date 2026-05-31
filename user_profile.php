<?php
include "config_db.php";
session_start();

if (!isset($_GET['id'])) {
    header("Location: index.php"); 
    exit();
}

$view_user_id = intval($_GET['id']);
$my_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

if ($view_user_id == $my_id) {
    header("Location: profile.php");
    exit();
}

$user_res = mysqli_query($conn, "SELECT username, email, bio FROM users WHERE id = $view_user_id");
$user = mysqli_fetch_assoc($user_res);

if (!$user) {
    echo "Kullanıcı bulunamadı.";
    exit();
}

$skills_res = mysqli_query($conn, "SELECT * FROM skills WHERE user_id = $view_user_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($user['username']); ?> Profili</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

<header>
    <h1>Profil Görüntüle</h1>
    <nav>
        <a href="index.php">Ana Sayfa</a>
        <?php if($my_id): ?>
            <a href="profile.php">Profilim</a>
        <?php else: ?>
            <a href="login_register.php">Giriş Yap</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">

    <div class="profile-card">
        <h2><?php echo htmlspecialchars($user['username']); ?></h2>
        
        <div class="profile-details">
            <p><b>E-posta:</b> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><b>Hakkımda:</b> <?php echo $user['bio'] ? htmlspecialchars($user['bio']) : "Bu kullanıcı henüz bir biyografi eklememiş."; ?></p>
        </div>
    </div>

    <h3 class="section-title"><?php echo htmlspecialchars($user['username']); ?> Tarafından Paylaşılan Beceriler</h3>
    <hr>
    <br>

    <?php if(mysqli_num_rows($skills_res) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($skills_res)): ?>
            <div class="post">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
                <small><b>Yükleme Tarihi:</b> <?php echo $row['created_at']; ?></small>
            </div>
            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Bu kullanıcı henüz bir beceri eklememiş.</p>
    <?php endif; ?>

</div>

</body>
</html>
