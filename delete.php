<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM transaksi WHERE id_transaksi = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
?>
