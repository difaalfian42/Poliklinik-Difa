<?php
// Include database connection file
include_once("conn.php");

// Database connection using mysqli
$mysqli = new mysqli($databaseHost, $databaseUsername, $databasePassword, $databaseName);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Initialize variables
$id_periksa = $_GET['id']; // Ambil ID periksa dari parameter URL

// Query untuk mengambil data periksa dari database
$query = "SELECT pr.*, p.nama as 'nama_pasien', p.alamat, p.no_hp, d.nama as 'nama_dokter', d.alamat as 'alamat_dokter', d.no_hp as 'no_hp_dokter'
          FROM periksa pr
          LEFT JOIN pasien p ON pr.id_pasien = p.id
          LEFT JOIN dokter d ON pr.id_dokter = d.id
          WHERE pr.id = $id_periksa";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $id_periksa = $data['id'];
    $tgl_periksa = date('Y-m-d H:i', strtotime($data['tgl_periksa']));
    $nama_pasien = $data['nama_pasien'];
    $alamat_pasien = $data['alamat'];
    $no_hp_pasien = $data['no_hp'];
    $nama_dokter = $data['nama_dokter'];
    $alamat_dokter = $data['alamat_dokter'];
    $no_hp_dokter = $data['no_hp_dokter'];

    // Query untuk mengambil obat yang terkait dengan periksa ini
    $obat_ids = explode(',', $data['obat']);
    $obat_details = array();
    foreach ($obat_ids as $obat_id) {
        $result_obat = $mysqli->query("SELECT nama_obat, harga FROM obat WHERE id = $obat_id");
        if ($result_obat->num_rows > 0) {
            $obat_data = $result_obat->fetch_assoc();
            $obat_details[] = array(
                'nama_obat' => $obat_data['nama_obat'],
                'harga' => $obat_data['harga']
            );
        }
    }
} else {
    // Jika tidak ada data periksa dengan ID yang sesuai
    echo "Data periksa tidak ditemukan.";
    exit();
}

// Close database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .invoice {
            max-width: 600px; /* Lebar maksimal invoice */
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #fff;
        }
        .invoice-header, .invoice-footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-table th, .invoice-table td {
            border: none; /* Menghilangkan border pada sel tabel */
            padding: 8px; /* Atur padding seperti sebelumnya */
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 10px;
            font-size: 16px;
            cursor: pointer;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        /* Style untuk versi cetak */
        @media print {
            body * {
                visibility: hidden;
            }
            .invoice, .invoice * {
                visibility: visible;
            }
            .invoice {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
                background: #fff;
            }
            .invoice-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                text-align: center;
                padding: 10px;
                border-top: 1px solid #ccc;
            }
        }
        .header-row, .details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .invoice-details-left, .invoice-details-right {
            flex: 1;
            text-align: left;
        }
        .invoice-details-right {
            margin-left: 20px;
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="invoice">
        <div class="invoice-header-left">
            <h2>Nota Pembayaran</h2>
            <hr>
        </div>
        <div class="header-row">
            <div class="invoice-details-left">
                <table class="invoice-table">
                    <tbody>
                        <tr>
                            <td>No. Periksa</td>
                        </tr>
                        <tr>
                            <th>#<?php echo $id_periksa; ?></th>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="invoice-details-right">
                <table class="invoice-table">
                    <tbody>
                        <tr>
                            <td>Tanggal Periksa</td>
                        </tr>
                        <tr>
                            <th><?php echo $tgl_periksa; ?></th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="details-row">
            <div class="invoice-details-left">
                <table class="invoice-table">
                    <tbody>
                        <tr>
                            <td>Pasien</td>
                        </tr>
                        <tr>
                            <th><?php echo $nama_pasien; ?></th>
                        </tr>
                        <tr>
                            <td><?php echo $alamat_pasien; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo $no_hp_pasien; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="invoice-details-right">
                <table class="invoice-table">
                    <tbody>
                        <tr>
                            <td>Dokter</td>
                        </tr>
                        <tr>
                            <th><?php echo $nama_dokter; ?></th>
                        </tr>
                        <tr>
                            <td><?php echo $alamat_dokter; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo $no_hp_dokter; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="invoice-section">
            <table class="invoice-table">
                <tbody>
                    <tr>
                        <th class="text-left">Deskripsi</th>
                        <th class="text-right">Harga</th>
                    </tr>
                    <tr>
                        <td>Jasa Dokter</td>
                        <td class="text-right">Rp. 150.000,00</td> <!-- Contoh, bisa dihitung berdasarkan data dari database -->
                    </tr>
                    <?php
                    $total_obat = 0; // Inisialisasi total obat
                    if (!empty($obat_details)) {
                        foreach ($obat_details as $obat) {
                            $total_obat += $obat['harga']; // Tambahkan harga obat ke total_obat
                            ?>
                            <tr>
                                <td><?php echo $obat['nama_obat']; ?></td>
                                <td class="text-right">Rp. <?php echo number_format($obat['harga'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
        </div>
        <hr>
        <div class="invoice-section">
            <table class="invoice-table">
                <tbody>
                    <tr>
                        <th class="text-right">Jasa Dokter</th>
                        <td width="150px" class="text-right">Rp. 150.000,00</td> <!-- Contoh, bisa dihitung berdasarkan data dari database -->
                    </tr>
                    <?php if (!empty($obat_details)) { ?>
                        <tr>
                            <th class="text-right">Subtotal Obat</th>
                            <td width="150px" class="text-right">Rp. <?php echo number_format($total_obat, 2, ',', '.'); ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th class="text-right">Total</th>
                        <td width="150px" class="text-right">Rp. <?php
                            $total_semua = 150000 + $total_obat; // Jasa dokter + total obat
                            echo number_format($total_semua, 2, ',', '.');
                        ?></td>
                    </tr>
                </tbody>
            </table>
            <button class="btn" onclick="printInvoice()">Cetak Invoice</button>
        </div>
    </div>

    <script>
        // Script untuk cetak halaman invoice
        function printInvoice() {
            window.print();
        }
    </script>
</body>
</html>