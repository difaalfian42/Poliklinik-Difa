<?php
// Mengecek jika sesi belum dimulai, maka mulai sesi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mengecek apakah pengguna belum login, jika ya, maka redirect ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include_once("conn.php");

// Menghubungkan ke database
$mysqli = mysqli_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);

// Cek koneksi
if (mysqli_connect_errno()) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Pesan error dan sukses
$error_message = '';
$success_message = '';

// Proses form jika sudah terkoneksi dengan database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama_obat = $_POST['nama_obat'];
    $kemasan = $_POST['kemasan'];
    $harga = $_POST['harga'];

    // Validasi input
    if (empty($nama_obat) || empty($kemasan) || empty($harga)) {
        $error_message = "Semua field harus diisi.";
    } else {
        // Periksa apakah form untuk menambah data baru atau mengubah data yang ada
        if (isset($_POST['id'])) {
            // Jika ada id, artinya form digunakan untuk mengubah data
            $id = $_POST['id'];
            $update_query = "UPDATE obat SET nama_obat=?, kemasan=?, harga=? WHERE id=?";
            $stmt = $mysqli->prepare($update_query);
            $stmt->bind_param("ssdi", $nama_obat, $kemasan, $harga, $id);
            if ($stmt->execute()) {
                $success_message = "Data obat berhasil diubah.";
            } else {
                $error_message = "Gagal mengubah data obat.";
            }
        } else {
            // Jika tidak ada id, artinya form digunakan untuk menambah data baru
            $insert_query = "INSERT INTO obat (nama_obat, kemasan, harga) VALUES (?, ?, ?)";
            $stmt = $mysqli->prepare($insert_query);
            $stmt->bind_param("ssd", $nama_obat, $kemasan, $harga);
            if ($stmt->execute()) {
                $success_message = "Data obat berhasil ditambahkan.";
            } else {
                $error_message = "Gagal menambahkan data obat.";
            }
        }
    }
}

// Proses untuk menghapus data jika ada aksi hapus
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete_query = "DELETE FROM obat WHERE id=?";
    $stmt = $mysqli->prepare($delete_query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // Redirect kembali ke halaman obat setelah penghapusan
        header("Location: index.php?page=obat");
        exit();
    } else {
        $error_message = "Error deleting record: " . $mysqli->error;
    }
}

// Mengambil data obat dari database
$result = mysqli_query($mysqli, "SELECT * FROM obat");
$no = 1;
?>

<div class="container">
    <?php
    // Tampilkan pesan sukses atau error jika ada
    if (!empty($error_message)) {
        echo "<div class='alert alert-danger'>$error_message</div>";
    } elseif (!empty($success_message)) {
        echo "<div class='alert alert-success'>$success_message</div>";
    }
    ?>

    <!-- Form Input Data -->
    <div class="row mb-3">
        <div class="col">
            <form class="form" method="POST" action="index.php?page=obat">
                <?php if (isset($_GET['id'])) { ?>
                    <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
                <?php } ?>
                <div class="mb-3">
                    <label for="inputNamaObat" class="form-label fw-bold">Nama Obat</label>
                    <input type="text" class="form-control" name="nama_obat" id="inputNamaObat" placeholder="Nama Obat" value="<?php echo isset($nama_obat) ? htmlspecialchars($nama_obat) : '' ?>">
                </div>
                <div class="mb-3">
                    <label for="inputKemasan" class="form-label fw-bold">Kemasan</label>
                    <input type="text" class="form-control" name="kemasan" id="inputKemasan" placeholder="Kemasan" value="<?php echo isset($kemasan) ? htmlspecialchars($kemasan) : '' ?>">
                </div>
                <div class="mb-3">
                    <label for="inputHarga" class="form-label fw-bold">Harga</label>
                    <input type="number" class="form-control" name="harga" id="inputHarga" placeholder="Harga" value="<?php echo isset($harga) ? htmlspecialchars($harga) : '' ?>">
                </div>
                <button type="submit" class="btn btn-primary rounded-pill px-3" name="simpan">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="row">
        <div class="col">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Obat</th>
                        <th>Kemasan</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($data = mysqli_fetch_array($result)) { ?>
                        <tr>
                            <td><?php echo $no++ ?></td>
                            <td><?php echo htmlspecialchars($data['nama_obat']) ?></td>
                            <td><?php echo htmlspecialchars($data['kemasan']) ?></td>
                            <td><?php echo htmlspecialchars($data['harga']) ?></td>
                            <td>
                                <a class="btn btn-success rounded-pill px-3" href="index.php?page=obat&id=<?php echo $data['id'] ?>">Ubah</a>
                                <a class="btn btn-danger rounded-pill px-3" href="index.php?page=obat&id=<?php echo $data['id'] ?>&aksi=hapus">Hapus</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Menutup koneksi database
mysqli_close($mysqli);
?>
