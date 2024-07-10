<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Sistem Informasi Poliklinik</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="index.php">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Data Master</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php?page=dokter">Dokter</a></li>
                        <li><a class="dropdown-item" href="index.php?page=pasien">Pasien</a></li>
                        <li><a class="dropdown-item" href="index.php?page=obat">Obat</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=periksa">Periksa</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php
                // Periksa apakah session 'username' telah ditetapkan
                if (isset($_SESSION['username'])) {
                    // Tampilkan tautan "Logout" jika pengguna telah login
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link" href="logout.php">Logout</a>';
                    echo '</li>';
                } else {
                    // Tampilkan tautan "Login" jika pengguna belum login
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link" href="index.php?page=login">Login</a>';
                    echo '</li>';
                    // Tampilkan tautan "Register" jika pengguna belum login
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link" href="index.php?page=register">Register</a>';
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
</nav>
