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
  `alamat` TEXT,
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
('admin', '$2y$10$YourHashedPasswordHere'); -- This will be set properly in config.php

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
