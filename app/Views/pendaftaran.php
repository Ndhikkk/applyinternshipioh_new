<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Pendaftaran Program - IOH <?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="form-card" data-aos="fade-up">
                <div class="card-header text-white">
                    <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Form Pendaftaran</h4>
                    <span class="mb-0 mt-2">Tumbuh Bersama IOH</span>
                </div>
                <div class="card-body p-4">
                    <!-- Status Notification -->
                    <?php if (isset($registration_open) && $registration_open == '0'): ?>
                        <div class="alert alert-warning text-center py-5 mb-0">
                            <i class="bi bi-clock-history display-1 text-warning mb-3"></i>
                            <h4 class="alert-heading fw-bold mb-3">Pendaftaran Ditutup</h4>
                            <p class="mb-0 fs-5">
                                Terima kasih atas ketertarikan Anda. Saat ini kami belum membuka Program.<br>
                                Informasi pembukaan berikutnya akan kami umumkan di halaman ini.
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-megaphone-fill me-2"></i>
                            <strong>Pendaftaran Dibuka!</strong>
                            Pendaftaran program telah dibuka. Silakan lengkapi data diri Anda dan unggah dokumen yang diperlukan sebelum batas waktu pendaftaran.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    
                        <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <p class="mb-1"><?= $error ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= base_url('pendaftaran') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <!-- Data Pribadi -->
                        <h5 class="mb-3"><i class="bi bi-person"></i> Data Pribadi</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap" class="form-control"
                                    value="<?= old('nama_lengkap') ?>" placeholder="Masukkan nama lengkap" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control"
                                    value="<?= old('email') ?>" placeholder="nama@email.com" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor WhatsApp *</label>
                                <input type="text" name="nomor_whatsapp" class="form-control"
                                    value="<?= old('nomor_whatsapp') ?>" placeholder="081234567890" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Asal Kampus *</label>
                                <input type="text" name="asal_kampus" class="form-control"
                                    value="<?= old('asal_kampus') ?>" placeholder="Nama kampus" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Program Studi *</label>
                                <input type="text" name="program_studi" class="form-control"
                                    value="<?= old('program_studi') ?>" placeholder="Program studi" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Semester *</label>
                                <input type="number" name="semester" class="form-control" value="<?= old('semester') ?>"
                                    min="1" max="14" placeholder="Semester saat ini" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Magang *</label>
                                <select name="jenis_magang" class="form-select" required>
                                    <option value="">Pilih Jenis Magang</option>
                                    <option value="Wajib" <?= old('jenis_magang') == 'Wajib' ? 'selected' : '' ?>>Magang Wajib</option>
                                    <option value="Mandiri" <?= old('jenis_magang') == 'Mandiri' ? 'selected' : '' ?>>Magang Mandiri</option>
                                </select>
                                <div class="form-text">
                                    <strong>Magang Wajib:</strong> Magang yang merupakan bagian dari kurikulum kampus<br>
                                    <strong>Magang Mandiri:</strong> Magang yang dilakukan secara sukarela untuk pengembangan skill
                                </div>
                            </div>

                           <div class="col-md-6 mb-3">
                                <label class="small fw-bold text-secondary">Divisi Pilihan *</label>
                                <select name="divisi_pilihan" id="form_divisi" class="form-control form-control-sm" style="border-radius: 6px;" required>
                                    <option value="">Pilih Divisi </option>
                                    <option value="Markom" <?= old('divisi_pilihan') == 'Markom' ? 'selected' : '' ?>>Markom</option>
                                    <option value="IT / Elang IT" <?= old('divisi_pilihan') == 'IT / Elang IT' ? 'selected' : '' ?>>IT / Elang IT</option>
                                    <option value="Technical" <?= old('divisi_pilihan') == 'Technical' ? 'selected' : '' ?>>Technical</option>
                                    <option value="Finance" <?= old('divisi_pilihan') == 'Finance' ? 'selected' : '' ?>>Finance</option>
                                    <option value="B2B" <?= old('divisi_pilihan') == 'B2B' ? 'selected' : '' ?>>B2B</option>
                                    <option value="Social Media 3ID & IM3" <?= old('divisi_pilihan') == 'Social Media 3ID & IM3' ? 'selected' : '' ?>>Social Media 3ID & IM3</option>
                                    <option value="Daily Project" <?= old('divisi_pilihan') == 'Daily Project' ? 'selected' : '' ?>>Daily Project</option>
                                    <option value="Project Post Paid" <?= old('divisi_pilihan') == 'Project Post Paid' ? 'selected' : '' ?>>Project Post Paid</option>
                                    <option value="Capability Building" <?= old('divisi_pilihan') == 'Capability Building' ? 'selected' : '' ?>>Capability Building</option>
                                </select>
                                <div class="form-text">
                                    Pilihlah salah satu spesifikasi divisi kerja yang paling sesuai dengan minat dan fokus keahlian akademik Anda.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Periode Magang - Mulai *</label>
                                <input type="date" name="periode_mulai" class="form-control"
                                    value="<?= old('periode_mulai') ?>" min="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Periode Magang - Selesai *</label>
                                <input type="date" name="periode_selesai" class="form-control"
                                    value="<?= old('periode_selesai') ?>" min="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- File Upload Section -->
                        <h5 class="mb-3"><i class="bi bi-cloud-upload"></i> Upload Berkas</h5>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Curriculum Vitae (CV) *</label>
                                <input type="file" name="cv" class="form-control" accept=".pdf" required>
                                <div class="form-text">Format: PDF, Maksimal: 2MB</div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Surat Pengantar</label>
                                <input type="file" name="surat_pengantar" class="form-control" accept=".pdf">
                                <div class="form-text">Format: PDF, Maksimal: 2MB<br><em>Opsional untuk magang mandiri</em></div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kartu Tanda Mahasiswa (KTM)</label>
                                <input type="file" name="ktm" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text">Format: PDF/JPG/PNG, Maksimal: 4MB<br><em>Opsional</em></div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle"></i> Informasi Upload Berkas:</h6>
                            <ul class="mb-0">
                                <li><strong>CV:</strong> Wajib diupload oleh semua pendaftar</li>
                                <li><strong>Surat Pengantar:</strong> Wajib untuk magang wajib, opsional untuk magang mandiri</li>
                                <li><strong>KTM:</strong> Opsional untuk semua jenis magang</li>
                                <li>File CV dan Surat Pengantar harus dalam format PDF</li>
                                <li>File KTM dapat berupa PDF, JPG, atau PNG</li>
                                <li>Total maksimal semua file: 8MB</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send"></i> Kirim Pendaftaran
                            </button>
                            <a href="<?= base_url() ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali ke Home
                            </a>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 Library CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (session()->getFlashdata('errors')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: `<div style="text-align: left;">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                       </div>`,
                confirmButtonColor: '#3085d6'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: '<?= esc(session()->getFlashdata("error")) ?>',
                confirmButtonColor: '#d33'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Pendaftaran Berhasil',
                text: '<?= esc(session()->getFlashdata("success")) ?>',
                confirmButtonColor: '#3085d6'
            });
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>