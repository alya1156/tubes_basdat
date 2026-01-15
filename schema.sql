-- Hotel Reservation System Database Schema
-- Created: 2026-01-14

-- Create Database
CREATE DATABASE IF NOT EXISTS `hotel_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hotel_db`;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS `kamar_foto`;
DROP TABLE IF EXISTS `kamar_fasilitas`;
DROP TABLE IF EXISTS `pembayaran`;
DROP TABLE IF EXISTS `reservasi`;
DROP TABLE IF EXISTS `galeri_hotel`;
DROP TABLE IF EXISTS `kamar`;
DROP TABLE IF EXISTS `tipe_kamar`;
DROP TABLE IF EXISTS `fasilitas`;
DROP TABLE IF EXISTS `tamu`;
DROP TABLE IF EXISTS `admin`;

-- 1. ADMIN TABLE
CREATE TABLE `admin` (
  `id_admin` INT PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TAMU TABLE
CREATE TABLE `tamu` (
  `id_tamu` INT PRIMARY KEY AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `no_identitas` VARCHAR(20) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `no_telp` VARCHAR(15) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TIPE_KAMAR TABLE
CREATE TABLE `tipe_kamar` (
  `id_tipe` INT PRIMARY KEY AUTO_INCREMENT,
  `nama_tipe` VARCHAR(50) NOT NULL,
  `kapasitas` INT NOT NULL,
  `harga_malam` DECIMAL(10,2) NOT NULL,
  `deskripsi` TEXT,
  `foto_cover` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. KAMAR TABLE
CREATE TABLE `kamar` (
  `id_kamar` INT PRIMARY KEY AUTO_INCREMENT,
  `no_kamar` VARCHAR(10) UNIQUE NOT NULL,
  `id_tipe` INT NOT NULL,
  `status` ENUM('tersedia', 'terpesan', 'ditempati', 'maintenance') DEFAULT 'tersedia',
  `catatan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_tipe`) REFERENCES `tipe_kamar`(`id_tipe`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. FASILITAS TABLE
CREATE TABLE `fasilitas` (
  `id_fasilitas` INT PRIMARY KEY AUTO_INCREMENT,
  `nama_fasilitas` VARCHAR(50) NOT NULL,
  `icon` VARCHAR(50),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. KAMAR_FASILITAS TABLE (Junction Table)
CREATE TABLE `kamar_fasilitas` (
  `id_kamar` INT NOT NULL,
  `id_fasilitas` INT NOT NULL,
  PRIMARY KEY (`id_kamar`, `id_fasilitas`),
  FOREIGN KEY (`id_kamar`) REFERENCES `kamar`(`id_kamar`) ON DELETE CASCADE,
  FOREIGN KEY (`id_fasilitas`) REFERENCES `fasilitas`(`id_fasilitas`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. RESERVASI TABLE
CREATE TABLE `reservasi` (
  `id_reservasi` INT PRIMARY KEY AUTO_INCREMENT,
  `kode_booking` VARCHAR(20) UNIQUE NOT NULL,
  `id_tamu` INT NOT NULL,
  `id_kamar` INT NOT NULL,
  `tgl_masuk` DATE NOT NULL,
  `tgl_keluar` DATE NOT NULL,
  `jumlah_tamu` INT NOT NULL,
  `status` ENUM('pending', 'konfirmasi', 'checked_in', 'checked_out', 'batal') DEFAULT 'pending',
  `total_harga` DECIMAL(10,2) NOT NULL,
  `tgl_pesan` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_tamu`) REFERENCES `tamu`(`id_tamu`) ON DELETE CASCADE,
  FOREIGN KEY (`id_kamar`) REFERENCES `kamar`(`id_kamar`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. PEMBAYARAN TABLE
CREATE TABLE `pembayaran` (
  `id_pembayaran` INT PRIMARY KEY AUTO_INCREMENT,
  `id_reservasi` INT NOT NULL,
  `jumlah` DECIMAL(10,2) NOT NULL,
  `metode` ENUM('transfer_bank', 'tunai', 'kartu', 'ewallet') DEFAULT 'transfer_bank',
  `status` ENUM('pending', 'verifikasi', 'lunas', 'batal') DEFAULT 'pending',
  `tgl_bayar` DATETIME,
  `tgl_verifikasi` DATETIME,
  `no_bukti` VARCHAR(100),
  `catatan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_reservasi`) REFERENCES `reservasi`(`id_reservasi`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. GALERI_HOTEL TABLE
CREATE TABLE `galeri_hotel` (
  `id_galeri` INT PRIMARY KEY AUTO_INCREMENT,
  `foto_path` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT,
  `urutan` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. KAMAR_FOTO TABLE
CREATE TABLE `kamar_foto` (
  `id_foto` INT PRIMARY KEY AUTO_INCREMENT,
  `id_kamar` INT NOT NULL,
  `foto_path` VARCHAR(255) NOT NULL,
  `urutan` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_kamar`) REFERENCES `kamar`(`id_kamar`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (username: admin, password: 1234 -> hashed)
INSERT INTO `admin` (`username`, `password_hash`) VALUES 
('admin', '$2y$10$qzLG0X5gVm5EfYU7W9ZGP.WVnVEGfNGJB2v3qCT5yv5QWJiWPUg1u');

-- Insert sample facilities
INSERT INTO `fasilitas` (`nama_fasilitas`, `icon`) VALUES
('WiFi', 'wifi'),
('AC', 'wind'),
('TV', 'display'),
('Kamar Mandi Pribadi', 'droplet'),
('Mini Bar', 'cup-straw'),
('Balkon', 'door-open'),
('Brankas', 'safe2'),
('Telepon', 'telephone'),
('Pemanas Air', 'fire'),
('Seprai Premium', 'layers');

-- Insert room types (Beach-themed)
INSERT INTO `tipe_kamar` (`nama_tipe`, `kapasitas`, `harga_malam`, `deskripsi`) VALUES
('Standard Beach Room', 2, 150000, 'Kamar nyaman dengan pemandangan taman'),
('Deluxe Ocean View', 2, 250000, 'Kamar mewah dengan pemandangan laut'),
('Premium Suite Sunset', 4, 450000, 'Suite premium dengan balkon private & sunset view');

-- Insert rooms (10 kamar total: 4 Standard, 3 Deluxe, 3 Premium)
INSERT INTO `kamar` (`no_kamar`, `id_tipe`, `status`) VALUES
('101', 1, 'tersedia'),
('102', 1, 'tersedia'),
('103', 1, 'terpesan'),
('104', 1, 'ditempati'),
('201', 2, 'tersedia'),
('202', 2, 'tersedia'),
('203', 2, 'terpesan'),
('301', 3, 'tersedia'),
('302', 3, 'ditempati'),
('303', 3, 'tersedia');

-- Insert guest data (15 tamu)
INSERT INTO `tamu` (`nama`, `no_identitas`, `email`, `no_telp`) VALUES
('Budi Santoso', '3527011001000001', 'budi@email.com', '08123456789'),
('Siti Nurhaliza', '3527021502950001', 'siti@email.com', '08234567890'),
('Ahmad Wijaya', '3527031015880001', 'ahmad@email.com', '08345678901'),
('Diana Kusuma', '3527041010920001', 'diana@email.com', '08456789012'),
('Eka Putri', '3527051205980001', 'eka@email.com', '08567890123'),
('Fajar Rahman', '3527061018850001', 'fajar@email.com', '08678901234'),
('Gita Suryanto', '3527071025960001', 'gita@email.com', '08789012345'),
('Hendra Maulana', '3527081110870001', 'hendra@email.com', '08890123456'),
('Indah Lestari', '3527091203990001', 'indah@email.com', '08901234567'),
('Joko Anggoro', '3527101215860001', 'joko@email.com', '08012345678'),
('Karina Wijaya', '3527111008920001', 'karina@email.com', '08123456788'),
('Luca Martino', '3527121120950001', 'luca@email.com', '08234567891'),
('Maya Pertiwi', '3527131028870001', 'maya@email.com', '08345678902'),
('Nico Subowo', '3527141115900001', 'nico@email.com', '08456789013'),
('Olivia Chen', '3527151207940001', 'olivia@email.com', '08567890124');

-- Insert reservations (8 reservasi)
INSERT INTO `reservasi` (`kode_booking`, `id_tamu`, `id_kamar`, `tgl_masuk`, `tgl_keluar`, `jumlah_tamu`, `status`, `total_harga`) VALUES
('HTL-2026-001-ABC123', 1, 1, '2026-01-20', '2026-01-22', 2, 'pending', 300000),
('HTL-2026-002-DEF456', 2, 2, '2026-01-22', '2026-01-25', 2, 'konfirmasi', 450000),
('HTL-2026-003-GHI789', 3, 3, '2026-01-18', '2026-01-19', 2, 'checked_in', 150000),
('HTL-2026-004-JKL012', 4, 4, '2026-01-15', '2026-01-17', 2, 'checked_in', 300000),
('HTL-2026-005-MNO345', 5, 5, '2026-01-25', '2026-01-27', 2, 'pending', 500000),
('HTL-2026-006-PQR678', 6, 6, '2026-01-20', '2026-01-23', 2, 'konfirmasi', 750000),
('HTL-2026-007-STU901', 7, 8, '2026-01-28', '2026-02-01', 4, 'pending', 1800000),
('HTL-2026-008-VWX234', 8, 9, '2026-01-16', '2026-01-18', 4, 'checked_in', 900000);

-- Insert payments
INSERT INTO `pembayaran` (`id_reservasi`, `jumlah`, `metode`, `status`, `tgl_bayar`, `no_bukti`) VALUES
(1, 300000, 'transfer_bank', 'pending', '2026-01-15 10:30:00', 'BCA-001'),
(2, 450000, 'transfer_bank', 'verifikasi', '2026-01-16 14:20:00', 'BCA-002'),
(3, 150000, 'transfer_bank', 'lunas', '2026-01-15 09:15:00', 'BCA-003'),
(4, 300000, 'transfer_bank', 'lunas', '2026-01-14 11:45:00', 'BCA-004'),
(5, 500000, 'transfer_bank', 'pending', '2026-01-15 16:00:00', 'BCA-005'),
(6, 750000, 'transfer_bank', 'verifikasi', '2026-01-17 13:30:00', 'BCA-006'),
(7, 1800000, 'transfer_bank', 'pending', NULL, NULL),
(8, 900000, 'transfer_bank', 'lunas', '2026-01-15 08:00:00', 'BCA-008');
