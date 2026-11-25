<?php 
session_start();
require 'koneksi.php';

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$success = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($nama_lengkap === '' || $username === '' || $password === '') {
        $error = 'Semua field harus diisi.';
    } else {
        $cek = $pdo->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
        $cek->execute([':username' => $username]);

        if ($cek->fetch()) {
            $error = "Username sudah digunakan.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
            INSERT INTO users (nama_lengkap, username, password)
            VALUES (:nama_lengkap, :username, :password)
            ");
            $stmt->execute([
                ':nama_lengkap' => $nama_lengkap,
                ':username' => $username, 
                ':password' => $hashed_password
            ]);

            $success = "Registrasi berhasil. Silakan login";
        }
    }
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Registrasi</h2>
    <?php if ($error !== ''): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success !== ''): ?>
        <p style="color: green;">
            <?php echo htmlspecialchars($success); ?>
            <a href="login.php">Login di sini</a>
        </p>
    <?php endif; ?>
    <form method="POST" action="">
        <label>Nama lengkap:</label><br>
        <input type="text" name="nama_lengkap" required><br>

        <label>Username:</label><br>
        <input type="text" name="username" required><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br>
        <input type="submit" value="Registrasi">
    </form>

    <p>sudah punya aku? <a href="login.php">Login disini</a></p>
</body>
</html>