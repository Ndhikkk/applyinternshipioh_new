<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Dashboard Admin - IOH<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-start align-items-xl-center mb-4 gap-3" data-aos="fade-down">
        <div class="me-xl-3">
            <h1 class="h3 mb-1 text-gray-800 fw-bold text-nowrap">
                <i class="bi bi-speedometer2 text-indosat"></i> <?= $is_arsip ? 'Arsip Data' : 'Dashboard Admin' ?>
            </h1>
            <p class="text-muted mb-0 small text-nowrap"><?= $is_arsip ? 'Data yang akan dihapus permanen (3 minggu tanpa perubahan / 7 hari di arsip)' : 'Kelola pendaftaran Industry-Academia Collaboration Program IOH' ?></p>
        </div>
        
        <div class="d-flex flex-wrap flex-xl-nowrap gap-2 align-items-center w-100 w-xl-auto justify-content-start justify-content-xl-end">
            
            <div class="d-flex align-items-center w-100 w-xl-auto mb-2 mb-xl-0 me-xl-2 justify-content-between justify-content-xl-start">
                <span class="fw-bold me-2 text-nowrap">Status Pendaftaran:</span>
                <?php if (($registration_open ?? '1') == '1'): ?>
                    <a href="<?= site_url('admin/toggle-registration') ?>" class="btn btn-success btn-sm rounded-pill px-3 text-nowrap js-toggle-registration" data-confirm-text="Tutup pendaftaran program?">
                        <i class="bi bi-unlock-fill me-1"></i> DIBUKA
                    </a>
                <?php else: ?>
                    <a href="<?= site_url('admin/toggle-registration') ?>" class="btn btn-danger btn-sm rounded-pill px-3 text-nowrap js-toggle-registration" data-confirm-text="Buka pendaftaran program?">
                        <i class="bi bi-lock-fill me-1"></i> DITUTUP
                    </a>
                <?php endif; ?>
            </div>

            <div class="d-flex flex-wrap flex-xl-nowrap gap-2 w-100 w-xl-auto flex-grow-1 flex-xl-grow-0">
                <a href="<?= site_url('admin/dashboard' . ($is_arsip ? '' : '?arsip=1')) ?>" class="btn btn-sm text-nowrap flex-grow-1 flex-xl-grow-0 <?= $is_arsip ? 'btn-primary' : 'btn-outline-secondary' ?>">
                    <i class="bi bi-archive"></i> <?= $is_arsip ? 'Kembali' : 'Arsip (' . $total_arsip . ')' ?>
                </a>

                <button type="button" class="btn btn-success btn-sm text-nowrap flex-grow-1 flex-xl-grow-0" id="btnExportExcel" onclick="openExportModal()">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </button>

                <a href="<?= site_url('admin/parsing-cv') ?>" class="btn btn-danger btn-sm text-nowrap flex-grow-1 flex-xl-grow-0">
                    <i class="bi bi-file-earmark-pdf"></i> Generate CV
                </a>

                <a href="<?= site_url('admin/logout') ?>" class="btn btn-outline-danger btn-sm text-nowrap flex-grow-1 flex-xl-grow-0" title="Logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Flashdata Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" data-aos="fade-up">
            <i class="bi bi-check-circle-fill"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" data-aos="fade-up">
            <i class="bi bi-exclamation-triangle-fill"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-6 col-xl-3 mb-3 mb-xl-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pendaftar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_pendaftar ?></div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-primary me-2"><i class="bi bi-arrow-up"></i> Semua waktu</span>
                            </div>
                        </div>
                        <div class="col-auto"><i class="bi bi-people display-6 text-indosat"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 mb-3 mb-xl-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Diterima</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_diterima ?></div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success me-2"><i class="bi bi-check-circle"></i> Final</span>
                            </div>
                        </div>
                        <div class="col-auto"><i class="bi bi-check-circle display-6 text-success"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 mb-3 mb-xl-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_menunggu ?></div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-warning me-2"><i class="bi bi-clock"></i> Dalam proses</span>
                            </div>
                        </div>
                        <div class="col-auto"><i class="bi bi-clock display-6 text-warning"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 mb-3 mb-xl-4" data-aos="fade-up" data-aos-delay="400">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Ditolak</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_ditolak ?></div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-danger me-2"><i class="bi bi-x-circle"></i> Selesai</span>
                            </div>
                        </div>
                        <div class="col-auto"><i class="bi bi-x-circle display-6 text-danger"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- Data Table -->
<style>
/* ── Professional Pagination Styling ── */
.ajax-pagination .pagination {
    gap: 5px;
    margin-bottom: 0;
    flex-wrap: wrap;
    align-items: center;
}

/* Base page-link style */
.ajax-pagination .page-item .page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    height: 38px;
    padding: 0 12px;
    border-radius: 10px;
    border: 1px solid #e3e6f0;
    color: #5a6072;
    font-size: 0.875rem;
    font-weight: 500;
    background-color: #fff;
    transition: all 0.25s cubic-bezier(.4,0,.2,1);
    text-decoration: none;
    line-height: 1;
    gap: 6px;
}

/* Icon size inside buttons */
.ajax-pagination .page-item .page-link i {
    font-size: 0.8rem;
    line-height: 1;
}

/* ── Nav buttons (First, Previous, Next, Last) ── */
.ajax-pagination .page-item .page-link.page-nav-btn {
    padding: 0 14px;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    color: #6b7280;
    border-color: #e5e7eb;
    background-color: #fafbfc;
}
.ajax-pagination .page-item:not(.disabled) .page-link.page-nav-btn:hover {
    background-color: #f0f4ff;
    border-color: #a3b8e0;
    color: #3b5998;
    box-shadow: 0 2px 8px rgba(59, 89, 152, 0.1);
    transform: translateY(-1px);
}
.ajax-pagination .page-item:not(.disabled) .page-link.page-nav-btn:active {
    transform: translateY(0);
    box-shadow: none;
}

/* Nav label text */
.ajax-pagination .page-nav-label {
    font-size: 0.8rem;
}

/* ── Visual divider between nav & numbers ── */
.ajax-pagination .page-separator .page-divider {
    border: none;
    background: none;
    color: #d1d5db;
    font-size: 0.9rem;
    padding: 0 2px;
    min-width: unset;
    cursor: default;
    pointer-events: none;
}

/* ── Page number buttons ── */
.ajax-pagination .page-number .page-link {
    font-weight: 600;
    min-width: 40px;
    border-radius: 10px;
    color: var(--indosat-primary);
}

/* Active page number */
.ajax-pagination .page-item.active .page-link {
    background: var(--indosat-gradient);
    border-color: transparent;
    color: #fff;
    font-weight: 700;
    box-shadow: 0 4px 14px rgba(230, 0, 126, 0.35);
    transform: translateY(-1px);
}

/* Number hover */
.ajax-pagination .page-number:not(.active) .page-link:hover {
    background-color: var(--indosat-accent);
    border-color: var(--indosat-primary);
    color: var(--indosat-primary);
    box-shadow: 0 2px 8px rgba(230, 0, 126, 0.12);
    transform: translateY(-1px);
}
.ajax-pagination .page-number:not(.active) .page-link:active {
    transform: translateY(0);
    box-shadow: none;
}

/* ── Disabled state ── */
.ajax-pagination .page-item.disabled .page-link {
    background-color: #f8f9fb;
    color: #c4c8d4;
    border-color: #eef0f4;
    cursor: not-allowed;
    opacity: 0.65;
}

/* ── Responsive: hide nav labels on small screens ── */
@media (max-width: 576px) {
    .ajax-pagination .page-nav-label {
        display: none;
    }
    .ajax-pagination .page-item .page-link.page-nav-btn {
        padding: 0 10px;
        min-width: 36px;
    }
    .ajax-pagination .page-separator {
        display: none;
    }
}

/* ── Fallback: style default CI4 pager (bare li > a without Bootstrap classes) ── */
.ajax-pagination .pagination > li {
    list-style: none;
}
.ajax-pagination .pagination > li > a,
.ajax-pagination .pagination > li > span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 14px;
    border-radius: 10px;
    border: 1px solid #e3e6f0;
    color: #5a6072;
    font-size: 0.875rem;
    font-weight: 600;
    background-color: #fff;
    transition: all 0.25s cubic-bezier(.4,0,.2,1);
    text-decoration: none;
    line-height: 1;
}
.ajax-pagination .pagination > li.active > a,
.ajax-pagination .pagination > li.active > span {
    background: var(--indosat-gradient);
    border-color: transparent;
    color: #fff;
    font-weight: 700;
    box-shadow: 0 4px 14px rgba(230, 0, 126, 0.35);
    transform: translateY(-1px);
}
.ajax-pagination .pagination > li:not(.active) > a:hover {
    background-color: var(--indosat-accent);
    border-color: var(--indosat-primary);
    color: var(--indosat-primary);
    box-shadow: 0 2px 8px rgba(230, 0, 126, 0.12);
    transform: translateY(-1px);
}
.ajax-pagination .pagination > li:not(.active) > a:active {
    transform: translateY(0);
    box-shadow: none;
}

/* CSS Responsif Khusus Tabel di Mobile (Card Layout) */
@media (max-width: 768px) {
    .table-mobile-cards thead {
        display: none;
    }
    .table-mobile-cards, .table-mobile-cards tbody, .table-mobile-cards tr, .table-mobile-cards td {
        display: block;
        width: 100%;
    }
    .table-mobile-cards tr {
        margin-bottom: 1.5rem;
        background-color: #fff;
        border: 1px solid #e3e6f0;
        border-radius: 0.75rem;
        box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.08);
        padding: 0;
        overflow: hidden;
    }
    .table-mobile-cards td {
        position: relative;
        text-align: right;
        padding: 0.75rem 1rem 0.75rem 40%; /* 40% padding kiri untuk tempat label */
        border: none;
        border-bottom: 1px solid #f8f9fc;
        min-height: 2.5rem;
    }
    /* Konten sel boleh turun baris (email/kampus panjang tidak meluber) */
    .table-mobile-cards td,
    .table-mobile-cards td .small,
    .table-mobile-cards td a {
        word-break: break-word;
        white-space: normal;
    }
    .table-mobile-cards td:last-child {
        border-bottom: none;
        background-color: #f8f9fc; /* Sedikit beda warna untuk kolom aksi */
    }
    .table-mobile-cards td::before {
        content: attr(data-label);
        font-weight: 700;
        color: #4e73df;
        font-size: 0.85rem;
        position: absolute;
        left: 1rem;
        top: 0.75rem;
        text-align: left;
        width: 35%;
        line-height: 1.4;
    }
    /* Memastikan elemen div/p di dalam td tersusun vertikal di kanan */
    .table-mobile-cards td > div {
        display: block;
        margin-bottom: 0.25rem;
    }
    .table-mobile-cards td > div:last-child {
        margin-bottom: 0;
    }
    /* Kolom Berkas: tombol rata kanan dan boleh turun baris bila sempit */
    .table-mobile-cards td .btn-group {
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    /* Pengecualian untuk kolom aksi agar tombolnya sejajar atau terpusat dengan rapi */
    .table-mobile-cards td.mobile-col-flex {
        padding-left: 1rem;
        text-align: center;
    }
    .table-mobile-cards td.mobile-col-flex::before {
        position: static;
        display: block;
        width: 100%;
        text-align: center;
        margin-bottom: 0.75rem;
    }
    .table-mobile-cards td.mobile-col-flex .d-flex {
        justify-content: center !important;
        flex-wrap: wrap !important;
    }
}

/* ── Export Excel Modal Custom Cards ── */
.export-format-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 1.1rem 1.25rem;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 1.1rem;
    text-align: left;
    width: 100%;
    position: relative;
    outline: none;
}
.export-format-card:hover, .export-format-card:focus-visible {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
}
.export-format-card.card-custom-format:hover, .export-format-card.card-custom-format:focus-visible {
    border-color: #3b82f6;
    background: #f8faff;
}
.export-format-card.card-dashboard-format:hover, .export-format-card.card-dashboard-format:focus-visible {
    border-color: #10b981;
    background: #f6fdf9;
}
.export-icon-box {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
    transition: transform 0.25s ease;
}
.export-format-card:hover .export-icon-box {
    transform: scale(1.08);
}
.export-icon-box.icon-box-blue {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #dbeafe;
}
.export-icon-box.icon-box-green {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #d1fae5;
}
.export-card-badge {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    letter-spacing: 0.3px;
    display: inline-block;
}
.badge-blue-subtle {
    background: #dbeafe;
    color: #1e40af;
}
.badge-green-subtle {
    background: #d1fae5;
    color: #065f46;
}
.export-card-title {
    font-size: 0.98rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.export-card-desc {
    font-size: 0.825rem;
    color: #64748b;
    line-height: 1.45;
    margin: 0;
}
.export-card-action-icon {
    color: #94a3b8;
    font-size: 1.25rem;
    margin-left: auto;
    flex-shrink: 0;
    transition: transform 0.2s ease, color 0.2s ease;
}
.export-format-card:hover .export-card-action-icon {
    color: #1e293b;
    transform: translateY(3px);
}
</style>

<div class="card border-0 shadow-sm" data-aos="fade-up">
    <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-md-between align-items-stretch align-items-md-center gap-2">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-table"></i> <?= $is_arsip ? 'Data Arsip (menunggu hapus permanen)' : 'Data Pendaftar Program' ?>
        </h6>
        <div class="d-flex gap-2 ms-md-auto">
            <!-- Diubah dari form menjadi div untuk menjamin tidak ada reload -->
            <div class="input-group input-group-sm flex-grow-1 flex-md-grow-0" id="searchContainer" style="min-width: 0;">
                <?php if ($is_arsip): ?><input type="hidden" name="arsip" id="arsipParam" value="1"><?php endif; ?>
                <input type="text" id="searchInput" class="form-control" placeholder="Cari nama, email, kampus..." value="<?= esc($keyword ?? '') ?>" autocomplete="off">
                <button class="btn btn-outline-secondary" type="button" id="searchBtn"><i class="bi bi-search"></i></button>
                <button class="btn btn-outline-danger" type="button" id="resetBtn" style="display: <?= !empty($keyword) ? 'block' : 'none' ?>;" title="Reset pencarian"><i class="bi bi-x"></i></button>
            </div>
            <button class="btn btn-outline-primary btn-sm" onclick="location.reload()" title="Refresh">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>
    <div class="card-body" id="tableContainer" style="position: relative; min-height: 400px;">
        <?= $this->include('admin/_table_data') ?>
    </div>
</div>

<!-- ============================================================= -->
<!-- MODAL AKSI TUNGGAL: semua aksi kandidat ada di sini             -->
<!-- ============================================================= -->
<div class="modal fade" id="actionModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-badge"></i> <span id="amNama">-</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="amLoading" class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="text-muted mt-2 mb-0">Memuat data...</p>
                </div>

                <div id="amContent" style="display:none;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="text-muted">Status saat ini:</span>
                        <span id="amStatusBadge"></span>
                    </div>

                    <!-- Riwayat Interview -->
                    <div id="amRiwayat" class="mb-3"></div>

                    <hr>

                    <!-- ==== Aksi tahap interview (tampil kalau status masih tahap interview) ==== -->
                    <div id="amInterviewActions">
                        <p class="fw-bold mb-2"><i class="bi bi-clipboard-check"></i> Proses Seleksi & Interview</p>
                        
                        <!-- Tempat tombol aksi dinamis -->
                        <div id="dynamicActionButtons" class="d-flex gap-2 mb-3"></div>

                        <!-- Sub form: UMUM (Jadwal/Zoom/Catatan) -->
                        <div id="subFormLolos" class="border rounded p-3 mb-3" style="display:none;">
                            <h6 id="subFormTitle" class="text-success"><i class="bi bi-check-lg"></i> Proses Lolos</h6>
                            <input type="hidden" id="targetStatusInput">
                            
                            <div id="jadwalZoomWrapper" class="mb-2">
                                <div class="mb-2">
                                    <label class="form-label small">Jadwal Interview</label>
                                    <input type="datetime-local" id="lolosJadwal" class="form-control form-control-sm">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Link Zoom / Meet</label>
                                    <input type="url" id="lolosZoom" class="form-control form-control-sm" placeholder="https://zoom.us/j/...">
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <label class="form-label small">Catatan (Opsional)</label>
                                <textarea id="lolosCatatan" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                            <button type="button" id="dynamicSaveButton" class="btn btn-success btn-sm" onclick="submitDynamicAction()">
                                <i class="bi bi-check2-circle"></i> Simpan
                            </button>
                            <button type="button" class="btn btn-link btn-sm" onclick="hideSubForms()">Batal</button>
                        </div>
                    </div>

                    <!-- ==== Ubah Manual (Status, Divisi, Periode) ==== -->
                    <div id="amManualActions">
                        <p class="fw-bold mb-2 mt-2"><i class="bi bi-pencil-square"></i> Ubah Data Manual</p>
                        
                        <!-- Baris 0: NIM (di atas Status) -->
                        <div class="row g-2 mb-2 align-items-end">
                            <div class="col-12">
                                <label class="form-label small mb-1"><i class="bi bi-card-text me-1"></i>NIM (Nomor Induk Mahasiswa)</label>
                                <input type="text" id="manualNim" class="form-control form-control-sm" placeholder="Masukkan NIM mahasiswa (opsional)">
                            </div>
                        </div>

                        <!-- Baris 1: Status, Regional Interview & Divisi -->
                        <div class="row g-2 mb-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small mb-1">Status</label>
                                <select id="manualStatus" class="form-select form-select-sm">
                                    <option value="Menunggu">Menunggu</option>
                                    <option value="Progress">Progress</option>
                                    <option value="Diterima">Diterima</option>
                                    <option value="Complete">Complete (Selesai Magang)</option>
                                    <option value="Ditolak">Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small mb-1">Regional Interview</label>
                                <select id="editKota" class="form-select form-select-sm">
                                    <option value="">Pilih Kota</option>
                                    <option value="Semarang">Semarang</option>
                                    <option value="Surabaya">Surabaya</option>
                                    <option value="Bali">Bali</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small mb-1">Divisi Pilihan</label>
                                <select id="editDivisi" class="form-select form-select-sm">
                                    <option value="">Pilih Divisi</option>
                                    <option value="Direct Sales Executive">Direct Sales Executive</option>
                                    <option value="Markom">Markom</option>
                                    <option value="IT / Elang IT">IT / Elang IT</option>
                                    <option value="Technical">Technical</option>
                                    <option value="Finance">Finance</option>
                                    <option value="B2B">B2B</option>
                                    <option value="Social Media 3ID & IM3">Social Media 3ID & IM3</option>
                                    <option value="Daily Project">Daily Project</option>
                                    <option value="Project Post Paid">Project Post Paid</option>
                                    <option value="Capability Building">Capability Building</option>
                                    <option value="SnD">SnD</option>
                                </select>
                            </div>
                        </div>

                        <!-- Baris 2: Kota Pilihan bertahap agar daftar kota lebih ringkas -->
                        <div class="row g-2 mb-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small mb-1">Provinsi Kota Pilihan</label>
                                <select id="editProvinsiPilihan" class="form-select form-select-sm" onchange="updateKotaPilihanOptions()">
                                    <option value="">Pilih Provinsi</option>
                                    <?php $options = $kota_pilihan_options ?? $kota_magang_options; ?>
                                    <?php foreach (array_keys($options) as $provinsi): ?>
                                        <option value="<?= esc($provinsi) ?>"><?= esc($provinsi) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small mb-1">Kota Pilihan</label>
                                <input type="search" id="editKotaPilihan" list="editKotaPilihanList" class="form-control form-control-sm" placeholder="Pilih provinsi, lalu cari kota/kabupaten" autocomplete="off" disabled>
                                <datalist id="editKotaPilihanList"></datalist>
                            </div>
                        </div>

                        <!-- Baris 3: Periode & Catatan -->
                        <div class="row g-2 mb-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Periode Program</label>
                                <div class="d-flex gap-1">
                                    <input type="date" id="editPeriodeMulai" class="form-control form-control-sm" title="Mulai">
                                    <span class="align-self-center">-</span>
                                    <input type="date" id="editPeriodeSelesai" class="form-control form-control-sm" title="Selesai">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Catatan Tambahan</label>
                                <input type="text" id="manualCatatan" class="form-control form-control-sm" placeholder="Catatan (opsional)">
                            </div>
                        </div>

                        <!-- Baris 4: Upload Proposal -->
                        <div class="row g-2 mb-3 align-items-end">
                            <div class="col-12">
                                <label class="form-label small mb-1"><i class="bi bi-journal-arrow-up me-1"></i>Upload / Ganti Proposal (PDF, maks 2MB)</label>
                                <input type="file" id="editProposalFile" class="form-control form-control-sm" accept=".pdf">
                                <div id="currentProposalInfo" class="form-text small mt-1"></div>
                            </div>
                        </div>
                        
                        <!-- Button Simpan -->
                        <div class="text-end">
                            <button type="button" id="manualSaveButton" class="btn btn-primary btn-sm px-4" onclick="submitManual()">Simpan Perubahan</button>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" id="btnSendEmailModal" class="btn btn-outline-secondary btn-sm" onclick="sendEmailNow(this)">
                            <i class="bi bi-envelope"></i> Kirim Email Sekarang
                        </button>
                        <button type="button" id="btnSendWaModal" class="btn btn-outline-success btn-sm" onclick="openWaLink(currentModalId, this)">
                            <i class="bi bi-whatsapp"></i> Kirim Pengingat WhatsApp
                        </button>
                        <button type="button" id="amBtnSurat" class="btn btn-primary btn-sm" style="display:none;" onclick="downloadSuratPenerimaan()" title="Unduh Surat Penerimaan (.docx)">
                            <i class="bi bi-file-earmark-word-fill"></i> Surat Penerimaan
                        </button>
                        <button type="button" id="amBtnSelesai" class="btn btn-info btn-sm text-white" style="display:none;" onclick="downloadSuratSelesai()" title="Unduh Surat Keterangan Selesai (.docx)">
                            <i class="bi bi-file-earmark-word-fill"></i> Surat Selesai
                        </button>
                        <button type="button" id="amBtnPdf" class="btn btn-danger btn-sm" style="display:none;" onclick="downloadCertPdf()" title="Unduh Sertifikat PDF">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Sertifikat PDF
                        </button>
                        <button type="button" id="amBtnPptx" class="btn btn-warning btn-sm text-dark" style="display:none;" onclick="downloadCertPptx()" title="Unduh Sertifikat PowerPoint (.pptx)">
                            <i class="bi bi-file-earmark-ppt-fill"></i> Sertifikat PPTX
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm ms-auto" onclick="hapusData(currentModalId, true)">
                            <i class="bi bi-trash"></i> Hapus Data Ini
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-card { border-radius: 15px; transition: transform 0.3s ease, box-shadow 0.3s ease; border: none; }
.stats-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; }
.table th { border-top: none; font-weight: 600; color: #2C3E50; background-color: #f8f9fa; padding: 14px 12px; }
.table td { vertical-align: middle; padding: 16px 12px; }
.badge { font-size: 0.75em; padding: 6px 10px; }
.bg-purple { background-color: #8b5cf6 !important; color: #fff; }
.card { border: none; border-radius: 15px; }
.card-header { border-radius: 15px 15px 0 0 !important; border-bottom: 1px solid #e3e6f0; background-color: white !important; }
.btn-outline-danger { border-color: #dc3545; color: #dc3545; }
.btn-outline-danger:hover { background-color: #dc3545; color: white; }

/* Kolom Aksi tetap terlihat walau tabel digeser kiri/kanan */
.table-responsive { overflow-x: auto; }
#dataTable thead th:last-child,
#dataTable tbody td:last-child {
    position: sticky;
    right: 0;
    background-color: #fff;
    z-index: 2;
    box-shadow: -3px 0 5px rgba(0,0,0,0.06);
}
#dataTable thead th:last-child { background-color: #f8f9fa; z-index: 3; }
#dataTable tbody tr:hover td:last-child { background-color: #f1f5fb; }

/* ── Tombol Aksi seragam: persegi sama sisi setebal tombol WhatsApp ── */
#dataTable td[data-label="Aksi"] .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    flex-shrink: 0;
}
#dataTable td[data-label="Aksi"] .btn i {
    font-size: 0.875rem;
    line-height: 1;
}
/* Tombol dengan label teks (Detail): lebar mengikuti isi, tinggi tetap sama */
#dataTable td[data-label="Aksi"] .btn.btn-aksi-label {
    width: auto;
    padding: 0 0.75rem;
    gap: 0.375rem;
}

/* Sticky kolom Aksi dinonaktifkan pada mode kartu mobile (tidak relevan saat tabel jadi blok vertikal) */
@media (max-width: 768px) {
    #dataTable thead th:last-child,
    #dataTable tbody td:last-child {
        position: static;
        box-shadow: none;
    }
}

/* ── Search bar: lebar penuh di mobile, lebar tetap di desktop ── */
@media (min-width: 768px) {
    #searchContainer { width: 280px; flex-grow: 0; }
}

/* ── Penyesuaian kartu statistik di layar kecil ── */
@media (max-width: 575.98px) {
    .stats-card .card-body { padding: 0.85rem; }
    .stats-card .col-auto i { font-size: 1.5rem; }
    .stats-card .text-xs { font-size: 0.68rem; }
    .stats-card .h5 { font-size: 1.05rem; margin-bottom: 0; }
}

/* ── Pagination overrides (ensures consistency) ── */
</style>

<!-- SweetAlert2 loaded globally via layout/main.php -->

<script>
    let currentModalId = null;
    let currentModalItem = null;

    function getActionModal() {
        return bootstrap.Modal.getOrCreateInstance(document.getElementById('actionModal'));
    }

    function buildUrl(id, action, params) {
        let url = `<?= site_url('admin/process-interview/') ?>${id}/${encodeURIComponent(action)}`;
        if (params) {
            const qs = new URLSearchParams(params).toString();
            if (qs) url += '?' + qs;
        }
        return url;
    }

    function apiGet(url) {
        return fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(async res => {
                const json = await res.json().catch(() => ({ success: false, message: 'Respon server tidak valid.' }));
                if (!res.ok && json.success === undefined) json.success = false;
                return json;
            })
            .catch(() => ({ success: false, message: 'Tidak dapat menghubungi server.' }));
    }

    function apiPost(url, params = {}) {
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        };
        let body;
        if (params instanceof FormData) {
            body = params;
        } else {
            headers['Content-Type'] = 'application/x-www-form-urlencoded;charset=UTF-8';
            body = new URLSearchParams(params).toString();
        }
        return fetch(url, { method: 'POST', headers, body })
            .then(async res => {
                const json = await res.json().catch(() => ({ success: false, message: 'Respon server tidak valid.' }));
                if (!res.ok && json.success === undefined) json.success = false;
                return json;
            })
            .catch(() => ({ success: false, message: 'Tidak dapat menghubungi server.' }));
    }

    function toast(icon, text) {
        IOH.toast(icon, text);
    }

    function formatStatusLabel(status) {
        if (!status) return 'MENUNGGU';
        if (status === 'Complete') return 'COMPLETE';
        if (status.includes('Interview')) return status.toUpperCase();
        if (status === 'Progress') return 'PROGRESS';
        if (status === 'Diterima') return 'DITERIMA';
        if (status === 'Ditolak') return 'DITOLAK';
        if (status === 'Menunggu') return 'MENUNGGU';
        return status.replace(/_/g, ' ').toUpperCase();
    }

    function statusBadgeMarkupFromLabel(status) {
        if (!status) status = 'Menunggu';
        const isComplete = status === 'Complete';
        const isFinal = status === 'Diterima';
        const isRejected = ['Ditolak'].includes(status);
        let cls = 'bg-warning text-dark';
        if (isComplete) cls = 'bg-purple text-white';
        else if (isFinal) cls = 'bg-success text-white';
        else if (isRejected) cls = 'bg-danger text-white';
        else if (status.includes('Interview') || status === 'Progress') cls = 'bg-info text-dark';
        
        return `<span class="badge ${cls}">${formatStatusLabel(status)}</span>`;
    }

    function displayStatus(item) {
        if (!item || !item.status) return 'Menunggu';
        if (item.status === 'Menunggu' && item.jadwal_interview_1) return 'Interview Tahap 1';
        if (item.status === 'Progress' && item.jadwal_interview_2) return 'Interview Tahap 2';
        return item.status;
    }

    // ===================== MODAL AKSI =====================
    function openActionModal(id, nama) {
        currentModalId = id;
        document.getElementById('amNama').textContent = nama;
        document.getElementById('amLoading').style.display = '';
        document.getElementById('amContent').style.display = 'none';
        hideSubForms();
        getActionModal().show();

        apiGet(buildUrl(id, 'info')).then(json => {
            document.getElementById('amLoading').style.display = 'none';
            if (!json.success) {
                getActionModal().hide();
                IOH.alert('error', 'Gagal Memuat Data', json.message || 'Tidak dapat memuat data kandidat.');
                return;
            }
            currentModalItem = json.item;
            renderModalContent(json.item);
            document.getElementById('amContent').style.display = '';
        });
    }

    function renderModalContent(item) {
        document.getElementById('amStatusBadge').innerHTML = statusBadgeMarkupFromLabel(displayStatus(item));

        // Riwayat interview
        let riwayat = '';
        for (let i = 1; i <= 3; i++) {
            const jadwal = item['jadwal_interview_' + i];
            const zoom = item['link_zoom_' + i];
            const catatan = item['catatan_interview_' + i];
            if (jadwal || zoom || catatan) {
                riwayat += `<div class="border rounded p-2 mb-2">
                    <strong>Interview Tahap ${i}</strong><br>
                    ${jadwal ? `<small><i class="bi bi-calendar-event"></i> ${new Date(jadwal.replace(' ', 'T')).toLocaleString('id-ID')}</small><br>` : ''}
                    ${zoom ? `<small><i class="bi bi-camera-video"></i> <a href="${zoom}" target="_blank">${zoom}</a></small><br>` : ''}
                    ${catatan ? `<small class="text-muted"><i class="bi bi-chat-left-text"></i> ${catatan}</small>` : ''}
                </div>`;
            }
        }
        document.getElementById('amRiwayat').innerHTML = riwayat || '<span class="text-muted small">Belum ada riwayat interview.</span>';

        // Tampilkan blok aksi interview hanya kalau statusnya memang masih tahap seleksi
        const interviewStatuses = ['Menunggu', 'Progress'];
        document.getElementById('amInterviewActions').style.display = interviewStatuses.includes(item.status) ? '' : 'none';

        renderDynamicButtons(item);

        document.getElementById('manualNim').value = item.nim || '';
        document.getElementById('manualStatus').value = item.status;
        document.getElementById('manualCatatan').value = item.catatan_admin || '';

        document.getElementById('editDivisi').value = item.divisi_pilihan || '';
        document.getElementById('editKota').value = item.regional_interview || '';
        const currentKotaPilihan = item.kota_pilihan || item.kota_magang || '';
        const provEl = document.getElementById('editProvinsiPilihan') || document.getElementById('editProvinsiMagang');
        if (provEl) provEl.value = findProvinsiKotaPilihan(currentKotaPilihan);
        updateKotaPilihanOptions(currentKotaPilihan);
        document.getElementById('editPeriodeMulai').value = item.periode_mulai || '';
        document.getElementById('editPeriodeSelesai').value = item.periode_selesai || '';

        // Reset proposal file input & show current status
        const proposalInput = document.getElementById('editProposalFile');
        if (proposalInput) proposalInput.value = '';
        const proposalInfo = document.getElementById('currentProposalInfo');
        if (proposalInfo) {
            proposalInfo.innerHTML = item.proposal_magang
                ? `<span class="text-success"><i class="bi bi-file-earmark-check"></i> Proposal saat ini tersedia</span>`
                : `<span class="text-muted"><i class="bi bi-file-earmark-x"></i> Belum ada proposal yang diunggah</span>`;
        }

        // Tombol Sertifikat PDF, PPTX & Surat Penerimaan di Modal (Status Diterima atau Complete)
        const btnPdf = document.getElementById('amBtnPdf');
        const btnPptx = document.getElementById('amBtnPptx');
        const btnSurat = document.getElementById('amBtnSurat');
        const btnSelesai = document.getElementById('amBtnSelesai');
        const isEligible = (item.status === 'Diterima' || item.status === 'Complete');
        if (btnPdf) btnPdf.style.display = isEligible ? '' : 'none';
        if (btnPptx) btnPptx.style.display = isEligible ? '' : 'none';
        if (btnSurat) btnSurat.style.display = isEligible ? '' : 'none';
        if (btnSelesai) btnSelesai.style.display = isEligible ? '' : 'none';

        hideSubForms();
    }

    function downloadCertPdf() {
        if (currentModalId) {
            window.open(`<?= site_url('admin/certificate/pdf') ?>/${currentModalId}`, '_blank');
        }
    }

    function downloadCertPptx() {
        if (currentModalId) {
            window.location.href = `<?= site_url('admin/certificate/pptx') ?>/${currentModalId}`;
        }
    }

    function downloadSuratPenerimaan() {
        if (currentModalId) {
            window.location.href = `<?= site_url('admin/surat/penerimaan') ?>/${currentModalId}`;
        }
    }

    function downloadSuratSelesai() {
        if (currentModalId) {
            window.location.href = `<?= site_url('admin/surat/selesai') ?>/${currentModalId}`;
        }
    }

    function renderDynamicButtons(item) {
        const status = item.status;
        const container = document.getElementById('dynamicActionButtons');
        let html = '';

        if (status === 'Menunggu') {
            if (item.jadwal_interview_1) {
                html += `<button type="button" id="approveInterview1Button" class="btn btn-success btn-sm" onclick="showSubForm('Progress', 'Lolos Interview 1 (ACC)', false, this)">
                            <i class="bi bi-check-lg"></i> Lolos Int 1 (ACC)
                         </button>`;
            } else {
                html += `<button type="button" id="scheduleInterview1Button" class="btn btn-primary btn-sm" onclick="showSubForm('schedule_interview_1', 'Jadwalkan Interview 1', true, this)">
                            <i class="bi bi-calendar-plus"></i> Jadwalkan Interview 1
                         </button>`;
            }
            html += `<button type="button" class="btn btn-danger btn-sm" onclick="showSubForm('Ditolak', 'Tolak Kandidat', false, this)">
                        <i class="bi bi-x-lg"></i> Tolak
                     </button>`;
        } else if (status === 'Progress') {
            if (item.jadwal_interview_2) {
                html += `<button type="button" id="acceptCandidateButton" class="btn btn-success btn-sm" onclick="showSubForm('Diterima', 'Diterima', false, this)">
                            <i class="bi bi-award"></i> Diterima
                         </button>`;
            } else {
                html += `<button type="button" id="scheduleInterview2Button" class="btn btn-primary btn-sm" onclick="showSubForm('schedule_interview_2', 'Jadwalkan Interview 2', true, this)">
                            <i class="bi bi-calendar-plus"></i> Jadwalkan Interview 2
                         </button>`;
            }
            html += `<button type="button" class="btn btn-danger btn-sm" onclick="showSubForm('Ditolak', 'Tolak Kandidat', false, this)">
                        <i class="bi bi-x-lg"></i> Tolak
                     </button>`;
        } else if (status === 'Diterima') {
            html += `<button type="button" id="completeCandidateButton" class="btn btn-purple btn-sm" onclick="showSubForm('Complete', 'Selesaikan Magang (Complete)', false, this)">
                        <i class="bi bi-award-fill"></i> Selesaikan Magang (Complete)
                     </button>`;
            html += `<button type="button" class="btn btn-danger btn-sm" onclick="showSubForm('Ditolak', 'Tolak Kandidat', false, this)">
                        <i class="bi bi-x-lg"></i> Tolak
                     </button>`;
        } else if (status === 'Complete') {
            html += `<button type="button" class="btn btn-outline-success btn-sm" onclick="showSubForm('Diterima', 'Kembalikan ke Status Diterima', false, this)">
                        <i class="bi bi-arrow-counterclockwise"></i> Kembalikan ke Diterima
                     </button>`;
        }

        container.innerHTML = html;
    }

    let activeActionButton = null;
    let submissionInProgress = false;
    const kotaPilihanByProvinsi = <?= json_encode($kota_pilihan_options ?? $kota_magang_options, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    function updateKotaPilihanOptions(selectedKota = '') {
        const provinsiInput = document.getElementById('editProvinsiPilihan') || document.getElementById('editProvinsiMagang');
        const kotaInput = document.getElementById('editKotaPilihan') || document.getElementById('editKotaMagang');
        const kotaList = document.getElementById('editKotaPilihanList') || document.getElementById('editKotaMagangList');
        if (!provinsiInput || !kotaInput || !kotaList) return;
        const cities = kotaPilihanByProvinsi[provinsiInput.value] || [];

        kotaList.innerHTML = cities.map(kota => `<option value="${kota}"></option>`).join('');
        kotaInput.disabled = cities.length === 0;
        kotaInput.placeholder = cities.length ? 'Ketik untuk mencari kota/kabupaten' : 'Pilih provinsi terlebih dahulu';
        kotaInput.value = selectedKota;
    }

    function updateKotaMagangOptions(selectedKota = '') {
        updateKotaPilihanOptions(selectedKota);
    }

    function findProvinsiKotaPilihan(kota) {
        return Object.keys(kotaPilihanByProvinsi).find(provinsi => kotaPilihanByProvinsi[provinsi].includes(kota)) || '';
    }

    function findProvinsiKotaMagang(kota) {
        return findProvinsiKotaPilihan(kota);
    }

    function showSubForm(targetStatus, titleText, requireSchedule, sourceButton) {
        if (activeActionButton && activeActionButton !== sourceButton) {
            activeActionButton.style.opacity = '';
            activeActionButton.disabled = false;
        }
        activeActionButton = sourceButton || null;
        if (activeActionButton) {
            activeActionButton.style.opacity = '0.7';
            activeActionButton.disabled = true;
        }

        document.getElementById('subFormLolos').style.display = '';
        document.getElementById('subFormTitle').innerHTML = `<i class="bi bi-check2-square"></i> ${titleText}`;
        document.getElementById('targetStatusInput').value = targetStatus;
        
        const isTolak = targetStatus.includes('Tidak') || targetStatus === 'Ditolak';
        document.getElementById('subFormTitle').className = isTolak ? 'text-danger fw-bold' : 'text-success fw-bold';
        
        if (requireSchedule) {
            document.getElementById('jadwalZoomWrapper').style.display = '';
        } else {
            document.getElementById('jadwalZoomWrapper').style.display = 'none';
            document.getElementById('lolosJadwal').value = '';
            document.getElementById('lolosZoom').value = '';
        }
    }

    function hideSubForms() {
        document.getElementById('subFormLolos').style.display = 'none';
        if (activeActionButton) {
            activeActionButton.style.opacity = '';
            activeActionButton.disabled = false;
            activeActionButton = null;
        }
    }

    function setButtonLoading(button, label) {
        if (!button || submissionInProgress) return false;
        submissionInProgress = true;
        button.dataset.originalHtml = button.innerHTML;
        button.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${label}`;
        button.disabled = true;
        return true;
    }

    function resetButtonLoading(button) {
        submissionInProgress = false;
        if (!button) return;
        button.innerHTML = button.dataset.originalHtml || button.innerHTML;
        button.disabled = false;
    }

    function submitDynamicAction() {
        const saveButton = document.getElementById('dynamicSaveButton');
        if (!setButtonLoading(saveButton, 'Menyimpan...')) return;
        const targetStatus = document.getElementById('targetStatusInput').value;
        const jadwalRaw = document.getElementById('lolosJadwal').value;
        const isScheduleVisible = document.getElementById('jadwalZoomWrapper').style.display !== 'none';

        submitStatusChange(targetStatus, {
            catatan: document.getElementById('lolosCatatan').value,
            jadwal: isScheduleVisible ? jadwalRaw : '',
            link_zoom: isScheduleVisible ? document.getElementById('lolosZoom').value : ''
        }, saveButton);
    }

    function submitManual() {
        const saveButton = document.getElementById('manualSaveButton');
        if (!setButtonLoading(saveButton, 'Menyimpan...')) return;
        const targetStatus = document.getElementById('manualStatus').value;
        const selectedKotaPilihan = (document.getElementById('editKotaPilihan') || document.getElementById('editKotaMagang'))?.value || '';

        const formData = new FormData();
        formData.append('is_manual', '1');
        formData.append('nim', document.getElementById('manualNim').value);
        formData.append('catatan', document.getElementById('manualCatatan').value);
        formData.append('regional_interview', document.getElementById('editKota').value);
        formData.append('kota_pilihan', selectedKotaPilihan);
        formData.append('divisi_pilihan', document.getElementById('editDivisi').value);
        formData.append('periode_mulai', document.getElementById('editPeriodeMulai').value);
        formData.append('periode_selesai', document.getElementById('editPeriodeSelesai').value);

        const proposalInput = document.getElementById('editProposalFile');
        if (proposalInput && proposalInput.files.length > 0) {
            formData.append('proposal_magang', proposalInput.files[0]);
        }

        submitStatusChange(targetStatus, formData, saveButton, true);
    }

    function submitStatusChange(targetStatus, params, saveButton, refreshTable = false) {
        const id = currentModalId;
        apiPost(buildUrl(id, targetStatus), params).then(json => {
            if (!json.success) {
                resetButtonLoading(saveButton);
                IOH.alert('error', 'Gagal Menyimpan', json.message || 'Terjadi kesalahan saat menyimpan perubahan.');
                return;
            }

            const statusCell = document.getElementById('status-cell-' + id);
            if (statusCell) {
                if (json.badge_html !== undefined) statusCell.innerHTML = json.badge_html;
                const norm = formatStatusLabel(targetStatus);
                statusCell.setAttribute('data-status-label', norm);
                if (window.applyStatusFilter) window.applyStatusFilter();
            }

            // The modal is reused. Restore button labels/states before hiding
            // it so the spinner or faded action never leaks into the next
            // interview/rejection/manual-update action.
            resetButtonLoading(saveButton);
            hideSubForms();
            getActionModal().hide();
            toast('success', json.message);

            if (refreshTable && window.refreshDashboardTable) {
                window.refreshDashboardTable();
            }

            if (json.should_prompt_notifications !== false) {
                const hasEmail = json.item && json.item.email;
                askFollowUps(id, !!hasEmail);
            }
        });
    }

    // Setelah simpan: tanya kirim email? lalu tanya kirim WA? (berbentuk popup, bukan checkbox)
    function askFollowUps(id, hasEmail) {
        let chain = Promise.resolve();

        if (hasEmail) {
            chain = chain.then(() => IOH.confirm({
                icon: 'question',
                title: 'Kirim Notifikasi Email?',
                text: 'Kirim email pemberitahuan otomatis ke kandidat sekarang?',
                confirmText: 'Ya, kirim email',
                cancelText: 'Tidak, nanti saja'
            })).then(res => {
                if (res.isConfirmed) {
                    return apiGet(buildUrl(id, 'email')).then(j => {
                        toast(j.success ? 'success' : 'error', j.message);
                    });
                }
            });
        }

        chain.then(() => IOH.confirm({
            icon: 'question',
            title: 'Kirim Pengingat WhatsApp?',
            text: 'Buka WhatsApp dengan pesan siap-kirim untuk kandidat ini?',
            confirmText: 'Buka WhatsApp',
            cancelText: 'Nanti saja'
        })).then(res => {
            if (res.isConfirmed) openWaLink(id);
        });
    }

    function sendEmailNow(btn) {
        const button = btn || document.getElementById('btnSendEmailModal');
        if (button && !setButtonLoading(button, 'Mengirim Email...')) return;

        apiGet(buildUrl(currentModalId, 'email'))
            .then(j => {
                toast(j.success ? 'success' : 'error', j.message);
            })
            .catch(() => {
                toast('error', 'Gagal mengirim email.');
            })
            .finally(() => {
                if (button) resetButtonLoading(button);
            });
    }

    // ===================== WHATSAPP =====================
    function openWaLink(id, btn) {
        const button = btn || (id === currentModalId ? document.getElementById('btnSendWaModal') : null);
        if (button && !setButtonLoading(button, 'Menyiapkan WA...')) return;

        apiGet(buildUrl(id, 'wa'))
            .then(json => {
                if (!json.success || !json.url) {
                    IOH.alert('info', 'Informasi', json.message || 'Nomor WhatsApp tidak valid atau tidak tersedia.');
                    return;
                }
                window.open(json.url, '_blank');
            })
            .catch(() => {
                IOH.alert('error', 'Koneksi Gagal', 'Tidak dapat menghubungi server. Silakan coba lagi.');
            })
            .finally(() => {
                if (button) resetButtonLoading(button);
            });
    }

    // ===================== HAPUS DATA =====================
    function hapusData(id, fromModal) {
        IOH.confirm({
            icon: 'warning',
            title: 'Hapus Data Secara Permanen?',
            text: 'Seluruh data dan berkas terkait akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.',
            confirmText: 'Ya, hapus sekarang',
            cancelText: 'Batal',
            danger: true
        }).then(result => {
            if (!result.isConfirmed) return;

            fetch(`<?= site_url('admin/hapus/') ?>${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(async res => {
                    const json = await res.json().catch(() => ({ success: false, message: 'Respon server tidak valid.' }));
                    return json;
                })
                .then(json => {
                    if (!json.success) {
                        IOH.alert('error', 'Gagal Menghapus', json.message || 'Terjadi kesalahan saat menghapus data.');
                        return;
                    }
                    if (fromModal) getActionModal().hide();
                    const row = document.getElementById('row-' + id);
                    if (row) row.remove();
                    toast('success', 'Data berhasil dihapus.');
                })
                .catch(() => IOH.alert('error', 'Koneksi Gagal', 'Tidak dapat menghubungi server. Silakan coba lagi.'));
        });
    }

    // ===================== PULIHKAN DARI ARSIP =====================
    function restoreData(id) {
        apiGet(buildUrl(id, 'restore')).then(json => {
            if (!json.success) {
                IOH.alert('error', 'Gagal Memulihkan', json.message || 'Terjadi kesalahan saat memulihkan data.');
                return;
            }
            const row = document.getElementById('row-' + id);
            if (row) row.remove();
            toast('success', json.message || 'Data berhasil dipulihkan.');
        });
    }

    // ===================== MODAL PILIHAN EXPORT EXCEL =====================
    function openExportModal() {
        const searchInput = document.getElementById('searchInput');
        const arsipParam = document.getElementById('arsipParam');
        const keyword = searchInput ? searchInput.value.trim() : '';
        const isArsip = !!arsipParam;

        Swal.fire({
            customClass: { popup: 'ioh-popup' },
            width: '620px',
            title: '<i class="bi bi-file-earmark-excel text-success me-2"></i>Pilihan Format Export Excel',
            html: `
                <p class="text-muted small mb-3">Pilih format susunan kolom yang ingin Anda unduh ke file Microsoft Excel:</p>
                <div class="d-flex flex-column gap-2 text-start">
                    <button type="button" id="swalBtnExportCustom" class="export-format-card card-custom-format">
                        <div class="export-icon-box icon-box-blue">
                            <i class="bi bi-layout-text-window-reverse"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="export-card-title">
                                <span>Susunan Kolom Kustom</span>
                                <span class="export-card-badge badge-blue-subtle">12 Kolom + Dropdown</span>
                            </div>
                            <p class="export-card-desc">Format rekap evaluasi: Profil, Periode, Status Magang (Active/Completed), Suket Penerimaan, Suket Selesai, & Sertifikat.</p>
                        </div>
                        <i class="bi bi-download export-card-action-icon"></i>
                    </button>

                    <button type="button" id="swalBtnExportDashboard" class="export-format-card card-dashboard-format">
                        <div class="export-icon-box icon-box-green">
                            <i class="bi bi-table"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="export-card-title">
                                <span>Susunan Lengkap Dashboard</span>
                                <span class="export-card-badge badge-green-subtle">19 Kolom Lengkap</span>
                            </div>
                            <p class="export-card-desc">Semua kolom database tabel: Kontak, Akademik, Lokasi Magang, Divisi, Status Terkini, Periode, Tanggal, & Catatan Admin.</p>
                        </div>
                        <i class="bi bi-download export-card-action-icon"></i>
                    </button>
                </div>
            `,
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: 'Batal',
            didOpen: () => {
                const baseExportUrl = '<?= site_url('admin/export') ?>';
                function runExport(mode) {
                    const url = new URL(baseExportUrl);
                    if (keyword) url.searchParams.set('keyword', keyword);
                    if (isArsip) url.searchParams.set('arsip', '1');
                    url.searchParams.set('mode', mode);
                    window.location.href = url.toString();
                    Swal.close();
                }

                document.getElementById('swalBtnExportCustom').addEventListener('click', () => runExport('custom'));
                document.getElementById('swalBtnExportDashboard').addEventListener('click', () => runExport('dashboard'));
            }
        });
    }

    // ===================== PENCARIAN LANGSUNG & PAGINATION (AJAX) =====================
    (function () {
        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');
        const resetBtn = document.getElementById('resetBtn');
        const tableContainer = document.getElementById('tableContainer');
        const arsipParam = document.getElementById('arsipParam');
        
        if (!searchInput || !tableContainer) return;

        let debounceTimer = null;
        let activeDivisiFilter = '<?= esc($divisi_filter ?? '', 'js') ?>';
        let activeJenisFilter = '<?= esc($jenis_filter ?? '', 'js') ?>';
        let activeStatusFilter = ''; // Frontend-only status filter (no backend)
        const baseUrl = '<?= site_url('admin/dashboard') ?>';

        function hasActiveFilters() {
            return searchInput.value.trim() || activeDivisiFilter || activeJenisFilter || activeStatusFilter;
        }

        function buildSearchUrl(keyword) {
            const url = new URL(baseUrl);
            if (keyword) url.searchParams.set('keyword', keyword);
            if (activeDivisiFilter) url.searchParams.set('divisi', activeDivisiFilter);
            if (activeJenisFilter) url.searchParams.set('jenis', activeJenisFilter);
            if (arsipParam) url.searchParams.set('arsip', '1');
            return url.toString();
        }

        function loadData(url) {
            tableContainer.style.opacity = '0.5';
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                tableContainer.innerHTML = html;
                tableContainer.style.opacity = '1';
                window.history.pushState({}, '', url);
                applyStatusFilter(); // Re-apply frontend status filter after AJAX reload
            })
            .catch(err => {
                console.error(err);
                tableContainer.style.opacity = '1';
                IOH.alert('error', 'Gagal Memuat Data', 'Terjadi kesalahan saat memuat data pencarian.');
            });
        }

        /**
         * Frontend-only Status Filter.
         * Hides/shows table rows based on data-status-label attribute.
         * Runs after every AJAX table reload to persist filter state.
         */
        function applyStatusFilter() {
            const rows = tableContainer.querySelectorAll('#dataTable tbody tr[id^="row-"]');
            let visibleCount = 0;

            rows.forEach(function(row) {
                if (!activeStatusFilter) {
                    row.style.display = '';
                    visibleCount++;
                    return;
                }
                const statusCell = row.querySelector('td[data-status-label]');
                let rowStatus = statusCell ? statusCell.getAttribute('data-status-label') : '';
                if (!rowStatus && statusCell) {
                    const badge = statusCell.querySelector('.badge');
                    rowStatus = badge ? badge.textContent.trim() : statusCell.textContent.trim();
                }
                rowStatus = (rowStatus || '').toUpperCase();

                const isMatch = (rowStatus === activeStatusFilter) || 
                    (activeStatusFilter === 'PROGRESS' && (rowStatus.includes('INTERVIEW') || rowStatus.includes('PROGRESS')));

                if (isMatch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Update filter icon: filled when active
            const filterIcon = tableContainer.querySelector('#statusFilterIcon');
            if (filterIcon) {
                filterIcon.className = activeStatusFilter
                    ? 'bi bi-funnel-fill text-primary ms-1'
                    : 'bi bi-funnel ms-1';
            }

            // Update badge next to column title (matching Divisi/Jenis badges, but with dynamic status colors)
            const filterBadge = tableContainer.querySelector('#statusFilterBadge');
            if (filterBadge) {
                if (activeStatusFilter) {
                    const statusConfig = {
                        'MENUNGGU': { label: 'Menunggu', class: 'bg-warning text-dark' },
                        'PROGRESS': { label: 'Progress', class: 'bg-info text-dark' },
                        'DITERIMA': { label: 'Diterima', class: 'bg-success text-white' },
                        'COMPLETE': { label: 'Complete', class: 'bg-purple text-white' },
                        'DITOLAK':  { label: 'Ditolak',  class: 'bg-danger text-white' }
                    };
                    const conf = statusConfig[activeStatusFilter] || { label: activeStatusFilter, class: 'bg-primary text-white' };
                    filterBadge.textContent = conf.label;
                    filterBadge.className = 'badge ms-1 ' + conf.class;
                    filterBadge.style.fontSize = '.65em';
                    filterBadge.style.display = 'inline-block';
                } else {
                    filterBadge.style.display = 'none';
                    filterBadge.textContent = '';
                    filterBadge.className = 'badge bg-primary ms-1';
                }
            }

            // Update active state on dropdown items
            tableContainer.querySelectorAll('[data-status-filter]').forEach(function(el) {
                const val = el.getAttribute('data-status-filter');
                if (val === activeStatusFilter) {
                    el.classList.add('active', 'fw-bold');
                } else {
                    el.classList.remove('active', 'fw-bold');
                }
            });

            // Show "no results" message if all rows hidden by status filter
            var noResultRow = tableContainer.querySelector('#statusFilterNoResult');
            if (activeStatusFilter && visibleCount === 0 && rows.length > 0) {
                if (!noResultRow) {
                    var tbody = tableContainer.querySelector('#dataTable tbody');
                    if (tbody) {
                        var tr = document.createElement('tr');
                        tr.id = 'statusFilterNoResult';
                        tr.innerHTML = '<td colspan="10" class="text-center py-4 text-muted"><i class="bi bi-filter-circle display-6 d-block mb-2 opacity-50"></i>Tidak ada data dengan status <strong>' + (activeStatusFilter === 'COMPLETE' ? 'Complete' : activeStatusFilter) + '</strong> di halaman ini.</td>';
                        tbody.appendChild(tr);
                    }
                }
            } else if (noResultRow) {
                noResultRow.remove();
            }
        }

        window.applyStatusFilter = applyStatusFilter;
        applyStatusFilter();

        window.refreshDashboardTable = function () {
            loadData(window.location.href);
        };

        function handleSearch() {
            const val = searchInput.value.trim();
            resetBtn.style.display = hasActiveFilters() ? 'block' : 'none';
            loadData(buildSearchUrl(val));
        }

        // Handle typing (debounce)
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            resetBtn.style.display = hasActiveFilters() ? 'block' : 'none';
            debounceTimer = setTimeout(handleSearch, 600);
        });

        // Handle search button click
        searchBtn.addEventListener('click', handleSearch);

        // Handle Enter key inside input
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(debounceTimer);
                handleSearch();
            }
        });

        // Handle reset button — clears all filters
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                searchInput.value = '';
                activeDivisiFilter = '';
                activeJenisFilter = '';
                activeStatusFilter = '';
                this.style.display = 'none';
                loadData(buildSearchUrl(''));
            });
        }

        // Handle pagination, divisi filter, and jenis filter clicks via event delegation
        tableContainer.addEventListener('click', function(e) {
            const paginationLink = e.target.closest('.ajax-pagination a');
            if (paginationLink) {
                e.preventDefault();
                loadData(paginationLink.href);
                return;
            }

            // Handle divisi filter dropdown clicks
            const divisiItem = e.target.closest('[data-divisi]');
            if (divisiItem) {
                e.preventDefault();
                activeDivisiFilter = divisiItem.getAttribute('data-divisi');
                const keyword = searchInput.value.trim();
                resetBtn.style.display = hasActiveFilters() ? 'block' : 'none';
                loadData(buildSearchUrl(keyword));
                return;
            }

            // Handle jenis filter dropdown clicks
            const jenisItem = e.target.closest('[data-jenis]');
            if (jenisItem) {
                e.preventDefault();
                activeJenisFilter = jenisItem.getAttribute('data-jenis');
                const keyword = searchInput.value.trim();
                resetBtn.style.display = hasActiveFilters() ? 'block' : 'none';
                loadData(buildSearchUrl(keyword));
                return;
            }

            // Handle status filter dropdown clicks (frontend-only, no server request)
            const statusItem = e.target.closest('[data-status-filter]');
            if (statusItem) {
                e.preventDefault();
                activeStatusFilter = statusItem.getAttribute('data-status-filter');
                resetBtn.style.display = hasActiveFilters() ? 'block' : 'none';
                applyStatusFilter();
                // Close the dropdown after selection
                var dropdownToggle = statusItem.closest('.dropdown');
                if (dropdownToggle) {
                    var toggleEl = dropdownToggle.querySelector('[data-bs-toggle="dropdown"]');
                    if (toggleEl) bootstrap.Dropdown.getOrCreateInstance(toggleEl).hide();
                }
                return;
            }
        });
    })();

    // ===================== TOGGLE STATUS PENDAFTARAN (POPUP, BUKAN confirm()) =====================
    document.querySelectorAll('.js-toggle-registration').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const text = this.getAttribute('data-confirm-text');
            IOH.confirm({
                icon: 'question',
                title: 'Konfirmasi Perubahan',
                text: text,
                confirmText: 'Ya, lanjutkan',
                cancelText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) window.location.href = url;
            });
        });
    });
</script>

<footer class="sticky-footer bg-white mt-5">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>&copy; 2026 IOH Industry-Academia Collaboration Program - Admin Panel</span>
        </div>
    </div>
</footer>
<?= $this->endSection() ?>
