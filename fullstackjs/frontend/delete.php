<?php
// Pastikan ada parameter id_barang yang dikirimkan
if (!isset($_GET['id'])) {
    die('ID barang tidak ditemukan.');
}

$id_barang = $_GET['id'];
$url_transaksi = 'http://localhost:3000/transaksi/' . $id_barang;

// Mengirim request DELETE ke API untuk transaksi terlebih dahulu
$options_transaksi = [
    'http' => [
        'method' => 'DELETE',
    ],
];

$context_transaksi = stream_context_create($options_transaksi);
$result_transaksi = @file_get_contents($url_transaksi, false, $context_transaksi);

if ($result_transaksi === false) {
    die('Gagal menghapus transaksi: ' . error_get_last()['message']);
}

// Jika transaksi berhasil dihapus, lanjutkan untuk menghapus barang
$url_barang = 'http://localhost:3000/barang/' . $id_barang;
$options_barang = [
    'http' => [
        'method' => 'DELETE',
    ],
];

$context_barang = stream_context_create($options_barang);
$result_barang = @file_get_contents($url_barang, false, $context_barang);

if ($result_barang === false) {
    die('Gagal menghapus barang: ' . error_get_last()['message']);
}

// Tampilkan isi dari $result_barang menggunakan var_dump
echo '<pre>';
var_dump($result_barang);
echo '</pre>';

// Redirect ke halaman index.php setelah berhasil menghapus
// header('Location: ../index.php');
// exit();
?>
