-- Tambahkan kota/kabupaten tujuan magang.
-- Jalankan sekali di phpMyAdmin pada database aplikasi.
ALTER TABLE `pendaftaran_magang`
  ADD COLUMN `kota_pilihan` VARCHAR(100) NULL AFTER `regional_interview`;
