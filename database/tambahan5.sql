-- Tambahan 5: Add nim column to pendaftaran_magang (after program_studi)
ALTER TABLE `pendaftaran_magang`
  ADD COLUMN `nim` VARCHAR(30) NULL DEFAULT NULL AFTER `program_studi`;
