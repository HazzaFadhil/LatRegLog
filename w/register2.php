<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <main>
        <section class="form-card">
            <h2 id="judul-registrasi">Form Registrasi</h2>

            <form method="POST" action="">
                <p class="field-group">
                    <label for="nama_lengkap">Nama Lengkap:</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required>
                </p>

                <p class="field-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </p>

                <p class="field-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </p>

                <button type="submit" class="btn-submit">Registrasi</button>
            </form>

            <p class="footer-link">sudah puya akun?<a href="login.php">login disini</a></p>
        </section>
    </main>
</body>

</html>