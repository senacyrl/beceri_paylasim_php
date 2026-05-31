<?php session_start(); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş & Kayıt</title>
    <style>
      
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('manzara.jpg') no-repeat center center fixed; 
            background-size: cover;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .auth-box {
            background: rgba(255, 255, 255, 0.15); 
            backdrop-filter: blur(15px); 
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 40px;
            width: 350px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            color: white;
            text-align: center;
        }

        .tab-group {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            justify-content: center;
        }

        .tab-btn {
            background: none;
            border: 2px solid rgba(255, 255, 255, 0.5);
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .tab-btn.active {
            background: purple; 
            border-color: purple;
        }

        .form-content {
            display: none; 
        }

        .form-content.active {
            display: block; 
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        input {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            outline: none;
        }

        input::placeholder { color: #eee; }

        .submit-btn {
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: purple;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover { background: #5e005e; }

        header h1 { margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
    </style>
</head>
<body>

<header>
    <h1 style="color: white;">Beceri Paylaşımı</h1>
</header>

<div class="auth-box">
    <div class="tab-group">
        <button class="tab-btn active" onclick="showForm('login', this)">Giriş Yap</button>
        <button class="tab-btn" onclick="showForm('register', this)">Kayıt Ol</button>
    </div>

    <div id="login-form" class="form-content active">
        <h2>Hoş Geldin</h2>
        <form action="process.php?mode=login" method="POST">
            <input type="email" name="email" placeholder="E-posta Adresi" required>
            <input type="password" name="password" placeholder="Şifre" required>
            <button type="submit" class="submit-btn">Giriş Yap</button>
        </form>
    </div>

    <div id="register-form" class="form-content">
        <h2>Yeni Hesap</h2>
        <form action="process.php?mode=register" method="POST">
            <input type="text" name="name" placeholder="Adınız Soyadınız" required>
            <input type="email" name="email" placeholder="E-posta Adresi" required>
            <input type="password" name="password" placeholder="Şifre Oluştur" required>
            <button type="submit" class="submit-btn">Kayıt Ol</button>
        </form>
    </div>
</div>

<script>
    function showForm(type, btn) {
        document.getElementById('login-form').classList.remove('active');
        document.getElementById('register-form').classList.remove('active');
        
        const btns = document.querySelectorAll('.tab-btn');
        btns.forEach(b => b.classList.remove('active'));

        document.getElementById(type + '-form').classList.add('active');
        btn.classList.add('active');
    }
</script>

</body>
</html>
