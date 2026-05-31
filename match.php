<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "config_db.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/*
    1. Kullanıcının:
    - öğrenmek istedikleri (ogren)
    - öğrettikleri (ogret)
*/

$my_wants = [];
$my_teaches = [];

// benim öğrenmek istediklerim
$q1 = mysqli_query($conn, "SELECT title FROM skills WHERE user_id = $user_id AND type = 'ogren'");
while ($row = mysqli_fetch_assoc($q1)) {
    $my_wants[] = $row['title'];
}

// benim öğrettiklerim
$q2 = mysqli_query($conn, "SELECT title FROM skills WHERE user_id = $user_id AND type = 'ogret'");
while ($row = mysqli_fetch_assoc($q2)) {
    $my_teaches[] = $row['title'];
}

// diğer kullanıcılar
$users = mysqli_query($conn, "SELECT * FROM users WHERE id != $user_id");

$matches = [];

while ($u = mysqli_fetch_assoc($users)) {

    $uid = $u['id'];

    // onların skill'leri
    $their_skills = mysqli_query($conn, "SELECT type, title FROM skills WHERE user_id = $uid");

    $they_teach = [];
    $they_want = [];

    while ($s = mysqli_fetch_assoc($their_skills)) {
        if ($s['type'] == 'ogret') $they_teach[] = $s['title'];
        if ($s['type'] == 'ogren') $they_want[] = $s['title'];
    }

    // MATCH kontrolü
    $match = false;
    $match_teach = "";
    $match_want = "";

    foreach ($they_teach as $t) {
        foreach ($my_wants as $w) {
            if (stripos($t, $w) !== false || stripos($w, $t) !== false) {
                $match = true;
                $match_teach = $t;
            }
        }
    }

    foreach ($they_want as $w) {
        foreach ($my_teaches as $t) {
            if (stripos($w, $t) !== false || stripos($t, $w) !== false) {
                $match = true;
                $match_want = $w;
            }
        }
    }

    if ($match) {
        $u['teaches'] = $match_teach;
        $u['wants'] = $match_want;
        $matches[] = $u;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Eşleşmeler</title>

    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
            padding: 30px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .username {
            font-weight: bold;
            font-size: 18px;
            color: #6a1b9a;
        }

        .tag {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .green { background: #e8f5e9; color: #2e7d32; }
        .blue { background: #e3f2fd; color: #1565c0; }
    </style>
</head>
<body>

<h1 style="text-align:center;">Eşleşmeler</h1>

<div class="grid">

<?php if (count($matches) > 0): ?>
    <?php foreach ($matches as $m): ?>
        <div class="card">
            <div class="username">@<?= htmlspecialchars($m['username']) ?></div>

            <div class="tag green">
                Öğretebilir: <?= htmlspecialchars($m['teaches']) ?>
            </div>

            <div class="tag blue">
                Öğrenmek İstiyor: <?= htmlspecialchars($m['wants']) ?>
            </div>

            <p><?= htmlspecialchars($m['bio'] ?? '') ?></p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p style="text-align:center;">Eşleşme yok</p>
<?php endif; ?>

</div>

</body>
</html>
