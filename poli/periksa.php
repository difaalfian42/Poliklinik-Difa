<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection file
include_once("conn.php");

// Database connection using mysqli
$mysqli = new mysqli($databaseHost, $databaseUsername, $databasePassword, $databaseName);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan'])) {
    // Retrieve data from form
    $id_dokter = $_POST['id_dokter'];
    $id_pasien = $_POST['id_pasien'];
    $tgl_periksa = $_POST['tgl_periksa'];
    $catatan = $_POST['catatan'];
    $obat = implode(',', $_POST['obat']); // Combine selected drug IDs into comma-separated string

    // Determine whether to insert new data or update existing data based on presence of 'id' in POST
    if (isset($_POST['id'])) {
        // Update existing record
        $id = $_POST['id'];
        $update_query = "UPDATE periksa SET id_dokter=?, id_pasien=?, tgl_periksa=?, catatan=?, obat=? WHERE id=?";
        $stmt = $mysqli->prepare($update_query);
        $stmt->bind_param("iisssi", $id_dokter, $id_pasien, $tgl_periksa, $catatan, $obat, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Insert new record
        $insert_query = "INSERT INTO periksa (id_dokter, id_pasien, tgl_periksa, catatan, obat) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insert_query);
        $stmt->bind_param("iisss", $id_dokter, $id_pasien, $tgl_periksa, $catatan, $obat);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect back to periksa.php after form submission
    header("Location: http://localhost/poli/index.php?page=periksa");
    exit();
}

// Process deletion if 'aksi=hapus' and 'id' is set in GET parameters
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete_query = "DELETE FROM periksa WHERE id=?";
    $stmt = $mysqli->prepare($delete_query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // Redirect back to periksa.php after deletion
        header("Location: http://localhost/poli/index.php?page=periksa");
        exit();
    } else {
        echo "Error deleting record: " . $mysqli->error;
    }
    $stmt->close();
}

// Retrieve doctors' data from database
$result_dokter = $mysqli->query("SELECT * FROM dokter");

// Retrieve patients' data from database
$result_pasien = $mysqli->query("SELECT * FROM pasien");

// Retrieve drugs' data from database
$result_obat = $mysqli->query("SELECT * FROM obat");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Periksa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/id.js"></script>
</head>
<body>
    <div class="container">
        <form method="POST" action="periksa.php">
            <?php
            // Initialize variables for form fields
            $id_dokter = '';
            $id_pasien = '';
            $tgl_periksa = '';
            $catatan = '';
            $obat = '';

            // Check if 'id' is set in GET, indicating edit mode
            if (isset($_GET['id'])) {
                // Fetch existing record data if 'id' is set
                $ambil = $mysqli->query("SELECT * FROM periksa WHERE id='" . $_GET['id'] . "'");
                $row = $ambil->fetch_assoc();
                $id_dokter = $row['id_dokter'];
                $id_pasien = $row['id_pasien'];
                $tgl_periksa = $row['tgl_periksa'];
                $catatan = $row['catatan'];
                $obat = $row['obat'];
            ?>
                <!-- Hidden input field to store ID for updating record -->
                <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
            <?php
            }
            ?>
            <div class="mb-3">
                <label for="inputDokter" class="form-label fw-bold">Dokter</label>
                <select class="form-select" name="id_dokter" id="inputDokter">
                    <?php while ($data_dokter = $result_dokter->fetch_assoc()) { ?>
                        <option value="<?php echo $data_dokter['id'] ?>" <?php echo ($id_dokter == $data_dokter['id']) ? 'selected' : ''; ?>><?php echo $data_dokter['nama'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="inputPasien" class="form-label fw-bold">Pasien</label>
                <select class="form-select" name="id_pasien" id="inputPasien">
                    <?php while ($data_pasien = $result_pasien->fetch_assoc()) { ?>
                        <option value="<?php echo $data_pasien['id'] ?>" <?php echo ($id_pasien == $data_pasien['id']) ? 'selected' : ''; ?>><?php echo $data_pasien['nama'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="inputTanggal" class="form-label fw-bold">Tanggal dan Jam Periksa</label>
                <input type="datetime-local" class="form-control" name="tgl_periksa" id="inputTanggal" value="<?php echo ($tgl_periksa != '') ? date('Y-m-d\TH:i', strtotime($tgl_periksa)) : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="inputCatatan" class="form-label fw-bold">Catatan</label>
                <textarea class="form-control" name="catatan" id="inputCatatan" rows="3"><?php echo $catatan ?></textarea>
            </div>
            <div class="mb-3">
                <label for="inputObat" class="form-label fw-bold">Obat</label>
                <select class="form-select" name="obat[]" id="inputObat" multiple>
                    <?php while ($data_obat = $result_obat->fetch_assoc()) { ?>
                        <option value="<?php echo $data_obat['id'] ?>" <?php echo (strpos($obat, (string)$data_obat['id']) !== false) ? 'selected' : ''; ?>><?php echo $data_obat['nama_obat'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary rounded-pill px-3" name="simpan">Simpan</button>
        </form>
        <br>
        <!-- Table to display existing data -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pasien</th>
                    <th>Nama Dokter</th>
                    <th>Tanggal Periksa</th>
                    <th>Catatan</th>
                    <th>Obat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query to fetch periksa data with join to dokter and pasien tables
                $query = "SELECT pr.*, d.nama as 'nama_dokter', p.nama as 'nama_pasien' FROM periksa pr LEFT JOIN dokter d ON (pr.id_dokter=d.id) LEFT JOIN pasien p ON (pr.id_pasien=p.id) ORDER BY pr.tgl_periksa DESC";
                $result = $mysqli->query($query);
                $no = 1;
                while ($data = $result->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $data['nama_pasien']; ?></td>
                        <td><?php echo $data['nama_dokter']; ?></td>
                        <td><?php echo date('d-m-Y H:i', strtotime($data['tgl_periksa'])); ?></td>
                        <td><?php echo $data['catatan']; ?></td>
                        <td>
                            <?php
                            // Ambil nama obat berdasarkan id obat yang disimpan dalam bentuk string dipisahkan koma
                            $obat_ids = explode(',', $data['obat']);
                            $obat_names = array();
                            foreach ($obat_ids as $obat_id) {
                                $result_obat_name = $mysqli->query("SELECT nama_obat FROM obat WHERE id='$obat_id'");
                                if ($result_obat_name->num_rows > 0) {
                                    $obat_name = $result_obat_name->fetch_assoc()['nama_obat'];
                                    $obat_names[] = $obat_name;
                                }
                            }
                            echo implode(', ', $obat_names);
                            ?>
                        </td>
                        <td>
                            <a class="btn btn-success rounded-pill px-3 mr-1" href="index.php?page=periksa&id=<?php echo $data['id']; ?>">Edit</a>
                            <a class="btn btn-danger rounded-pill px-3 mr-1" href="periksa.php?aksi=hapus&id=<?php echo $data['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                            <a class="btn btn-warning rounded-pill px-3" href="javascript:void(0);" onclick="printInvoice(<?php echo $data['id']; ?>)">Nota</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function() {
            $('#inputObat').select2({
                placeholder: "Pilih Obat",
                allowClear: true,
                language: "id"
            });
        });

        function printInvoice(id) {
            // Redirect to a PHP script to generate and display the invoice
            window.location.href = 'print_invoice.php?id=' + id;
        }
    </script>
</body>
</html>
