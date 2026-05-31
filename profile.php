<?php
include "config_db.php";
session_start();

if (!isset($_SESSION['user_id'])) { 
    header("Location: login_register.php"); 
    exit(); 
}

$user_id = intval($_SESSION['user_id']);
$message = "";

$all_notifications = [];
$unread_count = 0;

$all_notif_query = "SELECT id, message, link, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
if ($notif_stmt = mysqli_prepare($conn, $all_notif_query)) {
    mysqli_stmt_bind_param($notif_stmt, "i", $user_id);
    mysqli_stmt_execute($notif_stmt);
    $notif_res = mysqli_stmt_get_result($notif_stmt);
    
    while ($row = mysqli_fetch_assoc($notif_res)) {
        $all_notifications[] = $row;
        if ($row['is_read'] == 0) {
            $unread_count++;
        }
    }
    mysqli_stmt_close($notif_stmt);
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM skills WHERE id = $delete_id AND user_id = $user_id";
    
    if (mysqli_query($conn, $delete_query)) {
        $message = "Beceri başarıyla silindi.";
    } else {
        $message = "Silme hatası: " . mysqli_error($conn);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_bio = mysqli_real_escape_string($conn, $_POST['bio']);

    $update_query = "UPDATE users SET email = '$new_email', bio = '$new_bio' WHERE id = $user_id";
    
    if (mysqli_query($conn, $update_query)) {
        $message = "Profil bilgileriniz başarıyla güncellendi!";
    } else {
        $message = "Hata oluştu: " . mysqli_error($conn);
    }
}

$user_res = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_res);

$skills_res = mysqli_query($conn, "SELECT * FROM skills WHERE user_id = $user_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profilim | Beceri Paylaşım</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        #editSection { display: none; margin-top: 20px; border-top: 1px dashed #ccc; padding-top: 20px; }
        .success-msg { color: #27ae60; background: #dff0d8; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        textarea { width: 100%; height: 100px; padding: 10px; border-radius: 5px; border: 1px solid #ddd; resize: vertical; }
        input[type="email"] { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; }
        .btn-edit { background-color: #2ecc71; margin-right: 5px; }
        .btn-cancel { background-color: #e74c3c; }
        
        .btn-delete { 
            color: #e74c3c; 
            text-decoration: none; 
            font-weight: bold; 
            font-size: 0.85rem;
            float: right;
            padding: 5px 10px;
            border: 1px solid #e74c3c;
            border-radius: 4px;
            transition: 0.3s;
        }
        .btn-delete:hover { 
            background-color: #e74c3c; 
            color: white; 
        }
        .post { overflow: hidden; margin-bottom: 15px; }

        nav {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .notification-container { 
            position: relative; 
            display: inline-block; 
        }
        .bell-button { 
            cursor: pointer; 
            display: flex;
            align-items: center;
            gap: 6px;
            user-select: none;
        }
        .bell-badge { 
            background: #e74c3c; 
            color: white; 
            border-radius: 10px; 
            padding: 1px 6px; 
            font-size: 10px; 
            font-weight: bold; 
            line-height: 1.2;
            display: inline-block;
        }
        .dropdown-menu { 
            display: none; 
            position: absolute; 
            right: 0; 
            top: 100%; 
            background: #ffffff; 
            width: 240px; 
            box-shadow: 0px 4px 12px rgba(0,0,0,0.15); 
            border-radius: 6px; 
            z-index: 9999; 
            max-height: 250px; 
            overflow-y: auto; 
            border: 1px solid #eee;
            padding: 0;
            margin-top: 5px;
        }
        .notification-container:hover .dropdown-menu { 
            display: block; 
        }
        .notification-item { 
            padding: 10px 12px; 
            border-bottom: 1px solid #f5f5f5; 
            display: block; 
            text-decoration: none; 
            background: #ffffff;
            text-align: left;
            line-height: 1.4;
            transition: background 0.2s; 
        }
        .notification-item:hover { 
            background: #fafafa; 
        }
        .notification-item.unread { 
            background: #fdf7ff; 
            border-left: 3px solid #6a1b9a; 
        }
        .no-notif { 
            padding: 15px; 
            text-align: center; 
            color: #888; 
            font-size: 12px; 
            background: #ffffff;
        }
    </style>
</head>
<body>

<header>
    <h1>Profil Bilgileri</h1>
    <nav>
        <a href="index.php">Ana Sayfa</a>
        
        <div class="notification-container">
            <a class="bell-button" href="javascript:void(0);">
                <span>Bildirimler</span>
                <?php if ($unread_count > 0): ?>
                    <span class="bell-badge"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu" id="notifDropdown">
                <?php if (count($all_notifications) > 0): ?>
                    <?php foreach ($all_notifications as $notif): ?>
                        <a href="read.php?id=<?php echo $notif['id']; ?>&git=<?php echo urlencode($notif['link']); ?>" 
                           class="notification-item <?php echo $notif['is_read'] == 0 ? 'unread' : ''; ?>">
                            <div style="color: #333; font-size: 12px; font-weight: <?php echo $notif['is_read'] == 0 ? 'bold' : 'normal'; ?>;">
                                <?php echo htmlspecialchars($notif['message']); ?>
                            </div>
                            <small style="color: #999; font-size: 10px; display: block; margin-top: 3px;">
                                <?php echo date('d.m.Y H:i', strtotime($notif['created_at'])); ?>
                            </small>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-notif">Henüz bildirim yok.</div>
                <?php endif; ?>
            </div>
        </div>

        <a href="logout.php">Çıkış Yap</a>
    </nav>
</header>

<div class="container">

    <div class="profile-card">
        <?php if($message): ?>
            <div class="success-msg"><?php echo $message; ?></div>
        <?php endif; ?>

        <h2>Merhaba, <?php echo htmlspecialchars($user['username']); ?>!</h2>

        <div id="infoSection">
            <div class="profile-details">
                <p><b>E-posta:</b> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><b>Hakkımda:</b> <?php echo $user['bio'] ? htmlspecialchars($user['bio']) : "Henüz bir biyografi eklenmedi."; ?></p>
            </div>
            <br>
            <button onclick="toggleEdit()" class="button btn-edit">Profili Düzenle</button>
            <a href="add_skills.php" class="button">+ Beceri Ekle</a>
        </div>

        <div id="editSection">
            <h3>Profili Güncelle</h3>
            <form method="POST" action="">
                <label><b>E-posta Adresi:</b></label><br>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                <br><br>
                <label><b>Hakkımda (Biyografi):</b></label><br>
                <textarea name="bio" placeholder="Kendinizden bahsedin..."><?php echo htmlspecialchars($user['bio']); ?></textarea>
                <br><br>
                <button type="submit" name="update_profile" class="button">Kaydet</button>
                <button type="button" onclick="toggleEdit()" class="button btn-cancel">İptal</button>
            </form>
        </div>
    </div>

    <h3 class="section-title">Paylaştığım Beceriler</h3>
    <hr>
    <br>

    <?php if(mysqli_num_rows($skills_res) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($skills_res)): ?>
            <div class="post">
                <a href="profile.php?delete_id=<?php echo $row['id']; ?>" 
                   class="btn-delete" 
                   onclick="return confirm('Bu beceriyi silmek istediğinizden emin misiniz?')">
                    Sil
                </a>
                
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
                <small><b>Yükleme Tarihi:</b> <?php echo $row['created_at']; ?></small>
            </div>
            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Henüz herhangi bir beceri eklemediniz.</p>
    <?php endif; ?>

</div>

<div style="margin: 30px 0; border-top: 1px dashed #e74c3c; padding-top: 20px; text-align: center;">
    <p style="color: #666; font-size: 14px; margin-bottom: 5px;">Platformu artık kullanmak istemiyor musunuz?</p>
    <a href="delete_account.php" 
       class="button" 
       onclick="return confirm('DİKKAT! Hesabınızı silmek istediğinize emin misiniz? Bu işlem geri alınamaz; tüm ilanlarınız, becerileriniz ve bildirimleriniz kalıcı olarak silinecektir!');" 
       style="background-color: #740e03; color: white; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; display: inline-block;">
         Hesabımı Kalıcı Olarak Sil
    </a>
</div>

<script>
function toggleEdit() {
    var info = document.getElementById('infoSection');
    var edit = document.getElementById('editSection');
    
    if (edit.style.display === "none" || edit.style.display === "") {
        edit.style.display = "block";
        info.style.display = "none";
    } else {
        edit.style.display = "none";
        info.style.display = "block";
    }
}
</script>

</body>
</html>
