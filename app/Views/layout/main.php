<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title><?= $this->renderSection('title') ?> - IOH </title> -->
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?= $this->renderSection('meta_description') ?: 'Bergabunglah dengan Program Resmi Indosat Ooredoo Hutchison (IOH) Semarang. Kesempatan emas bagi mahasiswa untuk mendapatkan pengalaman kerja nyata di industri telekomunikasi digital.' ?>">
    <meta name="keywords" content="magang indosat, ioh semarang, internship semarang, magang telekomunikasi, magang IT, lowongan magang semarang, program magang mahasiswa, kampus merdeka">
    <meta name="author" content="Indosat Ooredoo Hutchison Semarang">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= current_url() ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:title" content="<?= $this->renderSection('title') ?> - IOH Internship Semarang">
    <meta property="og:description" content="<?= $this->renderSection('meta_description') ?: 'Gabung Program IOH. Kembangkan skill IT, Marketing, Finance, dan Teknikal bersama para expert industri.' ?>">
    <meta property="og:image" content="<?= base_url('assets/img/logo-ioh.svg') ?>">
    <meta property="og:site_name" content="IOH Internship Semarang">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= current_url() ?>">
    <meta property="twitter:title" content="<?= $this->renderSection('title') ?> - IOH Internship Semarang">
    <meta property="twitter:description" content="<?= $this->renderSection('meta_description') ?: 'Program Resmi Indosat Ooredoo Hutchison.' ?>">
    <meta property="twitter:image" content="<?= base_url('assets/img/logo-ioh.svg') ?>">

    <!-- Favicon -->
    <link rel="icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

    <style>
        :root {
            --indosat-red: #E31837;
            --indosat-dark-red: #8B1A3A;
            --indosat-blue: #0056B3;
            --indosat-gradient: linear-gradient(135deg, #E31837 0%, #8B1A3A 100%);
            --indosat-light: #FFF5F6;
        }

        /* Smooth Scroll & Base Styles */
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }

        /* Brand Logo */
        .brand-logo {
            height: 45px;
            margin-right: 12px;
            transition: all 0.3s ease;
            filter: brightness(0) invert(1);
        }

        .brand-logo:hover {
            transform: scale(1.08);
            filter: brightness(0) invert(1) drop-shadow(0 2px 8px rgba(255, 255, 255, 0.3));
        }

        /* Enhanced Navbar */
        .navbar {
            background: linear-gradient(135deg, #2C3E50 0%, #34495E 100%) !important;
            backdrop-filter: blur(15px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 18px 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar.scrolled {
            padding: 12px 0;
            background: rgba(44, 62, 80, 0.98) !important;
            backdrop-filter: blur(20px);
        }

        .nav-link {
            position: relative;
            margin: 0 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            color: rgba(255, 255, 255, 0.9) !important;
            padding: 8px 16px !important;
            border-radius: 25px;
        }

        .nav-link:hover {
            color: white !important;
            background: rgba(227, 24, 55, 0.1);
            transform: translateY(-1px);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background: var(--indosat-red);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 80%;
        }

        .nav-link.active {
            color: white !important;
            background: rgba(227, 24, 55, 0.15);
        }

        /* Enhanced Hero Section */
        .hero-section {
            background: var(--indosat-gradient);
            color: white;
            padding: 140px 0 100px;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
            display: flex;
            align-items: center;
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
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.08) 0%, transparent 50%);
            animation: floatBackground 20s ease-in-out infinite;
        }

        @keyframes floatBackground {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(10px, -10px) scale(1.02);
            }

            66% {
                transform: translate(-5px, 5px) scale(0.98);
            }
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        /* Enhanced Buttons */
        .btn-indosat {
            background: var(--indosat-gradient);
            border: none;
            color: white;
            padding: 14px 35px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(227, 24, 55, 0.4);
            position: relative;
            overflow: hidden;
        }

        .btn-indosat::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-indosat:hover::before {
            left: 100%;
        }

        .btn-indosat:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(227, 24, 55, 0.5);
        }

        .btn-outline-indosat {
            border: 2px solid var(--indosat-red);
            color: var(--indosat-red);
            background: transparent;
            padding: 14px 35px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-outline-indosat::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--indosat-gradient);
            transition: left 0.3s;
            z-index: -1;
        }

        .btn-outline-indosat:hover::before {
            left: 0;
        }

        .btn-outline-indosat:hover {
            color: white;
            border-color: transparent;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(227, 24, 55, 0.3);
        }

        /* Enhanced Cards */
        .feature-card {
            background: white;
            border: none;
            border-radius: 20px;
            padding: 35px 25px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--indosat-gradient);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 90px;
            height: 90px;
            background: var(--indosat-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 2.2rem;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(227, 24, 55, 0.3);
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 25px rgba(227, 24, 55, 0.4);
        }

        /* Enhanced Form Styling */
        .form-card {
            border: none;
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .form-card:hover {
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .form-card .card-header {
            background: var(--indosat-gradient);
            border: none;
            padding: 35px;
            position: relative;
            overflow: hidden;
        }

        .form-card .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }

            100% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
            }
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 14px 18px;
            transition: all 0.3s ease;
            font-size: 15px;
        }

        .form-control:focus {
            border-color: var(--indosat-red);
            box-shadow: 0 0 0 0.3rem rgba(227, 24, 55, 0.15);
            transform: translateY(-2px);
        }

        /* Enhanced Footer */
        .footer {
            background: linear-gradient(135deg, #2C3E50 0%, #34495E 100%);
            color: white;
            padding: 60px 0 25px;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--indosat-gradient);
        }

        .footer h5 {
            color: var(--indosat-red);
            margin-bottom: 20px;
            font-weight: 700;
        }

        .footer a {
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .footer a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: -2px;
            left: 0;
            background: var(--indosat-red);
            transition: width 0.3s ease;
        }

        .footer a:hover {
            color: var(--indosat-red);
            padding-left: 5px;
        }

        .footer a:hover::after {
            width: 100%;
        }

        /* Enhanced Animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            33% {
                transform: translateY(-15px) rotate(2deg);
            }

            66% {
                transform: translateY(-8px) rotate(-1deg);
            }
        }

        .floating {
            animation: float 4s ease-in-out infinite;
        }

        /* Progress Status */
        .status-badge {
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 0.85rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Enhanced Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 22px;
            height: 22px;
            border: 3px solid rgba(243, 243, 243, 0.3);
            border-top: 3px solid var(--indosat-red);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            box-shadow: 0 0 10px rgba(227, 24, 55, 0.3);
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Social Links Enhancement */
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .social-links a:hover {
            background: var(--indosat-red);
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 8px 20px rgba(227, 24, 55, 0.4);
            border-color: var(--indosat-red);
        }

        /* Responsive Enhancements */
        @media (max-width: 768px) {
            .hero-section {
                padding: 120px 0 80px;
                text-align: center;
                min-height: auto;
            }

            .feature-card {
                margin-bottom: 25px;
            }

            .brand-logo {
                height: 35px;
            }

            .nav-link {
                margin: 5px 0;
                text-align: center;
            }

            .btn-indosat,
            .btn-outline-indosat {
                padding: 12px 25px;
                font-size: 14px;
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--indosat-gradient);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--indosat-dark-red);
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= base_url() ?>">
                <!-- Hapus filter: brightness(0) invert(1) untuk logo berwarna asli -->
                <img src="<?= base_url('assets/img/tone-indosat.png') ?>" alt="Indosat Ooredoo Hutchison"
                    class="brand-logo" style="filter: none !important;">
                <span class="fw-bold d-none d-lg-block" style="font-size: 1.1rem;">Indosat Ooredoo Hutchison<br><small style="font-size: 0.8rem; font-weight: 500;">Future Talent Program</small></span>
                <span class="fw-bold d-lg-none" style="font-size: 1rem;">IOH Internship</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= current_url() == base_url() ? 'active' : '' ?>" href="<?= base_url() ?>">
                            <i class="bi bi-house me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= current_url() == base_url('pendaftaran') ? 'active' : '' ?>"
                            href="<?= base_url('pendaftaran') ?>">
                            <i class="bi bi-pencil-square me-1"></i>Pendaftaran
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= current_url() == base_url('progres') ? 'active' : '' ?>"
                            href="<?= base_url('progres') ?>">
                            <i class="bi bi-search me-1"></i>Cek Progres
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/login') ?>">
                            <i class="bi bi-shield-lock me-1"></i>Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main style="padding-top: 80px;">
        <?= $this->renderSection('content') ?>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        // Initialize AOS (Animate On Scroll)
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100,
            easing: 'ease-out-cubic'
        });

        // Enhanced Navbar scroll effect
        window.addEventListener('scroll', function () {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Enhanced smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Enhanced form loading states
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function () {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="loading-spinner me-2"></span>Memproses...';
                    submitBtn.disabled = true;

                    // Revert after 5 seconds if still processing (safety)
                    setTimeout(() => {
                        if (submitBtn.disabled) {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                    }, 5000);
                }
            });
        });

        // Enhanced floating animation
        document.addEventListener('DOMContentLoaded', function () {
            const floatingElements = document.querySelectorAll('.floating');
            floatingElements.forEach((el, index) => {
                el.style.animationDelay = (index * 0.3) + 's';
                el.style.animationDuration = (3 + index * 0.5) + 's';
            });
        });

        // Add parallax effect to hero section
        window.addEventListener('scroll', function () {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero-section');
            if (hero) {
                hero.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>