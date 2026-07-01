<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review CV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .parsing-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header-parsing {
            height: 52px;
            flex-shrink: 0;
        }
        .parsing-row {
            height: calc(100vh - 52px);
            flex-grow: 1;
        }
        .toast-container-custom {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        .custom-toast {
            min-width: 320px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            margin-bottom: 10px;
        }

        .viewer-wrapper {
            width: 100%;
            height: 100%;
            overflow: hidden; 
            position: relative;
            background-color: #1e1e1e;
            cursor: default;
            user-select: none;
        }
        
        .image-preview-style {
            position: absolute;
            top: 0;
            left: 0;
            max-width: none;
            max-height: none;
            transform-origin: 0 0; 
            cursor: grab;
            transition: transform 0.05s ease-out; 
        }
        .image-preview-style:active {
            cursor: grabbing;
        }
        
        .zoom-controls {
            position: absolute;
            bottom: 20px;
            right: 20px;
            z-index: 100;
            display: flex;
            gap: 5px;
        }

        @media (max-width: 767.98px) {
            body { overflow: auto; }
            .parsing-container { height: auto; }
            .parsing-row { height: auto; }
            .viewer-wrapper { min-height: 450px; }
            #file_queue { max-height: 200px; }
        }
    </style>

</head>
<body>

<div class="toast-container-custom" id="toastContainer"></div>

<div class="container-fluid p-0 parsing-container">
  
  <div class="bg-secondary text-white py-2 px-3 d-flex justify-content-between align-items-center header-parsing shadow-sm">
    <div class="d-flex align-items-center gap-2">
      <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-light btn-sm px-2.5 py-1 me-2 fw-semibold d-flex align-items-center gap-1" style="border-radius: 8px; font-size: 0.85rem;">
        <i class="bi bi-arrow-left-short fs-5"></i> Kembali ke Dashboard
      </a>
      <h5 class="mb-0 fs-6 fw-bold"><i class="bi bi-cpu-fill me-2"></i>Generate and Review CV</h5>
    </div>
    <!-- <a href="<?= base_url('admin/dashboard') ?>" class="btn-close btn-close-white" title="Kembali ke Dashboard"></a> -->
  </div>

  <div class="row g-0 parsing-row">
      
      <div class="col-md-2 border-end bg-light d-flex flex-column h-100">
        <div class="p-2 border-bottom">
          <label class="btn btn-outline-danger btn-sm w-100 fw-semibold" style="border-radius: 8px;">
            <i class="bi bi-plus-circle me-1"></i> Tambah Berkas (PDF/Foto)
            <input type="file" id="bulk_upload" multiple accept="application/pdf, image/*" class="d-none">
          </label>
        </div>
        <div id="file_queue" class="list-group list-group-flush overflow-auto flex-grow-1"></div>
      </div>
      
      <div class="col-md-5 border-end h-100 position-relative bg-dark">
        <div class="viewer-wrapper" id="viewer_container">
            <iframe id="pdf_viewer" class="w-100 h-100 border-0"></iframe>
            <img id="image_viewer" class="image-preview-style d-none" alt="Pratinjau Foto CV" draggable="false">
        </div>
        <div class="zoom-controls d-none" id="zoom_tools">
            <button class="btn btn-sm btn-light shadow border" onclick="zoomFoto(0.1)"><i class="bi bi-zoom-in"></i></button>
            <button class="btn btn-sm btn-light shadow border" onclick="zoomFoto(-0.1)"><i class="bi bi-zoom-out"></i></button>
            <button class="btn btn-sm btn-light shadow border" onclick="resetZoomFoto()"><i class="bi bi-arrow-counterclockwise"></i></button>
        </div>
      </div>
      
      <div class="col-md-5 p-3 overflow-auto bg-white h-100">
        <form action="<?= base_url('pendaftaran/import_cv') ?>" method="POST" enctype="multipart/form-data" id="main_scan_form" onsubmit="simpanDanLanjut(event)">
          <?= csrf_field() ?>
          <div class="row g-2">
            <div class="col-12 mb-3">
              <label class="small text-muted fw-bold text-uppercase">Batch Magang</label>
              <select name="batch_id" class="form-control form-control-sm" style="border-radius: 8px;">
                <option value="1">Internship IOH </option>
              </select>
            </div>
            
            <div class="col-12">
              <h6 class="text-dark border-bottom pb-2 mb-3 fw-bold"><i class="bi bi-file-earmark-text-fill me-1"></i>Data Hasil Scan</h6>
              
              <div class="mb-2">
                <label class="small fw-bold text-secondary">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" id="form_nama" class="form-control form-control-sm" style="border-radius: 6px;" required>
              </div>
              
              <div class="row g-2">
                <div class="col-6 mb-2">
                  <label class="small fw-bold text-secondary">Email</label>
                  <input type="email" name="email" id="form_email" class="form-control form-control-sm" style="border-radius: 6px;" required>
                </div>
                <div class="col-6 mb-2">
                  <label class="small fw-bold text-secondary">WhatsApp</label>
                  <input type="text" name="nomor_whatsapp" id="form_wa" class="form-control form-control-sm" style="border-radius: 6px;" required>
                </div>
              </div>
              
              <div class="mb-2">
                <label class="small fw-bold text-secondary">Asal Kampus / Sekolah</label>
                <input type="text" name="asal_kampus" id="form_kampus" class="form-control form-control-sm" style="border-radius: 6px;" required>
              </div>
              
              <div class="row g-2">
                <div class="col-8 mb-2">
                  <label class="small fw-bold text-secondary">Program Studi</label>
                  <input type="text" name="program_studi" id="form_prodi" class="form-control form-control-sm" style="border-radius: 6px;" required>
                </div>
                <div class="col-4 mb-2">
                  <label class="small fw-bold text-secondary">Semester</label>
                  <input type="number" name="semester" id="form_semester" class="form-control form-control-sm" value="0" style="border-radius: 6px;" required>
                </div>
              </div>
              
              <div class="row g-2">
                <div class="col-6 mb-2">
                  <label class="small fw-bold text-secondary">Jenis Magang</label>
                  <select name="jenis_magang" id="form_jenis" class="form-control form-control-sm" style="border-radius: 6px;">
                    <option value="Mandiri">Mandiri</option>
                    <option value="Wajib">Wajib</option>
                  </select>
                </div>
                <div class="col-6 mb-2">
                  <label class="small fw-bold text-secondary">Divisi Pilihan</label>
                  <select name="divisi_pilihan" id="form_divisi" class="form-control form-control-sm" style="border-radius: 6px;">
                    <option value="Markom">Markom</option>
                    <option value="IT / Elang IT">IT / Elang IT</option>
                    <option value="Technical">Technical</option>
                    <option value="Finance">Finance</option>
                    <option value="B2B">B2B</option>
                    <option value="Social Media 3ID & IM3">Social Media 3ID & IM3</option>
                    <option value="Daily Project">Daily Project</option>
                    <option value="Project Post Paid">Project Post Paid</option>
                    <option value="Capability Building">Capability Building</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          
          <div class="d-flex gap-2 mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-danger btn-sm flex-grow-1 fw-semibold" id="btn_simpan" style="border-radius: 8px;">
              <i class="bi bi-save me-1"></i> Simpan & Lanjut
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm fw-semibold" style="border-radius: 8px;" onclick="lewatiData()">
              Lewati
            </button>
          </div>
        </form>
      </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

<script>
let queue = [];
let currentIdx = 0; 
let skalaZoom = 1.0;
// Variabel koordinat posisi drag gambar
let isDragging = false;
let startX, startY, translateX = 0, translateY = 0;

pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

function tampilkanNotifikasi(tipe, pesan) {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const id = 'toast_' + Date.now();
    let bg = 'bg-success'; let ikon = 'bi-check-circle-fill';
    if (tipe === 'error') { bg = 'bg-danger'; ikon = 'bi-exclamation-triangle-fill'; }
    if (tipe === 'warning') { bg = 'bg-warning text-dark'; ikon = 'bi-exclamation-circle-fill'; }

    const HTMLToast = `
        <div id="${id}" class="toast custom-toast align-items-center text-white ${bg} border-0 p-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi ${ikon} fs-5"></i>
                    <span class="small fw-medium">${pesan}</span>
                </div>
                <button type="button" class="btn-close btn-close-white m-auto me-2" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', HTMLToast);
    const elemen = document.getElementById(id);
    const bsToast = new bootstrap.Toast(elemen, { delay: 4000 });
    bsToast.show();
    elemen.addEventListener('hidden.bs.toast', () => { elemen.remove(); });
}

// INTEGRASI: BATASAN MAKSIMAL UPLOAD MASSAL (CONTOH: MAX 20MB TOTAL)
document.getElementById('bulk_upload').addEventListener('change', function(e) {
    const filesSelected = Array.from(e.target.files);
    const MAX_SIZE_MB = 20; 
    const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;
    
    let totalUkuran = 0;
    filesSelected.forEach(file => totalUkuran += file.size);
    
    if (totalUkuran > MAX_SIZE_BYTES) {
        tampilkanNotifikasi('error', `Gagal! Total ukuran file melebihi batas maksimal ${MAX_SIZE_MB}MB.`);
        this.value = ""; // Reset berkas
        return;
    }

    queue = filesSelected.slice(0, 10);
    renderQueue();
    queue.forEach((file, index) => { processQueueItem(file, index); });
});

async function simpanDanLanjut(event) {
    if (event) event.preventDefault();
    const form = document.getElementById('main_scan_form');
    const formData = new FormData(form);
    const btnSimpan = document.getElementById('btn_simpan');
    const namaPelamar = document.getElementById('form_nama').value || 'Pelamar';
    
    if (queue[currentIdx]) { formData.append('file_cv', queue[currentIdx]); }
    if (btnSimpan) {
        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...';
    }

    try {
        const response = await fetch(form.action, {
            method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if (btnSimpan) {
            btnSimpan.disabled = false; btnSimpan.innerHTML = '<i class="bi bi-save me-1"></i> Simpan & Lanjut';
        }
        if (response.ok && data.status === 'success') {
            tampilkanNotifikasi('success', `Data "${namaPelamar}" berhasil disimpan!`);
            queue.splice(currentIdx, 1);
            renderQueue(); 
            form.reset();
            if(document.getElementById('form_semester')) document.getElementById('form_semester').value = 0;
            if (queue.length > 0) { loadFile(0); } else {
                document.getElementById('pdf_viewer').src = "";
                document.getElementById('image_viewer').classList.add('d-none');
                document.getElementById('zoom_tools').classList.add('d-none');
                tampilkanNotifikasi('warning', "Semua file di dalam antrean telah selesai diproses!");
            }
        } else { tampilkanNotifikasi('error', data.message || "Gagal menyimpan data."); }
    } catch (error) {
        if (btnSimpan) { btnSimpan.disabled = false; btnSimpan.innerHTML = '<i class="bi bi-save me-1"></i> Simpan & Lanjut'; }
        tampilkanNotifikasi('error', "Gagal menyimpan data karena kesalahan internal server.");
    }
}

function renderQueue() {
    const list = document.getElementById('file_queue');
    list.innerHTML = '';
    queue.forEach((file, index) => {
        const div = document.createElement('div');
        div.className = 'list-group-item list-group-item-action p-2 ' + (index === currentIdx ? 'active' : '');
        div.innerHTML = `<small class="text-truncate">${file.name}</small> <span class="badge bg-secondary float-end" id="stat_${index}">OCR...</span>`;
        div.onclick = () => loadFile(index);
        list.appendChild(div);
        
        if(file.isGagal) {
            const badge = document.getElementById(`stat_${index}`);
            if(badge) { badge.innerText = "Gagal"; badge.className = "badge bg-danger float-end"; }
        } else if(file.hasilScan) {
            const badge = document.getElementById(`stat_${index}`);
            if(badge) { badge.innerText = "Siap"; badge.className = "badge bg-success float-end"; }
        }
    });
}

async function processQueueItem(file, index) {
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function() {
            const img = new Image();
            img.onload = async function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0);
                
                const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const data = imgData.data;
                for (let i = 0; i < data.length; i += 4) {
                    let r = data[i], g = data[i+1], b = data[i+2];
                    let v = (0.2126*r + 0.7152*g + 0.0722*b);
                    let nilaiBaru = v > 128 ? 255 : v * 0.5; 
                    data[i] = nilaiBaru; data[i+1] = nilaiBaru; data[i+2] = nilaiBaru;
                }
                ctx.putImageData(imgData, 0, 0);
                
                try {
                    const { data: { text } } = await Tesseract.recognize(canvas.toDataURL('image/jpeg', 0.9), 'ind');
                    
                    // INTEGRASI: DETEKSI DAN VALIDASI RESOLUSI GAMBAR TERLALU RENDAH / BURAM
                    if (!text || text.trim().length < 15) {
                        queue[index].isGagal = true;
                        queue[index].hasilScan = "GAMBAR_BURAM_TIDAK_TERBACA";
                        const statusBadge = document.getElementById(`stat_${index}`);
                        if(statusBadge) { statusBadge.innerText = "Gagal"; statusBadge.className = "badge bg-danger float-end"; }
                        if (index === currentIdx) {
                            tampilkanNotifikasi('error', `Berkas "${file.name}" gagal di-scan: Resolusi gambar terlalu rendah/buram!`);
                            document.getElementById('main_scan_form').reset();
                        }
                        return;
                    }

                    queue[index].hasilScan = text;
                    queue[index].isGagal = false;
                    const statusBadge = document.getElementById(`stat_${index}`);
                    if(statusBadge) { statusBadge.innerText = "Siap"; statusBadge.className = "badge bg-success float-end"; }
                    if (index === currentIdx) { extractDataToForm(queue[index].hasilScan, queue[index].name); }
                } catch (err) {
                    queue[index].isGagal = true;
                    const sb = document.getElementById(`stat_${index}`); if(sb) { sb.innerText = "Gagal"; sb.className = "badge bg-danger float-end"; }
                }
            };
            img.src = this.result;
        };
        reader.readAsDataURL(file);
    } else {
        const reader = new FileReader();
        reader.onload = async function() {
            try {
                const typedarray = new Uint8Array(this.result);
                const pdf = await pdfjsLib.getDocument(typedarray).promise;
                const page = await pdf.getPage(1);
                const viewport = page.getViewport({ scale: 2.5 });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height; canvas.width = viewport.width;
                await page.render({ canvasContext: context, viewport: viewport }).promise;
                
                const imageData = canvas.toDataURL('image/jpeg');
                const { data: { text } } = await Tesseract.recognize(imageData, 'ind');
                
                queue[index].hasilScan = text;
                queue[index].isGagal = false;
                const statusBadge = document.getElementById(`stat_${index}`);
                if(statusBadge) { statusBadge.innerText = "Siap"; statusBadge.className = "badge bg-success float-end"; }
                if (index === currentIdx) { extractDataToForm(queue[index].hasilScan, queue[index].name); }
            } catch (e) {
                queue[index].isGagal = true;
                const statusBadge = document.getElementById(`stat_${index}`); if(statusBadge) { statusBadge.innerText = "Gagal"; statusBadge.className = "badge bg-danger float-end"; }
            }
        };
        reader.readAsArrayBuffer(file);
    }
}

// INTEGRASI PREVIEW BARU: MENYEMBUHKAN LOOP SCAN GAMBAR & MENERAPKAN SISTEM DRAG MOUSE
function loadFile(index) {
    currentIdx = index;
    renderQueue();
    resetZoomFoto();
    
    const fileAktif = queue[index];
    const iframeViewer = document.getElementById('pdf_viewer');
    const imageViewer = document.getElementById('image_viewer');
    const zoomTools = document.getElementById('zoom_tools');
    
    if (fileAktif.type.startsWith('image/')) {
        iframeViewer.classList.add('d-none');
        imageViewer.classList.remove('d-none');
        zoomTools.classList.remove('d-none');
        
        // KUNCI PENYEMBUHAN LOOPING: Cukup buat object URL sekali saja jika belum terpasang
        const objekUrlBaru = URL.createObjectURL(fileAktif);
        if(!imageViewer.src || imageViewer.getAttribute('data-filename') !== fileAktif.name) {
            imageViewer.src = objekUrlBaru;
            imageViewer.setAttribute('data-filename', fileAktif.name);
        }
    } else {
        imageViewer.classList.add('d-none');
        zoomTools.classList.add('d-none');
        iframeViewer.classList.remove('d-none');
        iframeViewer.src = URL.createObjectURL(fileAktif);
    }

    // Ekstrak data jika memang sudah berhasil di-scan, jika gagal beri notifikasi kosong
    if(fileAktif.isGagal) {
        tampilkanNotifikasi('error', "Berkas ini tidak terbaca karena resolusi terlalu rendah.");
        document.getElementById('main_scan_form').reset();
    } else if(fileAktif.hasilScan) { 
        extractDataToForm(fileAktif.hasilScan, fileAktif.name); 
    } else {
        document.getElementById('main_scan_form').reset();
    }
}


const imgElement = document.getElementById('image_viewer');
const containerElement = document.getElementById('viewer_container');

containerElement.addEventListener('mousedown', (e) => {
    if (queue[currentIdx] && queue[currentIdx].type.startsWith('image/')) {
        isDragging = true;
        startX = e.clientX - translateX;
        startY = e.clientY - translateY;
    }
});

window.addEventListener('mousemove', (e) => {
    if (!isDragging) return;
    translateX = e.clientX - startX;
    translateY = e.clientY - startY;
    imgElement.style.transform = `translate(${translateX}px, ${translateY}px) scale(${skalaZoom})`;
});

window.addEventListener('mouseup', () => { isDragging = false; });

// Zoom Menggunakan Wheel/Scroll Mouse di Dalam Area Gambar
containerElement.addEventListener('wheel', (e) => {
    if (queue[currentIdx] && queue[currentIdx].type.startsWith('image/')) {
        e.preventDefault();
        const arahZoom = e.deltaY < 0 ? 0.1 : -0.1;
        zoomFoto(arahZoom);
    }
}, { passive: false });

function zoomFoto(nilai) {
    skalaZoom += nilai;
    if (skalaZoom < 0.4) skalaZoom = 0.4;
    if (skalaZoom > 4.0) skalaZoom = 4.0;
    imgElement.style.transform = `translate(${translateX}px, ${translateY}px) scale(${skalaZoom})`;
}

function resetZoomFoto() {
    skalaZoom = 1.0;
    translateX = 0;
    translateY = 0;
    if (imgElement) imgElement.style.transform = `translate(0px, 0px) scale(1)`;
}

function lewatiData() {
    const namaPelamar = document.getElementById('form_nama').value || 'Pelamar';
    queue.splice(currentIdx, 1);
    renderQueue();
    tampilkanNotifikasi('warning', `Data "${namaPelamar}" dilewati.`);
    if (queue.length > 0) loadFile(0);
    else {
        document.getElementById('pdf_viewer').src = "";
        document.getElementById('image_viewer').classList.add('d-none');
        document.getElementById('zoom_tools').classList.add('d-none');
    }
}

function extractDataToForm(text, fileName) {
    if (text === "GAMBAR_BURAM_TIDAK_TERBACA") { return; }
    
    const lowerText = text.toLowerCase();
    const lines = text.split('\n').map(line => line.trim()).filter(line => line.length > 0);
    let namaTerdeteksi = "";
    let bersihFile = fileName.replace(/\.[^/.]+$/, "").replace(/[^a-zA-Z]/g, ' ').trim();
    let kataNamaFile = bersihFile.split(/\s+/).filter(kata => {
        const sampahFile = ['cv', 'resume', 'lampiran', 'skripsi', 'pendaftaran', 'doc', 'pdf', 'scan', 'copy', 'hasil', 'file'];
        return kata.length > 1 && !sampahFile.includes(kata.toLowerCase());
    });
    let jumlahKata = kataNamaFile.length;
    if (jumlahKata >= 2 && jumlahKata <= 4) {
        namaTerdeteksi = kataNamaFile.join(' ');
    } else {
        for (let i = 0; i < Math.min(lines.length, 6); i++) {
            let baris = lines[i].replace(/[^a-zA-Z\s]/g, ' ').trim();
            const sampah = ['curriculum', 'vitae', 'resume', 'summary', 'education', 'contact', 'universitas', 'sekolah', 'nama', 'email', 'public relations', 'information', 'management'];
            if (baris.length >= 5 && baris.length <= 40) {
                if (!sampah.some(kata => baris.toLowerCase().includes(kata))) {
                    namaTerdeteksi = baris.split(/\s+/).slice(0, 4).join(' ');
                    break;
                }
            }
        }
    }
    const inputNama = document.getElementById('form_nama');
    if (inputNama) inputNama.value = namaTerdeteksi.toUpperCase();
    
    let emailHasil = "MOHON INPUT MANUAL";
    const namaPelamar = document.getElementById('form_nama').value.toLowerCase();
    const namaDepan = namaPelamar.split(' ')[0] || "";
    let textTanpaSpasi = text.replace(/\s*@\s*/g, "@").replace(/\s*\.\s*/g, ".").replace(/\s+/g, " ");
    const emailRegex = /([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})/g;
    const matches = textTanpaSpasi.match(emailRegex);
    if (matches) {
        const emailValid = matches.filter(e => {
            const isNotHandle = !e.startsWith("@");
            const hasDot = e.includes(".");
            const hasAt = e.includes("@");
            return isNotHandle && hasDot && hasAt;
        });
        if (emailValid.length > 0) {
            const matchDenganNama = emailValid.find(e => namaDepan.length > 2 && e.toLowerCase().includes(namaDepan));
            emailHasil = matchDenganNama || emailValid[0];
        }
    }
    document.getElementById('form_email').value = emailHasil;

    // --- INTEGRASI TERKUNCI: REGEX NOMOR WHATSAPP ---
    let waHasil = "MOHON INPUT MANUAL";
    const matchMentah = text.match(/(?:\+62|62|0)\s*8[0-9]{1,4}[-\s\.]?[0-9]{3,4}[-\s\.]?[0-9]{3,5}/);
    
    if (matchMentah) {
        let nomorBersih = matchMentah[0].replace(/[^0-9]/g, "");
        
        if (nomorBersih.startsWith("62")) {
            nomorBersih = "0" + nomorBersih.substring(2);
        }
        
        if (nomorBersih.length > 13) {
            waHasil = nomorBersih.substring(0, 13);
        } else {
            waHasil = nomorBersih;
        }
    }
    document.getElementById('form_wa').value = waHasil;

    const kampusKeywords = ['universitas', 'institut', 'politeknik', 'sekolah tinggi', 'sma', 'smk'];
    const kampusRegex = new RegExp('(' + kampusKeywords.join('|') + ')\\s+([a-zA-Z]+)(?:\\s+([a-zA-Z]+))?', 'i');
    const matchKampus = text.match(kampusRegex);
    if (matchKampus) {
        document.getElementById('form_kampus').value = matchKampus.slice(1).filter(Boolean).join(' ');
    } else if (lowerText.includes('diponegoro') || lowerText.includes('undip')) {
        document.getElementById('form_kampus').value = "Universitas Diponegoro";
    } else if (lowerText.includes('unnes') || lowerText.includes('universitas negeri semarang')) {
        document.getElementById('form_kampus').value = "Universitas Negeri Semarang";
    }
    
    const kamusJurusan = {'teknik informatika': 'Teknik Informatika', 'sistem informasi': 'Sistem Informasi', 'rekayasa perangkat运行 RPL': 'Rekayasa Perangkat Lunak', 'ilmu komputer': 'Ilmu Komputer', 'teknik elektro': 'Teknik Elektro', 'teknik telekomunikasi': 'Teknik Telekomunikasi', 'ilmu komunikasi': 'Ilmu Komunikasi', 'public relations': 'Public Relations', 'manajemen publik': 'Manajemen Publik', 'manajemen bisnis': 'Manajemen Bisnis', 'akuntansi': 'Akuntansi', 'manajemen': 'Manajemen', 'desain komunikasi visual': 'Desain Komunikasi Visual', 'dkv': 'Desain Komunikasi Visual', 'administrasi bisnis': 'Administrasi Bisnis', 'administrasi publik': 'Administrasi Publik', 'ekonomi pembangunan': 'Ekonomi Pembangunan'};
    const mappingDivisi = {'Markom': ['ilmu komunikasi', 'public relations', 'manajemen publik', 'administrasi publik'], 'IT / Elang IT': ['teknik informatika', 'sistem informasi', 'ilmu komputer', 'rekayasa perangkat lunak'], 'Technical': ['teknik elektro', 'teknik telekomunikasi'], 'Finance': ['akuntansi', 'manajemen', 'ekonomi pembangunan'], 'B2B': ['manajemen bisnis', 'administrasi bisnis'], 'Social Media 3ID & IM3': ['desain komunikasi visual', 'dkv']};
    let prodiAsli = "MOHON INPUT MANUAL";
    let divisiCocok = "MOHON INPUT MANUAL";
    for (let kunci in kamusJurusan) { if (lowerText.includes(kunci)) { prodiAsli = kamusJurusan[kunci]; break; } }
    for (let divName in mappingDivisi) { let jurusanList = mappingDivisi[divName]; if (jurusanList.some(j => prodiAsli.toLowerCase().includes(j))) { divisiCocok = divName; break; } }
    const elProdi = document.getElementById('form_prodi'); const elDivisi = document.getElementById('form_divisi');
    if (elProdi) elProdi.value = prodiAsli;
    if (elDivisi) elDivisi.value = divisiCocok;
    
    const yearMatch = text.match(/(2020|2021|2022|2023|2024|2025)\s*-\s*(?:present|sekarang)/i);
    if (yearMatch) {
        let estimasiSemester = (2026 - parseInt(yearMatch[1])) * 2;
        document.getElementById('form_semester').value = estimasiSemester > 0 ? estimasiSemester : 0;
    }
}
</script>
</body>
</html>