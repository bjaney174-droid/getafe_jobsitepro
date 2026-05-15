<?php
require_once '../../config/config.php';
requireAdmin();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Security error');
    }
    
    $action = $_POST['action'] ?? '';
    $ticket_id = (int)$_POST['ticket_id'];
    
    if ($action === 'delete') {
        $conn->query("DELETE FROM support_tickets WHERE id = $ticket_id");
        $_SESSION['success'] = "Ticket deleted.";
        log_action('delete_support_ticket', 'Deleted support ticket ID: ' . $ticket_id);
    } elseif ($action === 'change_status') {
        $new_status = sanitize($_POST['status'] ?? '');
        $conn->query("UPDATE support_tickets SET status = '$new_status' WHERE id = $ticket_id");
        $_SESSION['success'] = "Ticket status updated.";
        log_action('update_ticket_status', 'Changed ticket status to: ' . $new_status);
    }
    
    header("Location: manage-support-tickets.php");
    exit();
}

// Fetch all support tickets
$sort = $_GET['sort'] ?? 'created_at';
$order = $_GET['order'] ?? 'DESC';
$status_filter = $_GET['status'] ?? '';

$query = "SELECT * FROM support_tickets WHERE 1=1";

if ($status_filter) {
    $status_filter = sanitize($status_filter);
    $query .= " AND status = '$status_filter'";
}

$query .= " ORDER BY $sort $order";

$tickets_result = $conn->query($query);

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

        <!-- Filters -->
        <div class="admin-card" style="margin-bottom: 20px; padding: 15px; background: #f8f9fa;">
            <form method="GET" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                <div>
                    <label style="margin-right: 8px;">Filter by Status:</label>
                    <select name="status" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All Tickets</option>
                        <option value="open" <?php echo $status_filter === 'open' ? 'selected' : ''; ?>>Open</option>
                        <option value="in_progress" <?php echo $status_filter === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </div>
                <?php if ($status_filter): ?>
                    <a href="manage-support-tickets.php" class="btn btn-sm btn-secondary">Clear Filter</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tickets Table -->
        <div class="admin-card">
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Status</th>
                            <th>User</th>
                            <th>Subject</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($tickets_result && $tickets_result->num_rows > 0): ?>
                            <?php while ($ticket = $tickets_result->fetch_assoc()): ?>
                                <tr style="background: <?php echo $ticket['status'] === 'open' ? '#fef3c7' : '#fff'; ?>;">
                                    <td>
                                        <strong><?php echo htmlspecialchars($ticket['ticket_id']); ?></strong>
                                    </td>
                                    <td>
                                        <span style="display: inline-block; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: bold;
                                            background: <?php 
                                                $status_colors = [
                                                    'open' => '#fef3c7',
                                                    'in_progress' => '#dbeafe',
                                                    'closed' => '#d1fae5'
                                                ];
                                                echo $status_colors[$ticket['status']] ?? '#f3f4f6';
                                            ?>;
                                            color: <?php 
                                                $text_colors = [
                                                    'open' => '#78350f',
                                                    'in_progress' => '#0c4a6e',
                                                    'closed' => '#065f46'
                                                ];
                                                echo $text_colors[$ticket['status']] ?? '#374151';
                                            ?>;">
                                            <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($ticket['name']); ?></strong><br>
                                        <small style="color: #666;"><?php echo htmlspecialchars($ticket['email']); ?></small>
                                    </td>
                                    <td>
                                        <div style="max-width: 250px;">
                                            <?php echo htmlspecialchars(substr($ticket['subject'], 0, 50)); ?>
                                            <?php if (strlen($ticket['subject']) > 50): ?>...<?php endif; ?>
                                        </div>
                                    </td>
                                    <td><small><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></small></td>
                                    <td><small><?php echo date('M d, Y', strtotime($ticket['updated_at'])); ?></small></td>
                                    <td>
                                        <a href="view-support-ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                        
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this ticket?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="muted">No support tickets found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stats -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 30px;">
            <?php
            $open_count = $conn->query("SELECT COUNT(*) as count FROM support_tickets WHERE status = 'open'")->fetch_assoc()['count'];
            $inprogress_count = $conn->query("SELECT COUNT(*) as count FROM support_tickets WHERE status = 'in_progress'")->fetch_assoc()['count'];
            $closed_count = $conn->query("SELECT COUNT(*) as count FROM support_tickets WHERE status = 'closed'")->fetch_assoc()['count'];
            ?>
            
            <div class="admin-card" style="text-align: center; padding: 20px;">
                <h3 style="margin: 0; color: #78350f; font-size: 24px;"><?php echo $open_count; ?></h3>
                <p style="margin: 8px 0 0 0; color: #666;">Open Tickets</p>
            </div>
            
            <div class="admin-card" style="text-align: center; padding: 20px;">
                <h3 style="margin: 0; color: #0c4a6e; font-size: 24px;"><?php echo $inprogress_count; ?></h3>
                <p style="margin: 8px 0 0 0; color: #666;">In Progress</p>
            </div>
            
            <div class="admin-card" style="text-align: center; padding: 20px;">
                <h3 style="margin: 0; color: #065f46; font-size: 24px;"><?php echo $closed_count; ?></h3>
                <p style="margin: 8px 0 0 0; color: #666;">Closed</p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
</body>
</html>
