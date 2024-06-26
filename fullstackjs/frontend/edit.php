<?php
// Pastikan ada parameter id_barang yang dikirimkan
if (!isset($_GET['id'])) {
    die('ID barang tidak ditemukan.');
}

$id_barang = $_GET['id'];
$url = 'http://localhost:3000/barang/' . urlencode($id_barang);

// Ambil data barang berdasarkan id dari API
$data = @file_get_contents($url);

// Handle jika gagal ambil data dari API
if ($data === false) {
    die('Gagal mengambil data barang dari API: ' . error_get_last()['message']);
}

// Decode JSON data menjadi array asosiatif
$barang = json_decode($data, true);

// Handle jika gagal decode JSON
if ($barang === null && json_last_error() !== JSON_ERROR_NONE) {
    die('Gagal decode data JSON: ' . json_last_error_msg());
}

// Proses form jika ada POST untuk menyimpan perubahan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lakukan validasi dan simpan perubahan di sini
    // Misalnya, simpan perubahan ke API dan redirect kembali ke index.php
    // Contoh sederhana untuk mengirim data kembali ke API:
    // $url = 'http://localhost:3000/barang/' . urlencode($id_barang);
    // $options = [
    //     'http' => [
    //         'method' => 'PUT',
    //         'header' => 'Content-Type: application/json',
    //         'content' => json_encode($_POST)
    //     ]
    // ];
    // $context = stream_context_create($options);
    // $result = @file_get_contents($url, false, $context);
    // if ($result === false) {
    //     die('Gagal menyimpan perubahan: ' . error_get_last()['message']);
    // }
    // header('Location: index.php');
    // exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-3">Edit Barang</h1>
        <form method="POST" action="./action/update.php?id=<?php echo $id_barang; ?>">
            <div class="form-group">
                <label for="nama_barang">Nama Barang:</label>
                <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?php echo isset($barang['nama_barang']) ? htmlspecialchars($barang['nama_barang']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="stok">Stok:</label>
                <input type="text" class="form-control" id="stok" name="stok" value="<?php echo isset($barang['stok']) ? htmlspecialchars($barang['stok']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="jumlah_terjual">Jumlah Terjual:</label>
                <input type="text" class="form-control" id="jumlah_terjual" name="jumlah_terjual" value="<?php echo isset($barang['jumlah_terjual']) ? htmlspecialchars($barang['jumlah_terjual']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="tanggal_transaksi">Tanggal Transaksi:</label>
                <input type="date" class="form-control" id="tanggal_transaksi" name="tanggal_transaksi" value="<?php echo isset($barang['tanggal_transaksi']) ? htmlspecialchars($barang['tanggal_transaksi']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="jenis_barang">Jenis Barang:</label>
                <input type="text" class="form-control" id="jenis_barang" name="jenis_barang" value="<?php echo isset($barang['jenis_barang']) ? htmlspecialchars($barang['jenis_barang']) : ''; ?>" readonly>
            </div>


            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
