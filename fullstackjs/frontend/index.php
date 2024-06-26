<?php
// URL dari API barang yang berjalan di localhost:3000
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : '';
$sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : '';
$url = 'http://localhost:3000/barang?search=' . urlencode($search) . '&sortBy=' . urlencode($sortBy) . '&sortOrder=' . urlencode($sortOrder);

// Ambil data dari API
$data = @file_get_contents($url);

// Handle jika gagal ambil data dari API
if ($data === false) {
    die('Gagal mengambil data dari API: ' . error_get_last()['message']);
}

// Decode JSON data menjadi array asosiatif
$barangList = json_decode($data, true);

// Handle jika gagal decode JSON
if ($barangList === null && json_last_error() !== JSON_ERROR_NONE) {
    die('Gagal decode data JSON: ' . json_last_error_msg());
}

// Konfigurasi pagination
$perPage = 10; // Jumlah data per halaman
$totalItems = count($barangList); // Total jumlah data
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1; // Halaman saat ini

// Hitung total halaman
$totalPages = ceil($totalItems / $perPage);

// Batasi data yang akan ditampilkan sesuai halaman saat ini
$start = ($currentPage - 1) * $perPage;
$end = $start + $perPage;
$paginatedBarangList = array_slice($barangList, $start, $perPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Inventaris</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-3">Manajemen Inventaris</h1>
        <form method="GET" action="index.php" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari barang..." value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                </div>
            </div>
            <div class="form-group mt-2">
                <label for="sortBy">Sortir berdasarkan:</label>
                <select name="sortBy" id="sortBy" class="form-control">
                    <option value="">Pilih</option>
                    <option value="nama_barang" <?php echo $sortBy === 'nama_barang' ? 'selected' : ''; ?>>Nama Barang</option>
                    <option value="tanggal_transaksi" <?php echo $sortBy === 'tanggal_transaksi' ? 'selected' : ''; ?>>Tanggal Transaksi</option>
                </select>
            </div>
            <div class="form-group mt-2">
                <label for="sortOrder">Urutan:</label>
                <select name="sortOrder" id="sortOrder" class="form-control">
                    <option value="ASC" <?php echo $sortOrder === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                    <option value="DESC" <?php echo $sortOrder === 'DESC' ? 'selected' : ''; ?>>Descending</option>
                </select>
            </div>
        </form>

        <a href="create.php" class="btn btn-primary float-right mb-3">Tambah Barang Baru</a>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Stok</th>
                        <th>Jumlah Terjual</th>
                        <th>Tanggal Transaksi</th>
                        <th>Jenis Barang</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = $start + 1; ?>
                    <?php foreach ($paginatedBarangList as $barang) : ?>
                        <tr>
                            <td><?php echo $counter; ?></td>
                            <td><?php echo isset($barang['nama_barang']) ? htmlspecialchars($barang['nama_barang']) : ''; ?></td>
                            <td><?php echo isset($barang['stok']) ? htmlspecialchars($barang['stok']) : ''; ?></td>
                            <td><?php echo isset($barang['jumlah_terjual']) ? htmlspecialchars($barang['jumlah_terjual']) : ''; ?></td>
                            <td><?php echo isset($barang['tanggal_transaksi']) ? htmlspecialchars($barang['tanggal_transaksi']) : ''; ?></td>
                            <td><?php echo isset($barang['jenis_barang']) ? htmlspecialchars($barang['jenis_barang']) : ''; ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $barang['id_barang']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="delete.php?id=<?php echo $barang['id_barang']; ?>" class="btn btn-sm btn-danger">Hapus</a>
                            </td>
                        </tr>
                        <?php $counter++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="clearfix">
            <ul class="pagination float-right">
                <?php if ($currentPage > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($search); ?>&sortBy=<?php echo urlencode($sortBy); ?>&sortOrder=<?php echo urlencode($sortOrder); ?>">Sebelumnya</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sortBy=<?php echo urlencode($sortBy); ?>&sortOrder=<?php echo urlencode($sortOrder); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($search); ?>&sortBy=<?php echo urlencode($sortBy); ?>&sortOrder=<?php echo urlencode($sortOrder); ?>">Selanjutnya</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <!-- End Pagination -->

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
