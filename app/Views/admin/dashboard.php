<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Dashboard Admin - IOH Semarang<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4 gap-3" data-aos="fade-down">
        <div class="me-lg-3">
            <h1 class="h3 mb-1 text-gray-800 fw-bold text-nowrap">
                <i class="bi bi-speedometer2 text-indosat"></i> <?= $is_arsip ? 'Arsip Data' : 'Dashboard Admin' ?>
            </h1>
            <p class="text-muted mb-0 small text-nowrap"><?= $is_arsip ? 'Data yang menunggu dihapus permanen (7 hari sejak diarsipkan)' : 'Kelola pendaftaran magang IOH Semarang' ?></p>
        </div>
        
        <div class="d-flex flex-wrap flex-lg-nowrap gap-2 align-items-center w-100 w-lg-auto justify-content-start justify-content-lg-end">
            
            <div class="d-flex align-items-center w-100 w-lg-auto mb-2 mb-lg-0 me-lg-2 justify-content-between justify-content-lg-start">
                <span class="fw-bold me-2 text-nowrap">Status Pendaftaran:</span>
                <?php if ($registration_open == '1'): ?>
                    <a href="<?= site_url('admin/toggle-registration') ?>" class="btn btn-success btn-sm rounded-pill px-3 text-nowrap js-toggle-registration" data-confirm-text="Tutup pendaftaran magang?">
                        <i class="bi bi-unlock-fill me-1"></i> DIBUKA
                    </a>
                <?php else: ?>
                    <a href="<?= site_url('admin/toggle-registration') ?>" class="btn btn-danger btn-sm rounded-pill px-3 text-nowrap js-toggle-registration" data-confirm-text="Buka pendaftaran magang?">
                        <i class="bi bi-lock-fill me-1"></i> DITUTUP
                    </a>
                <?php endif; ?>
            </div>

            <div class="d-flex flex-wrap flex-lg-nowrap gap-2 w-100 w-lg-auto flex-grow-1 flex-lg-grow-0">
                <a href="<?= site_url('admin/dashboard' . ($is_arsip ? '' : '?arsip=1')) ?>" class="btn btn-sm text-nowrap flex-grow-1 flex-lg-grow-0 <?= $is_arsip ? 'btn-primary' : 'btn-outline-secondary' ?>">
                    <i class="bi bi-archive"></i> <?= $is_arsip ? 'Kembali' : 'Arsip (' . $total_arsip . ')' ?>
                </a>

                <a href="<?= site_url('admin/export') ?>" class="btn btn-success btn-sm text-nowrap flex-grow-1 flex-lg-grow-0">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
                
                <a href="<?= site_url('admin/parsing-cv') ?>" class="btn btn-danger btn-sm text-nowrap flex-grow-1 flex-lg-grow-0">
                    <i class="bi bi-file-earmark-pdf"></i> genrate CV
                </a>

                <a href="<?= site_url('admin/logout') ?>" class="btn btn-outline-danger btn-sm text-nowrap flex-grow-1 flex-lg-grow-0" title="Logout">
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
        <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pendaftar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_pendaftar ?></div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success me-2"><i class="bi bi-arrow-up"></i> Semua waktu</span>
                            </div>
                        </div>
                        <div class="col-auto"><i class="bi bi-people display-6 text-indosat"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
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
        <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
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
        <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
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
/* CSS khusus untuk memberi jarak pada tombol pagination */
.ajax-pagination .pagination {
    gap: 8px; /* Memberi jarak antar tombol */
    margin-bottom: 0;
}
.ajax-pagination .page-item .page-link {
    border-radius: 8px; /* Membuat tombol membulat */
    padding: 8px 16px;
    border: 1px solid #dee2e6;
    color: #495057;
    transition: all 0.2s ease-in-out;
}
.ajax-pagination .page-item.active .page-link {
    background-color: var(--indosat-red, #E31837);
    border-color: var(--indosat-red, #E31837);
    color: white;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(227, 24, 55, 0.3);
}
.ajax-pagination .page-item:not(.active) .page-link:hover {
    background-color: #f8f9fa;
    color: var(--indosat-dark-red, #8B1A3A);
    border-color: #cdd3d8;
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
</style>

<div class="card border-0 shadow-sm" data-aos="fade-up">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-table"></i> <?= $is_arsip ? 'Data Arsip (menunggu hapus permanen)' : 'Data Pendaftar Magang' ?>
        </h6>
        <div class="d-flex gap-2">
            <!-- Diubah dari form menjadi div untuk menjamin tidak ada reload -->
            <div class="input-group input-group-sm" id="searchContainer" style="width: 280px;">
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
                        <p class="fw-bold mb-2"><i class="bi bi-clipboard-check"></i> Proses Interview</p>
                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn btn-success btn-sm" onclick="showSubForm('lolos')">
                                <i class="bi bi-check-lg"></i> Loloskan ke Tahap Selanjutnya
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="showSubForm('tolak')">
                                <i class="bi bi-x-lg"></i> Tidak Lolos
                            </button>
                        </div>

                        <!-- Sub form: LOLOS -->
                        <div id="subFormLolos" class="border rounded p-3 mb-3" style="display:none;">
                            <h6 class="text-success"><i class="bi bi-check-lg"></i> Loloskan Kandidat</h6>
                            <div class="mb-2">
                                <label class="form-label small">Jadwal Interview Berikutnya</label>
                                <input type="datetime-local" id="lolosJadwal" class="form-control form-control-sm">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Link Zoom / Meet</label>
                                <input type="url" id="lolosZoom" class="form-control form-control-sm" placeholder="https://zoom.us/j/...">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Catatan Hasil Interview</label>
                                <textarea id="lolosCatatan" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" onclick="submitLolos()">
                                <i class="bi bi-check2-circle"></i> Simpan
                            </button>
                            <button type="button" class="btn btn-link btn-sm" onclick="hideSubForms()">Batal</button>
                        </div>

                        <!-- Sub form: TOLAK -->
                        <div id="subFormTolak" class="border rounded p-3 mb-3" style="display:none;">
                            <h6 class="text-danger"><i class="bi bi-x-lg"></i> Tandai Tidak Lolos</h6>
                            <div class="mb-2">
                                <label class="form-label small">Alasan Tidak Lolos</label>
                                <textarea id="tolakCatatan" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm" onclick="submitTolak()">
                                <i class="bi bi-x-circle"></i> Simpan
                            </button>
                            <button type="button" class="btn btn-link btn-sm" onclick="hideSubForms()">Batal</button>
                        </div>
                    </div>

                    <!-- ==== Override manual (tampil untuk semua status, opsional) ==== -->
                    <div id="amManualActions">
                        <p class="fw-bold mb-2 mt-2"><i class="bi bi-pencil-square"></i> Ubah Status Manual</p>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <select id="manualStatus" class="form-select form-select-sm">
                                    <option value="Menunggu">Menunggu</option>
                                    <option value="Diterima">Diterima</option>
                                    <option value="Ditolak">Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" id="manualCatatan" class="form-control form-control-sm" placeholder="Catatan (opsional)">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary btn-sm w-100" onclick="submitManual()">Simpan</button>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="sendEmailNow()">
                            <i class="bi bi-envelope"></i> Kirim Email Sekarang
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="openWaLink(currentModalId)">
                            <i class="bi bi-whatsapp"></i> Kirim Pengingat WhatsApp
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

/* Menghilangkan kesan mepet pada Pagination (Pages) */
.ajax-pagination .pagination {
    gap: 8px; /* Memberi jarak antar tombol halaman */
    margin: 0;
}
.ajax-pagination .page-item .page-link {
    border-radius: 8px !important;
    padding: 8px 16px;
    border: 1px solid #e3e6f0;
    color: #4e73df;
    font-weight: 500;
}
.ajax-pagination .page-item.active .page-link {
    background-color: #4e73df;
    border-color: #4e73df;
    color: white;
}
.ajax-pagination .page-item.disabled .page-link {
    background-color: #f8f9fa;
    color: #858796;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let currentModalId = null;
    let currentModalItem = null;

    // Dibuat lazy (bukan langsung dieksekusi di awal script) supaya tidak
    // gagal kalau Bootstrap JS dari layout/main belum sempat ke-load duluan.
    // Ini juga yang bikin error "bootstrap is not defined" sebelumnya
    // menghentikan SELURUH script di bawahnya (termasuk fungsi openActionModal dkk).
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

    function toast(icon, text) {
        Swal.fire({ icon, text, toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
    }

    function statusBadgeMarkupFromLabel(status) {
        const isFinal = ['Diterima', 'Lolos_Final'].includes(status);
        const isRejected = ['Ditolak', 'Tidak_Lolos_Interview_1', 'Tidak_Lolos_Interview_2', 'Tidak_Lolos_Interview_3'].includes(status);
        let cls = 'bg-warning';
        if (isFinal) cls = 'bg-success';
        else if (isRejected) cls = 'bg-danger';
        else if (status === 'Lolos_Interview_1') cls = 'bg-primary';
        else if (status === 'Lolos_Interview_2') cls = 'bg-info';
        else if (status === 'Lolos_Interview_3') cls = 'bg-purple';
        return `<span class="badge ${cls}">${status.replace(/_/g, ' ')}</span>`;
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
                Swal.fire('Gagal', json.message || 'Tidak dapat memuat data kandidat.', 'error');
                return;
            }
            currentModalItem = json.item;
            renderModalContent(json.item);
            document.getElementById('amContent').style.display = '';
        });
    }

    function renderModalContent(item) {
        document.getElementById('amStatusBadge').innerHTML = statusBadgeMarkupFromLabel(item.status);

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

        // Tampilkan blok aksi interview hanya kalau statusnya memang masih tahap interview
        const interviewStatuses = ['Menunggu', 'Lolos_Interview_1', 'Lolos_Interview_2', 'Lolos_Interview_3'];
        document.getElementById('amInterviewActions').style.display = interviewStatuses.includes(item.status) ? '' : 'none';

        document.getElementById('manualStatus').value = ['Menunggu', 'Diterima', 'Ditolak'].includes(item.status) ? item.status : 'Menunggu';
        document.getElementById('manualCatatan').value = item.catatan_admin || '';

        hideSubForms();
    }

    function getInterviewStep(status) {
        const m = status.match(/Interview_(\d)/);
        return m ? parseInt(m[1]) : 0;
    }

    function showSubForm(type) {
        document.getElementById('subFormLolos').style.display = type === 'lolos' ? '' : 'none';
        document.getElementById('subFormTolak').style.display = type === 'tolak' ? '' : 'none';
    }
    function hideSubForms() {
        document.getElementById('subFormLolos').style.display = 'none';
        document.getElementById('subFormTolak').style.display = 'none';
    }

    function submitLolos() {
        const step = getInterviewStep(currentModalItem.status);
        const nextStep = step + 1;
        const targetStatus = nextStep > 3 ? 'Lolos_Final' : ('Lolos_Interview_' + nextStep);
        const jadwalRaw = document.getElementById('lolosJadwal').value; // format: YYYY-MM-DDTHH:MM
        submitStatusChange(targetStatus, {
            catatan: document.getElementById('lolosCatatan').value,
            jadwal: jadwalRaw,
            link_zoom: document.getElementById('lolosZoom').value
        });
    }

    function submitTolak() {
        const step = getInterviewStep(currentModalItem.status);
        const targetStatus = step > 0 ? ('Tidak_Lolos_Interview_' + step) : 'Ditolak';
        submitStatusChange(targetStatus, { catatan: document.getElementById('tolakCatatan').value });
    }

    function submitManual() {
        const targetStatus = document.getElementById('manualStatus').value;
        submitStatusChange(targetStatus, { catatan: document.getElementById('manualCatatan').value });
    }

    function submitStatusChange(targetStatus, params) {
        const id = currentModalId;
        apiGet(buildUrl(id, targetStatus, params)).then(json => {
            if (!json.success) {
                Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
                return;
            }

            const statusCell = document.getElementById('status-cell-' + id);
            if (statusCell && json.badge_html !== undefined) statusCell.innerHTML = json.badge_html;

            getActionModal().hide();
            toast('success', json.message);

            const hasEmail = json.item && json.item.email;
            askFollowUps(id, !!hasEmail);
        });
    }

    // Setelah simpan: tanya kirim email? lalu tanya kirim WA? (berbentuk popup, bukan checkbox)
    function askFollowUps(id, hasEmail) {
        let chain = Promise.resolve();

        if (hasEmail) {
            chain = chain.then(() => Swal.fire({
                icon: 'question',
                title: 'Kirim Notifikasi Email?',
                text: 'Kirim email otomatis ke kandidat sekarang?',
                showCancelButton: true,
                confirmButtonText: 'Ya, kirim',
                cancelButtonText: 'Tidak'
            })).then(res => {
                if (res.isConfirmed) {
                    return apiGet(buildUrl(id, 'email')).then(j => {
                        toast(j.success ? 'success' : 'error', j.message);
                    });
                }
            });
        }

        chain.then(() => Swal.fire({
            icon: 'question',
            title: 'Kirim Pengingat WhatsApp?',
            text: 'Buka WhatsApp dengan pesan siap-kirim untuk kandidat ini?',
            showCancelButton: true,
            confirmButtonText: 'Buka WhatsApp',
            cancelButtonText: 'Nanti saja'
        })).then(res => {
            if (res.isConfirmed) openWaLink(id);
        });
    }

    function sendEmailNow() {
        apiGet(buildUrl(currentModalId, 'email')).then(j => {
            toast(j.success ? 'success' : 'error', j.message);
        });
    }

    // ===================== WHATSAPP =====================
    function openWaLink(id) {
        apiGet(buildUrl(id, 'wa')).then(json => {
            if (!json.success || !json.url) {
                Swal.fire('Info', json.message || 'Nomor WhatsApp tidak valid / tidak tersedia.', 'info');
                return;
            }
            window.open(json.url, '_blank');
        });
    }

    // ===================== HAPUS DATA =====================
    function hapusData(id, fromModal) {
        Swal.fire({
            icon: 'warning',
            title: 'Hapus data ini secara permanen?',
            text: 'Data dan berkas terkait akan dihapus permanen dan tidak bisa dikembalikan.',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545'
        }).then(result => {
            if (!result.isConfirmed) return;

            fetch(`<?= site_url('admin/hapus/') ?>${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(async res => {
                    const json = await res.json().catch(() => ({ success: false, message: 'Respon server tidak valid.' }));
                    return json;
                })
                .then(json => {
                    if (!json.success) {
                        Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
                        return;
                    }
                    if (fromModal) getActionModal().hide();
                    const row = document.getElementById('row-' + id);
                    if (row) row.remove();
                    toast('success', 'Data berhasil dihapus.');
                })
                .catch(() => Swal.fire('Gagal', 'Tidak dapat menghubungi server.', 'error'));
        });
    }

    // ===================== PULIHKAN DARI ARSIP =====================
    function restoreData(id) {
        apiGet(buildUrl(id, 'restore')).then(json => {
            if (!json.success) {
                Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
                return;
            }
            const row = document.getElementById('row-' + id);
            if (row) row.remove();
            toast('success', json.message || 'Data berhasil dipulihkan.');
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
        const baseUrl = '<?= site_url('admin/dashboard') ?>';

        function buildSearchUrl(keyword) {
            const url = new URL(baseUrl);
            if (keyword) url.searchParams.set('keyword', keyword);
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
            })
            .catch(err => {
                console.error(err);
                tableContainer.style.opacity = '1';
                Swal.fire('Error', 'Gagal memuat data pencarian.', 'error');
            });
        }

        function handleSearch() {
            const val = searchInput.value.trim();
            resetBtn.style.display = val ? 'block' : 'none';
            loadData(buildSearchUrl(val));
        }

        // Handle typing (debounce)
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            resetBtn.style.display = this.value.trim() ? 'block' : 'none';
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

        // Handle reset button
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                searchInput.value = '';
                this.style.display = 'none';
                loadData(buildSearchUrl(''));
            });
        }

        // Handle pagination links clicks via event delegation
        tableContainer.addEventListener('click', function(e) {
            const paginationLink = e.target.closest('.ajax-pagination a');
            if (paginationLink) {
                e.preventDefault();
                loadData(paginationLink.href);
            }
        });
    })();

    // ===================== TOGGLE STATUS PENDAFTARAN (POPUP, BUKAN confirm()) =====================
    document.querySelectorAll('.js-toggle-registration').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const text = this.getAttribute('data-confirm-text');
            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi',
                text: text,
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) window.location.href = url;
            });
        });
    });
</script>

<footer class="sticky-footer bg-white mt-5">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>&copy; 2026 IOH Future Talent Program - Admin Panel</span>
        </div>
    </div>
</footer>
<?= $this->endSection() ?>