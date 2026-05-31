<?php
include __DIR__ . "/config_db.php";
session_start();

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";
$filter = isset($_GET['filter']) ? mysqli_real_escape_string($conn, $_GET['filter']) : "";

$sql = "SELECT skills.*, users.username FROM skills 
        JOIN users ON skills.user_id = users.id WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND (skills.title LIKE '%$search%' OR skills.description LIKE '%$search%')";
}

if (!empty($filter)) {
    $sql .= " AND skills.type = '$filter'";
}

$sql .= " ORDER BY skills.created_at DESC";
$result = mysqli_query($conn, $sql);


$all_notifications = [];
$unread_count = 0;

if (isset($_SESSION['user_id'])) {
    $current_user_id = $_SESSION['user_id'];

    $all_notif_query = "SELECT id, message, link, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
    if ($notif_stmt = mysqli_prepare($conn, $all_notif_query)) {
        mysqli_stmt_bind_param($notif_stmt, "i", $current_user_id);
        mysqli_stmt_execute($notif_stmt);
        $notif_res = mysqli_stmt_get_result($notif_stmt);
        
        while ($row = mysqli_fetch_assoc($notif_res)) {
            $all_notifications[] = $row;
            if ($row['is_read'] == 0) {
                $unread_count++; 
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ana Sayfa | Beceri Paylaşım</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        .search-container {
            background: rgb(219, 214, 203);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        .search-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .search-form input[type="text"] {
            flex: 2;
            margin: 0;
        }
        .search-form select {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .badge {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            color: white;
            float: right;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-ogret { background-color: #2ecc71; } /* Yeşil */
        .badge-ogren { background-color: #3498db; } /* Mavi */

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
    <h1>Beceri Paylaşım Platformu</h1>
    <nav>
        <a href="index.php">Ana Sayfa</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="profile.php">Profil</a>
            <a href="add_skills.php">Beceri Ekle</a>
            
            <div class="notification-container">
                <a class="bell-button" href="javascript:void(0);">
                    <a href="/proje/read.php">Bildirimler</a>
                    <?php if ($unread_count > 0): ?>
                        <span class="bell-badge"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
                <div class="dropdown-menu" id="notifDropdown">
                    <?php if (count($all_notifications) > 0): ?>
                        <?php foreach ($all_notifications as $notif): ?>
                            <a href="bildirim_oku.php?id=<?php echo $notif['id']; ?>&git=<?php echo urlencode($notif['link']); ?>" 
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
        <?php else: ?>
            <a href="login_register.php#register">Kayıt Ol</a>
            <a href="login_register.php#login">Giriş Yap</a>
        <?php endif; ?>
    </nav>
</header>

<?php if (isset($_SESSION['user_id'])): ?>
<div class="match-alert" style="background: #6a1b9a; color: white; padding: 15px; border-radius: 8px; margin: 20px 0; display: flex; justify-content: space-between; align-items: center;">
    <span>Senin yeteneklerinle eşleşen <b>yeni kişiler</b> var mı? Hemen kontrol et!</span>
    <a href="match.php" class="button" style="background: #c2a815; color: #333; font-weight: bold; text-decoration: none; padding: 8px 15px; border-radius: 5px;">Eşleşmeleri Gör</a>
</div>
<?php endif; ?>

<div class="container">
    <div class="search-container">
        <form class="search-form" method="GET" action="index.php">
            <input type="text" name="search" placeholder="Beceri veya içerik ara..." value="<?php echo htmlspecialchars($search); ?>">
            
            <select name="filter">
                <option value="">Tüm İlanlar</option>
                <option value="ogret" <?php if($filter == 'ogret') echo 'selected'; ?>>Öğretebilirim</option>
                <option value="ogren" <?php if($filter == 'ogren') echo 'selected'; ?>>Öğrenmek İstiyorum</option>
            </select>
            
            <button type="submit" class="button" style="min-width: 100px;">Ara</button>
            <?php if($search || $filter): ?>
                <a href="index.php" class="button btn-cancel" style="min-width: 80px; padding: 10px; font-size: 14px; text-decoration:none; text-align:center;">Temizle</a>
            <?php endif; ?>
        </form>
    </div>

    <h2><?php echo ($search || $filter) ? "Arama Sonuçları" : "Son Paylaşımlar"; ?></h2>
    <br>

    <?php if($result && mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="post">
                <?php if($row['type'] == 'ogret'): ?>
                    <span class="badge badge-ogret">Öğretebilirim</span>
                <?php else: ?>
                    <span class="badge badge-ogren">Öğrenmek İstiyorum</span>
                <?php endif; ?>

                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                
                <div style="margin-top: 10px; border-top: 1px solid #eee; padding-top: 8px;">
                    <small>
                        Paylaşan: 
                        <a href="user_profile.php?id=<?php echo $row['user_id']; ?>" 
                           style="color: purple; font-weight: bold; text-decoration: none;">
                           <?php echo htmlspecialchars($row['username']); ?>
                        </a>
                        <span style="color: #888; margin-left: 10px;">
                             | <?php echo date("d.m.Y", strtotime($row['created_at'])); ?>
                        </span>
                    </small>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Aradığınız kriterlere uygun bir beceri bulunamadı.</p>
    <?php endif; ?>
</div>
</body>
</html>
