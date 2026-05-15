<?php
require_once '../../config/config.php';
requireAdmin();

$page = (int)($_GET['page'] ?? 1);
if ($page < 1) $page = 1;

$limit = 20;
$offset = ($page - 1) * $limit;
$search = sanitize($_GET['search'] ?? '');
$user_type_filter = sanitize($_GET['type'] ?? '');

$sql = "SELECT * FROM users WHERE 1=1";
if (!empty($search)) {
    $sql .= " AND (first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%')";
}
if (!empty($user_type_filter)) {
    $sql .= " AND user_type = '$user_type_filter'";
}

$count_result = $conn->query($sql);
$total_users = $count_result ? $count_result->num_rows : 0;
$total_pages = max(1, (int)ceil($total_users / $limit));
if ($page > $total_pages) {
    $page = $total_pages;
    $offset = ($page - 1) * $limit;
}

$sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$users = $conn->query($sql);

// Handle status toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle-status') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Security error';
    } else {
        $user_id = (int)($_POST['user_id'] ?? 0);
        $check = $conn->query("SELECT is_active, user_type FROM users WHERE id = $user_id");
        $user_row = $check ? $check->fetch_assoc() : null;

        if ($user_row && $user_row['user_type'] !== 'admin') {
            $new_status = ((int)$user_row['is_active'] === 1) ? 0 : 1;
            $conn->query("UPDATE users SET is_active = $new_status WHERE id = $user_id");
            log_action('toggle_user_status', "User ID: $user_id");
            $_SESSION['success'] = 'User status updated';
        }
    }

    $redirect = "manage-users.php?page=$page";
    if (!empty($search)) $redirect .= "&search=" . urlencode($search);
    if (!empty($user_type_filter)) $redirect .= "&type=" . urlencode($user_type_filter);
    header("Location: $redirect");
    exit();
}

$page_title = 'Manage Users - Admin';
require_once '../../includes/header.php';
?>

<div class="container">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="admin-wrap">
        <div class="admin-head">
            <h1>Manage Users</h1>
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
                <input type="text" name="search" placeholder="Search name or email" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                <select name="type">
                    <option value="">All Types</option>
                    <option value="jobseeker" <?php echo $user_type_filter === 'jobseeker' ? 'selected' : ''; ?>>Job Seeker</option>
                    <option value="employer" <?php echo $user_type_filter === 'employer' ? 'selected' : ''; ?>>Employer</option>
                    <option value="admin" <?php echo $user_type_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>

        <div class="admin-card">
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th style="width: 170px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users && $users->num_rows > 0): ?>
                            <?php while ($u = $users->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($u['user_type'])); ?></td>
                                    <td>
                                        <?php if ((int)$u['is_active'] === 1): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                                    <td>
                                        <?php if ($u['user_type'] !== 'admin'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                                <input type="hidden" name="action" value="toggle-status">
                                                <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline">
                                                    <?php echo ((int)$u['is_active'] === 1) ? 'Deactivate' : 'Activate'; ?>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="muted">Protected</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="muted">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination" style="margin-top: 14px;">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($user_type_filter); ?>" class="btn btn-sm">← Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($user_type_filter); ?>"
                           class="btn btn-sm <?php echo $i === $page ? 'btn-primary' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($user_type_filter); ?>" class="btn btn-sm">Next →</a>
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
.admin-table th, .admin-table td { padding:10px; border-bottom:1px solid #f0f0f0; text-align:left; }
.muted { color:#6b7280; }
</style>

</body>
</html>