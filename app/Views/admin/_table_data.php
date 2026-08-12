<div class="table-responsive">
    <table class="table table-hover align-middle table-mobile-cards" id="dataTable" width="100%" cellspacing="0">
        <thead class="table-light">
            <tr>
                <th class="fw-bold" style="min-width:220px;">Kandidat</th>
                <th class="fw-bold text-nowrap" style="min-width:170px;">Akademik</th>
                <th class="fw-bold text-nowrap">Kota Magang</th>
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
                        $isFinal = $currentStatus === 'Diterima';
                        $isRejected = $currentStatus === 'Ditolak';
                        $isProgress = str_contains($currentStatus, 'Interview') || $currentStatus === 'Progress';
                        
                        $badgeClass = 'bg-warning text-dark';
                        if ($isFinal) {
                            $badgeClass = 'bg-success';
                        } elseif ($isRejected) {
                            $badgeClass = 'bg-danger';
                        } elseif ($isProgress) {
                            $badgeClass = 'bg-info text-dark';
                        }
                        
                        $statusLabel = 'MENUNGGU';
                        if ($isFinal) $statusLabel = 'DITERIMA';
                        if ($isRejected) $statusLabel = 'DITOLAK';
                        if ($isProgress) $statusLabel = 'PROGRESS';

                        $step = 0;
                        if (preg_match('/Interview_(\d)/', $currentStatus, $m)) { $step = (int) $m[1]; }
                        $jadwalKey = 'jadwal_interview_' . $step;
                    ?>
                    <tr data-aos="fade-in" id="row-<?= $data['id'] ?>">
                        <td data-label="Kandidat">
                            <div class="small text-muted font-monospace"><?= esc($data['token_pendaftaran'] ?? '-') ?></div>
                            <div class="fw-semibold mb-1"><?= esc($data['nama_lengkap']) ?></div>
                            <div class="small mb-1">
                                <a href="mailto:<?= esc($data['email'] ?? '') ?>" class="text-decoration-none" title="Kirim Email">
                                    <i class="bi bi-envelope"></i> <?= esc($data['email'] ?? 'Tidak ada Email') ?>
                                </a>
                            </div>
                            <div class="small text-muted"><i class="bi bi-whatsapp"></i> <?= esc($data['nomor_whatsapp']) ?></div>
                            <?php if (!empty($data['nomor_darurat'])): ?>
                                <div class="small text-muted mt-1" style="font-size: 0.8rem;">
                                    <i class="bi bi-telephone text-warning"></i> Darurat: <?= esc($data['nomor_darurat']) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="text-nowrap" data-label="Akademik">
                            <div class="fw-semibold mb-1"><?= esc($data['asal_kampus']) ?></div>
                            <div class="small text-muted mb-1"><?= esc($data['program_studi']) ?></div>
                            <span class="badge bg-light text-dark border">Smt <?= esc($data['semester']) ?></span>
                        </td>
                        <td class="text-nowrap" data-label="Kota Magang">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                            <?= esc($data['kota_pilihan'] ?? '-') ?>
                        </td>
                        <td class="text-nowrap" data-label="Divisi / Jenis">
                            <div class="mb-1"><?= esc($data['divisi_pilihan'] ?? '-') ?></div>
                            <span class="badge <?= $data['jenis_magang'] == 'Wajib' ? 'bg-info' : 'bg-secondary' ?>">
                                <?= esc($data['jenis_magang']) ?>
                            </span>
                        </td>
                        <td class="text-center" data-label="Status" id="status-cell-<?= $data['id'] ?>">
                            <span class="badge <?= $badgeClass ?>"><?= esc($statusLabel) ?></span>

                            <?php if ($step > 0 && !empty($data[$jadwalKey])): ?>
                                <div class="small text-muted mt-2">
                                    <i class="bi bi-calendar-event"></i> <?= date('d/m/Y H:i', strtotime($data[$jadwalKey])) ?> WIB
                                </div>
                            <?php endif; ?>
                            <?php if ($is_arsip && !empty($data['archived_at'])):
                                $deleteAt = strtotime($data['archived_at']) + (7 * 86400);
                                $daysLeft = max(0, ceil(($deleteAt - time()) / 86400));
                            ?>
                                <div class="small text-danger mt-2">
                                    <i class="bi bi-hourglass-split"></i> Hapus permanen <?= $daysLeft ?> hari lagi
                                </div>
                                <div class="small text-muted"><?= esc($data['archived_reason'] ?? '') ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-nowrap" data-label="Periode Magang">
                            <small class="d-block mb-1">
                                <span class="text-muted">Mulai:</span> <?= !empty($data['periode_mulai']) ? date('d/m/Y', strtotime($data['periode_mulai'])) : '-' ?>
                            </small>
                            <small class="d-block">
                                <span class="text-muted">Selesai:</span> <?= !empty($data['periode_selesai']) ? date('d/m/Y', strtotime($data['periode_selesai'])) : '-' ?>
                            </small>
                        </td>
                        <td class="text-nowrap" data-label="Tanggal Daftar">
                            <small>
                                <i class="bi bi-calendar3"></i><br>
                                <?php
                                $tanggalDaftar = $data['created_at'] ?? '';
                                if ($tanggalDaftar && trim($tanggalDaftar) !== ''):
                                ?>
                                    <?= date('d/m/Y H:i', strtotime($tanggalDaftar)) ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </small>
                        </td>
                        <td class="text-nowrap" data-label="Berkas">
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
                                <a href="<?= site_url('admin/download/' . $data['id'] . '/proposal') ?>"
                                class="btn btn-outline-warning <?= empty($data['proposal_magang']) ? 'disabled' : '' ?>"
                                title="<?= empty($data['proposal_magang']) ? 'Proposal tidak tersedia' : 'Download Proposal Magang' ?>"
                                <?= empty($data['proposal_magang']) ? 'onclick="return false;"' : '' ?>>
                                    <i class="bi bi-journal-text"></i>
                                <a href="<?= site_url('admin/download/' . $data['id'] . '/ktm') ?>"
                                class="btn btn-outline-info <?= empty($data['ktm']) ? 'disabled' : '' ?>"
                                title="<?= empty($data['ktm']) ? 'KTM tidak tersedia' : 'Download KTM' ?>"
                                <?= empty($data['ktm']) ? 'onclick="return false;"' : '' ?>>
                                    <i class="bi bi-card-image"></i>
                                </a>
                            </div>
                        </td>
                        <td class="text-nowrap text-center mobile-col-flex" data-label="Aksi" id="aksi-cell-<?= $data['id'] ?>">
                            <div class="d-flex gap-2 justify-content-center flex-nowrap">
                                <?php if ($is_arsip): ?>
                                    <button type="button" class="btn btn-outline-success btn-sm" onclick="restoreData(<?= $data['id'] ?>)" title="Pulihkan ke Data Aktif">
                                        <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="hapusData(<?= $data['id'] ?>)" title="Hapus Permanen Sekarang">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary btn-sm px-3" onclick="openActionModal(<?= $data['id'] ?>, '<?= esc($data['nama_lengkap'], 'js') ?>')" title="Lihat Detail & Kelola Status">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm px-2" onclick="openWaLink(<?= $data['id'] ?>)" title="Hubungi Kandidat (WA)">
                                        <i class="bi bi-whatsapp"></i>
                                    </button>
                                    <?php 
                                        $wa_darurat = preg_replace('/\D/', '', $data['nomor_darurat'] ?? '');
                                        if (str_starts_with($wa_darurat, '0')) {
                                            $wa_darurat = '62' . substr($wa_darurat, 1);
                                        }
                                    ?>
                                    <?php if (!empty($wa_darurat)): ?>
                                        <a href="https://wa.me/<?= $wa_darurat ?>" target="_blank" class="btn btn-warning btn-sm px-2 text-white" title="Hubungi Darurat (WA)">
                                            <i class="bi bi-telephone-fill"></i>
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-danger btn-sm px-2" onclick="hapusData(<?= $data['id'] ?>)" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center py-5">
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
    <div class="d-flex justify-content-between align-items-center mt-4 mb-2 flex-wrap gap-3 px-1">
        <div class="pagination-info d-flex align-items-center gap-2">
            <span class="badge bg-light text-secondary border" style="font-size: 0.8rem; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                <i class="bi bi-journal-text me-1"></i>
                Halaman <strong class="text-dark"><?= $pager->getCurrentPage('pendaftaran') ?></strong> dari <strong class="text-dark"><?= max($pager->getPageCount('pendaftaran'), 1) ?></strong>
            </span>
            <span class="badge bg-light text-secondary border" style="font-size: 0.8rem; padding: 6px 12px; border-radius: 8px; font-weight: 500;">
                <i class="bi bi-people me-1"></i>
                <strong class="text-dark"><?= $total_pendaftar ?></strong> data
            </span>
        </div>
        <!-- Custom class added to links for delegating click events in AJAX -->
        <div class="ajax-pagination">
            <?= $pager->links('pendaftaran', 'default_full') ?>
        </div>
    </div>
<?php endif; ?>
