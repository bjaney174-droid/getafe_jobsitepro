<?php
require_once '../config/config.php';
$page_title = 'Privacy Policy - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <div class="page-content" style="max-width: 900px; margin: 30px auto; background: #fff; padding: 30px; border-radius: 8px;">
        <h1>Privacy Policy</h1>
        <p><strong>Last Updated:</strong> <?php echo date('F d, Y'); ?></p>

        <p>
            <?php echo getSetting('site_name', 'Getafe Jobsite'); ?> values your privacy.
            This Privacy Policy explains how we collect, use, and protect your information
            when you use our website.
        </p>

        <h3>1. Information We Collect</h3>
        <p>
            We may collect personal information such as your name, email address, phone number,
            profile details, resume, and account activity when you register or use the platform.
        </p>

        <h3>2. How We Use Information</h3>
        <p>
            Your information is used to provide job-matching services, improve user experience,
            communicate important updates, and maintain platform security.
        </p>

        <h3>3. Data Sharing</h3>
        <p>
            We do not sell personal data. Information may be shared only with authorized employers,
            administrators, or when required by law.
        </p>

        <h3>4. Data Security</h3>
        <p>
            We implement reasonable technical and organizational measures to protect your data
            from unauthorized access, loss, or misuse.
        </p>

        <h3>5. User Rights</h3>
        <p>
            You may review, update, or request deletion of your account information by contacting us.
        </p>

        <h3>6. Contact Us</h3>
        <p>
            If you have questions about this policy, contact us at
            <strong><?php echo getSetting('site_email', 'info@getafejobsite.com'); ?></strong>.
        </p>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</div>

</body>
</html>