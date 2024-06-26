<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
        'nama_barang' => $_POST['nama_barang'],
        'stok' => $_POST['stok'],
        'jumlah_terjual' => $_POST['jumlah_terjual'],
        'tanggal_transaksi' => $_POST['tanggal_transaksi'],
        'id_jenis_barang' => $_POST['id_jenis_barang']
    ];

    $jsonData = json_encode($data);
    if ($jsonData === false) {
        die('Gagal mengkonversi data ke JSON: ' . json_last_error_msg());
    }

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $jsonData
        ]
    ];

    $url = 'http://localhost:3000/barang';
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    if ($result === false) {
        $error = error_get_last();
        die('Gagal menambahkan barang: ' . $error['message']);
    } else {
        // Hapus var_dump() dan die() untuk debugging
        // var_dump($result);
        // die();

        // Hapus htmlspecialchars() karena tidak perlu di sini
        // $result = htmlspecialchars($result);

        // Tidak perlu lagi decode JSON karena respons berupa string
        // $response = json_decode($result, true);
        
        // Langsung cek kondisi dari hasil respons
        if (strpos($result, 'Barang dan transaksi berhasil ditambahkan') !== false) {
            // Jika pesan sukses ditemukan, redirect ke halaman index.php
            header('Location: ../index.php');
            exit();
        } else {
            // Jika ada kesalahan atau format respons tidak sesuai, tampilkan pesan error
            die('Gagal menambahkan barang: Respons tidak valid');
        }
    }
}
?>
