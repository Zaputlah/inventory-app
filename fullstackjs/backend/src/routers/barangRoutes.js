// src/routes/barangRoutes.js
const express = require('express');
const router = express.Router();
const barangController = require('../controllers/barangController');


// Routes for barang
router.get('/', barangController.getAllBarang);
router.get('/:id', barangController.getBarangById);
router.post('/', barangController.createBarang);
router.put('/:id', barangController.updateBarang);
router.delete('/:id', barangController.deleteBarang);

module.exports = router;
