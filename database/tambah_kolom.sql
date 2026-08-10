ALTER TABLE pendaftaran_magang
  ADD COLUMN email VARCHAR(255) NULL AFTER nama_lengkap,
  ADD COLUMN jenis_magang ENUM('Wajib', 'Mandiri') NOT NULL DEFAULT 'Mandiri' AFTER semester,
  ADD COLUMN periode_mulai DATE NULL AFTER jenis_magang,
  ADD COLUMN periode_selesai DATE NULL AFTER periode_mulai,
  ADD COLUMN proposal_magang VARCHAR(255) NULL AFTER surat_pengantar;

ALTER TABLE pendaftaran_magang
  ADD UNIQUE KEY uq_pendaftaran_magang_email (email);

-- Jalankan juga di database production untuk mencegah dua request bersamaan
-- menyimpan pelamar yang sama dengan nomor WhatsApp yang sama.
ALTER TABLE pendaftaran_magang
  ADD UNIQUE KEY uq_pendaftaran_magang_nomor_whatsapp (nomor_whatsapp);
