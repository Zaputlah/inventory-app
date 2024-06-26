const Barang = require('../models/barangModel');
const db = require('../config/dbConfig');

exports.getAllBarang = (req, res) => {
    console.log('GET /barang');
    const sql = `
        SELECT b.id_barang, b.nama_barang, t.stok, t.jumlah_terjual, t.tanggal_transaksi, j.jenis_barang
        FROM barang b
        JOIN transaksi t ON b.id_barang = t.id_barang
        JOIN jenis_barang j ON t.id_jenis_barang = j.id_jenis_barang;
    `;
    db.query(sql, (err, results) => {
        if (err) {
            console.error('Error fetching barang:', err.message);
            res.status(500).json({ error: err.message });
            return;
        }
        res.json(results);
    });
};

exports.getBarangById = (req, res) => {
    console.log(`GET /barang/${req.params.id}`);
    Barang.getById(req.params.id, (err, data) => {
        if (err) {
            if (err.kind === "not_found") {
                res.status(404).send({
                    message: `Barang not found with id ${req.params.id}.`
                });
            } else {
                console.error(`Error retrieving Barang with id ${req.params.id}:`, err.message);
                res.status(500).send({
                    message: `Error retrieving Barang with id ${req.params.id}.`
                });
            }
        } else {
            res.send(data);
        }
    });
};

exports.createBarang = (req, res) => {
    console.log('POST /barang', req.body);
    const { nama_barang, stok, jumlah_terjual, tanggal_transaksi, id_jenis_barang } = req.body;

    // Validasi data yang diterima
    if (!nama_barang || !stok || !jumlah_terjual || !tanggal_transaksi || !id_jenis_barang) {
        console.error('400 Bad Request: Semua kolom harus diisi.');
        return res.status(400).json({ error: 'Semua kolom harus diisi.' });
    }

    const jenisBarangId = parseInt(id_jenis_barang, 10);
    if (isNaN(jenisBarangId)) {
        console.error('400 Bad Request: id_jenis_barang harus berupa integer yang valid.');
        return res.status(400).json({ error: 'id_jenis_barang harus berupa integer yang valid.' });
    }

    const barangData = {
        nama_barang: nama_barang
    };

    db.beginTransaction((err) => {
        if (err) {
            console.error('Error starting transaction:', err.message);
            res.status(500).json({ error: err.message });
            return;
        }

        db.query('INSERT INTO barang SET ?', barangData, (err, result) => {
            if (err) {
                console.error('Error inserting barang:', err.message);
                return db.rollback(() => {
                    res.status(500).json({ error: err.message });
                });
            }

            const barangId = result.insertId;

            const transaksiData = {
                id_barang: barangId,
                stok: stok,
                jumlah_terjual: jumlah_terjual,
                tanggal_transaksi: tanggal_transaksi,
                id_jenis_barang: jenisBarangId
            };

            db.query('INSERT INTO transaksi SET ?', transaksiData, (err, result) => {
                if (err) {
                    console.error('Error inserting transaksi:', err.message);
                    return db.rollback(() => {
                        res.status(500).json({ error: err.message });
                    });
                }

                db.commit((err) => {
                    if (err) {
                        console.error('Error committing transaction:', err.message);
                        return db.rollback(() => {
                            res.status(500).json({ error: err.message });
                        });
                    }

                    res.status(201).send('Barang dan transaksi berhasil ditambahkan');
                });
            });
        });
    });
};

exports.updateBarang = (req, res) => {
    console.log(`PUT /barang/${req.params.id}`, req.body); // Fix the backticks here
    const id = req.params.id;
    const { nama_barang } = req.body;
    const sql = 'UPDATE barang SET nama_barang = ? WHERE id_barang = ?';
    db.query(sql, [nama_barang, id], (err, result) => {
        if (err) {
            console.error('Error updating barang:', err.message);
            res.status(500).json({ error: err.message });
            return;
        }
        if (result.affectedRows === 0) {
            res.status(404).json({ message: 'Barang not found' });
            return;
        }
        res.send('Barang updated');
    });
};


exports.deleteBarang = (req, res) => {
    console.log(`DELETE /barang/${req.params.id}`);

    const id = req.params.id;

    // Hapus transaksi terlebih dahulu
    const deleteTransaksiSql = 'DELETE FROM transaksi WHERE id_barang = ?';
    db.query(deleteTransaksiSql, id, (err, result) => {
        if (err) {
            console.error('Error deleting transaksi:', err.message);
            res.status(500).json({ error: err.message });
            return;
        }

        // Jika tidak ada transaksi atau transaksi berhasil dihapus, lanjutkan menghapus barang
        const deleteBarangSql = 'DELETE FROM barang WHERE id_barang = ?';
        db.query(deleteBarangSql, id, (err, result) => {
            if (err) {
                console.error('Error deleting barang:', err.message);
                res.status(500).json({ error: err.message });
                return;
            }

            if (result.affectedRows === 0) {
                res.status(404).json({ message: 'Barang not found' });
                return;
            }

            res.send('Barang deleted');
        });
    });
};

