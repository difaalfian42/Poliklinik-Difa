<?php
// Memeriksa apakah session sudah dimulai sebelumnya
if(session_status() == PHP_SESSION_NONE) {
    session_start(); // Memulai session jika belum dimulai
}

// Menyertakan file koneksi database
include_once("conn.php");

// Mengatur pesan error kosong
$errors = array();

// Memproses data registrasi ketika formulir registrasi disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil nilai dari formulir registrasi
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Memeriksa apakah password cocok dengan konfirmasi password
    if ($password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok.";
    }

    // Memeriksa apakah username sudah digunakan
    $query = "SELECT * FROM user WHERE username='$username'";
    $result = mysqli_query($mysqli, $query);

    if (mysqli_num_rows($result) > 0) {
        $errors[] = "Username sudah digunakan.";
    }

    // Jika tidak ada error, masukkan data pengguna ke database
    if (empty($errors)) {
        // Hash password sebelum menyimpannya ke database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Query untuk menambahkan pengguna baru ke database
        $insert_query = "INSERT INTO user (username, password) VALUES ('$username', '$hashed_password')";

        if (mysqli_query($mysqli, $insert_query)) {
            // Jika registrasi berhasil, redirect ke halaman login
            header("Location: index.php?page=login");
            exit;
        } else {
            // Jika terjadi kesalahan saat menambahkan pengguna ke database, tambahkan pesan error
            $errors[] = "Terjadi kesalahan. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-center">Register</h3>
                        <?php
                        // Menampilkan pesan error jika ada
                        if (!empty($errors)) {
                            foreach ($errors as $error) {
                                echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
                            }
                        }
                        ?>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Register</button>
                        </form>
                        <br/>
                        <div class="mb-3">
                            Sudah punya akun? <a href="login.php">Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
