<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Admin Login - IOH Semarang<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="form-card" data-aos="zoom-in">
                <div class="card-header text-white text-center py-4">
                    <img src="<?= base_url('assets/img/tone-indosat.png') ?>" alt="IOH Semarang" class="brand-logo"
                        style="filter: none !important;">
                    <h4 class="mb-0">Admin Login</h4>
                    <p class="mb-0 mt-2">IOH Semarang</p>
                </div>
                <div class="card-body p-5">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i> <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= base_url('admin/login') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-4">
                            <label for="username" class="form-label fw-bold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-person text-indosat"></i>
                                </span>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?= old('username') ?>" placeholder="Masukkan username" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-lock text-indosat"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Masukkan password" required>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-indosat btn-lg py-3">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="<?= base_url() ?>" class="text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Kembali ke Home
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>