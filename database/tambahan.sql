-- =====================================================================
--  MIGRASI DATABASE - pendaftaran_magang 
--
--  ⚠️ WAJIB BACKUP DATABASE DULU SEBELUM MENJALANKAN INI.
-- =====================================================================


-- =====================================================================
-- BAGIAN A — Ini isinya SAMA PERSIS dengan tambahan.sql yang sudah Anda
-- jalankan duluan. KALAU tambahan.sql SUDAH PERNAH DIJALANKAN, LEWATI
-- BAGIAN A INI, langsung loncat ke BAGIAN B di bawah.
-- =====================================================================

-- STEP 1: Tambahkan semua kolom baru di awal (token dibuat NULL dulu agar data lama tidak error)
ALTER TABLE `pendaftaran_magang`
ADD COLUMN `token_pendaftaran` VARCHAR(20) NULL AFTER `id`,
ADD COLUMN `divisi_pilihan` VARCHAR(100) NOT NULL AFTER `program_studi`,
ADD COLUMN `import_source` VARCHAR(50) NULL DEFAULT NULL AFTER `status`,
ADD COLUMN `referral_token` VARCHAR(255) NULL DEFAULT NULL AFTER `import_source`,
ADD COLUMN `batch_id` INT NULL DEFAULT NULL AFTER `referral_token`;

-- STEP 2: Ubah tipe data ENUM pada kolom status yang sudah ada
ALTER TABLE `pendaftaran_magang`
MODIFY COLUMN `status` ENUM(
    'Menunggu',
    'Lolos_Interview_1', 'Tidak_Lolos_Interview_1',
    'Lolos_Interview_2', 'Tidak_Lolos_Interview_2',
    'Lolos_Interview_3', 'Tidak_Lolos_Interview_3',
    'Lolos_Final',
    'Diterima', 'Ditolak'
) NOT NULL DEFAULT 'Menunggu';

-- STEP 3: Tambahkan kolom untuk keperluan fitur Interview & Catatan secara berurutan
ALTER TABLE `pendaftaran_magang`
ADD COLUMN `jadwal_interview_1` DATETIME NULL DEFAULT NULL AFTER `catatan`,
ADD COLUMN `jadwal_interview_2` DATETIME NULL DEFAULT NULL AFTER `jadwal_interview_1`,
ADD COLUMN `jadwal_interview_3` DATETIME NULL DEFAULT NULL AFTER `jadwal_interview_2`,
ADD COLUMN `link_zoom_1` VARCHAR(255) NULL DEFAULT NULL AFTER `jadwal_interview_3`,
ADD COLUMN `link_zoom_2` VARCHAR(255) NULL DEFAULT NULL AFTER `link_zoom_1`,
ADD COLUMN `link_zoom_3` VARCHAR(255) NULL DEFAULT NULL AFTER `link_zoom_2`,
ADD COLUMN `catatan_interview_1` TEXT NULL DEFAULT NULL AFTER `link_zoom_3`,
ADD COLUMN `catatan_interview_2` TEXT NULL DEFAULT NULL AFTER `catatan_interview_1`,
ADD COLUMN `catatan_interview_3` TEXT NULL DEFAULT NULL AFTER `catatan_interview_2`,
ADD COLUMN `catatan_admin` TEXT NULL DEFAULT NULL AFTER `catatan_interview_3`,
ADD COLUMN `email_terkirim` TINYINT(1) NOT NULL DEFAULT 0 AFTER `catatan_admin`;

-- STEP 4: Isi data lama yang tokennya masih kosong (Aman untuk ID ribuan/puluhan ribu)
UPDATE `pendaftaran_magang`
SET `token_pendaftaran` = CONCAT('260714', LPAD(id, 6, '0'))
WHERE `token_pendaftaran` IS NULL OR `token_pendaftaran` = '';

-- STEP 5: Kunci kolom token menjadi NOT NULL dan berikan indeks UNIQUE
ALTER TABLE `pendaftaran_magang`
MODIFY COLUMN `token_pendaftaran` VARCHAR(20) NOT NULL,
ADD UNIQUE INDEX `ux_token_pendaftaran` (`token_pendaftaran`);


-- =====================================================================
-- BAGIAN B 
-- Dibutuhkan untuk fitur: hapus otomatis (arsip 7 hari lalu hapus permanen).
-- =====================================================================

-- STEP 6: Kolom penanda "kapan status terakhir kali berubah" — dipakai
-- sebagai patokan tenggat waktu retensi (Ditolak 7 hari, Lolos/Diterima 1 tahun).
ALTER TABLE `pendaftaran_magang`
ADD COLUMN `status_changed_at` DATETIME NULL DEFAULT NULL AFTER `email_terkirim`;

-- STEP 7: Isi status_changed_at untuk data yang sudah ada, pakai updated_at
-- sebagai perkiraan awal (supaya data lama tidak langsung dianggap kadaluarsa).
UPDATE `pendaftaran_magang` SET `status_changed_at` = `updated_at` WHERE `status_changed_at` IS NULL;

-- STEP 8: Kolom untuk fitur Arsip (data masuk arsip dulu 7 hari sebelum
-- dihapus permanen, dan bisa dipulihkan admin selama masa itu).
ALTER TABLE `pendaftaran_magang`
ADD COLUMN `is_archived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status_changed_at`,
ADD COLUMN `archived_at` DATETIME NULL DEFAULT NULL AFTER `is_archived`,
ADD COLUMN `archived_reason` VARCHAR(100) NULL DEFAULT NULL AFTER `archived_at`;

-- STEP 9: Index biar query listing (yang selalu filter is_archived) tetap cepat
ALTER TABLE `pendaftaran_magang`
ADD INDEX `idx_is_archived` (`is_archived`);


-- =====================================================================
-- Struktur akhir kolom pendaftaran_magang setelah migrasi :
--
-- id, token_pendaftaran, nama_lengkap, email, nomor_whatsapp, asal_kampus,
-- program_studi, divisi_pilihan, semester, jenis_magang,
-- periode_mulai, periode_selesai, cv, surat_pengantar, ktm,
-- status, import_source, referral_token, batch_id, catatan,
-- jadwal_interview_1/2/3, link_zoom_1/2/3, catatan_interview_1/2/3,
-- catatan_admin, email_terkirim, status_changed_at,
-- is_archived, archived_at, archived_reason,
-- created_at, updated_at
-- =====================================================================