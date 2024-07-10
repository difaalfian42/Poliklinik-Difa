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

// Proses form jika sudah terkoneksi dengan database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    
    // Periksa apakah form untuk menambah data baru atau mengubah data yang ada
    if (isset($_POST['id'])) {
        // Jika ada id, artinya form digunakan untuk mengubah data
        $id = $_POST['id'];
        $update_query = "UPDATE dokter SET nama='$nama', alamat='$alamat', no_hp='$no_hp' WHERE id=$id";
        mysqli_query($mysqli, $update_query);
    } else {
        // Jika tidak ada id, artinya form digunakan untuk menambah data baru
        $insert_query = "INSERT INTO dokter (nama, alamat, no_hp) VALUES ('$nama', '$alamat', '$no_hp')";
        mysqli_query($mysqli, $insert_query);
    }
}

// Proses untuk menghapus data jika ada aksi hapus
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete_query = "DELETE FROM dokter WHERE id=$id";
    if(mysqli_query($mysqli, $delete_query)) {
        // Redirect kembali ke halaman dokter setelah penghapusan
        header("Location: index.php?page=dokter");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($mysqli);
    }
}

// Mengambil data dokter dari database
$result = mysqli_query($mysqli, "SELECT * FROM dokter");
$no = 1;
?>

<div class="container">
    <!--Form Input Data-->
    <div class="row">
        <div class="col">
            <form class="form" method="POST" action="index.php?page=dokter">
                <?php
                $nama = '';
                $alamat = '';
                $no_hp = '';
                if (isset($_GET['id'])) {
                    $ambil = mysqli_query($mysqli, "SELECT * FROM dokter WHERE id='" . $_GET['id'] . "'");
                    while ($row = mysqli_fetch_array($ambil)) {
                        $nama = $row['nama'];
                        $alamat = $row['alamat'];
                        $no_hp = $row['no_hp'];
                    }
                ?>
                    <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
                <?php
                }
                ?>
                <div class="mb-3">
                    <label for="inputNama" class="form-label fw-bold">Nama</label>
                    <input type="text" class="form-control" name="nama" id="inputNama" placeholder="Nama" value="<?php echo $nama ?>">
                </div>
                <div class="mb-3">
                    <label for="inputAlamat" class="form-label fw-bold">Alamat</label>
                    <input type="text" class="form-control" name="alamat" id="inputAlamat" placeholder="Alamat" value="<?php echo $alamat ?>">
                </div>
                <div class="mb-3">
                    <label for="inputNoHp" class="form-label fw-bold">No. HP</label>
                    <input type="text" class="form-control" name="no_hp" id="inputNoHp" placeholder="No. HP" value="<?php echo $no_hp ?>">
                </div>
                <button type="submit" class="btn btn-primary rounded-pill px-3" name="simpan">Simpan</button>
            </form>
        </div>
    </div>
    <br/>
    <!-- Table -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>No. HP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($data = mysqli_fetch_array($result)) { ?>
                <tr>
                    <td><?php echo $no++ ?></td>
                    <td><?php echo $data['nama'] ?></td>
                    <td><?php echo $data['alamat'] ?></td>
                    <td><?php echo $data['no_hp'] ?></td>
                    <td>
                        <a class="btn btn-success rounded-pill px-3" href="dokter.php?id=<?php echo $data['id'] ?>">Ubah</a>
                        <a class="btn btn-danger rounded-pill px-3" href="dokter.php?id=<?php echo $data['id'] ?>&aksi=hapus">Hapus</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
