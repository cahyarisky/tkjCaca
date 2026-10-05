<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $result = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if ($password === $row['password']) { // Pencocokan password langsung
            $_SESSION['login'] = true;
            $_SESSION['username'] = $row['username'];
            header("Location: index.php");
            exit;
        }
    }
    $error = "Username atau password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pendaftaran</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f3f0f7;
            --primary-purple: #8e7cc3;
            --primary-dark: #6c5ce7;
            --card-bg: #ffffff;
            --text-main: #2d3436;
            --accent-red: #ff7675;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-main); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .login-card { background: var(--card-bg); border-radius: 16px; padding: 35px 30px; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(108, 92, 231, 0.1); }
        .login-card h2 { color: var(--primary-dark); font-size: 22px; margin-bottom: 8px; text-align: center; }
        .login-card p { color: #636e72; font-size: 13px; margin-bottom: 25px; text-align: center; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 12px 14px; border: 2px solid #e1dbe8; border-radius: 10px; font-size: 14px; transition: 0.3s; background-color: #faf9fc; }
        .form-control:focus { outline: none; border-color: var(--primary-dark); background-color: #fff; }
        .btn-login { width: 100%; background-color: var(--primary-dark); color: white; border: none; padding: 12px; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; transition: 0.3s; margin-top: 10px; box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3); }
        .btn-login:hover { background-color: #5b4cc4; transform: translateY(-2px); }
        .alert { background-color: #ffe5e5; color: var(--accent-red); padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 18px; text-align: center; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Selamat Datang</h2>
    <p>Silakan login untuk mengelola pendaftaran</p>

    <?php if ($error): ?>
        <div class="alert"><?= $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>
        <button type="submit" name="login" class="btn-login">Login</button>
    </form>
</div>

</body>
</html>
