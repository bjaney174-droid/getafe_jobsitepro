<?php
require_once '../config/config.php';
requireLogin();

$user_id = (int)getUserId();
$status_filter = sanitize($_GET['status'] ?? '');
$job_filter = (int)($_GET['job_id'] ?? 0);

$sql = "SELECT a.*, j.title, j.company, j.location, j.salary_min, j.salary_max, j.created_at as job_posted, j.category
        FROM applications a 
        JOIN jobs j ON a.job_id = j.id 
        WHERE a.user_id = $user_id";

if (!empty($status_filter)) {
    $sql .= " AND a.status = '$status_filter'";
}

if (getUserType() === 'employer') {
    $sql = "SELECT a.*, j.title, j.company, j.id as job_id, u.first_name, u.last_name, u.email, u.phone, u.skills
            FROM applications a 
            JOIN jobs j ON a.job_id = j.id 
            JOIN users u ON a.user_id = u.id 
            WHERE j.posted_by = $user_id";

    if (!empty($status_filter)) {
        $sql .= " AND a.status = '$status_filter'";
    }

    if ($job_filter > 0) {
        $sql .= " AND a.job_id = $job_filter";
    }
}

$sql .= " ORDER BY a.applied_at DESC";
$applications = $conn->query($sql);

// Handle status update (employer only) - DEPRECATED, moved to view-application.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $app_id = (int)($_POST['app_id'] ?? 0);
        $new_status = sanitize($_POST['status'] ?? '');

        if (in_array($new_status, ['pending', 'reviewed', 'approved', 'rejected'], true)) {
            $check = $conn->query("SELECT j.posted_by FROM applications a JOIN jobs j ON a.job_id = j.id WHERE a.id = $app_id");
            if ($check && $check->num_rows > 0) {
                $row = $check->fetch_assoc();
                if ((int)$row['posted_by'] === $user_id) {
                    $conn->query("UPDATE applications SET status = '$new_status', updated_at = NOW() WHERE id = $app_id");
                    log_action('application_status_updated', "Application ID: $app_id, Status: $new_status");
                }
            }
        }
    }

    $redirect = "my-applications.php";
    $params = [];
    if (!empty($status_filter)) $params[] = "status=" . urlencode($status_filter);
    if ($job_filter > 0) $params[] = "job_id=" . $job_filter;
    if (!empty($params)) $redirect .= "?" . implode("&", $params);

    header("Location: $redirect");
    exit();
}

$page_title = 'Applications - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <h1><?php echo getUserType() === 'employer' ? 'Applications Received' : 'My Applications'; ?></h1>

    <div style="margin: 20px 0; display: flex; gap: 10px; flex-wrap: wrap;">
        <?php
            $base_link = 'my-applications.php';
            if ($job_filter > 0) $base_link .= '?job_id=' . $job_filter;
            $all_link = $job_filter > 0 ? 'my-applications.php?job_id=' . $job_filter : 'my-applications.php';
            $pending_link = 'my-applications.php?status=pending' . ($job_filter > 0 ? '&job_id=' . $job_filter : '');
            $reviewed_link = 'my-applications.php?status=reviewed' . ($job_filter > 0 ? '&job_id=' . $job_filter : '');
            $approved_link = 'my-applications.php?status=approved' . ($job_filter > 0 ? '&job_id=' . $job_filter : '');
            $rejected_link = 'my-applications.php?status=rejected' . ($job_filter > 0 ? '&job_id=' . $job_filter : '');
        ?>
        <a href="<?php echo $all_link; ?>" class="btn <?php echo empty($status_filter) ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
        <a href="<?php echo $pending_link; ?>" class="btn <?php echo $status_filter === 'pending' ? 'btn-primary' : 'btn-secondary'; ?>">Pending</a>
        <a href="<?php echo $reviewed_link; ?>" class="btn <?php echo $status_filter === 'reviewed' ? 'btn-primary' : 'btn-secondary'; ?>">Reviewed</a>
        <a href="<?php echo $approved_link; ?>" class="btn <?php echo $status_filter === 'approved' ? 'btn-primary' : 'btn-secondary'; ?>">Approved</a>
        <a href="<?php echo $rejected_link; ?>" class="btn <?php echo $status_filter === 'rejected' ? 'btn-primary' : 'btn-secondary'; ?>">Rejected</a>
    </div>

    <div class="applications-list">
        <?php if (!$applications || $applications->num_rows === 0): ?>
            <div class="no-results">
                <p>No applications found.</p>
            </div>
        <?php else: ?>
            <?php while ($app = $applications->fetch_assoc()): ?>
                <a href="<?php echo BASE_URL; ?>view-application.php?id=<?php echo (int)$app['id']; ?>" style="text-decoration: none; color: inherit;">
                    <div class="application-card <?php echo htmlspecialchars($app['status']); ?>" style="cursor: pointer; transition: all 0.3s;">
                        <div class="app-header">
                            <?php if (getUserType() === 'employer'): ?>
                                <h3><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></h3>
                                <p style="color: #666;"><?php echo htmlspecialchars($app['title']); ?></p>
                            <?php else: ?>
                                <h3><?php echo htmlspecialchars($app['title']); ?></h3>
                                <p style="color: #666;"><?php echo htmlspecialchars($app['company']); ?></p>
                            <?php endif; ?>
                            <span class="status-badge status-<?php echo htmlspecialchars($app['status']); ?>">
                                <?php echo strtoupper(htmlspecialchars($app['status'])); ?>
                            </span>
                        </div>

                        <div class="app-details">
                            <?php if (getUserType() === 'employer'): ?>
                                <p>📧 <?php echo htmlspecialchars($app['email']); ?></p>
                                <?php if (!empty($app['phone'])): ?>
                                    <p>📱 <?php echo htmlspecialchars($app['phone']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($app['skills'])): ?>
                                    <p>💼 <strong>Skills:</strong> <?php echo htmlspecialchars($app['skills']); ?></p>
                                <?php endif; ?>
                            <?php else: ?>
                                <p>🏢 <strong><?php echo htmlspecialchars($app['company']); ?></strong></p>
                                <p>📍 <?php echo htmlspecialchars($app['location']); ?></p>
                                <?php if (!empty($app['salary_min']) && !empty($app['salary_max'])): ?>
                                    <p>💰 ₱<?php echo number_format($app['salary_min']); ?> - ₱<?php echo number_format($app['salary_max']); ?>/month</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="app-footer">
                            <small>Applied: <?php echo date('M d, Y', strtotime($app['applied_at'])); ?></small>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</div>

<style>
    .application-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    
    .application-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
        border-color: #007bff;
    }
    
    .app-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }
    
    .app-header h3 {
        margin: 0 0 5px 0;
        font-size: 18px;
    }
    
    .app-header p {
        margin: 0;
        color: #666;
    }
    
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .status-pending {
        background: #fef3c7;
        color: #78350f;
    }
    
    .status-reviewed {
        background: #dbeafe;
        color: #0c4a6e;
    }
    
    .status-approved {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .app-details {
        margin: 10px 0;
        padding: 10px 0;
        border-top: 1px solid #f0f0f0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .app-details p {
        margin: 5px 0;
        font-size: 14px;
        color: #666;
    }
    
    .app-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        font-size: 13px;
        color: #999;
    }
    
    .no-results {
        background: #f8f9fa;
        border: 2px dashed #ddd;
        padding: 60px 40px;
        border-radius: 12px;
        text-align: center;
    }
    
    .no-results p {
        font-size: 18px;
        color: #666;
    }
</style>

</body>
</html>
