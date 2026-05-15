<?php
require_once '../config/config.php';

$page_title = 'About Us - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <div class="about-page">
        <section class="about-hero">
            <h1><?php echo getSetting('site_name', 'Getafe Jobsite'); ?></h1>
            <p><?php echo getSetting('site_description', 'Find your dream job in Getafe, Bohol'); ?></p>
        </section>

        <section class="about-section">
            <h2>Our Mission</h2>
            <p>
                We are dedicated to connecting talented professionals with great employers in Getafe, Bohol.
                Our platform makes it easy for job seekers to find their dream jobs and for employers to find
                the perfect candidates for their teams.
            </p>
        </section>

        <section class="about-section">
            <h2>Why Choose Us?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3>🎯 Easy to Use</h3>
                    <p>Simple and intuitive interface for both job seekers and employers</p>
                </div>

                <div class="feature-card">
                    <h3>🔒 Secure & Safe</h3>
                    <p>Your data is protected with industry-standard security measures</p>
                </div>

                <div class="feature-card">
                    <h3>⚡ Fast & Reliable</h3>
                    <p>Quick job matching and responsive customer support</p>
                </div>

                <div class="feature-card">
                    <h3>🌟 Quality Jobs</h3>
                    <p>Verified job listings from reputable companies</p>
                </div>

                <div class="feature-card">
                    <h3>💼 Professional</h3>
                    <p>Built for professionals, by professionals</p>
                </div>

                <div class="feature-card">
                    <h3>🤝 Community</h3>
                    <p>Join thousands of professionals in Getafe</p>
                </div>
            </div>
        </section>
<section class="about-section">
    <h2>Contact Information</h2>
    <div class="contact-details">
        <p><strong>Email:</strong> <?php echo htmlspecialchars(getSetting('site_email', 'info@getafejobsite.com')); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars(getSetting('contact_phone', '0701918626')); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars(getSetting('address', 'Getafe, Bohol, Philippines')); ?></p>
    </div>
</section>
        
    </div>

    <?php require_once '../includes/footer.php'; ?>
</div>

</body>
</html>