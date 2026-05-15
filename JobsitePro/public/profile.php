<?php
require_once '../config/config.php';
requireLogin();

$user_id = getUserId();

// Get user data
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Get statistics based on user type
if (getUserType() === 'jobseeker') {
    $stats_sql = "SELECT 
                    COUNT(*) as total_applications,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
                  FROM applications WHERE user_id = $user_id";
} else {
    $stats_sql = "SELECT 
                    COUNT(*) as total_jobs,
                    SUM(j.applications_count) as total_applications,
                    SUM(j.views_count) as total_views
                  FROM jobs j WHERE j.posted_by = $user_id";
}

$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

$page_title = 'My Profile - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-info">
                <img src="<?php echo $user['avatar'] ?? BASE_URL . 'assets/images/default-avatar.png'; ?>" 
                     alt="Profile" class="profile-avatar">
                
                <div class="profile-details">
                    <h1><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h1>
                    <p class="profile-email">📧 <?php echo $user['email']; ?></p>
                    <?php if ($user['phone']): ?>
                        <p class="profile-phone">📱 <?php echo $user['phone']; ?></p>
                    <?php endif; ?>
                    <p class="profile-location">📍 <?php echo $user['location']; ?></p>
                    <p class="profile-type">👤 <?php echo ucfirst($user['user_type']); ?></p>
                </div>
            </div>

            <div class="profile-actions">
                <a href="edit-profile.php" class="btn btn-primary">Edit Profile</a>
                <?php if (getUserType() === 'jobseeker'): ?>
                    <a href="saved-jobs.php" class="btn btn-secondary">Saved Jobs</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-stats">
            <h2>Statistics</h2>
            <div class="stats-grid">
                <?php if (getUserType() === 'jobseeker'): ?>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['total_applications'] ?? 0; ?></div>
                        <div class="stat-label">Total Applications</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['approved'] ?? 0; ?></div>
                        <div class="stat-label">Approved</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['pending'] ?? 0; ?></div>
                        <div class="stat-label">Pending</div>
                    </div>
                <?php else: ?>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['total_jobs'] ?? 0; ?></div>
                        <div class="stat-label">Jobs Posted</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['total_applications'] ?? 0; ?></div>
                        <div class="stat-label">Total Applications</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['total_views'] ?? 0; ?></div>
                        <div class="stat-label">Total Views</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="profile-sections">
            <?php if (getUserType() === 'jobseeker' && $user['bio']): ?>
                <section class="profile-section">
                    <h3>About Me</h3>
                    <p><?php echo nl2br($user['bio']); ?></p>
                </section>
            <?php endif; ?>

            <?php if (getUserType() === 'jobseeker' && $user['skills']): ?>
                <section class="profile-section">
                    <h3>Skills</h3>
                    <div class="skills-list">
                        <?php foreach (explode(',', $user['skills']) as $skill): ?>
                            <span class="skill-tag"><?php echo trim($skill); ?></span>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (getUserType() === 'employer'): ?>
                <section class="profile-section">
                    <h3>Company Information</h3>
                    <p><strong>Company:</strong> <?php echo $user['company']; ?></p>
                    <p><strong>Website:</strong> <?php echo $user['company_website'] ?? 'Not provided'; ?></p>
                    <p><strong>Description:</strong> <?php echo nl2br($user['company_info']); ?></p>
                </section>
            <?php endif; ?>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>