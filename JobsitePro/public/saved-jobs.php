<?php
require_once '../config/config.php';
requireJobSeeker();

// Handle unsave job
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'unsave-job') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Security error. Please try again.';
    } else {
        $job_id = (int)($_POST['job_id'] ?? 0);
        $user_id = (int)getUserId();

        if ($job_id > 0) {
            $conn->query("DELETE FROM saved_jobs WHERE user_id = $user_id AND job_id = $job_id");
            $_SESSION['success'] = 'Job removed from saved jobs.';
        }
    }

    header("Location: saved-jobs.php?page=" . (int)($_GET['page'] ?? 1));
    exit();
}

$page = (int)($_GET['page'] ?? 1);
if ($page < 1) {
    $page = 1;
}

$limit = 10;
$offset = ($page - 1) * $limit;
$user_id = (int)getUserId();

// Get saved jobs count
$count = $conn->query("SELECT COUNT(*) as total FROM saved_jobs WHERE user_id = $user_id");
$count_row = $count ? $count->fetch_assoc() : ['total' => 0];
$total_saved = (int)($count_row['total'] ?? 0);
$total_pages = max(1, (int)ceil($total_saved / $limit));

if ($page > $total_pages) {
    $page = $total_pages;
    $offset = ($page - 1) * $limit;
}

// Get saved jobs
$sql = "SELECT j.*, u.first_name, u.last_name, u.company, u.company_logo, sj.saved_at
        FROM saved_jobs sj
        JOIN jobs j ON sj.job_id = j.id
        JOIN users u ON j.posted_by = u.id
        WHERE sj.user_id = $user_id AND j.is_active = 1
        ORDER BY sj.saved_at DESC
        LIMIT $limit OFFSET $offset";

$jobs = $conn->query($sql);

$page_title = 'Saved Jobs - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <div class="saved-jobs-header">
        <h1>Saved Jobs</h1>
        <p class="subtitle">Your bookmarked job listings</p>
    </div>

    <?php if (!$jobs || $jobs->num_rows === 0): ?>
        <div class="no-results">
            <p>You haven't saved any jobs yet.</p>
            <a href="home.php" class="btn btn-primary">Browse Jobs</a>
        </div>
    <?php else: ?>
        <div class="saved-jobs-list">
            <?php while ($job = $jobs->fetch_assoc()): ?>
                <div class="job-card">
                    <div class="job-header">
                        <?php if (!empty($job['company_logo'])): ?>
                            <img src="<?php echo BASE_URL . ltrim($job['company_logo'], '/'); ?>"
                                 alt="<?php echo htmlspecialchars($job['company']); ?>" class="company-logo">
                        <?php else: ?>
                            <div class="company-logo-placeholder">
                                <?php echo strtoupper(substr($job['company'], 0, 2)); ?>
                            </div>
                        <?php endif; ?>

                        <div class="job-header-content">
                            <h3><a href="job-detail.php?id=<?php echo (int)$job['id']; ?>"><?php echo htmlspecialchars($job['title']); ?></a></h3>
                            <p class="company"><?php echo htmlspecialchars($job['company']); ?></p>
                        </div>

                        <div class="saved-date">
                            <small>Saved: <?php echo date('M d, Y', strtotime($job['saved_at'])); ?></small>
                        </div>
                    </div>

                    <div class="job-tags">
                        <span class="tag">📍 <?php echo htmlspecialchars($job['location']); ?></span>
                        <span class="tag">💼 <?php echo ucfirst(str_replace('-', ' ', $job['job_type'])); ?></span>
                        <span class="tag">🏷️ <?php echo htmlspecialchars($job['category']); ?></span>
                        <?php if (!empty($job['salary_min']) && !empty($job['salary_max'])): ?>
                            <span class="tag">💰 <?php echo formatSalary($job['salary_min'], $job['salary_max']); ?></span>
                        <?php endif; ?>
                    </div>

                    <p class="job-description"><?php echo htmlspecialchars(substr(strip_tags($job['description']), 0, 150)) . '...'; ?></p>

                    <div class="job-footer" style="display:flex; gap:8px; align-items:center; justify-content:space-between;">
                        <small><?php echo timeAgo($job['created_at']); ?></small>

                        <div style="display:flex; gap:8px;">
                            <a href="job-detail.php?id=<?php echo (int)$job['id']; ?>" class="btn btn-sm btn-primary">View</a>

                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="action" value="unsave-job">
                                <input type="hidden" name="job_id" value="<?php echo (int)$job['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
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

    <?php require_once '../includes/footer.php'; ?>
</div>

</body>
</html>