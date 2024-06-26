const db = require('../config/dbConfig');
const mysql = require('mysql');

const connection = mysql.createConnection({
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME
});

// Mendefinisikan model barang
const Barang = {};

Barang.getAll = (result) => {
  const query = `
    SELECT b.id_barang, b.nama_barang, t.stok, t.jumlah_terjual, t.tanggal_transaksi, j.jenis_barang
    FROM transaksi t
    JOIN barang b ON t.id_barang = b.id_barang
    JOIN jenis_barang j ON t.id_jenis_barang = j.id_jenis_barang;
  `;
  db.query(query, (err, res) => {
    if (err) {
      console.log("Error fetching data: ", err);
      result(err, null);
      return;
    }
    result(null, res);
  });
};

// Mendapatkan data barang berdasarkan ID
Barang.getById = (id, result) => {
  const query = `
    SELECT b.id_barang, b.nama_barang, t.stok, t.jumlah_terjual, t.tanggal_transaksi, j.jenis_barang
    FROM transaksi t
    JOIN barang b ON t.id_barang = b.id_barang
    JOIN jenis_barang j ON t.id_jenis_barang = j.id_jenis_barang
    WHERE b.id_barang = ?;
  `;
  db.query(query, [id], (err, res) => {
    if (err) {
      console.log("Error fetching data: ", err);
      result(err, null);
      return;
    }
    if (res.length) {
      result(null, res[0]);
      return;
    }
    // Tidak ditemukan barang dengan ID tersebut
    result({ kind: "not_found" }, null);
  });
};

// Menambahkan data barang baru
Barang.create = (newBarang, result) => {
  const query = "INSERT INTO barang SET ?";
  db.query(query, newBarang, (err, res) => {
    if (err) {
      console.log("Error creating new barang: ", err);
      result(err, null);
      return;
    }
    result(null, { id: res.insertId, ...newBarang });
  });
};

// Fungsi untuk menyimpan data ke semua tabel yang terlibat
Barang.saveAllData = (barangData, transaksiData, jenisBarangData, callback) => {
  // Mulai transaksi
  db.beginTransaction((err) => {
    if (err) {
      callback(err, null);
      return;
    }

    // Insert ke tabel barang
    db.query('INSERT INTO barang SET ?', barangData, (err, result) => {
      if (err) {
        return db.rollback(() => {
          callback(err, null);
        });
      }

      const barangId = result.insertId;

      // Insert ke tabel transaksi
      transaksiData.id_barang = barangId; // Pastikan ada relasi dengan barang
      db.query('INSERT INTO transaksi SET ?', transaksiData, (err, result) => {
        if (err) {
          return db.rollback(() => {
            callback(err, null);
          });
        }

        const transaksiId = result.insertId;

        // Insert ke tabel jenis_barang
        jenisBarangData.id_transaksi = transaksiId; // Pastikan ada relasi dengan transaksi
        db.query('INSERT INTO jenis_barang SET ?', jenisBarangData, (err, result) => {
          if (err) {
            return db.rollback(() => {
              callback(err, null);
            });
          }

          // Commit transaksi jika semuanya berhasil
          db.commit((err) => {
            if (err) {
              return db.rollback(() => {
                callback(err, null);
              });
            }

            callback(null, 'Data berhasil disimpan ke semua tabel.');
          });
        });
      });
    });
  });
};

// Update data barang berdasarkan ID
Barang.updateById = (id, barang, result) => {
  const query = `
    UPDATE barang b
    JOIN transaksi t ON b.id_barang = t.id_barang
    SET b.nama_barang = ?, t.stok = ?, t.jumlah_terjual = ?, t.tanggal_transaksi = ?, t.id_jenis_barang = ?
    WHERE b.id_barang = ?;
  `;
  db.query(
    query,
    [
      barang.nama_barang,
      barang.stok,
      barang.jumlah_terjual,
      barang.tanggal_transaksi,
      barang.id_jenis_barang,
      id,
    ],
    (err, res) => {
      if (err) {
        console.log("Error updating barang: ", err);
        result(err, null);
        return;
      }
      if (res.affectedRows == 0) {
        // Tidak ditemukan barang dengan ID tersebut
        result({ kind: "not_found" }, null);
        return;
      }
      result(null, { id: id, ...barang });
    }
  );
};

// Menghapus data barang berdasarkan ID
Barang.remove = (id, result) => {
  const query = "DELETE FROM barang WHERE id_barang = ?";
  db.query(query, [id], (err, res) => {
    if (err) {
      console.log("Error deleting barang: ", err);
      result(err, null);
      return;
    }
    if (res.affectedRows == 0) {
      // Tidak ditemukan barang dengan ID tersebut
      result({ kind: "not_found" }, null);
      return;
    }
    result(null, res);
  });
};

module.exports = Barang;
