<?php
require_once 'includes/config.php';

// Redirect if already logged in
redirectIfLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UploadX - Secure, fast, and beautiful file management system">
    <title>UploadX - Professional File Management</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-container">
                <a href="index.php" class="navbar-logo">
                    <img src="assets/images/UploadX-logo.png" alt="UploadX" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                    <span style="display: none;">UploadX</span>
                </a>
                <ul class="navbar-menu">
                    <li><a href="login.php" class="btn btn-ghost">Login</a></li>
                    <li><a href="signup.php" class="btn btn-primary">Sign Up</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-graphic hero-graphic-1">☁️</div>
        <div class="hero-graphic hero-graphic-2">📁</div>
        <div class="hero-graphic hero-graphic-3">🔒</div>
        
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    Your Files, Organized.<br>
                    Simple. Secure. Beautiful.
                </h1>
                <p class="hero-subtitle">
                    Upload, manage, and share your files with ease. Experience the most elegant file management system designed for modern workflows.
                </p>
                <div class="hero-cta">
                    <a href="signup.php" class="btn btn-primary btn-lg">Get Started Free</a>
                    <a href="#features" class="btn btn-secondary btn-lg">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <div class="features-header scroll-reveal">
                <h2 class="features-title">Everything You Need</h2>
                <p class="features-subtitle">
                    Powerful features wrapped in a beautiful, intuitive interface
                </p>
            </div>

            <div class="features-grid">
                <!-- Feature 1: Upload -->
                <div class="feature-card scroll-reveal">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                    </div>
                    <h3 class="feature-title">Lightning Fast Uploads</h3>
                    <p class="feature-description">
                        Drag and drop your files or click to browse. Upload multiple files simultaneously with real-time progress tracking and instant feedback.
                    </p>
                </div>

                <!-- Feature 2: Organization -->
                <div class="feature-card scroll-reveal">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                            <line x1="12" y1="11" x2="12" y2="17"></line>
                            <line x1="9" y1="14" x2="15" y2="14"></line>
                        </svg>
                    </div>
                    <h3 class="feature-title">Smart Organization</h3>
                    <p class="feature-description">
                        Automatically categorize photos and documents. Find any file in seconds with powerful search and filtering. Toggle between grid and list views.
                    </p>
                </div>

                <!-- Feature 3: Security -->
                <div class="feature-card scroll-reveal">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Bank-Grade Security</h3>
                    <p class="feature-description">
                        Your files are encrypted and protected. Secure authentication, password recovery, and complete privacy. Only you can access your files.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Get Started?</h2>
                <p class="cta-subtitle">
                    Join thousands of users managing their files the smart way
                </p>
                <a href="signup.php" class="btn cta-button">Create Your Free Account</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">UploadX</div>
                <p class="footer-text">Professional file management made simple</p>
                
                <div class="footer-links">
                    <a href="#" class="footer-link">About</a>
                    <a href="#" class="footer-link">Privacy Policy</a>
                    <a href="#" class="footer-link">Terms of Service</a>
                    <a href="#" class="footer-link">Contact</a>
                </div>
                
                <div class="footer-divider"></div>
                
                <p class="footer-copyright">
                    &copy; <?php echo date('Y'); ?> UploadX. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="assets/js/landing.js"></script>
</body>
</html>
