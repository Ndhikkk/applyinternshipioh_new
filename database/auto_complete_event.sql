-- ===========================================================
-- AUTO COMPLETE EVENT (MySQL Event Scheduler)
-- Otomatis ubah Diterima -> Complete jika periode_selesai <= CURDATE()
-- Alternatif DB-level tanpa cron PHP, jalan setiap hari jam 02:00
-- ===========================================================

-- Pastikan event scheduler aktif (jalankan sekali sebagai SUPER user):
-- SET GLOBAL event_scheduler = ON;
-- Atau tambahkan ke my.cnf: event_scheduler=ON

-- Hapus event lama jika ada
DROP EVENT IF EXISTS auto_complete_internship;

-- Buat event harian
CREATE EVENT auto_complete_internship
ON SCHEDULE EVERY 1 DAY STARTS CURRENT_DATE + INTERVAL 1 DAY + INTERVAL 2 HOUR
DO
  UPDATE pendaftaran_magang
  SET status = 'Complete',
      status_changed_at = NOW(),
      updated_at = NOW()
  WHERE status = 'Diterima'
    AND periode_selesai IS NOT NULL
    AND periode_selesai != ''
    AND periode_selesai != '0000-00-00'
    AND periode_selesai <= CURDATE();

-- Index untuk performa (jalankan sekali):
-- ALTER TABLE pendaftaran_magang ADD INDEX idx_status_periode (status, periode_selesai);

-- Cek event:
-- SHOW EVENTS;
-- SELECT * FROM information_schema.EVENTS WHERE EVENT_NAME = 'auto_complete_internship';

-- Test manual tanpa tunggu scheduler:
-- UPDATE pendaftaran_magang SET status='Complete', status_changed_at=NOW() WHERE status='Diterima' AND periode_selesai <= CURDATE();
