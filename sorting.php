<?php
// Counter variable
$counter = 1;

// Ambil nilai dari input pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Ambil nilai dari input pengurutan
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'nama_barang'; // Default pengurutan berdasarkan nama_barang
$order_mode = isset($_GET['order_mode']) ? $_GET['order_mode'] : 'ASC'; // Default urutan ASC

// Query untuk mengambil data transaksi
$sql = "SELECT t.id_transaksi, b.nama_barang, t.stok, t.jumlah_terjual, t.tanggal_transaksi, j.jenis_barang
        FROM transaksi t
        JOIN barang b ON t.id_barang = b.id_barang
        JOIN jenis_barang j ON t.id_jenis_barang = j.id_jenis_barang
        WHERE b.nama_barang LIKE ?
        ORDER BY $order_by $order_mode"; // Query disesuaikan dengan input pengurutan

$stmt = $pdo->prepare($sql);
$stmt->execute(['%' . $search . '%']);