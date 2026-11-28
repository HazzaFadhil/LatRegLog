<?php
session_start();
require '../koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap    = trim($_POST['nama_lengkap'] ?? '');
    $username        = trim($_POST['username'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $password        = $_POST['password'] ?? '';
    $no_hp           = trim($_POST['no_hp'] ?? '');
    $telepon_darurat = trim($_POST['telepon_darurat'] ?? '');
    $alamat          = trim($_POST['alamat'] ?? '');
    $bio             = trim($_POST['bio'] ?? '');

    if ($nama_lengkap === '' || $username === '' || $password === '' || $no_hp === '') {
        $error = 'Nama lengkap, username, password, dan nomor HP wajib diisi.';
    } else {
        $cek = $pdo->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
        $cek->execute([':username' => $username]);

        if ($cek->fetch()) {
            $error = 'Username sudah digunakan.';
        } else {
            try {
                $pdo->beginTransaction();

                // 1. Insert ke tabel users
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmtUser = $pdo->prepare("
                    INSERT INTO users (nama_lengkap, username, password, email)
                    VALUES (:nama_lengkap, :username, :password, :email)
                ");
                $stmtUser->execute([
                    ':nama_lengkap' => $nama_lengkap,
                    ':username'     => $username,
                    ':password'     => $hashedPassword,
                    ':email'        => $email !== '' ? $email : null
                ]);

                $userId = (int) $pdo->lastInsertId();

                // 2. Insert ke tabel profiles
                $stmtProfile = $pdo->prepare("
                    INSERT INTO profiles (user_id, bio, alamat)
                    VALUES (:user_id, :bio, :alamat)
                ");
                $stmtProfile->execute([
                    ':user_id' => $userId,
                    ':bio'     => $bio !== '' ? $bio : null,
                    ':alamat'  => $alamat !== '' ? $alamat : null
                ]);

                // 3. Insert ke tabel contacts
                $stmtContact = $pdo->prepare("
                    INSERT INTO contacts (user_id, no_hp, telepon_darurat)
                    VALUES (:user_id, :no_hp, :telepon_darurat)
                ");
                $stmtContact->execute([
                    ':user_id'         => $userId,
                    ':no_hp'           => $no_hp,
                    ':telepon_darurat' => $telepon_darurat !== '' ? $telepon_darurat : null
                ]);

                $pdo->commit();
                $success = 'Registrasi berhasil! Data tersimpan di users, profiles, dan contacts.';
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error = 'Registrasi gagal diproses. Transaksi dibatalkan.';
            }
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
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 24px;
        }

        main {
            max-width: 440px;
            margin: 32px auto;
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

        .field-group input,
        .field-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #94a3b8;
            border-radius: 6px;
            font-family: inherit;
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
        }

        .alert-error {
            color: #991b1b;
            background-color: #fef2f2;
            border: 1px solid #fca5a5;
        }

        .alert-success {
            color: #166534;
            background-color: #f0fdf4;
            border: 1px solid #86efac;
        }

        .footer-link {
            margin-top: 16px;
            margin-bottom: 0;
        }
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

            <form method="POST" action="" novalidate>
                <p class="field-group">
                    <label for="nama_lengkap">Nama lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required>
                </p>

                <p class="field-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </p>

                <p class="field-group">
                    <label for="email">Email (opsional)</label>
                    <input type="email" id="email" name="email">
                </p>

                <p class="field-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </p>

                <p class="field-group">
                    <label for="no_hp">Nomor HP</label>
                    <input type="text" id="no_hp" name="no_hp" required>
                </p>

                <p class="field-group">
                    <label for="telepon_darurat">Telepon darurat (opsional)</label>
                    <input type="text" id="telepon_darurat" name="telepon_darurat">
                </p>

                <p class="field-group">
                    <label for="alamat">Alamat (opsional)</label>
                    <input type="text" id="alamat" name="alamat">
                </p>

                <p class="field-group">
                    <label for="bio">Bio singkat (opsional)</label>
                    <textarea id="bio" name="bio" rows="3"></textarea>
                </p>

                <button type="submit" class="btn-submit">Registrasi</button>
            </form>

            <p class="footer-link">Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </section>
    </main>
</body>
</html>