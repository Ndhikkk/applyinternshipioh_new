<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PendaftaranModel;

/**
 * Filter global untuk otomatis ubah Diterima -> Complete
 * jika periode_selesai sudah lewat.
 *
 * Dijalankan sebelum setiap request (atau filtered paths di Filters.php).
 * Bulk UPDATE 1 query, sangat ringan, namun menjamin konsistensi
 * tanpa menunggu cron atau kunjungan admin dashboard.
 */
class AutoComplete implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Skip CLI (spark commands sudah handle sendiri)
        if (PHP_SAPI === 'cli') {
            return;
        }

        try {
            $model = new PendaftaranModel();
            $model->autoCompleteExpired();
        } catch (\Throwable $e) {
            log_message('error', 'AutoComplete Filter error: ' . $e->getMessage());
        }
        // jangan return response, biarkan lanjut
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
