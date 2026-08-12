-- Tambahkan kolom regional interview peserta magang.
-- Jalankan sekali saja melalui phpMyAdmin pada database yang digunakan aplikasi.
ALTER TABLE `pendaftaran_magang`
  ADD COLUMN `regional_interview` VARCHAR(20) NULL AFTER `program_studi`;
