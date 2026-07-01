-- Create database and user
CREATE DATABASE IF NOT EXISTS indosat_magang;
USE indosat_magang;

-- Create tables
CREATE TABLE IF NOT EXISTS `pendaftaran_magang` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_lengkap` VARCHAR(255) NOT NULL,
  `nomor_whatsapp` VARCHAR(20) NOT NULL,
  `asal_kampus` VARCHAR(255) NOT NULL,
  `program_studi` VARCHAR(255) NOT NULL,
  `semester` INT NOT NULL,
  `cv` VARCHAR(255) NOT NULL,
  `surat_pengantar` VARCHAR(255) NOT NULL,
  `ktm` VARCHAR(255) NOT NULL,
  `status` ENUM('Menunggu','Diterima','Ditolak') NOT NULL DEFAULT 'Menunggu',
  `catatan` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO pendaftaran_magang 
(nama_lengkap, nomor_whatsapp, asal_kampus, program_studi, semester, cv, surat_pengantar, ktm, status, catatan)
VALUES 
('John Doe', '081234567890', 'Universitas Indonesia', 'Teknik Informatika', 5, 'uploads/dummy_cv.pdf', 'uploads/dummy_surat.pdf', 'uploads/dummy_ktm.jpg', 'Menunggu', NULL),
('Jane Smith', '081234567891', 'Institut Teknologi Bandung', 'Sistem Informasi', 6, 'uploads/dummy_cv.pdf', 'uploads/dummy_surat.pdf', 'uploads/dummy_ktm.jpg', 'Menunggu', NULL);

-- Insert admin user (password: admin123)
INSERT INTO admin_users (username, password) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Create application user and grant permissions
CREATE USER IF NOT EXISTS 'magang_user'@'localhost' IDENTIFIED BY 'Ioh@2025';
GRANT ALL PRIVILEGES ON indosat_magang.* TO 'magang_user'@'localhost';
FLUSH PRIVILEGES;