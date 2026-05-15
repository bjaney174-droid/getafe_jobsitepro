<?php
require_once '../../config/config.php';
requireAdmin();

$page = (int)($_GET['page'] ?? 1);
if ($page < 1) $page = 1;

$limit = 20;
$offset = ($page - 1) * $limit;
$search = sanitize($_GET['search'] ?? '');
$status_filter = sanitize($_GET['status'] ?? '');

// Handle actions first
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Security error';
    } else {
        $job_id = (int)($_POST['job_id'] ?? 0);
        $action = $_POST['action'] ?? '';

        if ($action === 'toggle-status') {
            $check = $conn->query("SELECT is_active FROM jobs WHERE id = $job_id");
            $job = $check ? $check->fetch_assoc() : null;
            if ($job) {
                $new_status = ((int)$job['is_active'] === 1) ? 0 : 1;
                $conn->query("UPDATE jobs SET is_active = $new_status WHERE id = $job_id");
                log_action('toggle_job_status', "Admin toggled job visibility. Job ID: $job_id, New status: " . ($new_status ? 'published' : 'hidden'));
                $_SESSION['success'] = $new_status ? 'Job published successfully.' : 'Job hidden successfully.';
            }
        } elseif ($action === 'delete') {
            // Locked policy: admin cannot hard-delete jobs
            $_SESSION['error'] = 'Permanent delete is disabled for admin safety. Use Hide instead.';
        }
    }

    $redirect = "manage-jobs.php?page=$page";
    if (!empty($search)) $redirect .= "&search=" . urlencode($search);
    if (!empty($status_filter)) $redirect .= "&status=" . urlencode($status_filter);
    header("Location: $redirect");
    exit();
}

$sql = "SELECT j.*, u.first_name, u.last_name, u.email
        FROM jobs j
        JOIN users u ON j.posted_by = u.id
        WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND (j.title LIKE '%$search%' OR j.company LIKE '%$search%' OR u.email LIKE '%$search%')";
}
if ($status_filter === 'active') {
    $sql .= " AND j.is_active = 1";
} elseif ($status_filter === 'inactive') {
    $sql .= " AND j.is_active = 0";
}

$count_result = $conn->query($sql);
$total_jobs = $count_result ? $count_result->num_rows : 0;
$total_pages = max(1, (int)ceil($total_jobs / $limit));
if ($page > $total_pages) {
    $page = $total_pages;
    $offset = ($page - 1) * $limit;
}

$sql .= " ORDER BY j.created_at DESC LIMIT $limit OFFSET $offset";
$jobs = $conn->query($sql);

$page_title = 'Manage Jobs - Admin';
require_once '../../includes/header.php';
?>

<div class="container">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="admin-wrap">
        <div class="admin-head">
            <h1>Manage Jobs</h1>
            <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
        </div>

        <?php if ($_SESSION['success'] ?? false): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if ($_SESSION['error'] ?? false): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="admin-card">
            <form method="GET" class="filters">
                <input type="text" name="search" placeholder="Search title/company/employer email" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                <select name="status">
                    <option value="">All Status</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Published</option>
                    <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Hidden</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>

        <div class="admin-card">
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Job</th>
                            <th>Company</th>
                            <th>Posted By</th>
                            <th>Views</th>
                            <th>Applications</th>
                            <th>Status</th>
                            <th>Posted</th>
                            <th style="width:210px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($jobs && $jobs->num_rows > 0): ?>
                            <?php while ($j = $jobs->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($j['title']); ?></td>
                                    <td><?php echo htmlspecialchars($j['company']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($j['first_name'] . ' ' . $j['last_name']); ?><br>
                                        <small class="muted"><?php echo htmlspecialchars($j['email']); ?></small>
                                    </td>
                                    <td><?php echo (int)$j['views_count']; ?></td>
                                    <td><?php echo (int)$j['applications_count']; ?></td>
                                    <td>
                                        <?php if ((int)$j['is_active'] === 1): ?>
                                            <span class="badge badge-success">Published</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Hidden</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($j['created_at'])); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>job-detail.php?id=<?php echo (int)$j['id']; ?>" class="btn btn-sm btn-primary">View</a>

                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <input type="hidden" name="action" value="toggle-status">
                                            <input type="hidden" name="job_id" value="<?php echo (int)$j['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline">
                                                <?php echo ((int)$j['is_active'] === 1) ? 'Hide' : 'Publish'; ?>
                                            </button>
                                        </form>

                                        <span class="muted" style="margin-left:6px;">Delete disabled</span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="muted">No jobs found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination" style="margin-top: 14px;">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>" class="btn btn-sm">← Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>"
                           class="btn btn-sm <?php echo $i === $page ? 'btn-primary' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>" class="btn btn-sm">Next →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php require_once '../../includes/footer.php'; ?>
</div>

<style>
.admin-wrap { margin-top: 20px; }
.admin-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
.admin-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:14px; margin-bottom:14px; }
.filters { display:flex; gap:8px; flex-wrap:wrap; }
.filters input, .filters select { padding:10px; border:1px solid #d1d5db; border-radius:8px; min-width:220px; }
.table-wrap { overflow:auto; }
.admin-table { width:100%; border-collapse: collapse; }
.admin-table th, .admin-table td { padding:10px; border-bottom:1px solid #f0f0f0; text-align:left; vertical-align: top; }
.muted { color:#6b7280; }
</style>

</body>
</html>