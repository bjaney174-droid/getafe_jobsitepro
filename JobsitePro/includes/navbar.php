<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_type = getUserType();
$avatar = $_SESSION['avatar'] ?? '';
$avatar_src = !empty($avatar)
    ? (strpos($avatar, 'http') === 0 ? $avatar : BASE_URL . ltrim($avatar, '/'))
    : BASE_URL . 'assets/images/default-avatar.png';

// Get unread support tickets count for admin
$unread_tickets = 0;
if ($user_type === 'admin') {
    $result = $conn->query("SELECT COUNT(*) as count FROM support_tickets WHERE status = 'open'");
    $unread_tickets = $result->fetch_assoc()['count'] ?? 0;
}

// Get ticket count for logged-in users
$user_tickets = 0;
if (isLoggedIn() && $user_type !== 'admin') {
    $user_id = getUserId();
    $result = $conn->query("SELECT COUNT(*) as count FROM support_tickets WHERE user_id = $user_id AND status != 'closed'");
    $user_tickets = $result->fetch_assoc()['count'] ?? 0;
}
?>
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-logo">
            <a href="<?php echo BASE_URL; ?>">
                🏢 <?php echo getSetting('site_name', 'Getafe Jobsite'); ?>
            </a>
        </div>

        <div class="nav-toggle" id="navToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="nav-menu" id="navMenu">
            <?php if (!isLoggedIn() || $user_type !== 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>home.php" class="nav-link">Jobs</a>
                <a href="<?php echo BASE_URL; ?>about.php" class="nav-link">About</a>
                <a href="<?php echo BASE_URL; ?>contact.php" class="nav-link">Contact</a>
            <?php endif; ?>

            <?php if (isLoggedIn()): ?>
                <?php if ($user_type === 'jobseeker'): ?>
                    <a href="<?php echo BASE_URL; ?>my-applications.php" class="nav-link">Applications</a>
                    <a href="<?php echo BASE_URL; ?>saved-jobs.php" class="nav-link">Saved Jobs</a>
                    <a href="<?php echo BASE_URL; ?>my-support-tickets.php" class="nav-link">
                        🎫 Tickets <?php if ($user_tickets > 0): ?><span class="badge"><?php echo $user_tickets; ?></span><?php endif; ?>
                    </a>

                <?php elseif ($user_type === 'employer'): ?>
                    <a href="<?php echo BASE_URL; ?>post-job.php" class="nav-link">Post Job</a>
                    <a href="<?php echo BASE_URL; ?>my-jobs.php" class="nav-link">My Jobs</a>
                    <a href="<?php echo BASE_URL; ?>my-applications.php" class="nav-link">Applications</a>
                    <a href="<?php echo BASE_URL; ?>my-support-tickets.php" class="nav-link">
                        🎫 Tickets <?php if ($user_tickets > 0): ?><span class="badge"><?php echo $user_tickets; ?></span><?php endif; ?>
                    </a>

                <?php elseif ($user_type === 'admin'): ?>
                    <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="nav-link">Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>admin/manage-users.php" class="nav-link">Users</a>
                    <a href="<?php echo BASE_URL; ?>admin/manage-jobs.php" class="nav-link">Jobs</a>
                    <a href="<?php echo BASE_URL; ?>admin/manage-applications.php" class="nav-link">Applications</a>
                    <a href="<?php echo BASE_URL; ?>admin/support-tickets.php" class="nav-link">
                        🎫 Tickets <?php if ($unread_tickets > 0): ?><span class="badge badge-danger"><?php echo $unread_tickets; ?></span><?php endif; ?>
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/manage-messages.php" class="nav-link">Messages</a>
                    <a href="<?php echo BASE_URL; ?>admin/settings.php" class="nav-link">Settings</a>
                    
                <?php endif; ?>

                <div class="nav-user" id="navUserMenu">
                    <button type="button" class="avatar-btn" id="avatarBtn" aria-label="Open profile menu">
                        <img src="<?php echo htmlspecialchars($avatar_src, ENT_QUOTES, 'UTF-8'); ?>" alt="Avatar" class="avatar-small">
                    </button>

                    <div class="dropdown" id="profileDropdown">
                        <a href="<?php echo BASE_URL; ?>profile.php">Profile</a>
                        <a href="<?php echo BASE_URL; ?>edit-profile.php">Edit Profile</a>
                        <hr>
                        <a href="<?php echo BASE_URL; ?>logout.php" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>login.php" class="btn btn-primary">Login</a>
                <a href="<?php echo BASE_URL; ?>register.php" class="btn btn-secondary">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
/* Force clean avatar dropdown styling */
.nav-user {
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    margin-left: 8px !important;
}

.avatar-btn {
    border: 0 !important;
    background: transparent !important;
    padding: 0 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.avatar-small {
    width: 38px !important;
    height: 38px !important;
    border-radius: 50% !important;
    object-fit: cover !important;
    border: 2px solid #ffffff !important;
    display: block !important;
}

.dropdown {
    display: none !important;
    position: absolute !important;
    top: 46px !important;
    right: 0 !important;
    min-width: 190px !important;
    background: #fff !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 10px !important;
    box-shadow: 0 12px 28px rgba(0,0,0,0.16) !important;
    z-index: 99999 !important;
    overflow: hidden !important;
    padding: 6px 0 !important;
}

.dropdown.open {
    display: block !important;
}

.dropdown a {
    display: block !important;
    width: 100% !important;
    padding: 10px 14px !important;
    color: #1f2937 !important;
    text-decoration: none !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    line-height: 1.35 !important;
    background: #fff !important;
}

.dropdown a:hover {
    background: #f3f4f6 !important;
    color: #0d6efd !important;
}

.dropdown hr {
    margin: 6px 0 !important;
    border: 0 !important;
    border-top: 1px solid #ececec !important;
}

/* Badge styling */
.badge {
    display: inline-block;
    background: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 50%;
    font-size: 11px;
    font-weight: bold;
    margin-left: 5px;
    min-width: 20px;
    text-align: center;
}

.badge-danger {
    background: #dc3545;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .nav-menu {
        flex-direction: column !important;
        gap: 12px !important;
    }
}
</style>

<script>
(function () {
    const avatarBtn = document.getElementById('avatarBtn');
    const dropdown = document.getElementById('profileDropdown');
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');

    if (!avatarBtn || !dropdown) return;

    avatarBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('open');
    });

    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target) && !avatarBtn.contains(e.target)) {
            dropdown.classList.remove('open');
        }
    });

    // Mobile menu toggle
    if (navToggle) {
        navToggle.addEventListener('click', function () {
            navMenu.classList.toggle('active');
        });
    }
})();
</script>
