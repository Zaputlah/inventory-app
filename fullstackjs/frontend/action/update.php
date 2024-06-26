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
    $data = [
        'nama_barang' => $_POST['nama_barang'],
        'stok' => $_POST['stok'],
        'jumlah_terjual' => $_POST['jumlah_terjual'],
        'tanggal_transaksi' => $_POST['tanggal_transaksi'],
        'jenis_barang' => $_POST['jenis_barang']
    ];

    $jsonData = json_encode($data);
    if ($jsonData === false) {
        die('Gagal mengkonversi data ke JSON: ' . json_last_error_msg());
    }

    $options = [
        'http' => [
            'method' => 'PUT',
            'header' => 'Content-Type: application/json',
            'content' => $jsonData
        ]
    ];

    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    if ($result === false) {
        $error = error_get_last();
        die('Gagal menyimpan perubahan: ' . $error['message']);
    }

    header('Location: ../index.php');
    exit();
}
?>

