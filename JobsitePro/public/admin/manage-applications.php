<?php
require_once '../../config/config.php';
requireAdmin();

$page = (int)($_GET['page'] ?? 1);
if ($page < 1) $page = 1;

$limit = 20;
$offset = ($page - 1) * $limit;
$status_filter = sanitize($_GET['status'] ?? '');

// Build base query
$sql = "SELECT a.*, u.first_name, u.last_name, u.email, j.title, j.company
        FROM applications a
        JOIN users u ON a.user_id = u.id
        JOIN jobs j ON a.job_id = j.id";

if (!empty($status_filter)) {
    $sql .= " WHERE a.status = '$status_filter'";
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update-status') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Security error';
    } else {
        $app_id = (int)($_POST['app_id'] ?? 0);
        $status = sanitize($_POST['status'] ?? '');

        if (in_array($status, ['pending', 'reviewed', 'approved', 'rejected', 'withdrawn'], true)) {
            $conn->query("UPDATE applications SET status = '$status', updated_at = NOW() WHERE id = $app_id");
            log_action('update_application_status', "Updated application $app_id to $status");
            $_SESSION['success'] = 'Application status updated';
        }
    }

    $redirect = "manage-applications.php?page=$page";
    if (!empty($status_filter)) $redirect .= "&status=" . urlencode($status_filter);
    header("Location: $redirect");
    exit();
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Security error';
    } else {
        $app_id = (int)($_POST['app_id'] ?? 0);
        $conn->query("DELETE FROM applications WHERE id = $app_id");
        log_action('delete_application', "Deleted application $app_id");
        $_SESSION['success'] = 'Application deleted';
    }

    $redirect = "manage-applications.php?page=$page";
    if (!empty($status_filter)) $redirect .= "&status=" . urlencode($status_filter);
    header("Location: $redirect");
    exit();
}

// Pagination count
$count_result = $conn->query($sql);
$total_applications = $count_result ? $count_result->num_rows : 0;
$total_pages = max(1, (int)ceil($total_applications / $limit));

if ($page > $total_pages) {
    $page = $total_pages;
    $offset = ($page - 1) * $limit;
}

// Data query with limit
$sql .= " ORDER BY a.applied_at DESC LIMIT $limit OFFSET $offset";
$applications = $conn->query($sql);

// Stats
$total = $conn->query("SELECT COUNT(*) as count FROM applications")->fetch_assoc()['count'] ?? 0;
$pending = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'pending'")->fetch_assoc()['count'] ?? 0;
$approved = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'approved'")->fetch_assoc()['count'] ?? 0;
$rejected = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'rejected'")->fetch_assoc()['count'] ?? 0;

$page_title = 'Manage Applications - Admin';
require_once '../../includes/header.php';
?>

<div class="container">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="admin-wrap">
        <div class="admin-head">
            <h1>Manage Applications</h1>
            <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
        </div>

        <?php if ($_SESSION['success'] ?? false): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if ($_SESSION['error'] ?? false): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="admin-card">
            <div class="status-filters">
                <a href="?status=" class="btn <?php echo empty($status_filter) ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
                <a href="?status=pending" class="btn <?php echo $status_filter === 'pending' ? 'btn-primary' : 'btn-secondary'; ?>">Pending</a>
                <a href="?status=reviewed" class="btn <?php echo $status_filter === 'reviewed' ? 'btn-primary' : 'btn-secondary'; ?>">Reviewed</a>
                <a href="?status=approved" class="btn <?php echo $status_filter === 'approved' ? 'btn-primary' : 'btn-secondary'; ?>">Approved</a>
                <a href="?status=rejected" class="btn <?php echo $status_filter === 'rejected' ? 'btn-primary' : 'btn-secondary'; ?>">Rejected</a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card"><h3><?php echo (int)$total; ?></h3><p>Total Applications</p></div>
            <div class="stat-card"><h3><?php echo (int)$pending; ?></h3><p>Pending</p></div>
            <div class="stat-card"><h3><?php echo (int)$approved; ?></h3><p>Approved</p></div>
            <div class="stat-card"><h3><?php echo (int)$rejected; ?></h3><p>Rejected</p></div>
        </div>

        <div class="admin-card">
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Job Title</th>
                            <th>Company</th>
                            <th>Applied</th>
                            <th>Status</th>
                            <th style="width: 230px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($applications && $applications->num_rows > 0): ?>
                            <?php while ($app = $applications->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?><br>
                                        <small class="muted"><?php echo htmlspecialchars($app['email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($app['title']); ?></td>
                                    <td><?php echo htmlspecialchars($app['company']); ?></td>
                                    <td><?php echo timeAgo($app['applied_at']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $app['status'] === 'approved' ? 'success' : ($app['status'] === 'rejected' ? 'danger' : 'primary'); ?>">
                                            <?php echo htmlspecialchars(ucfirst($app['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <input type="hidden" name="action" value="update-status">
                                            <input type="hidden" name="app_id" value="<?php echo (int)$app['id']; ?>">
                                            <select name="status" onchange="this.form.submit()" class="status-select">
                                                <option value="pending" <?php echo $app['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="reviewed" <?php echo $app['status'] === 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                                <option value="approved" <?php echo $app['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                                <option value="rejected" <?php echo $app['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                                <option value="withdrawn" <?php echo $app['status'] === 'withdrawn' ? 'selected' : ''; ?>>Withdrawn</option>
                                            </select>
                                        </form>

                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="app_id" value="<?php echo (int)$app['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this application?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="muted">No applications found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination" style="margin-top: 14px;">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&status=<?php echo urlencode($status_filter); ?>" class="btn btn-sm">← Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status_filter); ?>"
                           class="btn btn-sm <?php echo $i === $page ? 'btn-primary' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&status=<?php echo urlencode($status_filter); ?>" class="btn btn-sm">Next →</a>
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
.status-filters { display:flex; gap:8px; flex-wrap:wrap; }

.stats-grid {
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
    gap:10px;
    margin-bottom:14px;
}
.stat-card {
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:14px;
    text-align:center;
}
.stat-card h3 { margin:0; font-size:24px; color:#0d6efd; }
.stat-card p { margin:4px 0 0; color:#6b7280; font-size:13px; }

.table-wrap { overflow:auto; }
.admin-table { width:100%; border-collapse: collapse; }
.admin-table th, .admin-table td {
    padding:10px;
    border-bottom:1px solid #f0f0f0;
    text-align:left;
    vertical-align: top;
}
.status-select {
    padding:6px 8px;
    border:1px solid #d1d5db;
    border-radius:6px;
    margin-right:6px;
}
.muted { color:#6b7280; }
</style>

</body>
</html>