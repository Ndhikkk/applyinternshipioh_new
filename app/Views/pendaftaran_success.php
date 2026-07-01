<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Pendaftaran Berhasil<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-success shadow">
                <div class="card-body text-center p-5">
                    <div class="text-success mb-4">
                        <i class="bi bi-check-circle-fill" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="card-title text-success mb-3">Pendaftaran Berhasil!</h2>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-info">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <a href="<?= base_url('progres') ?>" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cek Status
                        </a>
                        <a href="<?= base_url() ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-house"></i> Kembali ke Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>