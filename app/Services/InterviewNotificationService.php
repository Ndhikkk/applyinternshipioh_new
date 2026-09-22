<?php

namespace App\Services;

class InterviewNotificationService
{
    public const SCENARIO_MENUNGGU_PENDING   = 'MENUNGGU_PENDING';
    public const SCENARIO_MENUNGGU_SCHEDULED = 'MENUNGGU_SCHEDULED';
    public const SCENARIO_PROGRESS_PENDING   = 'PROGRESS_PENDING';
    public const SCENARIO_PROGRESS_SCHEDULED = 'PROGRESS_SCHEDULED';
    public const SCENARIO_ACCEPTED           = 'ACCEPTED';
    public const SCENARIO_REJECTED           = 'REJECTED';

    private const DAYS = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
    ];

    private const MONTHS = [
        1  => 'Januari',
        2  => 'Februari',
        3  => 'Maret',
        4  => 'April',
        5  => 'Mei',
        6  => 'Juni',
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Tentukan skenario notifikasi berdasarkan status kandidat & jadwal interview
     */
    public static function getNotificationScenario(array $item): string
    {
        $status = (string) ($item['status'] ?? '');

        if (in_array($status, ['Ditolak', 'Tidak_Lolos_Interview_1', 'Tidak_Lolos_Interview_2', 'Tidak_Lolos_Interview_3'], true)) {
            return self::SCENARIO_REJECTED;
        }

        if (in_array($status, ['Diterima', 'Complete'], true)) {
            return self::SCENARIO_ACCEPTED;
        }

        if ($status === 'Progress' || $status === 'Lolos_Interview_2') {
            if (!empty($item['jadwal_interview_2']) || !empty($item['link_zoom_2'])) {
                return self::SCENARIO_PROGRESS_SCHEDULED;
            }
            return self::SCENARIO_PROGRESS_PENDING;
        }

        // Status Menunggu atau default awal pendaftaran
        if (!empty($item['jadwal_interview_1']) || !empty($item['link_zoom_1']) || $status === 'Lolos_Interview_1') {
            return self::SCENARIO_MENUNGGU_SCHEDULED;
        }

        return self::SCENARIO_MENUNGGU_PENDING;
    }

    /**
     * Tentukan tahap interview aktif (hanya bernilai > 0 jika jadwal aktif terisi)
     */
    public static function getActiveInterviewStep(array $item): int
    {
        $scenario = self::getNotificationScenario($item);

        if ($scenario === self::SCENARIO_MENUNGGU_SCHEDULED) {
            return 1;
        }
        if ($scenario === self::SCENARIO_PROGRESS_SCHEDULED) {
            return 2;
        }

        return 0;
    }

    /**
     * Format tanggal & waktu ke bahasa Indonesia
     */
    public static function formatTanggalIndo(?string $datetime, bool $withTime = true): string
    {
        if (empty($datetime)) {
            return '(jadwal menyusul)';
        }

        $ts = strtotime($datetime);
        if ($ts === false) {
            return $datetime;
        }

        $dayName = self::DAYS[date('l', $ts)] ?? date('l', $ts);
        $dayNum = date('j', $ts);
        $monthNum = (int) date('n', $ts);
        $monthName = self::MONTHS[$monthNum] ?? date('F', $ts);
        $year = date('Y', $ts);

        $result = "{$dayName}, {$dayNum} {$monthName} {$year}";
        if ($withTime) {
            $result .= ' pukul ' . date('H:i', $ts) . ' WIB';
        }

        return $result;
    }

    /**
     * Normalisasi nomor telepon ke format internasional 62...
     */
    public static function normalizeWaNumber(?string $number): ?string
    {
        if (empty($number)) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $number);
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (!str_starts_with($digits, '62')) {
            $digits = '62' . $digits;
        }

        return $digits;
    }

    /**
     * Bangun pesan dan URL WhatsApp untuk pendaftar
     */
    public static function buildWaTemplate(array $item): array
    {
        $nama = trim($item['nama_lengkap'] ?? 'Kandidat');
        $token = trim((string) ($item['token_pendaftaran'] ?? '-'));
        $scenario = self::getNotificationScenario($item);

        switch ($scenario) {
            case self::SCENARIO_MENUNGGU_PENDING:
                $message = "Halo *{$nama}*,\n\n"
                    . "Terima kasih telah mendaftar pada program Industry-Academia Collaboration Program (IOH Semarang).\n\n"
                    . "🎫 Nomor Token Pendaftaran Anda:\n"
                    . "*{$token}*\n\n"
                    . "Saat ini berkas pendaftaran Anda sedang dalam proses verifikasi oleh tim rekrutmen. Simpan nomor token ini untuk mengecek status seleksi Anda secara berkala melalui website.\n\n"
                    . "Salam,\nTim Rekrutmen Magang IOH Semarang";
                break;

            case self::SCENARIO_MENUNGGU_SCHEDULED:
                $jadwal = $item['jadwal_interview_1'] ?? null;
                $zoom = trim((string) ($item['link_zoom_1'] ?? ''));
                $catatan = trim((string) ($item['catatan_interview_1'] ?? ''));

                $hariTanggal = $jadwal ? self::formatTanggalIndo($jadwal, false) : '(jadwal menyusul)';
                $jamText = $jadwal ? date('H:i', strtotime($jadwal)) . ' WIB' : '(menyusul)';
                $zoomText = $zoom !== '' ? $zoom : '(link Zoom menyusul)';

                $message = "Halo *{$nama}*,\n\n"
                    . "Selamat! Anda dijadwalkan mengikuti *Interview Tahap 1* program magang IOH Semarang.\n\n"
                    . "🎫 Nomor Token: *{$token}*\n"
                    . "🗓️ Hari/Tanggal: {$hariTanggal}\n"
                    . "⏰ Waktu: {$jamText}\n"
                    . "💻 Link Zoom: {$zoomText}\n";

                if ($catatan !== '') {
                    $message .= "📝 Catatan Penting: {$catatan}\n";
                }

                $message .= "\nMohon hadir 10 menit sebelum jadwal dan pastikan koneksi internet stabil ya. Sampai jumpa!\n\n"
                    . "Salam,\nTim Rekrutmen Magang IOH Semarang";
                break;

            case self::SCENARIO_PROGRESS_PENDING:
                $message = "Halo *{$nama}*,\n\n"
                    . "Selamat! Anda dinyatakan *LOLOS Interview Tahap 1* pada program magang Industry-Academia Collaboration Program (IOH Semarang).\n\n"
                    . "🎫 Nomor Token: *{$token}*\n\n"
                    . "Saat ini tim rekrutmen sedang mempersiapkan jadwal untuk tahap berikutnya (*Interview Tahap 2*). Mohon menunggu informasi selanjutnya yang akan kami kirimkan melalui WhatsApp atau email.\n\n"
                    . "Salam,\nTim Rekrutmen Magang IOH Semarang";
                break;

            case self::SCENARIO_PROGRESS_SCHEDULED:
                $jadwal = $item['jadwal_interview_2'] ?? null;
                $zoom = trim((string) ($item['link_zoom_2'] ?? ''));
                $catatan = trim((string) ($item['catatan_interview_2'] ?? ''));

                $hariTanggal = $jadwal ? self::formatTanggalIndo($jadwal, false) : '(jadwal menyusul)';
                $jamText = $jadwal ? date('H:i', strtotime($jadwal)) . ' WIB' : '(menyusul)';
                $zoomText = $zoom !== '' ? $zoom : '(link Zoom menyusul)';

                $message = "Halo *{$nama}*,\n\n"
                    . "Selamat! Anda dijadwalkan mengikuti *Interview Tahap 2* program magang IOH Semarang.\n\n"
                    . "🎫 Nomor Token: *{$token}*\n"
                    . "🗓️ Hari/Tanggal: {$hariTanggal}\n"
                    . "⏰ Waktu: {$jamText}\n"
                    . "💻 Link Zoom: {$zoomText}\n";

                if ($catatan !== '') {
                    $message .= "📝 Catatan Penting: {$catatan}\n";
                }

                $message .= "\nMohon hadir 10 menit sebelum jadwal dan pastikan koneksi internet stabil ya. Sampai jumpa!\n\n"
                    . "Salam,\nTim Rekrutmen Magang IOH Semarang";
                break;

            case self::SCENARIO_ACCEPTED:
                $message = "Halo *{$nama}*,\n\n"
                    . "Selamat! Anda dinyatakan *LOLOS* dan resmi diterima pada program magang Industry-Academia Collaboration Program (IOH Semarang).\n\n"
                    . "🎫 Nomor Token: *{$token}*\n\n"
                    . "Tim kami akan segera menghubungi Anda untuk informasi persiapan dan langkah selanjutnya. Selamat bergabung!\n\n"
                    . "Salam,\nTim Rekrutmen Magang IOH Semarang";
                break;

            case self::SCENARIO_REJECTED:
            default:
                $message = "Halo *{$nama}*,\n\n"
                    . "Terima kasih atas partisipasi Anda pada seleksi magang Industry-Academia Collaboration Program (IOH Semarang).\n\n"
                    . "🎫 Nomor Token: *{$token}*\n\n"
                    . "Untuk saat ini kami belum dapat melanjutkan proses Anda ke tahap berikutnya. Semoga sukses di kesempatan berikutnya!\n\n"
                    . "Salam,\nTim Rekrutmen Magang IOH Semarang";
                break;
        }

        $number = self::normalizeWaNumber($item['nomor_whatsapp'] ?? '');
        $url = $number ? 'https://wa.me/' . $number . '?text=' . rawurlencode($message) : null;

        return ['message' => $message, 'url' => $url];
    }

    /**
     * Bangun subject & body email notifikasi untuk pendaftar
     */
    public static function buildEmailTemplate(array $item): array
    {
        $nama = esc(trim($item['nama_lengkap'] ?? 'Kandidat'));
        $token = esc(trim((string) ($item['token_pendaftaran'] ?? '-')));
        $scenario = self::getNotificationScenario($item);

        $logoUrl = 'https://cdn-icons-png.flaticon.com/512/3135/3135665.png';
        $nomorKontak = '0853-7849-1566';

        switch ($scenario) {
            case self::SCENARIO_MENUNGGU_PENDING:
                $subject = "Informasi Pendaftaran Magang - Industry-Academia Collaboration Program";
                $headline = "Pendaftaran Anda Sedang Diproses";
                $intro = "Terima kasih telah mendaftar pada program <strong>Industry-Academia Collaboration Program</strong>. Berkas pendaftaran Anda saat ini sedang dalam proses peninjauan oleh tim rekrutmen kami.";
                $boxLabel = "Nomor Token Pendaftaran";
                $boxValue = $token;
                $extra = "<p style='font-size:14px;color:#555;'>Simpan nomor token Anda dengan baik. Anda dapat menggunakannya untuk mengecek progres seleksi secara berkala pada menu <strong>Cek Progres</strong> di website kami.</p>";
                $footerNote = "Pemberitahuan jadwal wawancara akan kami sampaikan lebih lanjut setelah berkas terverifikasi.";
                break;

            case self::SCENARIO_MENUNGGU_SCHEDULED:
                $jadwal = $item['jadwal_interview_1'] ?? null;
                $zoom = trim((string) ($item['link_zoom_1'] ?? ''));
                $catatan = trim((string) ($item['catatan_interview_1'] ?? ''));
                $jadwalText = $jadwal ? self::formatTanggalIndo($jadwal, true) : 'akan diinformasikan kemudian';

                $subject = "Undangan Interview Tahap 1 - Industry-Academia Collaboration Program";
                $headline = "Undangan Interview Tahap 1";
                $intro = "Selamat! Anda dijadwalkan untuk mengikuti <strong>Interview Tahap 1</strong> pada program Industry-Academia Collaboration Program.";
                $boxLabel = "Jadwal Interview Tahap 1";
                $boxValue = esc($jadwalText);

                $extra = "<div style='margin:15px 0 20px 0;text-align:center;'>"
                    . "<span style='font-size:13px;color:#64748b;'>Nomor Token Pendaftaran:</span> "
                    . "<strong style='font-family:monospace;color:#1e3a8a;font-size:16px;'>{$token}</strong>"
                    . "</div>";

                if ($zoom !== '') {
                    $extra .= "<div style='text-align:center;margin:20px 0 15px 0;'>"
                        . "<a href='" . esc($zoom) . "' target='_blank' rel='noopener noreferrer' style='background-color:#1e3a8a;color:#ffffff;padding:12px 28px;text-decoration:none;font-size:15px;font-weight:bold;border-radius:6px;display:inline-block;box-shadow:0 4px 6px rgba(0,0,0,0.1);'>Gabung Link Zoom / Meet</a>"
                        . "</div>"
                        . "<p style='text-align:center;font-size:13px;color:#64748b;margin:0 0 15px 0;'>Tautan alternatif: <a href='" . esc($zoom) . "' target='_blank' rel='noopener noreferrer' style='color:#1e3a8a;word-break:break-all;'>" . esc($zoom) . "</a></p>";
                } else {
                    $extra .= "<p style='font-size:14px;color:#64748b;text-align:center;'>Link Zoom akan diinformasikan lebih lanjut oleh tim kami.</p>";
                }

                if ($catatan !== '') {
                    $extra .= "<div style='margin-top:20px;padding:14px;background-color:#f8fafc;border-left:4px solid #1e3a8a;border-radius:4px;text-align:left;'>"
                        . "<strong style='font-size:13px;color:#334155;'>Catatan Penting:</strong>"
                        . "<p style='margin:5px 0 0 0;font-size:13px;color:#475569;line-height:1.5;'>" . nl2br(esc($catatan)) . "</p>"
                        . "</div>";
                }

                $footerNote = "Mohon hadir 10 menit sebelum jadwal dan pastikan koneksi internet Anda stabil.";
                break;

            case self::SCENARIO_PROGRESS_PENDING:
                $subject = "Selamat! Anda Lolos Interview Tahap 1 - Industry-Academia Collaboration Program";
                $headline = "Lolos Interview Tahap 1 🎉";
                $intro = "Selamat! Berdasarkan hasil evaluasi, Anda dinyatakan <strong>LOLOS Interview Tahap 1</strong> pada program Industry-Academia Collaboration Program.";
                $boxLabel = "Nomor Token Pendaftaran";
                $boxValue = $token;
                $extra = "<div style='padding:16px;background-color:#eff6ff;border-radius:6px;border-left:4px solid #1e3a8a;margin-top:20px;text-align:left;'>"
                    . "<p style='margin:0;font-size:14px;color:#1e3a8a;font-weight:600;'>Tahap Selanjutnya: Interview Tahap 2</p>"
                    . "<p style='margin:6px 0 0 0;font-size:13px;color:#475569;line-height:1.5;'>Saat ini tim rekrutmen kami sedang menyusun jadwal wawancara untuk tahap berikutnya. Mohon menunggu pemberitahuan resmi selanjutnya melalui email atau WhatsApp.</p>"
                    . "</div>";
                $footerNote = "Pantau terus status pendaftaran Anda secara berkala.";
                break;

            case self::SCENARIO_PROGRESS_SCHEDULED:
                $jadwal = $item['jadwal_interview_2'] ?? null;
                $zoom = trim((string) ($item['link_zoom_2'] ?? ''));
                $catatan = trim((string) ($item['catatan_interview_2'] ?? ''));
                $jadwalText = $jadwal ? self::formatTanggalIndo($jadwal, true) : 'akan diinformasikan kemudian';

                $subject = "Undangan Interview Tahap 2 - Industry-Academia Collaboration Program";
                $headline = "Undangan Interview Tahap 2";
                $intro = "Selamat! Anda dijadwalkan untuk mengikuti <strong>Interview Tahap 2</strong> pada program Industry-Academia Collaboration Program.";
                $boxLabel = "Jadwal Interview Tahap 2";
                $boxValue = esc($jadwalText);

                $extra = "<div style='margin:15px 0 20px 0;text-align:center;'>"
                    . "<span style='font-size:13px;color:#64748b;'>Nomor Token Pendaftaran:</span> "
                    . "<strong style='font-family:monospace;color:#1e3a8a;font-size:16px;'>{$token}</strong>"
                    . "</div>";

                if ($zoom !== '') {
                    $extra .= "<div style='text-align:center;margin:20px 0 15px 0;'>"
                        . "<a href='" . esc($zoom) . "' target='_blank' rel='noopener noreferrer' style='background-color:#1e3a8a;color:#ffffff;padding:12px 28px;text-decoration:none;font-size:15px;font-weight:bold;border-radius:6px;display:inline-block;box-shadow:0 4px 6px rgba(0,0,0,0.1);'>Gabung Link Zoom / Meet</a>"
                        . "</div>"
                        . "<p style='text-align:center;font-size:13px;color:#64748b;margin:0 0 15px 0;'>Tautan alternatif: <a href='" . esc($zoom) . "' target='_blank' rel='noopener noreferrer' style='color:#1e3a8a;word-break:break-all;'>" . esc($zoom) . "</a></p>";
                } else {
                    $extra .= "<p style='font-size:14px;color:#64748b;text-align:center;'>Link Zoom akan diinformasikan lebih lanjut oleh tim kami.</p>";
                }

                if ($catatan !== '') {
                    $extra .= "<div style='margin-top:20px;padding:14px;background-color:#f8fafc;border-left:4px solid #1e3a8a;border-radius:4px;text-align:left;'>"
                        . "<strong style='font-size:13px;color:#334155;'>Catatan Penting:</strong>"
                        . "<p style='margin:5px 0 0 0;font-size:13px;color:#475569;line-height:1.5;'>" . nl2br(esc($catatan)) . "</p>"
                        . "</div>";
                }

                $footerNote = "Mohon hadir 10 menit sebelum jadwal dan pastikan koneksi internet Anda stabil.";
                break;

            case self::SCENARIO_ACCEPTED:
                $subject = "Selamat! Anda Diterima - Industry-Academia Collaboration Program";
                $headline = "Selamat, Anda Diterima! 🎉";
                $intro = "Selamat! Anda dinyatakan <strong>LOLOS</strong> dan resmi diterima pada program <strong>Industry-Academia Collaboration Program</strong>.";
                $boxLabel = "Nomor Token Pendaftaran";
                $boxValue = $token;
                $extra = "<p style='font-size:14px;color:#555;'>Tim kami akan segera menghubungi Anda untuk koordinasi orientasi magang dan administrasi penerimaan selanjutnya. Selamat bergabung!</p>";
                $footerNote = "Terima kasih atas partisipasi dan dedikasi Anda selama proses seleksi.";
                break;

            case self::SCENARIO_REJECTED:
            default:
                $subject = "Informasi Status Pendaftaran - Industry-Academia Collaboration Program";
                $headline = "Informasi Status Pendaftaran";
                $intro = "Terima kasih atas partisipasi Anda pada proses seleksi <strong>Industry-Academia Collaboration Program</strong>.";
                $boxLabel = "Nomor Token Pendaftaran";
                $boxValue = $token;
                $extra = "<p style='font-size:14px;color:#666;'>Untuk saat ini kami belum dapat melanjutkan proses Anda ke tahap berikutnya. Semoga sukses untuk kesempatan berikutnya!</p>";
                $footerNote = "Terima kasih telah meluangkan waktu mengikuti proses seleksi kami.";
                break;
        }

        $body = "
        <div style='background-color: #f4f6f9; padding: 30px 15px; font-family: Arial, sans-serif; color: #333;'>
            <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); overflow: hidden;'>
                <tr>
                    <td align='center' style='background-color: #1e3a8a; padding: 30px 20px;'>
                        <img src='{$logoUrl}' alt='Logo Program' style='width: 80px; height: auto; margin-bottom: 10px; display: block;'>
                        <h2 style='color: #ffffff; margin: 0; font-size: 20px; font-weight: 600; letter-spacing: 0.5px;'>Industry-Academia Collaboration Program</h2>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 40px 30px;'>
                        <h3 style='margin-top:0;color:#1e3a8a;'>{$headline}</h3>
                        <p style='font-size: 16px; line-height: 1.6; margin-top: 0;'>Halo <strong>{$nama}</strong>,</p>
                        <p style='font-size: 15px; line-height: 1.6; color: #555;'>{$intro}</p>

                        <div style='background-color: #f0f4f8; border-left: 4px solid #1e3a8a; border-radius: 4px; padding: 20px; margin: 25px 0; text-align: center;'>
                            <span style='font-size: 13px; text-transform: uppercase; color: #666; display: block; margin-bottom: 5px;'>{$boxLabel}</span>
                            <span style='font-size: 18px; font-weight: bold; color: #1e3a8a; font-family: monospace;'>{$boxValue}</span>
                        </div>

                        {$extra}

                        <p style='font-size: 13px; line-height: 1.6; color: #888; margin-top: 25px;'>{$footerNote}</p>

                        <!-- INFORMASI KONTAK / BANTUAN -->
                        <div style='margin-top: 25px; padding: 16px; background-color: #f8fafc; border-radius: 6px; border: 1px dashed #cbd5e1; text-align: center;'>
                            <p style='font-size: 13px; color: #64748b; margin: 0 0 6px 0;'>Butuh bantuan atau informasi lebih lanjut? Hubungi narahubung kami di:</p>
                            <p style='font-size: 15px; font-weight: bold; color: #1e3a8a; margin: 0;'>
                                📞 {$nomorKontak}
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align='center' style='background-color: #f8fafc; padding: 20px; border-top: 1px solid #edf2f7; font-size: 12px; color: #999;'>
                        <p style='margin: 0 0 5px 0;'>Email ini dikirim otomatis oleh sistem rekrutmen Industry-Academia Collaboration Program.</p>
                        <p style='margin: 0;'>&copy; " . date('Y') . " Industry-Academia Collaboration Program. All rights reserved.</p>
                    </td>
                </tr>
            </table>
        </div>
        ";

        return ['subject' => $subject, 'body' => $body];
    }

    /**
     * Konfigurasi standar SMTP Gmail aplikasi
     */
    public static function emailConfig(): \Config\Email
    {
        $config = config('Email') ?? new \Config\Email();
        $config->CRLF = "\r\n";
        $config->newline = "\r\n";
        return $config;
    }

    /**
     * Kirim ulang email konfirmasi token pendaftaran awal ke kandidat
     */
    public static function sendRegistrationTokenEmail(array $candidate): array
    {
        $toEmail = trim((string) ($candidate['email'] ?? ''));
        if ($toEmail === '') {
            return ['sent' => false, 'error' => 'Kandidat tidak memiliki alamat email.'];
        }

        $recipientName = esc(trim($candidate['nama_lengkap'] ?? 'Kandidat'));
        $token = esc(trim((string) ($candidate['token_pendaftaran'] ?? '-')));
        $logoUrl = 'https://cdn-icons-png.flaticon.com/512/3135/3135665.png';
        $nomorKontak = '0853-7849-1566';
        $linkProgres = base_url('progres');

        $message = "
        <div style='background-color: #f4f6f9; padding: 30px 15px; font-family: Arial, sans-serif; color: #333;'>
            <table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); overflow: hidden;'>
                <tr>
                    <td align='center' style='background-color: #1e3a8a; padding: 30px 20px;'>
                        <img src='{$logoUrl}' alt='Logo Program' style='width: 80px; height: auto; margin-bottom: 10px; display: block;'>
                        <h2 style='color: #ffffff; margin: 0; font-size: 20px; font-weight: 600; letter-spacing: 0.5px;'>Industry-Academia Collaboration Program</h2>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 40px 30px;'>
                        <p style='font-size: 16px; line-height: 1.6; margin-top: 0;'>Halo <strong>{$recipientName}</strong>,</p>
                        <p style='font-size: 15px; line-height: 1.6; color: #555;'>Terima kasih telah mendaftar dalam program <strong>Industry-Academia Collaboration Program</strong>. Kami sangat mengapresiasi minat dan antusiasme Anda untuk bertumbuh bersama kami.</p>

                        <div style='background-color: #f0f4f8; border-left: 4px solid #1e3a8a; border-radius: 4px; padding: 20px; margin: 30px 0; text-align: center;'>
                            <span style='font-size: 13px; text-transform: uppercase; color: #666; display: block; margin-bottom: 5px;'>Nomor Token Pendaftaran Anda</span>
                            <span style='font-size: 26px; font-weight: bold; color: #1e3a8a; letter-spacing: 3px; font-family: monospace;'>{$token}</span>
                        </div>

                        <p style='font-size: 14px; line-height: 1.6; color: #666;'>Simpan dan gunakan nomor token di atas untuk melacak status seleksi berkas Anda melalui menu <strong>Cek Progres</strong> pada website kami.</p>

                        <div style='text-align: center; margin-top: 35px;'>
                            <a href='{$linkProgres}' target='_blank' rel='noopener noreferrer' style='background-color: #1e3a8a; color: #ffffff; padding: 12px 30px; text-decoration: none; font-size: 15px; font-weight: bold; border-radius: 5px; display: inline-block; box-shadow: 0 2px 5px rgba(0,0,0,0.1);'>Cek Status Pendaftaran</a>
                        </div>

                        <div style='margin-top: 35px; padding: 16px; background-color: #f8fafc; border-radius: 6px; border: 1px dashed #cbd5e1; text-align: center;'>
                            <p style='font-size: 13px; color: #64748b; margin: 0 0 6px 0;'>Ada pertanyaan atau kendala seputar pendaftaran? Hubungi narahubung kami di:</p>
                            <p style='font-size: 15px; font-weight: bold; color: #1e3a8a; margin: 0;'>
                                📞 {$nomorKontak}
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align='center' style='background-color: #f8fafc; padding: 20px; border-top: 1px solid #edf2f7; font-size: 12px; color: #999;'>
                        <p style='margin: 0 0 5px 0;'>Email ini dikirim resmi oleh sistem rekrutmen Industry-Academia Collaboration Program.</p>
                        <p style='margin: 0;'>&copy; " . date('Y') . " Industry-Academia Collaboration Program. All rights reserved.</p>
                    </td>
                </tr>
            </table>
        </div>
        ";

        try {
            $email = \Config\Services::email(self::emailConfig());
            $email->setFrom('farezaairo@gmail.com', 'Industry-Academia Collaboration Program');
            $email->setTo($toEmail);
            $email->setSubject('🔑 Token Pendaftaran - Industry-Academia Collaboration Program');
            $email->setMessage($message);

            $sent = $email->send();
            if (!$sent) {
                $debug = $email->printDebugger(['headers']);
                log_message('error', 'Gagal kirim ulang email token ke {email}: {debug}', ['email' => $toEmail, 'debug' => $debug]);
                return ['sent' => false, 'error' => 'Gagal mengirim email token. Periksa log email.'];
            }

            return ['sent' => true, 'error' => ''];
        } catch (\Throwable $e) {
            log_message('error', 'Exception sendRegistrationTokenEmail: ' . $e->getMessage());
            return ['sent' => false, 'error' => $e->getMessage()];
        }
    }
}
