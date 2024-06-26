<?php
include 'db.php';
include 'search.php';
include 'sorting.php';

// Initialize counter variable
$counter = 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .table-responsive {
            overflow-x: auto;
        }

        @media (max-width: 767.98px) {
            .select-mobile {
                margin-bottom: 10px; 
            }
            .actions-column {
                white-space: nowrap; /* Mencegah pembungkusan teks */
            }
            .actions-column .btn {
                margin-bottom: 5px; /* Margin bawah untuk tombol di dalam kolom Actions */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-3">Inventory Management</h1>

        <!-- Search and sort form -->
        <div class="row mb-3">
            <div class="col-md-6">
                <form action="index.php" method="get" class="form-inline">
                    <div class="form-group mr-2">
                        <input type="text" class="form-control" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Search</button>
                </form>
            </div>
            <div class="col-md-6">
                <form action="index.php" method="get" class="form-inline">
                    <div class="form-group mr-2">
                        <label for="order_by">Sort By:</label>
                        <select class="form-control form-control-sm mr-2" name="order_by">
                            <option value="nama_barang" <?php echo ($order_by == 'nama_barang') ? 'selected' : ''; ?>>Nama Barang</option>
                            <option value="tanggal_transaksi" <?php echo ($order_by == 'tanggal_transaksi') ? 'selected' : ''; ?>>Tanggal Transaksi</option>
                        </select>
                        <select class="form-control form-control-sm mr-2 select-mobile" name="order_mode">
                            <option value="ASC" <?php echo ($order_mode == 'ASC') ? 'selected' : ''; ?>>Ascending</option>
                            <option value="DESC" <?php echo ($order_mode == 'DESC') ? 'selected' : ''; ?>>Descending</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Sort</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add New Item button -->
        <a href="create.php" class="btn btn-primary float-right mb-3">Add New Item</a>

        <!-- Table -->
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                                <td>{$counter}</td>
                                <td>" . htmlspecialchars($row['nama_barang']) . "</td>
                                <td>" . htmlspecialchars($row['stok']) . "</td>
                                <td>" . htmlspecialchars($row['jumlah_terjual']) . "</td>
                                <td>" . htmlspecialchars($row['tanggal_transaksi']) . "</td>
                                <td>" . htmlspecialchars($row['jenis_barang']) . "</td>
                                <td class='actions-column'>
                                    <a href='update.php?id={$row['id_transaksi']}' class='btn btn-warning btn-sm' title='Edit'>
                                        <i class='fas fa-edit'></i>
                                    </a>
                                    <a href='javascript:void(0);' onclick='confirmDelete({$row['id_transaksi']});' class='btn btn-danger btn-sm ml-1' title='Delete'>
                                        <i class='fas fa-trash-alt'></i>
                                    </a>
                                </td>
                              </tr>";
                        $counter++; // Increment counter for the next row
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    // Function untuk menampilkan dialog konfirmasi sebelum menghapus
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this item?')) {
            window.location.href = 'delete.php?id=' + id; // Redirect ke halaman delete.php dengan parameter id
        }
    }
    </script>
</body>
</html>
