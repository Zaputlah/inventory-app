// Import modul yang diperlukan
const express = require('express');
const mysql = require('mysql');
const dotenv = require('dotenv');
const cors = require('cors');

// Panggil konfigurasi dari file .env
dotenv.config();

// Inisialisasi aplikasi Express
const app = express();
const port = process.env.PORT || 3000;

// Middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(cors()); // Mengaktifkan CORS untuk semua permintaan

// Koneksi ke database MySQL
const db = mysql.createConnection({
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME
});

db.connect((err) => {
  if (err) {
    console.error('Error connecting to database:', err);
    return;
  }
  console.log('Connected to database');
});

// Definisikan rute-rute aplikasi
const barangRoutes = require('./src/routers/barangRoutes');
app.use('/barang', barangRoutes); // Menggunakan router untuk /barang

// Menjalankan server
app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});
