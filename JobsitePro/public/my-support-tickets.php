<?php
require_once '../config/config.php';
requireLogin();

$user_id = getUserId();
$page_title = 'My Support Tickets - ' . getSetting('site_name', 'Getafe Jobsite');

// Fetch user's support tickets
$query = "SELECT * FROM support_tickets WHERE user_id = $user_id ORDER BY created_at DESC";
$tickets = $conn->query($query);

require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="tickets-page" style="margin: 30px 0;">
        <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1>📋 My Support Tickets</h1>
            <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-primary">+ Create New Ticket</a>
        </div>

        <?php if ($tickets && $tickets->num_rows > 0): ?>
            <div class="tickets-list" style="display: grid; gap: 15px;">
                <?php while ($ticket = $tickets->fetch_assoc()): ?>
                    <a href="<?php echo BASE_URL; ?>view-ticket.php?id=<?php echo $ticket['id']; ?>" style="text-decoration: none; color: inherit;">
                        <div class="ticket-card" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; cursor: pointer; transition: all 0.3s; border-left: 4px solid #007bff;">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <div style="flex: 1;">
                                    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 8px;">
                                        <h3 style="margin: 0; font-size: 18px;"><?php echo htmlspecialchars($ticket['subject']); ?></h3>
                                        <span class="ticket-status-badge" style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;
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
                                    </div>
                                    <p style="color: #666; margin: 5px 0; font-size: 14px;">Ticket ID: <strong>#<?php echo htmlspecialchars($ticket['ticket_id']); ?></strong></p>
                                    <p style="color: #666; margin: 5px 0; font-size: 14px;"><?php echo htmlspecialchars(substr($ticket['message'], 0, 100)) . '...'; ?></p>
                                </div>
                                <div style="text-align: right; color: #999; font-size: 13px;">
                                    <?php echo date('M d, Y', strtotime($ticket['created_at'])); ?>
                                    <br>
                                    <small style="color: #999;"><?php echo date('H:i', strtotime($ticket['created_at'])); ?></small>
                                </div>
                            </div>
                            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f0f0f0;">
                                <small style="color: #999;">Priority: <strong style="color: #007bff;"><?php echo ucfirst($ticket['priority']); ?></strong></small>
                                <span style="margin: 0 10px; color: #ddd;">|</span>
                                <small style="color: #999;">Last updated: <?php echo timeAgo($ticket['updated_at']); ?></small>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-results" style="background: #f8f9fa; border: 2px dashed #ddd; padding: 60px 40px; border-radius: 12px; text-align: center;">
                <p style="font-size: 18px; color: #666; margin-bottom: 20px;">📭 You have no support tickets yet</p>
                <p style="color: #999; margin-bottom: 20px;">Need help? Send us a message and we'll get back to you!</p>
                <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-primary">Create Your First Ticket</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .tickets-page {
        max-width: 900px;
        margin: 0 auto;
    }

    .ticket-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
        border-left-color: #0056b3;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 15px;
        }
        
        .page-header a {
            width: 100%;
        }
    }
</style>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>
