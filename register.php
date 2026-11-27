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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <style>
        /* * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 24px;
        }

        main {
            max-width: 400px;
            margin: 40px auto;
        }

        .form-card {
            padding: 24px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: #ffffff;
        }

        .form-card h2 {
            margin-top: 0;
            margin-bottom: 16px;
        }

        .field-group {
            margin-top: 0;
            margin-bottom: 14px;
        }

        .field-group label {
            display: block;
            margin-bottom: 6px;
        }

        .field-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #94a3b8;
            border-radius: 6px;
        }

        .btn-submit {
            width: 100%;
            padding: 10px 14px;
            border: none;
            border-radius: 6px;
            background-color: #2563eb;
            color: #ffffff;
            cursor: pointer;
        }

        .alert {
            padding: 10px 12px;
            margin-top: 0;
            margin-bottom: 16px;
            border-radius: 6px;
            border: 1px solid transparent;
        }

        .alert-error {
            color: #991b1b;
            background-color: #fef2f2;
            border-color: #fca5a5;
        }

        .alert-success {
            color: #166534;
            background-color: #f0fdf4;
            border-color: #86efac;
        }

        .footer-link {
            margin-top: 16px;
            margin-bottom: 0; 
        } */
    </style>
</head>

<body>
    <main>
        <section class="form-card" aria-labelledby="judul-registrasi">
            <h2 id="judul-registrasi">Registrasi</h2>

            <?php if ($error !== ''): ?>
                <p class="alert alert-error" role="alert"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <?php if ($success !== ''): ?>
                <p class="alert alert-success" role="status">
                    <?php echo htmlspecialchars($success); ?>
                    <a href="login.php">Login di sini</a>
                </p>
            <?php endif; ?>

            <form method="POST" action="">
                <p class="field-group">
                    <label for="nama_lengkap">Nama lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required>
                </p>

                <p class="field-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </p>

                <p class="field-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </p>

                <button type="submit" class="btn-submit">Registrasi</button>
            </form>

            <p class="footer-link">Sudah punya akun?<a href="login.php">Login di sini</a></p>
        </section>
    </main>
</body>
</html>