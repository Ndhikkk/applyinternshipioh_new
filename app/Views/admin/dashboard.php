<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Dashboard Admin - IOH Semarang<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">
                <i class="bi bi-speedometer2 text-indosat"></i> <?= $is_arsip ? 'Arsip Data' : 'Dashboard Admin' ?>
            </h1>
            <p class="text-muted mb-0"><?= $is_arsip ? 'Data yang menunggu dihapus permanen (7 hari sejak diarsipkan, bisa dipulihkan)' : 'Kelola pendaftaran magang IOH Semarang' ?></p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <div class="me-3">
                <span class="fw-bold me-2">Status Pendaftaran:</span>
                <?php if ($registration_open == '1'): ?>
                    <a href="<?= site_url('admin/toggle-registration') ?>" class="btn btn-success btn-sm rounded-pill px-3 js-toggle-registration" data-confirm-text="Tutup pendaftaran magang?">
                        <i class="bi bi-unlock-fill me-1"></i> DIBUKA
                    </a>
                <?php else: ?>
                    <a href="<?= site_url('admin/toggle-registration') ?>" class="btn btn-danger btn-sm rounded-pill px-3 js-toggle-registration" data-confirm-text="Buka pendaftaran magang?">
                        <i class="bi bi-lock-fill me-1"></i> DITUTUP
                    </a>
                <?php endif; ?>
            </div>

            <a href="<?= site_url('admin/dashboard' . ($is_arsip ? '' : '?arsip=1')) ?>" class="btn btn-sm <?= $is_arsip ? 'btn-primary' : 'btn-outline-secondary' ?>">
                <i class="bi bi-archive"></i> <?= $is_arsip ? 'Kembali ke Data Aktif' : 'Arsip (' . $total_arsip . ')' ?>
            </a>

            <a href="<?= site_url('admin/export') ?>" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
             <a href="<?= site_url('admin/parsing-cv') ?>" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> genrate CV
            </a>

            <a href="<?= site_url('admin/logout') ?>" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
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
<div class="card border-0 shadow-sm" data-aos="fade-up">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-table"></i> <?= $is_arsip ? 'Data Arsip (menunggu hapus permanen)' : 'Data Pendaftar Magang' ?>
        </h6>
        <div class="d-flex gap-2">
            <form method="get" action="<?= site_url('admin/dashboard') ?>" class="input-group input-group-sm" id="searchForm" style="width: 280px;">
                <?php if ($is_arsip): ?><input type="hidden" name="arsip" value="1"><?php endif; ?>
                <input type="text" name="keyword" id="searchInput" class="form-control" placeholder="Cari nama, Email, kampus, token..." value="<?= esc($keyword ?? '') ?>" autocomplete="off">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                <?php if (!empty($keyword)): ?>
                    <a href="<?= site_url('admin/dashboard' . ($is_arsip ? '?arsip=1' : '')) ?>" class="btn btn-outline-danger" title="Reset pencarian"><i class="bi bi-x"></i></a>
                <?php endif; ?>
            </form>
            <button class="btn btn-outline-primary btn-sm" onclick="location.reload()" title="Refresh">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th class="fw-bold" style="min-width:220px;">Kandidat</th>
                        <th class="fw-bold text-nowrap" style="min-width:170px;">Akademik</th>
                        <th class="fw-bold text-nowrap">Divisi / Jenis</th>
                        <th class="fw-bold text-center" style="min-width:140px;">Status</th>
                        <th class="fw-bold text-nowrap">Periode Magang</th>
                        <th class="fw-bold text-nowrap">Tanggal Daftar</th>
                        <th class="fw-bold text-center">Berkas</th>
                        <th class="fw-bold text-center text-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pendaftaran)): ?>
                        <?php foreach ($pendaftaran as $data): ?>
                            <?php
                                $currentStatus = $data['status'];
                                $isFinalStage = in_array($currentStatus, ['Diterima', 'Lolos_Final']);
                                $isRejected = in_array($currentStatus, ['Ditolak', 'Tidak_Lolos_Interview_1', 'Tidak_Lolos_Interview_2', 'Tidak_Lolos_Interview_3']);
                                $badgeClass = match (true) {
                                    $isFinalStage => 'bg-success',
                                    $isRejected => 'bg-danger',
                                    $currentStatus === 'Lolos_Interview_1' => 'bg-primary',
                                    $currentStatus === 'Lolos_Interview_2' => 'bg-info',
                                    $currentStatus === 'Lolos_Interview_3' => 'bg-purple',
                                    default => 'bg-warning',
                                };
                                $step = 0;
                                if (preg_match('/Interview_(\d)/', $currentStatus, $m)) { $step = (int) $m[1]; }
                                $jadwalKey = 'jadwal_interview_' . $step;
                            ?>
                            <tr data-aos="fade-in" id="row-<?= $data['id'] ?>">
                                <td>
                                    <div class="small text-muted font-monospace"><?= esc($data['token_pendaftaran'] ?? '-') ?></div>
                                    <div class="fw-semibold"><?= esc($data['nama_lengkap']) ?></div>
                                    <div class="small">
                                        <a href="mailto:<?= esc($data['email'] ?? '') ?>" class="text-decoration-none" title="Kirim Email">
                                            <i class="bi bi-envelope"></i> <?= esc($data['email'] ?? 'Tidak ada Email') ?>
                                        </a>
                                    </div>
                                    <div class="small text-muted"><i class="bi bi-whatsapp"></i> <?= esc($data['nomor_whatsapp']) ?></div>
                                </td>
                                <td class="text-nowrap">
                                    <div><?= esc($data['asal_kampus']) ?></div>
                                    <div class="small text-muted"><?= esc($data['program_studi']) ?></div>
                                    <span class="badge bg-light text-dark">Smt <?= esc($data['semester']) ?></span>
                                </td>
                                <td class="text-nowrap">
                                    <div><?= esc($data['divisi_pilihan'] ?? '-') ?></div>
                                    <span class="badge <?= $data['jenis_magang'] == 'Wajib' ? 'bg-info' : 'bg-secondary' ?>">
                                        <?= esc($data['jenis_magang']) ?>
                                    </span>
                                </td>
                                <td class="text-center" id="status-cell-<?= $data['id'] ?>">
                                    <span class="badge <?= $badgeClass ?>"><?= str_replace('_', ' ', esc($currentStatus)) ?></span>
                                    <?php if ($step > 0 && !empty($data[$jadwalKey])): ?>
                                        <div class="small text-muted mt-1">
                                            <i class="bi bi-calendar-event"></i> <?= date('d/m/Y H:i', strtotime($data[$jadwalKey])) ?> WIB
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($is_arsip && !empty($data['archived_at'])):
                                        $deleteAt = strtotime($data['archived_at']) + (7 * 86400);
                                        $daysLeft = max(0, ceil(($deleteAt - time()) / 86400));
                                    ?>
                                        <div class="small text-danger mt-1">
                                            <i class="bi bi-hourglass-split"></i> Hapus permanen <?= $daysLeft ?> hari lagi
                                        </div>
                                        <div class="small text-muted"><?= esc($data['archived_reason'] ?? '') ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap">
                                    <small>
                                        <?= !empty($data['periode_mulai']) ? date('d/m/Y', strtotime($data['periode_mulai'])) : '-' ?> -<br>
                                        <?= !empty($data['periode_selesai']) ? date('d/m/Y', strtotime($data['periode_selesai'])) : '-' ?>
                                    </small>
                                </td>
                                <td class="text-nowrap">
                                    <small>
                                        <i class="bi bi-calendar3"></i><br>
                                        <?php
                                        $tanggalDaftar = $data['created_at'] ?? '';
                                        if ($tanggalDaftar && trim($tanggalDaftar) !== ''):
                                            $date = new DateTime($tanggalDaftar, new DateTimeZone('UTC'));
                                            $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                        ?>
                                            <?= $date->format('d/m/Y H:i') ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td class="text-nowrap">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= site_url('admin/download/' . $data['id'] . '/cv') ?>"
                                        class="btn btn-outline-primary <?= empty($data['cv']) ? 'disabled' : '' ?>"
                                        title="<?= empty($data['cv']) ? 'CV tidak tersedia' : 'Download CV' ?>"
                                        <?= empty($data['cv']) ? 'onclick="return false;"' : '' ?>>
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                        <a href="<?= site_url('admin/download/' . $data['id'] . '/surat') ?>"
                                        class="btn btn-outline-success <?= empty($data['surat_pengantar']) ? 'disabled' : '' ?>"
                                        title="<?= empty($data['surat_pengantar']) ? 'Surat tidak tersedia' : 'Download Surat Pengantar' ?>"
                                        <?= empty($data['surat_pengantar']) ? 'onclick="return false;"' : '' ?>>
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>
                                        <a href="<?= site_url('admin/download/' . $data['id'] . '/ktm') ?>"
                                        class="btn btn-outline-info <?= empty($data['ktm']) ? 'disabled' : '' ?>"
                                        title="<?= empty($data['ktm']) ? 'KTM tidak tersedia' : 'Download KTM' ?>"
                                        <?= empty($data['ktm']) ? 'onclick="return false;"' : '' ?>>
                                            <i class="bi bi-card-image"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-nowrap text-center" id="aksi-cell-<?= $data['id'] ?>">
                                    <div class="d-flex gap-1 justify-content-center flex-nowrap">
                                        <?php if ($is_arsip): ?>
                                            <button type="button" class="btn btn-outline-success btn-sm" onclick="restoreData(<?= $data['id'] ?>)" title="Pulihkan ke Data Aktif">
                                                <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="hapusData(<?= $data['id'] ?>)" title="Hapus Permanen Sekarang">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="openActionModal(<?= $data['id'] ?>, '<?= esc($data['nama_lengkap'], 'js') ?>')" title="Lihat Detail & Kelola Status">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm" onclick="openWaLink(<?= $data['id'] ?>)" title="Ingatkan via WhatsApp">
                                                <i class="bi bi-whatsapp"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="hapusData(<?= $data['id'] ?>)" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                    <?php if ($is_arsip): ?>
                                        <h5>Arsip kosong</h5>
                                        <p>Tidak ada data yang sedang menunggu hapus permanen.</p>
                                    <?php else: ?>
                                        <h5>Belum ada data pendaftaran</h5>
                                        <p>Data pendaftaran akan muncul di sini setelah ada yang mendaftar.</p>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($pager)): ?>
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <small class="text-muted">
                    Halaman <?= $pager->getCurrentPage('pendaftaran') ?> dari <?= max($pager->getPageCount('pendaftaran'), 1) ?>
                    (Total <?= $total_pendaftar ?> data, 15/halaman)
                </small>
                <?= $pager->links('pendaftaran', 'default_full') ?>
            </div>
        <?php endif; ?>
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
.table th { border-top: none; font-weight: 600; color: #2C3E50; background-color: #f8f9fa; }
.table td { vertical-align: middle; padding: 12px 8px; }
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

    // ===================== PENCARIAN LANGSUNG (DEBOUNCE) =====================
    // Ketik -> otomatis submit form pencarian setelah jeda singkat, tidak perlu
    // menekan tombol/Enter dulu.
    (function () {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        if (!searchInput || !searchForm) return;

        let debounceTimer = null;
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => searchForm.submit(), 600);
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