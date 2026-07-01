<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Dashboard Admin - IOH Semarang<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">
                <i class="bi bi-speedometer2 text-indosat"></i> Dashboard Admin
            </h1>
            <p class="text-muted mb-0">Kelola pendaftaran magang IOH Semarang</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <!-- Registration Status Toggle -->
            <div class="me-3">
                <span class="fw-bold me-2">Status Pendaftaran:</span>
                <?php if ($registration_open == '1'): ?>
                    <a href="<?= base_url('admin/toggle-registration') ?>" class="btn btn-success btn-sm rounded-pill px-3" onclick="return confirm('Tutup pendaftaran magang?')">
                        <i class="bi bi-unlock-fill me-1"></i> DIBUKA
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('admin/toggle-registration') ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('Buka pendaftaran magang?')">
                        <i class="bi bi-lock-fill me-1"></i> DITUTUP
                    </a>
                <?php endif; ?>
            </div>

            <a href="<?= base_url('admin/export') ?>" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="<?= base_url('admin/logout') ?>" class="btn btn-outline-danger btn-sm">
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pendaftar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_pendaftar ?></div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success me-2">
                                    <i class="bi bi-arrow-up"></i> Semua waktu
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people display-6 text-indosat"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Diterima</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_diterima ?></div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success me-2">
                                    <i class="bi bi-check-circle"></i> Final
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle display-6 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menunggu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_menunggu ?></div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-warning me-2">
                                    <i class="bi bi-clock"></i> Dalam proses
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock display-6 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Ditolak</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_ditolak ?></div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-danger me-2">
                                    <i class="bi bi-x-circle"></i> Selesai
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-x-circle display-6 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- Data Table -->
<div class="card border-0 shadow-sm" data-aos="fade-up">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-table"></i> Data Pendaftar Magang
        </h6>
        <div class="d-flex gap-2">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari nama, Email, kampus...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <button class="btn btn-outline-primary btn-sm" onclick="refreshTable()" title="Refresh">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th class="fw-bold text-center">Nama</th>
                        <th class="fw-bold text-center">Email</th>
                        <th class="fw-bold text-nowrap">Jenis Magang</th>
                        <th class="fw-bold text-nowrap">Universitas</th>
                        <th class="fw-bold text-nowrap">Program Studi</th>
                        <th class="fw-bold text-nowrap">Semester</th>
                        <th class="fw-bold text-center">Status</th>
                        <th class="fw-bold text-nowrap">Periode Magang</th>
                        <th class="fw-bold text-nowrap">Tanggal Daftar</th>
                        <th class="fw-bold text-center">Berkas</th>
                        <th class="fw-bold text-center text-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pendaftaran)): ?>
                        <?php foreach ($pendaftaran as $data): ?>
                            <tr data-aos="fade-in">
                                <td class="fw-semibold text-nowrap"><?= esc($data['nama_lengkap']) ?></td>
                                <td class="text-nowrap">
                                    <a href="mailto:<?= esc($data['email'] ?? '') ?>" class="text-decoration-none" title="Kirim Email">
                                        <?= esc($data['email'] ?? 'Tidak ada Email') ?>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= $data['jenis_magang'] == 'Wajib' ? 'bg-info' : 'bg-secondary' ?>">
                                        <?= esc($data['jenis_magang']) ?>
                                    </span>
                                </td>
                                <td class="text-center"><?= esc($data['asal_kampus']) ?></td>
                                <td class="text-center"><?= esc($data['program_studi']) ?></td>
                                <td class="text-nowrap">
                                    <span class="badge bg-light text-dark">Smt <?= esc($data['semester']) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge 
                                        <?= in_array($data['status'], ['Diterima', 'Lolos_Final']) ? 'bg-success' :
                                            (in_array($data['status'], ['Ditolak', 'Tidak_Lolos_Interview_1', 'Tidak_Lolos_Interview_2', 'Tidak_Lolos_Interview_3']) ? 'bg-danger' : 'bg-warning') ?>">
                                        <?= str_replace('_', ' ', esc($data['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    <small>
                                        <?= date('d/m/Y', strtotime($data['periode_mulai'])) ?> -<br>
                                        <?= date('d/m/Y', strtotime($data['periode_selesai'])) ?>
                                    </small>
                                </td>
                                <td class="text-nowrap">
                                    <small>
                                        <i class="bi bi-calendar3"></i><br>
                                        <?php
                                        $tanggalDaftar = $data['created_at'] ?? '';
                                        if ($tanggalDaftar && trim($tanggalDaftar) !== ''):
                                            // Set timezone ke Asia/Jakarta
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
                                        <!-- Tombol CV -->
                                        <a href="<?= base_url('admin/download/' . $data['id'] . '/cv') ?>" 
                                        class="btn btn-outline-primary <?= empty($data['cv']) ? 'disabled' : '' ?>" 
                                        title="<?= empty($data['cv']) ? 'CV tidak tersedia' : 'Download CV' ?>"
                                        <?= empty($data['cv']) ? 'onclick="return false;"' : '' ?>>
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>

                                        <!-- Tombol Surat Pengantar -->
                                        <a href="<?= base_url('admin/download/' . $data['id'] . '/surat') ?>" 
                                        class="btn btn-outline-success <?= empty($data['surat_pengantar']) ? 'disabled' : '' ?>" 
                                        title="<?= empty($data['surat_pengantar']) ? 'Surat tidak tersedia' : 'Download Surat Pengantar' ?>"
                                        <?= empty($data['surat_pengantar']) ? 'onclick="return false;"' : '' ?>>
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>

                                        <!-- Tombol KTM -->
                                        <a href="<?= base_url('admin/download/' . $data['id'] . '/ktm') ?>" 
                                        class="btn btn-outline-info <?= empty($data['ktm']) ? 'disabled' : '' ?>" 
                                        title="<?= empty($data['ktm']) ? 'KTM tidak tersedia' : 'Download KTM' ?>"
                                        <?= empty($data['ktm']) ? 'onclick="return false;"' : '' ?>>
                                            <i class="bi bi-card-image"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-nowrap">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <?php
                                        $currentStatus = $data['status'];
                                        $isInterviewStage = in_array($currentStatus, ['Menunggu', 'Lolos_Interview_1', 'Lolos_Interview_2', 'Lolos_Interview_3']);
                                        $isFinalStage = in_array($currentStatus, ['Diterima', 'Lolos_Final']);
                                        $isRejected = in_array($currentStatus, ['Ditolak', 'Tidak_Lolos_Interview_1', 'Tidak_Lolos_Interview_2', 'Tidak_Lolos_Interview_3']);
                                        ?>

                                        <?php if ($isInterviewStage): ?>
                                            <!-- Tombol Lolos ke Tahap Selanjutnya -->
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="processInterview(<?= $data['id'] ?>, 'lolos')"
                                                title="Lolos ke Tahap Selanjutnya">
                                                <i class="bi bi-check-lg"></i>
                                            </button>

                                            <!-- Tombol Tidak Lolos -->
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="processInterview(<?= $data['id'] ?>, 'tolak')" title="Tidak Lolos">
                                                <i class="bi bi-x-lg"></i>
                                            </button>

                                        <?php elseif ($isFinalStage): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-trophy"></i> <?= str_replace('_', ' ', $data['status']) ?>
                                            </span>

                                        <?php elseif ($isRejected): ?>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle"></i> <?= str_replace('_', ' ', $data['status']) ?>
                                            </span>

                                        <?php else: ?>
                                            <span class="badge bg-warning">
                                                <?= str_replace('_', ' ', $data['status']) ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <!-- Tombol Detail -->
                                        <a href="<?= base_url('admin/detail/' . $data['id']) ?>" class="btn btn-primary btn-sm" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <!-- Tombol Hapus -->
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="hapusData(<?= $data['id'] ?>)"
                                            title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                    <h5>Belum ada data pendaftaran</h5>
                                    <p>Data pendaftaran akan muncul di sini setelah ada yang mendaftar.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.stats-card {
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #2C3E50;
    background-color: #f8f9fa;
}

.table td {
    vertical-align: middle;
    padding: 12px 8px;
}

.badge {
    font-size: 0.75em;
    padding: 6px 10px;
}

.card {
    border: none;
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    border-bottom: 1px solid #e3e6f0;
    background-color: white !important;
    
}
.btn-outline-danger {
    border-color: #dc3545;
    color: #dc3545;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    color: white;
}
</style>

<script>
    function refreshTable() {
        const btn = event.target;
        btn.innerHTML = '<span class="loading-spinner"></span>';
        setTimeout(() => {
            location.reload();
        }, 500);
    }

    // Simple search functionality
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#dataTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    function hapusData(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            window.location.href = `<?= base_url('admin/hapus/') ?>${id}`;
        }
    }

    function processInterview(id, action) {
        let catatan = '';
        let jadwal = '';

        if (action === 'lolos') {
            catatan = prompt('Masukkan catatan untuk kandidat (opsional):');
            if (catatan === null) return;

            jadwal = prompt('Masukkan jadwal interview selanjutnya (format: YYYY-MM-DD HH:MM) atau kosongkan:');
        } else {
            catatan = prompt('Masukkan alasan tidak lolos (opsional):');
            if (catatan === null) return;
        }

        const url = `<?= base_url('admin/process-interview/') ?>${id}/${action}?catatan=${encodeURIComponent(catatan)}&jadwal=${encodeURIComponent(jadwal)}`;
        window.location.href = url;
    }
</script>

<footer class="sticky-footer bg-white mt-5">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>&copy; 2025 IOH Semarang - Admin Panel</span>
        </div>
    </div>
</footer>
<?= $this->endSection() ?>