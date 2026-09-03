<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Pendaftar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        /* SweetAlert2 IOH Theme */
        .swal2-popup.ioh-popup {
            border-radius: 20px !important;
            padding: 2rem 1.5rem !important;
            font-family: 'Poppins', sans-serif !important;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15) !important;
        }
        .swal2-popup.ioh-popup .swal2-title { font-size: 1.35rem !important; font-weight: 700 !important; color: #111827 !important; }
        .swal2-popup.ioh-popup .swal2-html-container { font-size: 0.95rem !important; color: #6B7280 !important; }
        .swal2-popup.ioh-popup .swal2-confirm {
            background: linear-gradient(135deg, #E6007E, #FF4FA3) !important;
            border: none !important; border-radius: 999px !important; padding: 10px 28px !important;
            font-weight: 600 !important; box-shadow: 0 4px 15px rgba(230, 0, 126, 0.3) !important;
        }
        .swal2-popup.ioh-popup .swal2-cancel {
            background: #F3F4F6 !important; color: #374151 !important; border: none !important;
            border-radius: 999px !important; padding: 10px 28px !important; font-weight: 600 !important;
        }
        .swal2-popup.ioh-toast {
            border-radius: 14px !important; padding: 12px 18px !important;
            font-family: 'Poppins', sans-serif !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12) !important;
            border-left: 4px solid transparent;
        }
        .swal2-popup.ioh-toast .swal2-title { font-size: 0.88rem !important; font-weight: 500 !important; margin: 0 !important; padding: 0 !important; }
        .swal2-popup.ioh-toast.toast-success { border-left-color: #22C55E !important; }
        .swal2-popup.ioh-toast.toast-error { border-left-color: #EF4444 !important; }
        .swal2-popup.ioh-toast.toast-warning { border-left-color: #F59E0B !important; }
        .swal2-popup.ioh-toast.toast-info { border-left-color: #3B82F6 !important; }
        .swal2-popup.ioh-toast .swal2-timer-progress-bar {
            background: linear-gradient(90deg, #E6007E, #FF4FA3) !important; height: 4px !important;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <h3>Detail Pendaftar</h3>
        <div class="d-flex gap-2 flex-wrap mb-3">
            <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-secondary">Kembali</a>
            <?php if (in_array($item['status'], ['Diterima', 'Complete'], true)): ?>
                <a href="<?= site_url('admin/surat/penerimaan/' . $item['id']) ?>" class="btn btn-primary">
                    <i class="bi bi-file-earmark-word"></i> Surat Penerimaan
                </a>
                <a href="<?= site_url('admin/surat/selesai/' . $item['id']) ?>" class="btn btn-info text-white">
                    <i class="bi bi-file-earmark-word-fill"></i> Surat Selesai
                </a>
                <a href="<?= site_url('admin/certificate/pdf/' . $item['id']) ?>" target="_blank" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf"></i> Sertifikat PDF
                </a>
                <a href="<?= site_url('admin/certificate/pptx/' . $item['id']) ?>" class="btn btn-warning text-dark">
                    <i class="bi bi-file-earmark-ppt"></i> Sertifikat PPTX
                </a>
            <?php endif; ?>
        </div>

        <table class="table table-bordered">
            <tr>
                <th>Nama</th>
                <td><?= esc($item['nama_lengkap']) ?></td>
            </tr>
            <tr>
                <th>Nomor WA</th>
                <td><?= esc($item['nomor_whatsapp']) ?></td>
            </tr>
            <tr>
                <th>Nomor Darurat</th>
                <td><?= esc($item['nomor_darurat'] ?? '-') ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><a href="mailto:<?= esc($item['email'] ?? '') ?>" class="text-decoration-none"><?= esc($item['email'] ?? '-') ?></a></td>
            </tr>
            <tr>
                <th>Asal Kampus</th>
                <td><?= esc($item['asal_kampus']) ?></td>
            </tr>
            <tr>
                <th>Program Studi</th>
                <td><?= esc($item['program_studi']) ?></td>
            </tr>
            <tr>
                <th>NIM</th>
                <td><?= esc($item['nim'] ?? '-') ?></td>
            </tr>
            <tr>
                <th>Semester</th>
                <td><?= esc($item['semester']) ?></td>
            </tr>
            <tr>
                <th>CV</th>
                <td>
                    <a href="<?= site_url('admin/download/' . $item['id'] . '/cv') ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-2">Lihat</a>
                    <button type="button" class="btn btn-sm btn-info text-white" onclick="analyzeCv(<?= $item['id'] ?>)">
                        <i class="bi bi-robot"></i> Analys CV
                    </button>
                </td>
            </tr>
            <tr>
                <th>Surat Pengantar</th>
                <td><a href="<?= site_url('admin/download/' . $item['id'] . '/surat') ?>" target="_blank">Lihat</a></td>
            </tr>
            <tr>
                <th>KTM</th>
                <td><a href="<?= site_url('admin/download/' . $item['id'] . '/ktm') ?>" target="_blank">Lihat</a></td>
            </tr>
            <tr>
                <th>Riwayat Interview</th>
                <td>
                    <?php $ada = false; ?>
                    <?php for ($i = 1; $i <= 3; $i++): ?>
                        <?php $jadwal = $item['jadwal_interview_' . $i] ?? null; $zoom = $item['link_zoom_' . $i] ?? null; $catatan = $item['catatan_interview_' . $i] ?? null; ?>
                        <?php if ($jadwal || $zoom || $catatan): $ada = true; ?>
                            <div class="border rounded p-2 mb-2">
                                <strong>Interview Tahap <?= $i ?></strong><br>
                                <?php if ($jadwal): ?>
                                    <small><i class="bi bi-calendar-event"></i> <?= date('d/m/Y H:i', strtotime($jadwal)) ?> WIB</small><br>
                                <?php endif; ?>
                                <?php if ($zoom): ?>
                                    <small><i class="bi bi-camera-video"></i> <a href="<?= esc($zoom) ?>" target="_blank"><?= esc($zoom) ?></a></small><br>
                                <?php endif; ?>
                                <?php if ($catatan): ?>
                                    <small class="text-muted"><i class="bi bi-chat-left-text"></i> <?= esc($catatan) ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php if (!$ada): ?>
                        <span class="text-muted small">Belum ada riwayat interview. Atur jadwal lewat tombol Lolos/Tidak Lolos di halaman Dashboard.</span>
                    <?php endif; ?>
                    <br>
                    <button type="button" id="btnWaDetail" class="btn btn-sm btn-outline-success mt-1" onclick="kirimWaPengingat(<?= $item['id'] ?>, this)">
                        <i class="bi bi-whatsapp"></i> Kirim Pengingat WhatsApp
                    </button>
                </td>
            </tr>
            <tr>
                <th>Status & Catatan</th>
                <td>
                    <form action="<?= site_url('admin/update-status/' . $item['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label small text-muted">Ubah status manual (di luar alur interview bertahap)</label>
                            <select name="status" class="form-select">
                                <option value="Menunggu" <?= $item['status'] === 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="Progress" <?= $item['status'] === 'Progress' ? 'selected' : '' ?>>Progress</option>
                                <option value="Diterima" <?= $item['status'] === 'Diterima' ? 'selected' : '' ?>>Diterima</option>
                                <option value="Complete" <?= $item['status'] === 'Complete' ? 'selected' : '' ?>>Complete</option>
                                <option value="Ditolak" <?= $item['status'] === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Catatan Admin</label>
                            <textarea name="catatan_admin" class="form-control"><?= esc($item['catatan_admin'] ?? '') ?></textarea>
                        </div>
                        <button class="btn btn-primary">Simpan</button>
                    </form>
                </td>
            </tr>
        </table>
    </div>
    <!-- Analysis Result Modal -->
    <div class="modal fade" id="analysisModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hasil Analisis CV (AI Powered)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Rekomendasi Divisi:</h6>
                            <div class="alert alert-primary" id="resultDivision">
                                -
                            </div>
                            
                            <h6 class="fw-bold mt-3">Kategori Terdeteksi:</h6>
                            <p id="resultCategory" class="text-muted">-</p>

                            <h6 class="fw-bold mt-3">Estimasi Pengalaman:</h6>
                            <p><span id="resultExperience" class="fw-bold text-dark">-</span> Tahun</p>

                            <h6 class="fw-bold mt-3">Pendidikan:</h6>
                            <p id="resultEducation" class="fw-bold text-dark">-</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Skills Terdeteksi:</h6>
                            <div id="resultSkills" class="d-flex flex-wrap gap-1 mb-3">
                                <!-- Skills badges -->
                            </div>
                            
                            <h6 class="fw-bold">Visualisasi Kecocokan:</h6>
                            <canvas id="divisionChart"></canvas>
                        </div>
                    </div>
                    
                    <hr class="my-3">
                    
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-bold mb-3"><i class="bi bi-person-badge"></i> Analisis Kepribadian & Potensi:</h6>
                        </div>
                        <div class="col-md-5">
                            <ul class="list-group list-group-flush small" id="personalityList">
                                <!-- List populated by JS -->
                            </ul>
                        </div>
                        <div class="col-md-7">
                            <canvas id="personalityChart" style="max-height: 350px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="spinner mb-3"></div>
            <h5 class="text-white">Sedang Menganalisis CV...</h5>
            <p class="text-white small">Mohon tunggu, AI sedang membaca dokumen.</p>
        </div>
    </div>

    <!-- Load Dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        let divisionChart = null;

        function kirimWaPengingat(id, btn) {
            const button = btn || document.getElementById('btnWaDetail');
            let originalHtml = '';
            if (button) {
                originalHtml = button.innerHTML;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyiapkan WA...';
                button.disabled = true;
            }

            fetch(`<?= site_url('admin/process-interview/') ?>${id}/wa`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => res.json())
                .then(json => {
                    if (!json.success || !json.url) {
                        IOH.alert('info', 'Informasi', json.message || 'Nomor WhatsApp tidak valid atau tidak tersedia.');
                        return;
                    }
                    window.open(json.url, '_blank');
                })
                .catch(() => IOH.alert('error', 'Koneksi Gagal', 'Tidak dapat menghubungi server. Silakan coba lagi.'))
                .finally(() => {
                    if (button) {
                        button.innerHTML = originalHtml;
                        button.disabled = false;
                    }
                });
        }

        function analyzeCv(id) {
            const overlay = document.getElementById('loadingOverlay');
            overlay.style.display = 'flex';

            fetch(`<?= site_url('admin/analyze-cv/') ?>${id}`)
                .then(async response => {
                    const text = await response.text();
                    try {
                        const json = JSON.parse(text);
                        if (!response.ok) {
                            throw new Error(json.error || 'Server Error');
                        }
                        return json;
                    } catch (e) {
                         // Jika gagal parse JSON, tampilkan snippet response aslinya (misal HTML error)
                        throw new Error("Respon Server Bermasalah: " + text.substring(0, 100) + "..."); 
                    }
                })
                .then(result => {
                    overlay.style.display = 'none';

                    if (result.error) {
                         // Error logic dari backend
                        IOH.alert('error', 'Analisis Gagal', result.error);
                        return;
                    }

                    // ... (lanjut render)
                    const data = result.data;

                    if (data.is_rule_based) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Analisis Rule-Based',
                            text: 'Koneksi AI sibuk/limit. Menggunakan analisis kata kunci lokal.',
                            toast: true,
                            position: 'top-end',
                            timer: 5000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            customClass: { popup: 'ioh-toast toast-info' }
                        });
                    } else if (data.is_mock) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Mode Simulasi (Offline)',
                            text: 'Gagal membaca file. Menampilkan data dummy.',
                            toast: true,
                            position: 'top-end',
                            timer: 5000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            customClass: { popup: 'ioh-toast toast-warning' }
                        });
                    }
                    
                    document.getElementById('resultDivision').textContent = data.division;
                    document.getElementById('resultCategory').textContent = data.category;
                    document.getElementById('resultExperience').textContent = data.experience_years;
                    document.getElementById('resultEducation').textContent = data.education;

                    const skillsContainer = document.getElementById('resultSkills');
                    skillsContainer.innerHTML = '';
                    if (data.skills && data.skills.length > 0) {
                        data.skills.forEach(skill => {
                            skillsContainer.innerHTML += `<span class="badge bg-secondary me-1">${skill}</span>`;
                        });
                    } else {
                        skillsContainer.innerHTML = '<span class="text-muted small">Tidak ada skill spesifik terdeteksi</span>';
                    }

                    const modal = new bootstrap.Modal(document.getElementById('analysisModal'));
                    modal.show();
                    renderChart(data.division);
                    
                    if (data.personality) {
                        renderPersonalityChart(data.personality);
                        renderPersonalityList(data.personality);
                    }
                })
                .catch(error => {
                    overlay.style.display = 'none';
                    IOH.alert('error', 'Terjadi Kesalahan', error.message);
                    console.error(error);
                });
        }

        function renderChart(targetDivision) {
            const ctx = document.getElementById('divisionChart').getContext('2d');
            
            // Destroy existing chart if any
            if (divisionChart) {
                divisionChart.destroy();
            }

            // Divisions list
            const divisions = [
                'Capability Building', 'Markom', 'Elang IT', 'Finance', 
                'Sosmed 3 & IM3', 'B2B', 'Postpaid-Prepaid'
            ];

            // Create data: 100 for target, 10 for others (just for visualization purposes)
            const dataValues = divisions.map(d => d === targetDivision ? 90 : 20);
            const backgroundColors = divisions.map(d => d === targetDivision ? 'rgba(54, 162, 235, 0.8)' : 'rgba(200, 200, 200, 0.2)');

            divisionChart = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: divisions,
                    datasets: [{
                        label: 'Kecocokan Divisi',
                        data: dataValues,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        pointBackgroundColor: backgroundColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        r: {
                            angleLines: {
                                display: false
                            },
                            suggestedMin: 0,
                            suggestedMax: 100
                        }
                    }
                }
            });
        }

        let personalityChartInstance = null;

        function renderPersonalityList(p) {
            const list = document.getElementById('personalityList');
            list.innerHTML = '';
            
            for (const [key, score] of Object.entries(p)) {
                let badge = 'bg-secondary';
                let label = 'LOW';
                if (score >= 75) { badge = 'bg-success'; label = 'STRONG'; }
                else if (score >= 50) { badge = 'bg-info'; label = 'MODERATE'; }
                
                const clearKey = key.replace(/_/g, ' ');
                
                list.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>${clearKey}</span>
                        <span>
                            <span class="badge ${badge} me-2">${label}</span>
                            <span class="fw-bold">${score}</span>
                        </span>
                    </li>
                `;
            }
        }

        function renderPersonalityChart(p) {
             const ctx = document.getElementById('personalityChart').getContext('2d');
             if (personalityChartInstance) personalityChartInstance.destroy();
             
             const labels = Object.keys(p).map(k => k.replace(/_/g, ' '));
             const data = Object.values(p);
             
             personalityChartInstance = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Skor Potensi',
                        data: data,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        r: {
                            angleLines: { display: false },
                            suggestedMin: 0,
                            suggestedMax: 100,
                            ticks: { display: false } // Hide numbers for cleaner look
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
             });
        }
    </script>

    <!-- IOH Notification Helpers (standalone page) -->
    <script>
    window.IOH = window.IOH || {};
    IOH.toast = function(icon, text, timer) {
        Swal.fire({ icon: icon, title: text, toast: true, position: 'top-end',
            timer: timer || 3000, timerProgressBar: true, showConfirmButton: false, showCloseButton: true,
            customClass: { popup: 'ioh-toast toast-' + icon },
            didOpen: function(t) { t.addEventListener('mouseenter', Swal.stopTimer); t.addEventListener('mouseleave', Swal.resumeTimer); }
        });
    };
    IOH.confirm = function(o) {
        o = o || {};
        return Swal.fire({ icon: o.icon||'question', title: o.title||'Konfirmasi',
            text: o.text, html: o.html, showCancelButton: true,
            confirmButtonText: o.confirmText||'Ya, lanjutkan', cancelButtonText: o.cancelText||'Batal',
            reverseButtons: true, focusCancel: true,
            customClass: { popup: 'ioh-popup', confirmButton: o.danger ? 'swal2-confirm--danger' : '' }
        });
    };
    IOH.alert = function(icon, title, textOrOpts) {
        var opts = { icon: icon, title: title, customClass: { popup: 'ioh-popup' }, confirmButtonText: 'Mengerti' };
        if (typeof textOrOpts === 'string') opts.text = textOrOpts;
        else if (textOrOpts) { if (textOrOpts.text) opts.text = textOrOpts.text; if (textOrOpts.html) opts.html = textOrOpts.html; }
        return Swal.fire(opts);
    };
    </script>
</body>

</html>