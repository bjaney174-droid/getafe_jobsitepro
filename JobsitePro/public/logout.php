<?php
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    log_action('logout', "User logged out");
    session_destroy();
    header("Location: " . BASE_URL);
    exit();
}

$page_title = 'Logout - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <div class="logout-confirm">
        <div class="logout-box">
            <h2>Confirm Logout</h2>
            <p>Are you sure you want to logout?</p>
            
            <div class="logout-buttons">
                <a href="?confirm=yes" class="btn btn-danger">Yes, Logout</a>
                <a href="home.php" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>