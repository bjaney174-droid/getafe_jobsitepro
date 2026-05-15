<?php
require_once '../../config/config.php';
requireAdmin();

// Stats
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type != 'admin'")->fetch_assoc()['count'] ?? 0;
$total_employers = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'employer'")->fetch_assoc()['count'] ?? 0;
$total_jobseekers = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'jobseeker'")->fetch_assoc()['count'] ?? 0;
$total_jobs = $conn->query("SELECT COUNT(*) as count FROM jobs")->fetch_assoc()['count'] ?? 0;
$active_jobs = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE is_active = 1")->fetch_assoc()['count'] ?? 0;
$total_applications = $conn->query("SELECT COUNT(*) as count FROM applications")->fetch_assoc()['count'] ?? 0;
$unread_messages = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'unread'")->fetch_assoc()['count'] ?? 0;

// Recent data
$recent_users = $conn->query("SELECT * FROM users WHERE user_type != 'admin' ORDER BY created_at DESC LIMIT 5");
$recent_jobs = $conn->query("SELECT * FROM jobs ORDER BY created_at DESC LIMIT 5");

$page_title = 'Admin Dashboard - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../../includes/header.php';
?>

<div class="container">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="admin-dashboard">
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
            <p>System overview and quick management actions</p>
        </div>

        <div class="admin-stat-grid">
            <div class="admin-stat-card">
                <h3><?php echo (int)$total_users; ?></h3>
                <p>Total Users</p>
            </div>
            <div class="admin-stat-card">
                <h3><?php echo (int)$total_employers; ?></h3>
                <p>Employers</p>
            </div>
            <div class="admin-stat-card">
                <h3><?php echo (int)$total_jobseekers; ?></h3>
                <p>Job Seekers</p>
            </div>
            <div class="admin-stat-card">
                <h3><?php echo (int)$total_jobs; ?></h3>
                <p>Total Jobs</p>
            </div>
            <div class="admin-stat-card">
                <h3><?php echo (int)$active_jobs; ?></h3>
                <p>Active Jobs</p>
            </div>
            <div class="admin-stat-card">
                <h3><?php echo (int)$total_applications; ?></h3>
                <p>Applications</p>
            </div>
        </div>

        <div class="admin-actions-card">
            <h2>Quick Actions</h2>
            <div class="admin-actions">
                <a href="manage-users.php" class="btn btn-primary">Manage Users</a>
                <a href="manage-jobs.php" class="btn btn-primary">Manage Jobs</a>
                <a href="manage-applications.php" class="btn btn-primary">Manage Applications</a>
                <a href="manage-featured-jobs.php" class="btn btn-secondary">⭐ Featured Jobs</a>
                <a href="manage-messages.php" class="btn btn-warning">
                    Messages <?php if ($unread_messages > 0): ?><span class="badge"><?php echo $unread_messages; ?></span><?php endif; ?>
                </a>
                <a href="settings.php" class="btn btn-secondary">Settings</a>
                <a href="logs.php" class="btn btn-secondary">Logs</a>
                <a href="backup.php" class="btn btn-secondary">Backup</a>
            </div>
        </div>

        <div class="admin-grid-2">
            <section class="admin-panel-card">
                <h2>Recent Users</h2>
                <?php if ($recent_users && $recent_users->num_rows > 0): ?>
                    <?php while ($user = $recent_users->fetch_assoc()): ?>
                        <div class="list-row">
                            <div>
                                <p class="row-title"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                <p class="row-sub"><?php echo htmlspecialchars($user['email']); ?> • <?php echo htmlspecialchars(ucfirst($user['user_type'])); ?></p>
                            </div>
                            <small><?php echo timeAgo($user['created_at']); ?></small>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-text">No recent users.</p>
                <?php endif; ?>
            </section>

            <section class="admin-panel-card">
                <h2>Recent Jobs Posted</h2>
                <?php if ($recent_jobs && $recent_jobs->num_rows > 0): ?>
                    <?php while ($job = $recent_jobs->fetch_assoc()): ?>
                        <div class="list-row">
                            <div>
                                <p class="row-title"><?php echo htmlspecialchars($job['title']); ?></p>
                                <p class="row-sub"><?php echo htmlspecialchars($job['company']); ?> • <?php echo htmlspecialchars($job['category']); ?></p>
                            </div>
                            <small><?php echo timeAgo($job['created_at']); ?></small>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-text">No recent jobs.</p>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <?php require_once '../../includes/footer.php'; ?>
</div>

<style>
.admin-dashboard {
    margin-top: 20px;
}

.admin-header h1 {
    margin: 0;
}

.admin-header p {
    color: #6b7280;
    margin: 6px 0 18px;
}

.admin-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
    margin-bottom: 18px;
}

.admin-stat-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 14px;
    text-align: center;
    transition: all 0.3s ease;
}

.admin-stat-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.admin-stat-card h3 {
    margin: 0;
    font-size: 26px;
    color: #0d6efd;
}

.admin-stat-card p {
    margin: 6px 0 0;
    color: #6b7280;
    font-size: 13px;
}

.admin-actions-card,
.admin-panel-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 14px;
}

.admin-actions-card h2,
.admin-panel-card h2 {
    margin: 0 0 12px;
    font-size: 20px;
}

.admin-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    position: relative;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
}

.badge {
    display: inline-block;
    background: #ef4444;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    line-height: 24px;
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    margin-left: 4px;
}

.admin-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.list-row {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.list-row:last-child {
    border-bottom: 0;
}

.row-title {
    margin: 0;
    font-weight: 600;
}

.row-sub {
    margin: 2px 0 0;
    color: #6b7280;
    font-size: 14px;
}

.empty-text {
    color: #9ca3af;
    margin: 0;
}

@media (max-width: 900px) {
    .admin-grid-2 {
        grid-template-columns: 1fr;
    }
    
    .admin-stat-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    }
}

@media (max-width: 600px) {
    .admin-stat-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .admin-actions {
        flex-direction: column;
    }
    
    .admin-actions .btn {
        width: 100%;
    }
}
</style>

</body>
</html>