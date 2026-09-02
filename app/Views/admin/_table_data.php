<div class="table-responsive">
    <table class="table table-hover align-middle table-mobile-cards" id="dataTable" width="100%" cellspacing="0">
        <thead class="table-light">
            <tr>
                <th class="fw-bold" style="min-width:220px;">Kandidat</th>
                <th class="fw-bold text-nowrap" style="min-width:170px;">Akademik</th>
                <th class="fw-bold text-nowrap">Regional Interview</th>
                <th class="fw-bold text-nowrap">Kota Pilihan</th>
                <th class="fw-bold text-nowrap" style="cursor:pointer;">
                    <div class="dropdown d-inline-block">
                        <span class="divisi-filter-toggle d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-display="dynamic" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false">
                            Divisi / Jenis
                            <?php if (!empty($divisi_filter)): ?>
                                <span class="badge bg-primary ms-1" style="font-size:.65em;"><?= esc($divisi_filter) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($jenis_filter)): ?>
                                <span class="badge bg-info ms-1" style="font-size:.65em;"><?= esc($jenis_filter) ?></span>
                            <?php endif; ?>
                            <i class="bi bi-funnel<?= (!empty($divisi_filter) || !empty($jenis_filter)) ? '-fill text-primary' : '' ?> ms-1" style="font-size:.75em;"></i>
                        </span>
                        <div class="dropdown-menu dropdown-menu-start shadow-lg border p-3" style="width:480px; min-width:480px; max-width:95vw; z-index:1060; white-space:normal;">
                            <div class="row g-3">
                                <!-- Kolom Kiri: Divisi -->
                                <div class="col-6 border-end pe-3">
                                    <h6 class="dropdown-header px-1 text-primary fw-bold mb-2"><i class="bi bi-diagram-3 me-1"></i>Pilih Divisi</h6>
                                    <div style="max-height:280px; overflow-y:auto;" class="pe-1">
                                        <a class="dropdown-item small rounded py-1.5 px-2 mb-1 text-truncate <?= empty($divisi_filter) ? 'active fw-bold' : '' ?>" href="#" data-divisi="" title="Semua Divisi">Semua Divisi</a>
                                        <?php
                                            $divisiList = [
                                                'Direct Sales Executive', 'Markom', 'IT / Elang IT', 'Technical',
                                                'Finance', 'B2B', 'Social Media 3ID & IM3', 'Daily Project',
                                                'Project Post Paid', 'Capability Building', 'SnD'
                                            ];
                                            foreach ($divisiList as $div):
                                        ?>
                                        <a class="dropdown-item small rounded py-1.5 px-2 mb-1 text-truncate <?= ($divisi_filter ?? '') === $div ? 'active fw-bold' : '' ?>" href="#" data-divisi="<?= esc($div, 'attr') ?>" title="<?= esc($div) ?>"><?= esc($div) ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <!-- Kolom Kanan: Jenis Magang (Setelah Divisi) -->
                                <div class="col-6 ps-3">
                                    <h6 class="dropdown-header px-1 text-info fw-bold mb-2"><i class="bi bi-tag me-1"></i>Jenis Magang</h6>
                                    <div class="d-flex flex-column gap-1">
                                        <a class="dropdown-item small rounded py-2 px-2 <?= empty($jenis_filter) ? 'active fw-bold' : '' ?>" href="#" data-jenis="">
                                            <i class="bi bi-asterisk me-1 text-muted"></i> Semua Jenis
                                        </a>
                                        <a class="dropdown-item small rounded py-2 px-2 d-flex align-items-center <?= ($jenis_filter ?? '') === 'Wajib' ? 'active fw-bold' : '' ?>" href="#" data-jenis="Wajib">
                                            <span class="badge bg-info me-2">Wajib</span>
                                            <span>Magang Wajib</span>
                                        </a>
                                        <a class="dropdown-item small rounded py-2 px-2 d-flex align-items-center <?= ($jenis_filter ?? '') === 'Tidak Wajib' ? 'active fw-bold' : '' ?>" href="#" data-jenis="Tidak Wajib">
                                            <span class="badge bg-secondary me-2">Tidak Wajib</span>
                                            <span>Tidak Wajib</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </th>
                <th class="fw-bold text-center" style="min-width:140px; cursor:pointer;">
                    <div class="dropdown d-inline-block">
                        <span class="status-filter-toggle d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-display="dynamic" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false">
                            Status
                            <span id="statusFilterBadge" class="badge bg-primary ms-1" style="font-size:.65em; display:none;"></span>
                            <i class="bi bi-funnel ms-1" style="font-size:.75em;" id="statusFilterIcon"></i>
                        </span>
                        <div class="dropdown-menu dropdown-menu-start shadow-lg border p-3" style="width:230px; min-width:230px; max-width:95vw; z-index:1060; white-space:normal;">
                            <h6 class="dropdown-header px-1 text-primary fw-bold mb-2"><i class="bi bi-filter-circle me-1"></i>Filter Status</h6>
                            <div class="d-flex flex-column gap-1" style="max-height:280px; overflow-y:auto;">
                                <a class="dropdown-item small rounded py-2 px-2 d-flex align-items-center active fw-bold" href="#" data-status-filter="">
                                    <i class="bi bi-asterisk me-2 text-muted"></i> Semua Status
                                </a>
                                <a class="dropdown-item small rounded py-2 px-2 d-flex align-items-center" href="#" data-status-filter="MENUNGGU">
                                    <span class="badge bg-warning text-dark me-2" style="min-width:14px; height:14px; padding:0; border-radius:50%;"></span> Menunggu
                                </a>
                                <a class="dropdown-item small rounded py-2 px-2 d-flex align-items-center" href="#" data-status-filter="PROGRESS">
                                    <span class="badge bg-info me-2" style="min-width:14px; height:14px; padding:0; border-radius:50%;"></span> Progress
                                </a>
                                <a class="dropdown-item small rounded py-2 px-2 d-flex align-items-center" href="#" data-status-filter="DITERIMA">
                                    <span class="badge bg-success me-2" style="min-width:14px; height:14px; padding:0; border-radius:50%;"></span> Diterima
                                </a>
                                <a class="dropdown-item small rounded py-2 px-2 d-flex align-items-center" href="#" data-status-filter="COMPLETE">
                                    <span class="badge bg-purple text-white me-2" style="min-width:14px; height:14px; padding:0; border-radius:50%;"></span> Complete (Selesai Magang)
                                </a>
                                <a class="dropdown-item small rounded py-2 px-2 d-flex align-items-center" href="#" data-status-filter="DITOLAK">
                                    <span class="badge bg-danger me-2" style="min-width:14px; height:14px; padding:0; border-radius:50%;"></span> Ditolak
                                </a>
                            </div>
                        </div>
                    </div>
                </th>
                <th class="fw-bold text-nowrap">Periode</th>
                <th class="fw-bold text-nowrap">Tanggal Daftar</th>
                <th class="fw-bold text-center">Berkas</th>
                <th class="fw-bold text-center text-nowrap">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($pendaftaran)): ?>
                <?php foreach ($pendaftaran as $data): ?>
                    <?php
                        $currentStatus = $data['status'] ?? 'Menunggu';
                        if (empty($currentStatus)) $currentStatus = 'Menunggu';
                        $isComplete = $currentStatus === 'Complete';
                        $isFinal = $currentStatus === 'Diterima';
                        $isRejected = $currentStatus === 'Ditolak';
                        $isProgress = str_contains($currentStatus, 'Interview') || $currentStatus === 'Progress';
                        
                        $badgeClass = 'bg-warning text-dark';
                        if ($isComplete) {
                            $badgeClass = 'bg-purple text-white';
                        } elseif ($isFinal) {
                            $badgeClass = 'bg-success';
                        } elseif ($isRejected) {
                            $badgeClass = 'bg-danger';
                        } elseif ($isProgress) {
                            $badgeClass = 'bg-info text-dark';
                        }
                        
                        $statusLabel = 'MENUNGGU';
                        if ($isComplete) $statusLabel = 'COMPLETE';
                        elseif ($isFinal) $statusLabel = 'DITERIMA';
                        elseif ($isRejected) $statusLabel = 'DITOLAK';
                        elseif ($isProgress) $statusLabel = 'PROGRESS';

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
                        <td data-label="Akademik">
                            <div class="fw-semibold mb-1"><?= esc($data['asal_kampus']) ?></div>
                            <div class="small text-muted mb-1"><?= esc($data['program_studi']) ?></div>
                            <?php if (!empty($data['nim'])): ?>
                                <div class="small text-muted mb-1"><i class="bi bi-card-text"></i> NIM: <?= esc($data['nim']) ?></div>
                            <?php endif; ?>
                            <span class="badge bg-light text-dark border">Smt <?= esc($data['semester']) ?></span>
                        </td>
                        <td data-label="Regional Interview">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                            <?= esc($data['regional_interview'] ?? '-') ?>
                        </td>
                        <td data-label="Kota Pilihan">
                            <i class="bi bi-building text-primary me-1"></i>
                            <?= esc($data['kota_pilihan'] ?? $data['kota_magang'] ?? '-') ?>
                        </td>
                        <td data-label="Divisi / Jenis">
                            <div class="mb-1"><?= esc($data['divisi_pilihan'] ?? '-') ?></div>
                            <span class="badge <?= $data['jenis_magang'] == 'Wajib' ? 'bg-info' : 'bg-secondary' ?>">
                                <?= esc($data['jenis_magang']) ?>
                            </span>
                        </td>
                        <td class="text-center" data-label="Status" data-status-label="<?= esc($statusLabel) ?>" id="status-cell-<?= $data['id'] ?>">
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
                        <td data-label="Periode">
                            <small class="d-block mb-1">
                                <span class="text-muted">Mulai:</span> <?= !empty($data['periode_mulai']) ? date('d/m/Y', strtotime($data['periode_mulai'])) : '-' ?>
                            </small>
                            <small class="d-block">
                                <span class="text-muted">Selesai:</span> <?= !empty($data['periode_selesai']) ? date('d/m/Y', strtotime($data['periode_selesai'])) : '-' ?>
                            </small>
                        </td>
                        <td data-label="Tanggal Daftar">
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
                        <td data-label="Berkas">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="<?= site_url('admin/download/' . $data['id'] . '/cv') ?>"
                                target="_blank" rel="noopener"
                                class="btn btn-outline-primary <?= empty($data['cv']) ? 'disabled' : '' ?>"
                                title="<?= empty($data['cv']) ? 'CV tidak tersedia' : 'Download CV' ?>"
                                <?= empty($data['cv']) ? 'onclick="return false;"' : '' ?>>
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                <a href="<?= site_url('admin/download/' . $data['id'] . '/surat') ?>"
                                target="_blank" rel="noopener"
                                class="btn btn-outline-success <?= empty($data['surat_pengantar']) ? 'disabled' : '' ?>"
                                title="<?= empty($data['surat_pengantar']) ? 'Surat tidak tersedia' : 'Download Surat Pengantar' ?>"
                                <?= empty($data['surat_pengantar']) ? 'onclick="return false;"' : '' ?>>
                                    <i class="bi bi-file-earmark-text"></i>
                                </a>
                                <a href="<?= site_url('admin/download/' . $data['id'] . '/proposal') ?>"
                                target="_blank" rel="noopener"
                                class="btn btn-outline-warning <?= empty($data['proposal_magang']) ? 'disabled' : '' ?>"
                                title="<?= empty($data['proposal_magang']) ? 'Proposal tidak tersedia' : 'Download Proposal Magang' ?>"
                                <?= empty($data['proposal_magang']) ? 'onclick="return false;"' : '' ?>>
                                    <i class="bi bi-journal-text"></i>
                                </a>
                                <a href="<?= site_url('admin/download/' . $data['id'] . '/ktm') ?>"
                                target="_blank" rel="noopener"
                                class="btn btn-outline-info <?= empty($data['ktm']) ? 'disabled' : '' ?>"
                                title="<?= empty($data['ktm']) ? 'KTM tidak tersedia' : 'Download KTM' ?>"
                                <?= empty($data['ktm']) ? 'onclick="return false;"' : '' ?>>
                                    <i class="bi bi-card-image"></i>
                                </a>
                            </div>
                        </td>
                        <td class="text-center mobile-col-flex" data-label="Aksi" id="aksi-cell-<?= $data['id'] ?>">
                            <div class="d-flex gap-2 justify-content-center flex-nowrap">
                                <?php if ($is_arsip): ?>
                                    <button type="button" class="btn btn-outline-success btn-sm px-2" onclick="restoreData(<?= $data['id'] ?>)" title="Pulihkan ke Data Aktif">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="hapusData(<?= $data['id'] ?>)" title="Hapus Permanen Sekarang">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-primary btn-sm btn-aksi-label" onclick="openActionModal(<?= $data['id'] ?>, '<?= esc($data['nama_lengkap'], 'js') ?>')" title="Lihat Detail & Kelola Status">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                    <?php if (in_array($currentStatus, ['Diterima', 'Complete'], true)): ?>
                                        <a href="<?= site_url('admin/surat/penerimaan/' . $data['id']) ?>" class="btn btn-outline-primary btn-sm px-2" title="Unduh Surat Penerimaan (Word)">
                                            <i class="bi bi-file-earmark-word"></i>
                                        </a>
                                        <a href="<?= site_url('admin/surat/selesai/' . $data['id']) ?>" class="btn btn-info btn-sm px-2 text-white" title="Unduh Surat Keterangan Selesai (Word)">
                                            <i class="bi bi-file-earmark-word-fill"></i>
                                        </a>
                                        <a href="<?= site_url('admin/certificate/pdf/' . $data['id']) ?>" target="_blank" class="btn btn-outline-danger btn-sm px-2" title="Unduh Sertifikat (PDF)">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                        <a href="<?= site_url('admin/certificate/pptx/' . $data['id']) ?>" class="btn btn-warning btn-sm px-2 text-dark" title="Unduh Sertifikat (PPTX)">
                                            <i class="bi bi-file-earmark-ppt"></i>
                                        </a>
                                    <?php endif; ?>
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
                    <td colspan="10" class="text-center py-5">
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
