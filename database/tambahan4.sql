-- File: database/tambahan4.sql
-- Menambahkan pilihan 'Complete' ke dalam ENUM kolom status pada tabel pendaftaran_magang
-- Jalankan query ini di phpMyAdmin / MySQL database aplikasi Anda.

ALTER TABLE `pendaftaran_magang`
MODIFY COLUMN `status` ENUM(
    'Menunggu',
    'Progress',
    'Lolos_Interview_1',
    'Tidak_Lolos_Interview_1',
    'Lolos_Interview_2',
    'Tidak_Lolos_Interview_2',
    'Lolos_Interview_3',
    'Tidak_Lolos_Interview_3',
    'Lolos_Final',
    'Diterima',
    'Complete',
    'Ditolak'
) NOT NULL DEFAULT 'Menunggu';
