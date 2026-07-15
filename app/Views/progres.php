<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Cek Progres Pendaftaran<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center" data-aos="fade-down">
                <h1 class="h2 fw-bold text-indosat mb-3">
                    <i class="bi bi-search me-2"></i>Cek Progres Pendaftaran
                </h1>
                <p class="text-muted">Pantau status pendaftaran Anda di IOH</p>
            </div>
        </div>
    </div>

    <!-- Flash Message Sukses / Gagal -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="row justify-content-center mb-3">
            <div class="col-lg-6">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="row justify-content-center mb-3">
            <div class="col-lg-6">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Form Pencarian Menggunakan Token -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-6">
            <div class="card search-card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-body p-4">
                    <form action="<?= base_url('progres/cek') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="token" class="form-label fw-semibold">
                                <i class="bi bi-key me-2 text-indosat"></i>Token Pendaftaran
                            </label>
                            <input type="text" class="form-control form-control-lg text-center fw-bold text-uppercase" id="token" name="token"
                                placeholder="Masukkan token pendaftaran Anda" required style="letter-spacing: 1px;" value="<?= old('token') ?>">
                        </div>
                        <button type="submit" class="btn btn-indosat btn-lg w-100">
                            <i class="bi bi-search me-2"></i>Cek Status Pendaftaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($pendaftaran) && is_array($pendaftaran)): ?>
        <!-- Results Section -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card result-card border-0 shadow-sm" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header bg-indosat text-white py-3">
                        <h4 class="mb-0">
                            <i class="bi bi-file-earmark-person me-2"></i>Data Pendaftaran Magang IOH
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row justify-content-center">
                            <div class="col-md-10 text-center">
                                <div class="p-5 bg-light rounded-4 border-2">
                                    <h5 class="text-muted mb-2">Nama Lengkap</h5>
                                    <h3 class="fw-bold text-dark mb-4 display-6">
                                        <?= esc($pendaftaran['nama_lengkap'] ?? 'Tidak tersedia') ?>
                                    </h3>

                                    <h5 class="text-muted mb-3">Status Pendaftaran</h5>
                                    <?php
                                    $statusClass = [
                                        'Menunggu' => 'bg-warning text-dark',
                                        'Diterima' => 'bg-success text-white',
                                        'Ditolak' => 'bg-danger text-white',
                                        'Lolos_Interview_1' => 'bg-info text-dark',
                                        'Lolos_Interview_2' => 'bg-info text-dark',
                                        'Lolos_Interview_3' => 'bg-info text-dark',
                                        'Lolos_Final' => 'bg-success text-white',
                                        'Tidak_Lolos_Interview_1' => 'bg-danger text-white',
                                        'Tidak_Lolos_Interview_2' => 'bg-danger text-white',
                                        'Tidak_Lolos_Interview_3' => 'bg-danger text-white'
                                    ];
                                    $currentStatus = $pendaftaran['status'] ?? 'Tidak tersedia';
                                    $badgeClass = $statusClass[$currentStatus] ?? 'bg-secondary text-white';
                                    ?>
                                    <div>
                                        <span class="badge <?= $badgeClass ?> fs-4 px-5 py-3 rounded-pill shadow-sm fw-bold">
                                            <?= str_replace('_', ' ', $currentStatus) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="<?= base_url('pendaftaran') ?>" class="btn btn-outline-indosat">
                                        <i class="bi bi-pencil-square me-2"></i>Daftar Baru
                                    </a>
                                    <!-- TOMBOL DIUBAH KE MODAL EMAIL -->
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#emailModal">
                                        <i class="bi bi-envelope me-2"></i>Kirim Data ke Email
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL INPUT EMAIL -->
        <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-indosat text-white">
                        <h5 class="modal-title" id="emailModalLabel"><i class="bi bi-shield-lock me-2"></i>Verifikasi Email</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?= base_url('progres/kirimEmail') ?>" method="POST">
                        <?= csrf_field() ?>
                        <!-- Kirim token secara hidden untuk verifikasi di controller -->
                        <input type="hidden" name="token_pendaftaran" value="<?= esc($pendaftaran['token_pendaftaran']) ?>">
                        
                        <div class="modal-body p-4">
                            <p class="text-muted">Untuk alasan keamanan data, masukkan alamat email yang Anda gunakan saat mendaftar Pada Future Talent Program.</p>
                            <div class="mb-3">
                                <label for="email_verifikasi" class="form-label fw-semibold">Email Terdaftar</label>
                                <input type="email" class="form-control" id="email_verifikasi" name="email" placeholder="contoh@email.com" required>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-indosat">Proses & Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php elseif (isset($notFound)): ?>
        <!-- Not Found Message -->
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm" data-aos="fade-up">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-search display-1 text-muted mb-3"></i>
                        <h4 class="text-muted mb-3">Data Tidak Ditemukan</h4>
                        <p class="text-muted mb-4">Data pendaftaran dengan token "<strong><?= esc($searchTerm ?? '') ?></strong>" tidak ditemukan.</p>
                        <a href="<?= base_url('progres') ?>" class="btn btn-indosat">
                            <i class="bi bi-arrow-left me-2"></i>Cari Lagi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .bg-indosat { background: linear-gradient(135deg, #E00034 0%, #8B1A3A 100%) !important; }
    .text-indosat { color: #E00034 !important; }
    .search-card, .result-card { border-radius: 15px; border: none; }
    .btn-indosat { background: linear-gradient(135deg, #E00034 0%, #8B1A3A 100%); border: none; color: white; font-weight: 600; border-radius: 10px; transition: all 0.3s ease; }
    .btn-indosat:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(224, 0, 52, 0.3); color: white; }
    .btn-outline-indosat { border: 2px solid #E00034; color: #E00034; background: transparent; font-weight: 600; border-radius: 10px; transition: all 0.3s ease; }
    .btn-outline-indosat:hover { background: #E00034; color: white; transform: translateY(-2px); }
    .form-control { border-radius: 10px; border: 2px solid #e9ecef; padding: 0.75rem 1rem; transition: all 0.3s ease; }
    .form-control:focus { border-color: #E00034; box-shadow: 0 0 0 0.2rem rgba(224, 0, 52, 0.15); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('token');
        if (searchInput) { searchInput.focus(); }

        // Tambah loading state saat submit kirim email di dalam modal
        const modalForm = document.querySelector('#emailModal form');
        if (modalForm) {
            modalForm.addEventListener('submit', function () {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
                    submitBtn.disabled = true;
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>