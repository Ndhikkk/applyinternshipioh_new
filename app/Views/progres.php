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

    <!-- Flash Message -->
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
    <div class="row justify-content-center mb-5">
        <div class="col-lg-6">
            <div class="card search-card border-0 shadow-sm" data-aos="fade-up">
                <div class="card-body p-4">
                    <!-- PASTIKAN ACTION MENGGUNAKAN 'cek' BUKAN 'cekStatus' -->
                    <form action="<?= base_url('progres/cek') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="nama" class="form-label fw-semibold">
                                <i class="bi bi-person me-2 text-indosat"></i>Nama Lengkap
                            </label>
                            <input type="text" class="form-control form-control-lg" id="nama" name="nama"
                                placeholder="Masukkan nama lengkap Anda" required value="<?= old('nama') ?>">
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
                            <i class="bi bi-file-earmark-person me-2"></i>Data Pendaftaran
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row justify-content-center">
                            <div class="col-md-8 text-center">
                                <div class="p-5 bg-light rounded-4 border-2">
                                    <h5 class="text-muted mb-2">Nama Lengkap</h5>
                                    <h3 class="fw-bold text-dark mb-4 display-6">
                                        <?= esc($pendaftaran['nama_lengkap'] ?? 'Tidak tersedia') ?>
                                    </h3>

                                    <h5 class="text-muted mb-3">Status Pendaftaran</h5>
                                    <?php
                                    $statusClass = [
                                        'Menunggu' => 'bg-warning',
                                        'Diterima' => 'bg-success',
                                        'Ditolak' => 'bg-danger',
                                        'Lolos_Interview_1' => 'bg-info',
                                        'Lolos_Interview_2' => 'bg-info',
                                        'Lolos_Interview_3' => 'bg-info',
                                        'Lolos_Final' => 'bg-success',
                                        'Tidak_Lolos_Interview_1' => 'bg-danger',
                                        'Tidak_Lolos_Interview_2' => 'bg-danger',
                                        'Tidak_Lolos_Interview_3' => 'bg-danger'
                                    ];
                                    $currentStatus = $pendaftaran['status'] ?? 'Tidak tersedia';
                                    $badgeClass = $statusClass[$currentStatus] ?? 'bg-secondary';
                                    ?>
                                    <div>
                                        <span class="badge <?= $badgeClass ?> fs-4 px-5 py-3 rounded-pill shadow-sm">
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
                                    <button onclick="window.print()" class="btn btn-outline-secondary">
                                        <i class="bi bi-printer me-2"></i>Cetak Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                        <p class="text-muted mb-4">Data pendaftaran dengan nama "<?= esc($searchTerm ?? '') ?>" tidak
                            ditemukan. Silakan periksa kembali nama yang Anda masukkan.</p>
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
    .bg-indosat {
        background: linear-gradient(135deg, #E00034 0%, #8B1A3A 100%) !important;
    }

    .text-primary {
        color: #E00034 !important;
    }

    .info-value .bi {
        font-size: 0.9em;
    }

    .search-card,
    .result-card {
        border-radius: 15px;
        border: none;
    }

    .bg-indosat {
        background: linear-gradient(135deg, #E00034 0%, #8B1A3A 100%) !important;
    }

    .btn-indosat {
        background: linear-gradient(135deg, #E00034 0%, #8B1A3A 100%);
        border: none;
        color: white;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-indosat:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(224, 0, 52, 0.3);
        color: white;
    }

    .btn-outline-indosat {
        border: 2px solid #E00034;
        color: #E00034;
        background: transparent;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-outline-indosat:hover {
        background: #E00034;
        color: white;
        transform: translateY(-2px);
    }

    .info-section {
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 10px;
        border-left: 4px solid #E00034;
    }

    .info-item {
        padding-bottom: 0.5rem;
    }

    .info-value {
        font-weight: 500;
        color: #2C3E50;
        font-size: 1rem;
    }

    .text-indosat {
        color: #E00034 !important;
    }

    .badge {
        font-size: 0.8rem;
        padding: 0.5rem 0.8rem;
    }

    .form-control {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #E00034;
        box-shadow: 0 0 0 0.2rem rgba(224, 0, 52, 0.15);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem;
        }

        .info-section {
            padding: 1rem;
        }

        .btn-indosat,
        .btn-outline-indosat {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Focus on search input
        const searchInput = document.getElementById('nama');
        if (searchInput) {
            searchInput.focus();
        }

        // Add loading state to form
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function () {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mencari...';
                    submitBtn.disabled = true;
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>