<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($docTitle ?? 'Pratinjau Dokumen') ?> - <?= esc($candidate['nama_lengkap'] ?? '') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo_indosat.png') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        * {
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Poppins', sans-serif;
            background-color: #334155;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Sticky Top Toolbar */
        .preview-topbar {
            height: 58px;
            background: #0f172a;
            border-bottom: 1px solid #1e293b;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            color: #f8fafc;
            flex-shrink: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .preview-topbar .brand-info {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .preview-topbar .doc-title {
            font-size: 0.95rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .preview-topbar .meta-info {
            font-size: 0.82rem;
            color: #94a3b8;
            white-space: nowrap;
        }
        .preview-topbar .actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        /* Notice Banner for PPTX */
        .pptx-notice {
            background: #fef3c7;
            color: #92400e;
            border-bottom: 1px solid #fde68a;
            padding: 6px 16px;
            font-size: 0.82rem;
            text-align: center;
            flex-shrink: 0;
        }

        /* Viewer Container */
        .viewer-container {
            flex: 1;
            width: 100%;
            height: 100%;
            position: relative;
            background: #525659;
            overflow: hidden;
        }

        .pdf-viewer-iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
    </style>
</head>
<body>

    <!-- Header Toolbar -->
    <header class="preview-topbar">
        <div class="brand-info">
            <span class="badge <?= esc($badgeClass ?? 'bg-primary') ?> px-2 py-1">
                <i class="bi <?= esc($iconClass ?? 'bi-file-earmark-text') ?> me-1"></i>
                <?= esc($badgeTitle ?? 'Dokumen') ?>
            </span>
            <div class="doc-title text-white">
                <?= esc($docTitle ?? 'Pratinjau Dokumen') ?>
            </div>
            <div class="meta-info d-none d-md-inline">
                &bull; Peserta: <strong class="text-white"><?= esc($candidate['nama_lengkap'] ?? '-') ?></strong>
                <?php if (!empty($candidate['divisi_pilihan'])): ?>
                    (<?= esc($candidate['divisi_pilihan']) ?>)
                <?php endif; ?>
            </div>
        </div>

        <div class="actions">
            <!-- Tombol Unduh Utama (Sesuai format asli dokumen: Word / PPTX / PDF) -->
            <a href="<?= esc($downloadUrl) ?>" class="btn btn-sm <?= esc($btnClass ?? 'btn-primary') ?> fw-semibold shadow-sm px-3">
                <i class="bi bi-download me-1"></i> Unduh <?= strtoupper(esc($docType ?? 'berkas')) ?>
            </a>

            <!-- Tombol Unduh PDF Tambahan untuk Word / PPTX jika diperlukan -->
            <?php if (!empty($downloadPdfUrl)): ?>
            <a href="<?= esc($downloadPdfUrl) ?>" class="btn btn-sm btn-outline-danger text-light d-none d-lg-inline-flex align-items-center" title="Unduh versi PDF">
                <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF
            </a>
            <?php endif; ?>

            <button type="button" onclick="window.close()" class="btn btn-sm btn-outline-secondary text-light px-2 ms-1" title="Tutup Tab">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </header>

    <?php if (($docType ?? '') === 'pptx'): ?>
    <!-- Notice for PPTX preview -->
    <div class="pptx-notice">
        <i class="bi bi-info-circle-fill me-1"></i>
        <strong>Pratinjau Visual Sertifikat:</strong> Tampilan di bawah menunjukkan visual sertifikat secara akurat. Klik tombol <strong>"Unduh PPTX"</strong> di kanan atas untuk mengunduh template PowerPoint (.pptx) asli yang dapat diedit.
    </div>
    <?php endif; ?>

    <!-- Unified PDF Viewer Area (Uses Native Browser PDF Engine for 100% Accuracy) -->
    <main class="viewer-container">
        <iframe src="<?= esc($inlineUrl ?? '') ?>" class="pdf-viewer-iframe" title="Pratinjau Dokumen"></iframe>
    </main>

</body>
</html>
