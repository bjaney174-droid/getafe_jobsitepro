<?php
require_once '../../config/config.php';
requireAdmin();

$page = (int)($_GET['page'] ?? 1);
$limit = 50;
$offset = ($page - 1) * $limit;

// Get total logs
$count_result = $conn->query("SELECT COUNT(*) as count FROM admin_logs");
$total_logs = $count_result->fetch_assoc()['count'];
$total_pages = ceil($total_logs / $limit);

// Get logs
$sql = "SELECT l.*, u.first_name, u.last_name, u.email
        FROM admin_logs l
        LEFT JOIN users u ON l.admin_id = u.id
        ORDER BY l.created_at DESC
        LIMIT $limit OFFSET $offset";

$logs = $conn->query($sql);

// Handle clear logs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'clear') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Security error';
    } else {
        if ($_POST['confirm'] === 'yes') {
            $conn->query("DELETE FROM admin_logs");
            log_action('clear_logs', 'Admin logs cleared');
            $_SESSION['success'] = 'All logs cleared';
            header("Location: logs.php");
            exit();
        }
    }
}

$page_title = 'Admin Logs - Admin';
require_once '../../includes/header.php';
?>

<div class="container">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="admin-panel">
        <div class="page-header">
            <h1>Admin Activity Logs</h1>
            <div>
                <a href="dashboard.php" class="btn btn-secondary">â† Dashboard</a>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="hidden" name="action" value="clear">
                    <input type="hidden" name="confirm" value="yes">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Clear all logs? This cannot be undone!');">Clear All Logs</button>
                </form>
            </div>
        </div>

        <?php if ($_SESSION['success'] ?? false): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if ($_SESSION['error'] ?? false): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="logs-stats">
            <div class="stat-box">
                <p class="stat-number"><?php echo $total_logs; ?></p>
                <p class="stat-label">Total Logs</p>
            </div>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $logs->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php 
                            if ($log['admin_id']) {
                                echo $log['first_name'] . ' ' . $log['last_name'];
                                echo '<br><small>' . $log['email'] . '</small>';
                            } else {
                                echo '<em>System</em>';
                            }
                            ?>
                        </td>
                        <td>
                            <code><?php echo $log['action']; ?></code>
                        </td>
                        <td>
                            <small><?php echo $log['details'] ?? '-'; ?></small>
                        </td>
                        <td>
                            <?php echo date('M d, Y H:i:s', strtotime($log['created_at'])); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php if ($total_logs === 0): ?>
            <div class="no-results">
                <p>No logs found.</p>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="btn btn-sm">â† Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="btn btn-sm <?php echo $i === $page ? 'btn-primary' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="btn btn-sm">Next â†’</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php require_once '../../includes/footer.php'; ?>
</div>

<?php require_once '../../includes/footer.php'; ?>
</body>
</html>
