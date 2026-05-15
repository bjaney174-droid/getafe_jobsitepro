<?php
$site_email = getSetting('site_email', 'info@getafejobsite.com');
$contact_phone = getSetting('contact_phone', '0701918626');
$address = getSetting('address', 'Getafe, Bohol, Philippines');

$facebook_url = getSetting('facebook_url', 'https://facebook.com/');
$twitter_url = getSetting('twitter_url', 'https://twitter.com/');
$linkedin_url = getSetting('linkedin_url', 'https://linkedin.com/');
?>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h4>About <?php echo getSetting('site_name', 'Getafe Jobsite'); ?></h4>
            <p><?php echo getSetting('site_description', 'Find your dream job in Getafe, Bohol'); ?></p>
        </div>

        <div class="footer-section">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>about.php">About</a></li>
                <li><a href="<?php echo BASE_URL; ?>contact.php">Contact</a></li>
                <li><a href="<?php echo BASE_URL; ?>privacy-policy.php">Privacy Policy</a></li>
                <li><a href="<?php echo BASE_URL; ?>terms-and-conditions.php">Terms & Conditions</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Contact</h4>
            <p>Email: <?php echo htmlspecialchars($site_email); ?></p>
            <p>Phone: <?php echo htmlspecialchars($contact_phone); ?></p>
            <p>Address: <?php echo htmlspecialchars($address); ?></p>
        </div>

        <div class="footer-section">
            <h4>Follow Us</h4>
            <div class="social-links">
                <a href="<?php echo htmlspecialchars($facebook_url); ?>" class="social-link" target="_blank" rel="noopener noreferrer">Facebook</a>
                <a href="<?php echo htmlspecialchars($twitter_url); ?>" class="social-link" target="_blank" rel="noopener noreferrer">Twitter</a>
                <a href="<?php echo htmlspecialchars($linkedin_url); ?>" class="social-link" target="_blank" rel="noopener noreferrer">LinkedIn</a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2026 <?php echo getSetting('site_name', 'Getafe Jobsite'); ?>. All rights reserved.</p>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const userBtn = document.querySelector('.nav-user');
    const dropdown = document.querySelector('.dropdown');

    if (userBtn && dropdown) {
        userBtn.onclick = function (e) {
            e.stopPropagation();
            const isShown = dropdown.style.display === 'block';
            dropdown.style.display = isShown ? 'none' : 'block';
        };

        document.onclick = function () {
            dropdown.style.display = 'none';
        };
    }
});
</script>