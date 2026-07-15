<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Pendaftaran Berhasil<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-body text-center p-5">
                    <!-- Icon Status -->
                    <div class="text-success mb-4">
                        <i class="bi bi-check-circle-fill" style="font-size: 4.5rem;"></i>
                    </div>
                    
                    <!-- Judul Utama -->
                    <h2 class="card-title fw-bold text-success mb-3">Pendaftaran Berhasil!</h2>
                    
                    <!-- Informasi Email -->
                    <p class="text-muted mb-4 fs-6">
                        Silakan periksa kotak masuk atau folder spam pada email Anda secara berkala untuk menerima Token Pendaftaran.
                    </p>
                    
                    <!-- Peringatan Keamanan Token -->
                    <div class="alert alert-warning border-0 rounded-3 mb-4 py-2 px-3 fs-7" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Penting:</strong> Jangan membagikan token pendaftaran Anda kepada siapa pun.
                    </div>

                    <!-- Flashdata System Info -->
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-info border-0 rounded-3 mb-4">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <!-- Tombol Navigasi -->
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <a href="<?= base_url('progres') ?>" class="btn btn-primary px-4 py-2 fw-semibold">
                            <i class="bi bi-search me-2"></i> Cek Status
                        </a>
                        <a href="<?= base_url() ?>" class="btn btn-light px-4 py-2 fw-semibold text-secondary">
                            <i class="bi bi-house me-2"></i> Kembali ke Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>