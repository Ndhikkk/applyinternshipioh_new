<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Home - Program Magang IOH Semarang<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Hero Section -->
<section class="hero-section" role="banner" aria-label="Hero section program magang">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-12 col-lg-6">
                <div class="hero-content text-center text-lg-start">
                    <h1 class="hero-title fw-bold mb-4" aria-label="Program Magang Indosat Ooredoo Hutchison Semarang">
                        Program Magang<br>Indosat Ooredoo Hutchison Semarang
                    </h1>
                    <p class="hero-description mb-5">
                        Siap melangkah ke dunia industri?<br>
                        Program Magang IOH Semarang membuka kesempatan bagi mahasiswa untuk mengasah keterampilan di bidang telekomunikasi dan teknologi digital melalui pengalaman kerja nyata bersama para profesional industri.
                    </p>
                    <div class="hero-buttons">
                        <a href="<?= base_url('pendaftaran') ?>" class="btn btn-primary-hero"
                            aria-label="Daftar sekarang program magang">
                            <i class="bi bi-pencil-square me-2" aria-hidden="true"></i>Daftar Sekarang
                        </a>
                        <a href="<?= base_url('progres') ?>" class="btn btn-secondary-hero"
                            aria-label="Cek progres pendaftaran">
                            <i class="bi bi-search me-2" aria-hidden="true"></i>Cek Progres
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="tech-visual text-center">
                    <img src="<?= base_url('assets/img/logo-ioh.svg') ?>" alt="IOH Semarang Logo" class="tech-logo"
                        loading="lazy" width="400" height="200">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section - Clean Design -->
<section class="benefits-section clean-section" aria-label="Keunggulan program magang">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold mb-3 section-main-title">Keunggulan Program Magang</h2>
            <p class="section-subtitle">Manfaat yang akan Anda dapatkan selama program magang</p>
        </div>
        <div class="row g-4">
            <!-- Benefit 1 -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="benefit-card-clean">
                    <div class="benefit-icon-clean">
                        <i class="bi bi-laptop"></i>
                    </div>
                    <div class="benefit-content">
                        <h3 class="benefit-title-clean">Pengalaman Praktis</h3>
                        <p class="benefit-description-clean">Belajar langsung dengan teknologi terkini dan infrastruktur
                            telekomunikasi modern di perusahaan telekomunikasi terkemuka.</p>
                    </div>
                </div>
            </div>

            <!-- Benefit 2 -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="benefit-card-clean">
                    <div class="benefit-icon-clean">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="benefit-content">
                        <h3 class="benefit-title-clean">Mentorship Expert</h3>
                        <p class="benefit-description-clean">Bimbingan langsung dari profesional berpengalaman di
                            industri telekomunikasi untuk pengembangan karir optimal.</p>
                    </div>
                </div>
            </div>

            <!-- Benefit 3 -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="benefit-card-clean">
                    <div class="benefit-icon-clean">
                        <i class="bi bi-award"></i>
                    </div>
                    <div class="benefit-content">
                        <h3 class="benefit-title-clean">Sertifikat Magang</h3>
                        <p class="benefit-description-clean">Dapatkan sertifikat resmi yang diakui industri untuk
                            meningkatkan nilai CV dan portofolio profesional Anda.</p>
                    </div>
                </div>
            </div>

            <!-- Benefit 4 -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="benefit-card-clean">
                    <div class="benefit-icon-clean">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                    <div class="benefit-content">
                        <h3 class="benefit-title-clean">Jaringan Profesional</h3>
                        <p class="benefit-description-clean">Bangun koneksi dengan profesional industri dan sesama
                            magang untuk masa depan karir yang lebih luas.</p>
                    </div>
                </div>
            </div>

            <!-- Benefit 5 -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="benefit-card-clean">
                    <div class="benefit-icon-clean">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <div class="benefit-content">
                        <h3 class="benefit-title-clean">Proyek Nyata</h3>
                        <p class="benefit-description-clean">Kerjakan proyek nyata yang berdampak langsung pada bisnis
                            perusahaan untuk pengalaman kerja autentik.</p>
                    </div>
                </div>
            </div>

            <!-- Benefit 6 -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="benefit-card-clean">
                    <div class="benefit-icon-clean">
                        <i class="bi bi-briefcase"></i>
                    </div>
                    <div class="benefit-content">
                        <h3 class="benefit-title-clean">Peluang Karir</h3>
                        <p class="benefit-description-clean">Akses peluang karir lebih besar dengan pengalaman magang di
                            perusahaan telekomunikasi terdepan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Divisi Section -->
<section class="py-5 bg-light" id="divisi">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center" data-aos="fade-up">
                <h2 class="fw-bold mb-3 divisi-main-title">Divisi Magang IOH Semarang</h2>
                <p class="divisi-subtitle mb-0">Bergabunglah dengan divisi yang sesuai dengan minat dan keahlian Anda
                </p>
            </div>
        </div>

        <!-- Grid Divisi -->
        <div class="divisi-grid">
            <!-- Divisi 1: Markom -->
            <div class="divisi-card" data-aos="fade-up" data-aos-delay="100">
                <div class="divisi-icon">
                    <i class="bi bi-megaphone"></i>
                </div>
                <h4 class="divisi-title">Markom (Marketing Communication)</h4>
                <p class="divisi-desc">Mengembangkan strategi komunikasi pemasaran, kampanye brand, dan manajemen
                    hubungan media untuk meningkatkan brand awareness.</p>
                <div class="divisi-tags">
                    <span class="divisi-tag">Marketing</span>
                    <span class="divisi-tag">Communication</span>
                    <span class="divisi-tag">Branding</span>
                </div>
            </div>

            <!-- Divisi 2: IT -->
            <div class="divisi-card" data-aos="fade-up" data-aos-delay="150">
                <div class="divisi-icon">
                    <i class="bi bi-laptop"></i>
                </div>
                <h4 class="divisi-title">IT (Information Technology)</h4>
                <p class="divisi-desc">Pengembangan sistem, maintenance infrastruktur IT, cybersecurity, dan
                    implementasi solusi teknologi untuk operasional perusahaan.</p>
                <div class="divisi-tags">
                    <span class="divisi-tag">Software</span>
                    <span class="divisi-tag">Infrastructure</span>
                    <span class="divisi-tag">Cybersecurity</span>
                </div>
            </div>

            <!-- Divisi 3: Technical -->
            <div class="divisi-card" data-aos="fade-up" data-aos-delay="200">
                <div class="divisi-icon">
                    <i class="bi bi-tools"></i>
                </div>
                <h4 class="divisi-title">Technical</h4>
                <p class="divisi-desc">Maintenance jaringan telekomunikasi, troubleshooting teknis, optimisasi performa
                    jaringan, dan implementasi teknologi terbaru.</p>
                <div class="divisi-tags">
                    <span class="divisi-tag">Network</span>
                    <span class="divisi-tag">Maintenance</span>
                    <span class="divisi-tag">Optimization</span>
                </div>
            </div>

            <!-- Divisi 4: Finance -->
            <div class="divisi-card" data-aos="fade-up" data-aos-delay="250">
                <div class="divisi-icon">
                    <i class="bi bi-calculator"></i>
                </div>
                <h4 class="divisi-title">Finance Circle Java & Region</h4>
                <p class="divisi-desc">Manajemen keuangan regional, analisis finansial, budgeting, reporting keuangan,
                    dan pengelolaan aset untuk wilayah Jawa.</p>
                <div class="divisi-tags">
                    <span class="divisi-tag">Finance</span>
                    <span class="divisi-tag">Regional</span>
                    <span class="divisi-tag">Analysis</span>
                </div>
            </div>

            <!-- Divisi 5: B2B -->
            <div class="divisi-card" data-aos="fade-up" data-aos-delay="300">
                <div class="divisi-icon">
                    <i class="bi bi-briefcase"></i>
                </div>
                <h4 class="divisi-title">B2B (Business to Business)</h4>
                <p class="divisi-desc">Pengembangan bisnis korporat, manajemen hubungan klien B2B, solusi enterprise,
                    dan penjualan produk untuk perusahaan.</p>
                <div class="divisi-tags">
                    <span class="divisi-tag">Corporate</span>
                    <span class="divisi-tag">Enterprise</span>
                    <span class="divisi-tag">Sales</span>
                </div>
            </div>

            <!-- Divisi 6: Sosmed -->
            <div class="divisi-card" data-aos="fade-up" data-aos-delay="350">
                <div class="divisi-icon">
                    <i class="bi bi-instagram"></i>
                </div>
                <h4 class="divisi-title">Social Media 3ID & IM3</h4>
                <p class="divisi-desc">Manajemen konten media sosial, engagement campaign, community management, dan
                    analisis performa platform 3ID & IM3.</p>
                <div class="divisi-tags">
                    <span class="divisi-tag">Social Media</span>
                    <span class="divisi-tag">Content</span>
                    <span class="divisi-tag">Community</span>
                </div>
            </div>

            <!-- Divisi 7: Daily Project -->
            <div class="divisi-card" data-aos="fade-up" data-aos-delay="400">
                <div class="divisi-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <h4 class="divisi-title">Daily Project</h4>
                <p class="divisi-desc">Manajemen proyek harian, koordinasi tim, monitoring progress, dan reporting
                    operasional untuk kegiatan bisnis sehari-hari.</p>
                <div class="divisi-tags">
                    <span class="divisi-tag">Project Management</span>
                    <span class="divisi-tag">Coordination</span>
                    <span class="divisi-tag">Monitoring</span>
                </div>
            </div>

            <!-- Divisi 8: Project Post Paid -->
            <div class="divisi-card" data-aos="fade-up" data-aos-delay="450">
                <div class="divisi-icon">
                    <i class="bi bi-credit-card"></i>
                </div>
                <h4 class="divisi-title">Project Post Paid</h4>
                <p class="divisi-desc">Pengembangan dan manajemen produk postpaid, customer retention, value-added
                    services, dan strategi pelayanan pelanggan pasca bayar.</p>
                <div class="divisi-tags">
                    <span class="divisi-tag">Postpaid</span>
                    <span class="divisi-tag">Product Development</span>
                    <span class="divisi-tag">Retention</span>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="row mt-5">
            <div class="col-12" data-aos="fade-up">
                <div class="cta-section">
                    <div class="cta-content text-center">
                        <h2 class="cta-title">Tertarik Bergabung?</h2>
                        <p class="cta-description">Pilih divisi yang sesuai dengan passion dan keahlian Anda. Kami akan
                            membantu mengembangkan potensi terbaik Anda bersama tim profesional di IOH Semarang.</p>

                        <div class="cta-checklist">
                            <div class="check-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Pengalaman Kerja Nyata</span>
                            </div>
                            <div class="check-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Mentoring Profesional</span>
                            </div>
                            <div class="check-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Sertifikat Magang</span>
                            </div>
                            <div class="check-item">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Peluang Karir</span>
                            </div>
                        </div>

                        <a href="<?= base_url('pendaftaran') ?>" class="btn btn-cta">
                            <i class="bi bi-rocket-takeoff"></i>
                            Daftar Sekarang
                        </a>

                        <div class="cta-statistics">
                            <div class="stat-item">
                                <div class="stat-number">100+</div>
                                <div class="stat-label">Magang Terima</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">93%</div>
                                <div class="stat-label">Kepuasan</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">8</div>
                                <div class="stat-label">Divisi</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number">1-6</div>
                                <div class="stat-label">Bulan Durasi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<noscript>
    <style>
        .hero-buttons {
            flex-direction: column;
            width: 100%;
        }

        .btn-primary-hero,
        .btn-secondary-hero {
            width: 100%;
        }
    </style>
</noscript>

<style>
    /* ===== VARIABLES ===== */
    :root {
        --indosat-red: #E31837;
        --indosat-dark-red: #8B1A3A;
        --indosat-blue: #0056B3;
        --indosat-gradient: linear-gradient(135deg, #E31837 0%, #8B1A3A 100%);
        --text-light: #ffffff;
        --text-gray: rgba(255, 255, 255, 0.85);
        --light-bg: #f8f9fa;
        --dark-bg: #2C3E50;
        --shadow-light: 0 10px 30px rgba(0, 0, 0, 0.08);
        --shadow-hover: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    /* ===== GLOBAL STYLES ===== */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f8f9fa;
    }

    /* ===== HERO SECTION ===== */
    .hero-section {
        background: var(--indosat-gradient);
        color: var(--text-light);
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding: 2rem 0;
        position: relative;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
        pointer-events: none;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-title {
        font-size: 2.5rem;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        font-weight: 700;
    }

    .hero-description {
        font-size: 1.2rem;
        line-height: 1.6;
        color: var(--text-gray);
        margin-bottom: 2rem;
    }

    .hero-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-primary-hero {
        background: var(--text-light);
        color: var(--indosat-red);
        border: none;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        font-size: 1.1rem;
    }

    .btn-primary-hero:hover {
        background: #f8f9fa;
        transform: translateY(-2px);
        color: var(--indosat-red);
        box-shadow: var(--shadow-hover);
    }

    .btn-secondary-hero {
        background: transparent;
        color: var(--text-light);
        border: 2px solid var(--text-light);
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        font-size: 1.1rem;
    }

    .btn-secondary-hero:hover {
        background: var(--text-light);
        color: var(--indosat-red);
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
    }

    .tech-visual {
        padding: 2rem;
        position: relative;
        z-index: 2;
    }

    .tech-logo {
        width: 100%;
        max-width: 400px;
        height: auto;
        display: block;
        margin: 0 auto;
    }

    /* ===== SECTION TITLES ===== */
    .section-main-title {
        background: var(--indosat-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        color: transparent;
        font-size: 2.8rem;
        font-weight: 800;
        position: relative;
        display: inline-block;
        padding-bottom: 20px;
        margin-bottom: 15px;
    }

    .section-main-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 120px;
        height: 5px;
        background: var(--indosat-gradient);
        border-radius: 3px;
    }

    .section-subtitle {
        font-size: 1.2rem;
        color: #6c757d;
        line-height: 1.5;
    }

    /* ===== DIVISI SECTION ===== */
    .divisi-main-title {
        background: var(--indosat-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        color: transparent;
        font-size: 2.8rem;
        font-weight: 800;
        position: relative;
        display: inline-block;
        padding-bottom: 20px;
        margin-bottom: 15px;
        text-shadow: 0 4px 6px rgba(227, 24, 55, 0.1);
    }

    .divisi-main-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 150px;
        height: 5px;
        background: linear-gradient(90deg,
                rgba(227, 24, 55, 0) 0%,
                #E31837 50%,
                rgba(139, 26, 58, 0) 100%);
        border-radius: 3px;
    }

    .divisi-subtitle {
        font-size: 1.2rem;
        color: #6c757d;
        line-height: 1.6;
        max-width: 600px;
        margin: 0 auto;
    }

    /* ===== CLEAN BENEFITS SECTION ===== */
    .clean-section {
        background: #ffffff;
        padding: 5rem 0;
        position: relative;
    }

    .benefit-card-clean {
        background: #ffffff;
        border: 1px solid rgba(227, 24, 55, 0.1);
        border-radius: 16px;
        padding: 30px 25px;
        height: 100%;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .benefit-card-clean::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #E31837 0%, #8B1A3A 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .benefit-card-clean:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(227, 24, 55, 0.08);
        border-color: rgba(227, 24, 55, 0.2);
    }

    .benefit-card-clean:hover::before {
        opacity: 1;
    }

    .benefit-icon-clean {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, rgba(227, 24, 55, 0.1) 0%, rgba(139, 26, 58, 0.05) 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        transition: all 0.3s ease;
    }

    .benefit-card-clean:hover .benefit-icon-clean {
        background: linear-gradient(135deg, #E31837 0%, #8B1A3A 100%);
        transform: scale(1.05);
    }

    .benefit-icon-clean i {
        font-size: 1.8rem;
        color: #E31837;
        transition: all 0.3s ease;
    }

    .benefit-card-clean:hover .benefit-icon-clean i {
        color: white;
    }

    .benefit-title-clean {
        color: #2C3E50;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 12px;
        line-height: 1.4;
    }

    .benefit-description-clean {
        color: #5a6c7d;
        font-size: 0.95rem;
        line-height: 1.6;
        margin: 0;
    }

    /* Atau alternatif lebih minimalis: */
    .benefit-card-minimal {
        background: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 30px;
        height: 100%;
        transition: all 0.3s ease;
        position: relative;
    }

    .benefit-card-minimal:hover {
        background: rgba(227, 24, 55, 0.02);
        transform: translateY(-3px);
    }

    .benefit-icon-minimal {
        width: 60px;
        height: 60px;
        background: #ffffff;
        border: 2px solid rgba(227, 24, 55, 0.1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .benefit-card-minimal:hover .benefit-icon-minimal {
        border-color: #E31837;
        transform: translateY(-3px);
    }

    .benefit-icon-minimal i {
        font-size: 1.5rem;
        color: #E31837;
    }

    /* Responsive styles untuk clean section */
    @media (max-width: 768px) {
        .clean-section {
            padding: 4rem 0;
        }

        .benefit-card-clean,
        .benefit-card-minimal {
            padding: 25px 20px;
        }

        .benefit-icon-clean,
        .benefit-icon-minimal {
            width: 60px;
            height: 60px;
            margin-bottom: 20px;
        }

        .benefit-icon-clean i {
            font-size: 1.5rem;
        }

        .benefit-title-clean {
            font-size: 1.2rem;
        }
    }

    @media (max-width: 576px) {
        .clean-section {
            padding: 3rem 0;
        }

        .benefit-card-clean,
        .benefit-card-minimal {
            padding: 20px 15px;
        }

        .benefit-icon-clean,
        .benefit-icon-minimal {
            width: 50px;
            height: 50px;
            margin-bottom: 15px;
        }

        .benefit-icon-clean i {
            font-size: 1.3rem;
        }

        .benefit-title-clean {
            font-size: 1.1rem;
        }

        .benefit-description-clean {
            font-size: 0.9rem;
        }
    }

    /* ===== STATS SECTION ===== */
    .stats-section {
        background: var(--light-bg);
        padding: 5rem 0;
    }

    .stat-item {
        padding: 1rem;
    }

    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        color: var(--indosat-red);
        margin-bottom: 0.5rem;
        line-height: 1;
    }

    .stat-label {
        color: #6c757d;
        font-size: 1.1rem;
        margin: 0;
        line-height: 1.4;
    }

    /* ===== DIVISI GRID ===== */
    .divisi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .divisi-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: var(--shadow-light);
        transition: all 0.3s ease;
        border: 1px solid rgba(227, 24, 55, 0.1);
        position: relative;
        overflow: hidden;
    }

    .divisi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--indosat-gradient);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .divisi-card:hover::before {
        opacity: 1;
    }

    .divisi-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-hover);
    }

    .divisi-icon {
        width: 60px;
        height: 60px;
        background: var(--indosat-gradient);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        color: white;
        font-size: 1.5rem;
    }

    .divisi-title {
        font-weight: 700;
        color: #E31837;
        margin-bottom: 10px;
        font-size: 1.2rem;
    }

    .divisi-desc {
        color: #5a6c7d;
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .divisi-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 15px;
    }

    .divisi-tag {
        background: rgba(227, 24, 55, 0.1);
        color: #E31837;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    /* ===== CTA SECTION ===== */
    .cta-section {
        background: linear-gradient(135deg, rgba(227, 24, 55, 0.1) 0%, rgba(139, 26, 58, 0.05) 100%);
        border-radius: 25px;
        border: 1px solid rgba(227, 24, 55, 0.2);
        position: relative;
        overflow: hidden;
        padding: 50px;
        margin-top: 40px;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(circle at 20% 30%, rgba(227, 24, 55, 0.08) 0%, transparent 50%),
            radial-gradient(circle at 80% 70%, rgba(139, 26, 58, 0.05) 0%, transparent 50%);
        z-index: 1;
    }

    .cta-content {
        position: relative;
        z-index: 2;
    }

    .cta-title {
        background: var(--indosat-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        color: transparent;
        font-weight: 800;
        font-size: 2.5rem;
        margin-bottom: 20px;
        line-height: 1.2;
        position: relative;
        display: inline-block;
        padding-bottom: 15px;
    }

    .cta-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: var(--indosat-gradient);
        border-radius: 2px;
    }

    .cta-description {
        color: #5a6c7d;
        font-size: 1.1rem;
        line-height: 1.7;
        margin-bottom: 30px;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }

    .btn-cta {
        background: var(--indosat-gradient);
        color: white;
        border: none;
        padding: 16px 45px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 25px rgba(227, 24, 55, 0.3);
        position: relative;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
    }

    .btn-cta::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.7s;
    }

    .btn-cta:hover::before {
        left: 100%;
    }

    .btn-cta:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 20px 40px rgba(227, 24, 55, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-cta i {
        font-size: 1.2rem;
        animation: rocketPulse 2s ease-in-out infinite;
    }

    @keyframes rocketPulse {

        0%,
        100% {
            transform: translateY(0) rotate(0);
        }

        50% {
            transform: translateY(-3px) rotate(5deg);
        }
    }

    .cta-checklist {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: center;
        margin-bottom: 30px;
    }

    .check-item {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.9);
        padding: 10px 20px;
        border-radius: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .check-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    }

    .check-item i {
        color: #28a745;
        font-size: 1.1rem;
        background: rgba(40, 167, 69, 0.1);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .check-item span {
        color: #2C3E50;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .cta-statistics {
        display: flex;
        justify-content: center;
        gap: 40px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .stat-item {
        text-align: center;
    }

    .cta-statistics .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: #E31837;
        line-height: 1;
        margin-bottom: 5px;
    }

    .cta-statistics .stat-label {
        color: #5a6c7d;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ===== RESPONSIVE STYLES ===== */
    @media (max-width: 1200px) {
        .hero-title {
            font-size: 2.2rem;
        }

        .section-main-title,
        .divisi-main-title,
        .cta-title {
            font-size: 2.4rem;
        }
    }

    @media (max-width: 992px) {
        .tech-logo {
            max-width: 350px;
        }
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
            text-align: center;
        }

        .hero-description {
            font-size: 1.1rem;
            text-align: center;
        }

        .hero-buttons {
            justify-content: center;
        }

        .tech-logo {
            max-width: 300px;
        }

        .section-main-title,
        .divisi-main-title,
        .cta-title {
            font-size: 2rem;
        }

        .benefit-card {
            padding: 2rem 1.5rem;
        }

        .stat-number {
            font-size: 2.5rem;
        }

        .benefits-section,
        .stats-section {
            padding: 4rem 0;
        }

        .cta-section {
            padding: 30px 20px;
            margin-top: 30px;
        }

        .cta-description {
            font-size: 1rem;
        }

        .btn-cta {
            padding: 14px 35px;
            font-size: 1rem;
            width: 100%;
            max-width: 300px;
        }

        .divisi-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .divisi-card {
            padding: 20px;
        }

        .cta-checklist {
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .check-item {
            width: 100%;
            max-width: 300px;
            justify-content: center;
        }

        .cta-statistics {
            gap: 20px;
        }

        .cta-statistics .stat-number {
            font-size: 2rem;
        }
    }

    @media (max-width: 576px) {
        .hero-section {
            padding: 3rem 0;
            min-height: auto;
        }

        .hero-title {
            font-size: 1.8rem;
        }

        .hero-buttons {
            flex-direction: column;
            width: 100%;
            gap: 0.75rem;
        }

        .btn-primary-hero,
        .btn-secondary-hero {
            width: 100%;
            justify-content: center;
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
        }

        .tech-logo {
            max-width: 250px;
        }

        .benefits-section,
        .stats-section {
            padding: 3rem 0;
        }

        .benefit-icon {
            width: 70px;
            height: 70px;
        }

        .benefit-icon i {
            font-size: 1.75rem;
        }

        .stat-number {
            font-size: 2rem;
        }

        .stat-label {
            font-size: 1rem;
        }

        .section-main-title,
        .divisi-main-title,
        .cta-title {
            font-size: 1.8rem;
        }

        .btn-cta {
            padding: 12px 30px;
        }

        .cta-statistics {
            flex-direction: column;
            gap: 15px;
        }
    }

    @media (max-width: 400px) {
        .hero-title {
            font-size: 1.6rem;
        }

        .hero-description {
            font-size: 1rem;
        }

        .benefit-card {
            padding: 1.5rem 1rem;
        }

        .stat-number {
            font-size: 1.8rem;
        }

        .section-main-title,
        .divisi-main-title,
        .cta-title {
            font-size: 1.6rem;
        }
    }

    /* Print Styles */
    @media print {

        .hero-buttons,
        .btn-cta {
            display: none !important;
        }

        .benefit-card,
        .divisi-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Debounce function untuk performance
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Smooth scrolling dengan debounce
        const handleSmoothScroll = function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        };

        // Attach event listeners dengan debounce
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', debounce(handleSmoothScroll, 100));
        });

        // Responsive behavior dengan debounce
        const handleResponsive = debounce(() => {
            const heroButtons = document.querySelector('.hero-buttons');
            if (!heroButtons) return;

            if (window.innerWidth < 576) {
                heroButtons.classList.add('flex-column', 'w-100');
            } else {
                heroButtons.classList.remove('flex-column', 'w-100');
            }
        }, 250);

        // Intersection Observer untuk lazy loading
        const observerOptions = {
            rootMargin: '50px 0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe benefit cards untuk subtle animation
        document.querySelectorAll('.benefit-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });

        // Event listeners
        window.addEventListener('resize', handleResponsive);

        // Initial calls
        handleResponsive();
    });
</script>
<?= $this->endSection() ?>