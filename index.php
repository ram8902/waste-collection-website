<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Collection Service - Home</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light modern-navbar">
        <div class="container">
            <a class="navbar-brand fw-bold brand-logo" href="index.php">
                <span class="brand-icon">♻️</span> Waste Collection
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="book_pickup.php">Book Pickup</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="track.php">Track Status</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">

                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item me-4">
                            <a class="nav-link" href="book_pickup.php">Book Pickup</a>
                        </li>
                        <li class="nav-item me-4">
                            <a class="nav-link" href="history.php">My History</a>
                        </li>
                        <li class="nav-item me-4">
                            <a class="nav-link" href="profile.php">Profile & Settings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn-nav" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item me-4">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item me-4">
                            <a class="nav-link btn-nav-primary" href="register.php">Get Started</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-outline-primary btn-sm btn-nav-outline" href="admin/login.php">Admin</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section-modern">
        <div class="hero-background"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <div class="hero-badge mb-4">
                        <span class="badge-text">✨ Trusted by 1000+ Customers</span>
                    </div>
                    <h1 class="hero-title">
                        Book Waste Pickup <span class="highlight-text">Easily</span>
                    </h1>
                    <p class="hero-subtitle">
                        Efficient, reliable, and eco-friendly waste collection service at your doorstep. 
                        Schedule pickups in minutes and track your requests in real-time.
                    </p>
                    <div class="hero-buttons mt-4">
                        <?php if (isLoggedIn()): ?>
                            <a href="book_pickup.php" class="btn btn-hero-primary">Book a Pickup Now</a>
                            <a href="#about" class="btn btn-hero-secondary">Learn More</a>
                        <?php else: ?>
                            <a href="register.php" class="btn btn-hero-primary">Get Started Free</a>
                            <a href="#about" class="btn btn-hero-secondary">Learn More</a>
                        <?php endif; ?>
                    </div>
                    <div class="hero-stats mt-5">
                        <div class="row g-4">
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-number">1200+</div>
                                    <div class="stat-label">Pickups</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-number">95%</div>
                                    <div class="stat-label">On-Time</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-number">24/7</div>
                                    <div class="stat-label">Support</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 hero-visual mt-5 mt-lg-0">
                    <div class="hero-card">
                        <div class="card-icon">♻️</div>
                        <h3>Eco-Friendly</h3>
                        <p>Responsible waste management for a cleaner planet</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section-modern">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="section-badge mb-3">About Us</div>
                    <h2 class="section-title mb-4">About Our Service</h2>
                    <p class="section-lead">
                        We provide comprehensive waste collection services to help you dispose of waste responsibly. 
                        Our mission is to make waste management easy, accessible, and environmentally friendly.
                    </p>
                    <p class="section-text">
                        Whether you need to dispose of household waste, electronic waste, plastic, or metal, 
                        we've got you covered. Simply book a pickup, and our trained staff will collect your waste 
                        at your preferred time.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section-modern">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-badge mb-3">Our Services</div>
                <h2 class="section-title">Types of Waste We Handle</h2>
                <p class="section-subtitle">Comprehensive waste management solutions for all your needs</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="service-card h-100">
                        <div class="service-icon">💻</div>
                        <h5 class="service-title">E-Waste</h5>
                        <p class="service-text">Electronic devices, computers, phones, and other electronic equipment.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card h-100">
                        <div class="service-icon">🥤</div>
                        <h5 class="service-title">Plastic</h5>
                        <p class="service-text">Plastic bottles, containers, packaging materials, and other plastic items.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card h-100">
                        <div class="service-icon">🔩</div>
                        <h5 class="service-title">Metal</h5>
                        <p class="service-text">Scrap metal, aluminum cans, steel items, and other metallic waste.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card h-100">
                        <div class="service-icon">🏠</div>
                        <h5 class="service-title">Household Waste</h5>
                        <p class="service-text">General household waste, organic waste, and other domestic refuse.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section-modern">
        <div class="container">
            <div class="text-center mb-5">
                <div class="section-badge mb-3">Get In Touch</div>
                <h2 class="section-title">Contact Us</h2>
                <p class="section-subtitle">We're here to help you with all your waste management needs</p>
            </div>
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="contact-info">
                        <div class="contact-item">
                            <div class="contact-icon">📞</div>
                            <div>
                                <h5>Phone</h5>
                                <p>+1 (555) 123-4567</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">✉️</div>
                            <div>
                                <h5>Email</h5>
                                <p>info@wastecollection.com</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">📍</div>
                            <div>
                                <h5>Address</h5>
                                <p>123 Green Street, Eco City, EC 12345</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">🕒</div>
                            <div>
                                <h5>Business Hours</h5>
                                <p>Monday - Friday: 8:00 AM - 6:00 PM<br>
                                Saturday: 9:00 AM - 4:00 PM<br>
                                Sunday: Closed</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact-map-card" style="border-radius:8px; position: relative;">
                        <iframe id="google-map" src="https://maps.google.com/maps?q=India&z=5&output=embed" width="100%" height="450" style="border:3px; min-height: 650px;" allowfullscreen="" loading="lazy">
                        </iframe>
                        <button onclick="getLocation()" class="btn btn-primary" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 10; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                            📍 Show My Location
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Waste Collection Service</h5>
                    <p class="text-muted">Making waste management easy and eco-friendly.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">
                        &copy; <?php echo date('Y'); ?> Waste Collection Service. All rights reserved.
                    </p>
                    <p class="text-muted">
                        <a href="index.php" class="text-decoration-none">Home</a> | 
                        <a href="#about" class="text-decoration-none">About</a> | 
                        <a href="#contact" class="text-decoration-none">Contact</a> |
                        <a href="admin/login.php" class="text-decoration-none">Admin</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <?php include 'includes/chatbot.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/theme.js"></script>
    <script>
        function getLocation() {
            if (navigator.geolocation) {
                // Show loading state
                const btn = document.querySelector('button[onclick="getLocation()"]');
                const originalText = btn.innerHTML;
                btn.innerHTML = '⌛ Locating...';
                btn.disabled = true;

                navigator.geolocation.getCurrentPosition(showPosition, showError);

                function showPosition(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const mapFrame = document.getElementById("google-map");
                    mapFrame.src = `https://maps.google.com/maps?q=${lat},${lng}&z=15&output=embed`;
                    
                    // Reset button
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }

                function showError(error) {
                    let msg = "An error occurred.";
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            msg = "User denied the request for Geolocation."
                            break;
                        case error.POSITION_UNAVAILABLE:
                            msg = "Location information is unavailable."
                            break;
                        case error.TIMEOUT:
                            msg = "The request to get user location timed out."
                            break;
                        case error.UNKNOWN_ERROR:
                            msg = "An unknown error occurred."
                            break;
                    }
                    alert(msg);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }
    </script>
</body>
</html>

