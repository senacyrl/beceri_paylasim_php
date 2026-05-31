<?php
include "config_db.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $notification_id = intval($_GET['id']);
    $redirect_page = 'match.php'; 

    $sql_link = "SELECT link FROM notifications WHERE id = ? AND user_id = ?";
    if ($stmt_link = mysqli_prepare($conn, $sql_link)) {
        mysqli_stmt_bind_param($stmt_link, "ii", $notification_id, $user_id);
        mysqli_stmt_execute($stmt_link);
        $result_link = mysqli_stmt_get_result($stmt_link);
        if ($row = mysqli_fetch_assoc($result_link)) {
            if (!empty($row['link'])) {
                $redirect_page = $row['link'];
            }
        }
    }

    $sql_update = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
    if ($stmt_update = mysqli_prepare($conn, $sql_update)) {
        mysqli_stmt_bind_param($stmt_update, "ii", $notification_id, $user_id);
        mysqli_stmt_execute($stmt_update);
    }

    header("Location: " . $redirect_page);
    exit();
}

$all_notif_query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $all_notif_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tüm Bildirimlerim</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 40px; }
        .notif-box { max-width: 600px; margin: 0 auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        h2 { color: #6a1b9a; margin-bottom: 20px; text-align: center; }
        .notif-card { padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; transition: 0.2s; }
        .notif-card:hover { background: #fafafa; }
        .notif-card.unread { border-left: 4px solid #6a1b9a; background: #fdf7ff; }
        .notif-link { background: #6a1b9a; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; }
        .btn-back { display: inline-block; margin-bottom: 20px; color: #6a1b9a; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <div class="notif-box">
        <a href="index.php" class="btn-back">← Ana Sayfaya Dön</a>
        <h2>Tüm Bildirimlerin</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($notif = mysqli_fetch_assoc($result)): ?>
                <div class="notif-card <?php echo $notif['is_read'] == 0 ? 'unread' : ''; ?>">
                    <div>
                        <div style="font-weight: <?php echo $notif['is_read'] == 0 ? 'bold' : 'normal'; ?>; color: #333;">
                            <?php echo htmlspecialchars($notif['message']); ?>
                        </div>
                        <small style="color: #999;"><?php echo date('d.m.Y H:i', strtotime($notif['created_at'])); ?></small>
                    </div>
                    <div>
                        <a href="read.php?id=<?php echo $notif['id']; ?>" class="notif-link">Git</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center; color: #999; padding: 20px;">Henüz hiçbir bildiriminiz bulunmuyor.</p>
        <?php endif; ?>
    </div>

</body>
</html>
