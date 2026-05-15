<?php
require_once '../config/config.php';
requireEmployer();

$page = (int)($_GET['page'] ?? 1);
if ($page < 1) {
    $page = 1;
}

$limit = 10;
$offset = ($page - 1) * $limit;
$user_id = (int)getUserId();

// Get jobs count
$count = $conn->query("SELECT COUNT(*) as total FROM jobs WHERE posted_by = $user_id");
$count_row = $count ? $count->fetch_assoc() : ['total' => 0];
$total_jobs = (int)($count_row['total'] ?? 0);
$total_pages = max(1, (int)ceil($total_jobs / $limit));

if ($page > $total_pages) {
    $page = $total_pages;
    $offset = ($page - 1) * $limit;
}

// Get jobs
$sql = "SELECT * FROM jobs WHERE posted_by = $user_id ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$jobs = $conn->query($sql);

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Security error';
    } else {
        $job_id = (int)($_POST['job_id'] ?? 0);
        $check = $conn->query("SELECT posted_by FROM jobs WHERE id = $job_id");
        $job = $check ? $check->fetch_assoc() : null;

        if ($job && (int)$job['posted_by'] === $user_id) {
            $conn->query("DELETE FROM applications WHERE job_id = $job_id");
            $conn->query("DELETE FROM saved_jobs WHERE job_id = $job_id");
            $conn->query("DELETE FROM transactions WHERE job_id = $job_id");
            $conn->query("DELETE FROM jobs WHERE id = $job_id");

            log_action('delete_job', "Deleted job ID: $job_id");
            $_SESSION['success'] = 'Job deleted successfully';
            header("Location: my-jobs.php");
            exit();
        }
    }
}

// Handle toggle active
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle-active') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Security error';
    } else {
        $job_id = (int)($_POST['job_id'] ?? 0);
        $check = $conn->query("SELECT posted_by, is_active FROM jobs WHERE id = $job_id");
        $job = $check ? $check->fetch_assoc() : null;

        if ($job && (int)$job['posted_by'] === $user_id) {
            $new_status = ((int)$job['is_active'] === 1) ? 0 : 1;
            $conn->query("UPDATE jobs SET is_active = $new_status WHERE id = $job_id");
            $_SESSION['success'] = 'Job status updated';
            header("Location: my-jobs.php");
            exit();
        }
    }
}

// Handle make featured request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'request-featured') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Security error';
    } else {
        $job_id = (int)($_POST['job_id'] ?? 0);
        $check = $conn->query("SELECT posted_by, is_featured FROM jobs WHERE id = $job_id");
        $job = $check ? $check->fetch_assoc() : null;

        if ($job && (int)$job['posted_by'] === $user_id) {
            // Check if already featured
            if ((int)$job['is_featured'] === 1) {
                $_SESSION['warning'] = 'This job is already featured';
            } else {
                // Create transaction record
                $amount = 500;
                $insert = $conn->query("INSERT INTO transactions (user_id, job_id, amount, transaction_type, status) 
                                       VALUES ($user_id, $job_id, $amount, 'featured_job', 'pending')");
                
                if ($insert) {
                    $_SESSION['success'] = 'Feature request submitted! Please wait for admin approval.';
                    log_action('request_featured', "Requested feature for job ID: $job_id");
                } else {
                    $_SESSION['error'] = 'Error processing request';
                }
            }
            header("Location: my-jobs.php");
            exit();
        }
    }
}

$page_title = 'My Job Postings - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <div class="my-jobs-header">
        <div>
            <h1>My Job Postings</h1>
            <p class="subtitle">Manage your job listings and view applications</p>
        </div>
        <a href="post-job.php" class="btn btn-success btn-large">+ Post New Job</a>
    </div>

    <?php if ($_SESSION['success'] ?? false): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if ($_SESSION['warning'] ?? false): ?>
        <div class="alert alert-warning"><?php echo $_SESSION['warning']; unset($_SESSION['warning']); ?></div>
    <?php endif; ?>

    <?php if ($_SESSION['error'] ?? false): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (!$jobs || $jobs->num_rows === 0): ?>
        <div class="no-results">
            <p>You haven't posted any jobs yet.</p>
            <a href="post-job.php" class="btn btn-primary">Post Your First Job</a>
        </div>
    <?php else: ?>
        <div class="jobs-management">
            <?php while ($job = $jobs->fetch_assoc()): ?>
                <div class="job-management-card <?php echo (int)$job['is_featured'] === 1 ? 'featured-job' : ''; ?>">
                    <div class="job-card-header">
                        <div>
                            <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                            <p class="company"><?php echo htmlspecialchars($job['company']); ?></p>
                        </div>
                        <div class="job-status">
                            <?php if ((int)$job['is_active'] === 1): ?>
                                <span class="badge badge-success">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                            <?php if ((int)$job['is_featured'] === 1): ?>
                                <span class="badge badge-primary">⭐ Featured</span>
                                <?php if (!empty($job['featured_until'])): ?>
                                    <small class="featured-info">Until: <?php echo date('M d, Y', strtotime($job['featured_until'])); ?></small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="job-management-stats">
                        <div class="stat">
                            <p class="stat-value"><?php echo (int)$job['views_count']; ?></p>
                            <p class="stat-label">Views</p>
                        </div>
                        <div class="stat">
                            <p class="stat-value"><?php echo (int)$job['applications_count']; ?></p>
                            <p class="stat-label">Applications</p>
                        </div>
                        <div class="stat">
                            <p class="stat-value"><?php echo timeAgo($job['created_at']); ?></p>
                            <p class="stat-label">Posted</p>
                        </div>
                    </div>

                    <div class="job-management-actions">
                        <a href="job-detail.php?id=<?php echo (int)$job['id']; ?>" class="btn btn-sm btn-primary">Preview</a>
                        <a href="edit-job.php?id=<?php echo (int)$job['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="my-applications.php?job_id=<?php echo (int)$job['id']; ?>" class="btn btn-sm btn-secondary">
                            Applications (<?php echo (int)$job['applications_count']; ?>)
                        </a>

                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <input type="hidden" name="action" value="toggle-active">
                            <input type="hidden" name="job_id" value="<?php echo (int)$job['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-outline">
                                <?php echo ((int)$job['is_active'] === 1) ? '⏸ Pause' : '▶ Activate'; ?>
                            </button>
                        </form>

                        <?php if ((int)$job['is_featured'] === 0): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="action" value="request-featured">
                                <input type="hidden" name="job_id" value="<?php echo (int)$job['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-info" title="Make this job featured for ₱500">
                                    ⭐ Featured (₱100)
                                </button>
                            </form>
                        <?php endif; ?>

                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="job_id" value="<?php echo (int)$job['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this job?')">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="btn btn-sm">← Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="btn btn-sm <?php echo $i === $page ? 'btn-primary' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="btn btn-sm">Next →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <style>
        .featured-job {
            border-left: 4px solid #f59e0b;
            background: linear-gradient(90deg, rgba(245, 158, 11, 0.05) 0%, transparent 100%);
        }
        
        .featured-info {
            display: block;
            color: #d97706;
            font-size: 11px;
            margin-top: 4px;
        }
        
        .btn-info {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
        }
        
        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
    </style>

    <?php require_once '../includes/footer.php'; ?>
</div>

</body>
</html>