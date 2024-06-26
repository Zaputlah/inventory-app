<?php
include 'db.php';

$stok = '';
$jumlah_terjual = '';
$tanggal_transaksi = date('Y-m-d'); 
$jenis_barang = '';

$nama_barang_options = [];

try {
    $stmt = $pdo->query("SELECT nama_barang FROM barang");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nama_barang_options[] = $row['nama_barang'];
    }
} catch (PDOException $e) {
   
    $error_message = "Error retrieving data: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nama_barang = $_POST['nama_barang'];
    $stok = $_POST['stok'];
    $jumlah_terjual = $_POST['jumlah_terjual'];
    $tanggal_transaksi = $_POST['tanggal_transaksi'];
    $jenis_barang = $_POST['jenis_barang'];

    try {
        
        $stmt = $pdo->prepare("INSERT INTO transaksi (id_barang, stok, jumlah_terjual, tanggal_transaksi, id_jenis_barang) 
                               VALUES ((SELECT id_barang FROM barang WHERE nama_barang = ?), ?, ?, ?, ?)");
        
        $stmt->execute([$nama_barang, $stok, $jumlah_terjual, $tanggal_transaksi, $jenis_barang]);

        header("Location: index.php");
        exit(); 
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Item</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Add New Item</h1>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <form action="create.php" method="post">
            <div class="form-group">
                <label for="nama_barang">Nama Barang</label>
                <select class="form-control" name="nama_barang" required>
                    <option value="">Pilih Nama Barang</option>
                    <?php foreach ($nama_barang_options as $option): ?>
                        <option value="<?php echo htmlspecialchars($option); ?>"><?php echo htmlspecialchars($option); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" class="form-control" name="stok" value="<?php echo htmlspecialchars($stok); ?>" required>
            </div>
            <div class="form-group">
                <label for="jumlah_terjual">Jumlah Terjual</label>
                <input type="number" class="form-control" name="jumlah_terjual" value="<?php echo htmlspecialchars($jumlah_terjual); ?>" required>
            </div>
            <div class="form-group">
                <label for="tanggal_transaksi">Tanggal Transaksi</label>
                <input type="date" class="form-control" name="tanggal_transaksi" value="<?php echo htmlspecialchars($tanggal_transaksi); ?>" required>
            </div>
            <div class="form-group">
                <label for="jenis_barang">Jenis Barang</label>
                <select class="form-control" name="jenis_barang" required>
                    <option value="1" <?php if ($jenis_barang == '1') echo 'selected'; ?>>Konsumsi</option>
                    <option value="2" <?php if ($jenis_barang == '2') echo 'selected'; ?>>Pembersih</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
