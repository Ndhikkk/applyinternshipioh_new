<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($docTitle ?? 'Unduh Dokumen') ?> - <?= esc($candidate['nama_lengkap'] ?? '') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo_indosat.png') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2f6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #1e293b;
        }
        .download-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.8);
            max-width: 540px;
            width: 100%;
            padding: 2.25rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .download-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #E6007E, #ED1C24, #FFB800);
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 1.25rem;
            transition: transform 0.3s ease;
        }
        .icon-wrapper.docx {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
        }
        .icon-wrapper.pptx {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        .icon-wrapper.pdf {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fee2e2;
        }
        .download-card:hover .icon-wrapper {
            transform: scale(1.06);
        }
        .file-pill {
            display: inline-block;
            background: #f1f5f9;
            color: #334155;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 999px;
            margin-bottom: 0.75rem;
        }
        .candidate-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            margin: 1.25rem 0;
            text-align: left;
            font-size: 0.85rem;
        }
        .candidate-box .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .candidate-box .item-row:last-child {
            margin-bottom: 0;
        }
        .btn-download-main {
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.2s ease;
        }
        .btn-download-main:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        .btn-close-tab {
            font-size: 0.85rem;
            color: #64748b;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 1rem;
            border: none;
            background: none;
            cursor: pointer;
        }
        .btn-close-tab:hover {
            color: #0f172a;
        }
        .status-badge-pulse {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #22c55e;
            margin-right: 6px;
            animation: pulse 1.8s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
    </style>
</head>
<body>

    <div class="download-card">
        <div class="icon-wrapper <?= esc($docType ?? 'docx') ?>">
            <i class="bi <?= esc($iconClass ?? 'bi-file-earmark-arrow-down') ?>"></i>
        </div>

        <div>
            <span class="file-pill">
                <span class="status-badge-pulse"></span>
                <?= esc($badgeTitle ?? 'Dokumen Siap Diunduh') ?>
            </span>
        </div>

        <h4 class="fw-bold mb-1" style="color: #0f172a;"><?= esc($docTitle ?? 'Unduh Dokumen') ?></h4>
        <p class="text-muted small mb-0" style="word-break: break-all;">
            <code><?= esc($fileName ?? '') ?></code>
        </p>

        <div class="candidate-box">
            <div class="item-row">
                <span class="text-muted">Nama Peserta:</span>
                <strong class="text-dark"><?= esc($candidate['nama_lengkap'] ?? '-') ?></strong>
            </div>
            <?php if (!empty($candidate['asal_kampus'])): ?>
            <div class="item-row">
                <span class="text-muted">Asal Kampus:</span>
                <span class="text-dark"><?= esc($candidate['asal_kampus']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($candidate['divisi_pilihan'])): ?>
            <div class="item-row">
                <span class="text-muted">Divisi:</span>
                <span class="text-dark"><?= esc($candidate['divisi_pilihan']) ?></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="d-grid gap-2">
            <a href="<?= esc($downloadUrl) ?>" id="manualDownloadBtn" class="btn btn-download-main <?= esc($btnClass ?? 'btn-primary') ?>">
                <i class="bi bi-download me-2"></i> Unduh Berkas Sekarang
            </a>

            <?php if (!empty($inlineUrl)): ?>
            <a href="<?= esc($inlineUrl) ?>" class="btn btn-outline-secondary btn-sm py-2">
                <i class="bi bi-eye me-1"></i> Buka / Pratinjau PDF Langsung
            </a>
            <?php endif; ?>
        </div>

        <div class="mt-3">
            <p class="text-muted" style="font-size: 0.8rem;" id="autoDownloadNotice">
                <span class="spinner-border spinner-border-sm text-primary me-1" role="status"></span>
                Memulai unduhan secara otomatis...
            </p>
        </div>

        <div>
            <button type="button" onclick="window.close()" class="btn-close-tab">
                <i class="bi bi-x-circle"></i> Tutup Tab Ini
            </button>
        </div>
    </div>

    <script>
        // Memicu unduhan secara otomatis setelah halaman terbuka
        (function() {
            const downloadUrl = "<?= esc($downloadUrl, 'js') ?>";
            setTimeout(function() {
                // Gunakan iframe tersembunyi agar tab tidak berpindah halaman saat mengunduh
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = downloadUrl;
                document.body.appendChild(iframe);

                const notice = document.getElementById('autoDownloadNotice');
                if (notice) {
                    notice.innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i> Unduhan telah dimulai. Klik tombol jika berkas belum terunduh.';
                }
            }, 700);
        })();
    </script>
</body>
</html>
