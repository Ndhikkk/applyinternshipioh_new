<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Pendaftar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    </style>
</head>

<body>
    <div class="container py-5">
        <h3>Detail Pendaftar</h3>
        <a href="/admin/dashboard" class="btn btn-secondary mb-3">Kembali</a>

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
                <th>Semester</th>
                <td><?= esc($item['semester']) ?></td>
            </tr>
            <tr>
                <th>CV</th>
                <td>
                    <a href="<?= base_url('admin/download/' . $item['id'] . '/cv') ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-2">Lihat</a>
                    <button type="button" class="btn btn-sm btn-info text-white" onclick="analyzeCv(<?= $item['id'] ?>)">
                        <i class="bi bi-robot"></i> Analys CV
                    </button>
                </td>
            </tr>
            <tr>
                <th>Surat Pengantar</th>
                <td><a href="<?= base_url('admin/download/' . $item['id'] . '/surat') ?>" target="_blank">Lihat</a></td>
            </tr>
            <tr>
                <th>KTM</th>
                <td><a href="<?= base_url('admin/download/' . $item['id'] . '/ktm') ?>" target="_blank">Lihat</a></td>
            </tr>
            <tr>
                <th>Status & Catatan</th>
                <td>
                    <form action="/admin/update-status/<?= $item['id'] ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <select name="status" class="form-select">
                                <option value="Menunggu" <?= $item['status'] === 'Menunggu' ? 'selected' : '' ?>>Menunggu
                                </option>
                                <option value="Diterima" <?= $item['status'] === 'Diterima' ? 'selected' : '' ?>>Diterima
                                </option>
                                <option value="Ditolak" <?= $item['status'] === 'Ditolak' ? 'selected' : '' ?>>Ditolak
                                </option>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let divisionChart = null;

        function analyzeCv(id) {
            const overlay = document.getElementById('loadingOverlay');
            overlay.style.display = 'flex';

            fetch(`<?= base_url('admin/analyze-cv/') ?>${id}`)
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
                        Swal.fire('Gagal', result.error, 'error');
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
                            showConfirmButton: false
                        });
                    } else if (data.is_mock) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Mode Simulasi (Offline)',
                            text: 'Gagal membaca file. Menampilkan data dummy.',
                            toast: true,
                            position: 'top-end',
                            timer: 5000,
                            showConfirmButton: false
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
                    Swal.fire('Error', error.message, 'error');
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
</body>

</html>