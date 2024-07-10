<?php
session_start();

// Mengecek jika pengguna telah melakukan login sebelumnya, jika ya, maka redirect ke halaman dashboard atau halaman lainnya.
if (isset($_SESSION['username'])) {
    header("Location: index.php?page=login");
    exit;
}

// Menyertakan file koneksi database
include_once("conn.php");

// Mengatur pesan error kosong
$errors = array();

// Memproses data login ketika formulir login disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil nilai dari formulir login
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Memeriksa apakah ada pengguna dengan username yang sesuai
    $query = "SELECT * FROM user WHERE username='$username'";
    $result = mysqli_query($mysqli, $query);

    // Jika pengguna ditemukan, memeriksa password yang sesuai
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            // Jika password cocok, buat sesi login dan redirect ke halaman dashboard
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit;
        } else {
            // Jika password tidak cocok, tambahkan pesan error
            $errors[] = "Username atau password salah.";
        }
    } else {
        // Jika pengguna tidak ditemukan, tambahkan pesan error
        $errors[] = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-center">Login</h3>
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
                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>
                        <br/>
                        <div class="mb-3">
                            Belum punya akun ? <a href="register.php">Daftar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>
