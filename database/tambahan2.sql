-- Tambahkan kolom kota pilihan peserta magang.
-- Jalankan sekali saja melalui phpMyAdmin pada database yang digunakan aplikasi.
ALTER TABLE `pendaftaran_magang`
  ADD COLUMN `kota_pilihan` VARCHAR(20) NULL AFTER `program_studi`;
