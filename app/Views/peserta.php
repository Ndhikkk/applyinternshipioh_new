<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h2>Daftar Peserta Magang</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Asal Kampus</th>
                        <th>Program Studi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendaftar as $p): ?>
                        <tr>
                            <td><?= esc($p['nama_lengkap']) ?></td>
                            <td><?= esc($p['asal_kampus']) ?></td>
                            <td><?= esc($p['program_studi']) ?></td>
                            <td>
                                <?php
                                $badge_class = match ($p['status']) {
                                    'Menunggu' => 'bg-warning',
                                    'Diterima' => 'bg-success',
                                    'Ditolak' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= esc($p['status']) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>