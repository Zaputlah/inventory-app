<?php
// Include file db.php untuk koneksi ke database
include 'db.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '1970-01-01';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '2099-12-31';

$searchValue = '%' . $search . '%';

$sql = "SELECT t.id_transaksi, b.nama_barang, t.stok, t.jumlah_terjual, t.tanggal_transaksi, j.jenis_barang
        FROM transaksi t
        JOIN barang b ON t.id_barang = b.id_barang
        JOIN jenis_barang j ON t.id_jenis_barang = j.id_jenis_barang
        WHERE b.nama_barang LIKE ? 
        AND t.tanggal_transaksi BETWEEN ? AND ?
        ORDER BY b.nama_barang, t.tanggal_transaksi";

$stmt = $pdo->prepare($sql);

$params = [$searchValue, $start_date, $end_date];
$stmt->execute($params);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
