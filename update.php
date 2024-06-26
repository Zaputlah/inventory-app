<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $nama_barang = $_POST['nama_barang'];
        $stok = $_POST['stok'];
        $jumlah_terjual = $_POST['jumlah_terjual'];
        $tanggal_transaksi = $_POST['tanggal_transaksi'];
        $jenis_barang = $_POST['jenis_barang'];

        $sql = "UPDATE transaksi 
                SET stok = ?, jumlah_terjual = ?, tanggal_transaksi = ? 
                WHERE id_transaksi = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$stok, $jumlah_terjual, $tanggal_transaksi, $id]);

        header("Location: index.php");
        exit();
    } else {
        $sql = "SELECT t.id_transaksi, b.nama_barang, t.stok, t.jumlah_terjual, t.tanggal_transaksi, j.jenis_barang
                FROM transaksi t
                JOIN barang b ON t.id_barang = b.id_barang
                JOIN jenis_barang j ON t.id_jenis_barang = j.id_jenis_barang
                WHERE t.id_transaksi = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $nama_barang = $data['nama_barang'];
        $jenis_barang = $data['jenis_barang'];
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Item</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-3">Update Item</h1>
        <form action="update.php?id=<?php echo htmlspecialchars($id); ?>" method="post">
            <div class="form-group">
                <label for="nama_barang">Nama Barang</label>
                <input type="text" class="form-control" name="nama_barang" value="<?php echo htmlspecialchars($nama_barang); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" class="form-control" name="stok" value="<?php echo htmlspecialchars($data['stok']); ?>" required>
            </div>
            <div class="form-group">
                <label for="jumlah_terjual">Jumlah Terjual</label>
                <input type="number" class="form-control" name="jumlah_terjual" value="<?php echo htmlspecialchars($data['jumlah_terjual']); ?>" required>
            </div>
            <div class="form-group">
                <label for="tanggal_transaksi">Tanggal Transaksi</label>
                <input type="date" class="form-control" name="tanggal_transaksi" value="<?php echo htmlspecialchars($data['tanggal_transaksi']); ?>" required>
            </div>
            <div class="form-group">
                <label for="jenis_barang">Jenis Barang</label>
                <input type="text" class="form-control" name="jenis_barang" value="<?php echo htmlspecialchars($jenis_barang); ?>" readonly>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</body>
</html>
