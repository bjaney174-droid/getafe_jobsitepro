<?php
require_once '../config/config.php';
$page_title = 'Terms and Conditions - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <div class="page-content" style="max-width: 900px; margin: 30px auto; background: #fff; padding: 30px; border-radius: 8px;">
        <h1>Terms and Conditions</h1>
        <p><strong>Last Updated:</strong> <?php echo date('F d, Y'); ?></p>

        <h3>1. Acceptance of Terms</h3>
        <p>By accessing and using this platform, you agree to comply with these Terms and Conditions.</p>

        <h3>2. User Accounts</h3>
        <p>Users are responsible for keeping account credentials secure and for all activities under their accounts.</p>

        <h3>3. Job Listings and Applications</h3>
        <p>Employers are responsible for the accuracy of job posts. Job seekers are responsible for the accuracy of their applications.</p>

        <h3>4. Prohibited Use</h3>
        <p>Users must not post misleading content, violate laws, or misuse platform features.</p>

        <h3>5. Content and Data</h3>
        <p>By submitting content, you grant the platform permission to display and process it for service functionality.</p>

        <h3>6. Limitation of Liability</h3>
        <p>The platform is provided “as is” and is not liable for indirect damages arising from use of the service.</p>

        <h3>7. Changes to Terms</h3>
        <p>We may update these terms at any time. Continued use of the platform means acceptance of the updated terms.</p>

        <h3>8. Contact</h3>
        <p>For concerns, contact <strong><?php echo getSetting('site_email', 'info@getafejobsite.com'); ?></strong>.</p>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</div>

</body>
</html>