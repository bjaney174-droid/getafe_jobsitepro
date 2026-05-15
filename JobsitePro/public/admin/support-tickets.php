<?php 
require_once '../../config/config.php'; 
requireAdmin(); 

// Handle ticket status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $ticket_id = (int)$_POST['ticket_id'];
        $status = sanitize($_POST['status']);
        $conn->query("UPDATE support_tickets SET status = '$status' WHERE id = $ticket_id");
        $_SESSION['success'] = 'Ticket status updated.';
    }
    header("Location: support-tickets.php");
    exit();
}

// Fetch all support tickets
$page = (int)($_GET['page'] ?? 1);
$per_page = 20;
$offset = ($page - 1) * $per_page;

$total_result = $conn->query("SELECT COUNT(*) as total FROM support_tickets");
$total = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

$tickets = $conn->query("SELECT * FROM support_tickets ORDER BY created_at DESC LIMIT $offset, $per_page");

$page_title = 'Support Tickets - Admin';
require_once '../../includes/header.php'; 
?>

<div class="container">
    <?php require_once '../../includes/navbar.php'; ?>
    
    <div class="admin-wrap">
        <div class="admin-head">
            <h1>🎫 Support Tickets</h1>
            <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
        </div>

        <?php if ($_SESSION['success'] ?? false): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="admin-card">
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>From</th>
                            <th>Subject</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($tickets && $tickets->num_rows > 0): ?>
                            <?php while ($ticket = $tickets->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo htmlspecialchars($ticket['ticket_id']); ?></strong></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($ticket['name']); ?></strong><br>
                                        <small style="color: #666;"><?php echo htmlspecialchars($ticket['email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars(substr($ticket['subject'], 0, 50)); ?></td>
                                    <td>
                                        <span style="display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;
                                            background: <?php 
                                            if ($ticket['priority'] === 'high') echo '#fee2e2';
                                            elseif ($ticket['priority'] === 'medium') echo '#fef3c7';
                                            else echo '#dbeafe';
                                            ?>;
                                            color: <?php 
                                            if ($ticket['priority'] === 'high') echo '#991b1b';
                                            elseif ($ticket['priority'] === 'medium') echo '#78350f';
                                            else echo '#0c4a6e';
                                            ?>;
                                        ">
                                            <?php echo ucfirst($ticket['priority']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span style="display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;
                                            background: <?php 
                                            if ($ticket['status'] === 'open') echo '#fef3c7';
                                            elseif ($ticket['status'] === 'in_progress') echo '#dbeafe';
                                            else echo '#dcfce7';
                                            ?>;
                                            color: <?php 
                                            if ($ticket['status'] === 'open') echo '#78350f';
                                            elseif ($ticket['status'] === 'in_progress') echo '#0c4a6e';
                                            else echo '#15803d';
                                            ?>;
                                        ">
                                            <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                                        </span>
                                    </td>
                                    <td><small><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></small></td>
                                    <td>
                                        <a href="support-ticket-detail.php?ticket_id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="muted">No support tickets yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="btn <?php echo $page === $i ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 8px 12px; font-size: 12px;">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
</body>
</html>