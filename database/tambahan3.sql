-- Tambahkan kota/kabupaten tujuan magang.
-- Jalankan sekali di phpMyAdmin pada database aplikasi.
ALTER TABLE `pendaftaran_magang`
  ADD COLUMN `kota_pilihan` VARCHAR(100) NULL AFTER `regional_interview`;

ALTER TABLE `pendaftaran_magang`
  MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'Menunggu';