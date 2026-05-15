<?php
require_once '../config/config.php';
requireLogin();

$user_id = getUserId();
$ticket_id = (int)($_GET['ticket_id'] ?? 0);
$user_type = getUserType();

// Fetch ticket
$ticket_query = "SELECT * FROM support_tickets WHERE id = $ticket_id";
if ($user_type === 'user' || $user_type === 'jobseeker' || $user_type === 'employer') {
    $ticket_query .= " AND user_id = $user_id";
}

$ticket_result = $conn->query($ticket_query);

if (!$ticket_result || $ticket_result->num_rows === 0) {
    header("Location: " . BASE_URL . "my-support-tickets.php");
    exit();
}

$ticket = $ticket_result->fetch_assoc();

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reply') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Security token invalid';
    } else {
        $message = sanitize($_POST['message'] ?? '');
        
        if (empty($message)) {
            $error = 'Message cannot be empty';
        } else {
            $sender_type = $user_type === 'admin' ? 'admin' : 'user';
            
            $insert_query = "INSERT INTO support_replies (ticket_id, sender_id, sender_type, message) 
                            VALUES ($ticket_id, $user_id, '$sender_type', '$message')";
            
            if ($conn->query($insert_query)) {
                // Update ticket updated_at timestamp
                $conn->query("UPDATE support_tickets SET updated_at = NOW() WHERE id = $ticket_id");
                
                // Send email notification
                require_once '../config/email.php';
                $emailer = new EmailSender();
                
                if ($sender_type === 'user') {
                    // Notify admin
                    $admin_result = $conn->query("SELECT email FROM users WHERE user_type = 'admin' LIMIT 1");
                    if ($admin_result && $admin_result->num_rows > 0) {
                        $admin = $admin_result->fetch_assoc();
                        $ticket_link = BASE_URL . "admin/support-ticket-detail.php?ticket_id=$ticket_id";
                        $email_subject = "New Reply on Support Ticket #" . $ticket['ticket_id'];
                        $email_body = "A user has replied to support ticket #" . $ticket['ticket_id'] . "<br>\n<a href='$ticket_link'>View Ticket</a>";
                        
                        mail($admin['email'], $email_subject, $email_body, "From: " . getSetting('site_email') . "\r\nContent-Type: text/html; charset=UTF-8");
                    }
                } else if ($sender_type === 'admin') {
                    // Notify user
                    $user_result = $conn->query("SELECT email FROM users WHERE id = " . $ticket['user_id']);
                    if ($user_result && $user_result->num_rows > 0) {
                        $user = $user_result->fetch_assoc();
                        $ticket_link = BASE_URL . "view-support-ticket.php?ticket_id=$ticket_id";
                        $email_subject = "Support Ticket #" . $ticket['ticket_id'] . " - New Reply";
                        $email_body = "We have replied to your support ticket #" . $ticket['ticket_id'] . "<br>\n<a href='$ticket_link'>View Your Ticket</a>";
                        
                        mail($user['email'], $email_subject, $email_body, "From: " . getSetting('site_email') . "\r\nContent-Type: text/html; charset=UTF-8");
                    }
                }
                
                $success = 'Reply sent successfully!';
            } else {
                $error = 'Error sending reply';
            }
        }
    }
}

// Fetch replies
$replies_query = "SELECT sr.*, u.first_name, u.last_name, u.avatar FROM support_replies sr 
                 JOIN users u ON sr.sender_id = u.id 
                 WHERE sr.ticket_id = $ticket_id 
                 ORDER BY sr.created_at ASC";
$replies = $conn->query($replies_query);

$page_title = 'Support Ticket #' . $ticket['ticket_id'] . ' - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="ticket-detail" style="margin: 30px 0; max-width: 900px; margin-left: auto; margin-right: auto;">
        <!-- Ticket Header -->
        <div style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                <div>
                    <h1 style="margin: 0; font-size: 24px;"><?php echo htmlspecialchars($ticket['subject']); ?></h1>
                    <p style="color: #666; margin: 8px 0;">Ticket ID: <strong>#<?php echo htmlspecialchars($ticket['ticket_id']); ?></strong></p>
                </div>
                <span class="ticket-status-badge" style="display: inline-block; padding: 8px 16px; border-radius: 20px; font-weight: bold; font-size: 14px;
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
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; padding-top: 15px; border-top: 1px solid #f0f0f0;">
                <div>
                    <small style="color: #999;">Created</small>
                    <p style="margin: 5px 0; font-weight: 500;"><?php echo date('M d, Y H:i', strtotime($ticket['created_at'])); ?></p>
                </div>
                <div>
                    <small style="color: #999;">Priority</small>
                    <p style="margin: 5px 0; font-weight: 500;"><?php echo ucfirst($ticket['priority']); ?></p>
                </div>
                <div>
                    <small style="color: #999;">Last Updated</small>
                    <p style="margin: 5px 0; font-weight: 500;"><?php echo timeAgo($ticket['updated_at']); ?></p>
                </div>
            </div>
        </div>

        <!-- Original Message -->
        <div style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; border: 1px solid #e5e7eb;">
            <h3 style="margin: 0 0 15px 0; font-size: 16px;">Original Message</h3>
            <div style="background: #f9fafb; padding: 15px; border-radius: 6px; border-left: 4px solid #007bff; white-space: pre-wrap; word-wrap: break-word;">
                <?php echo htmlspecialchars($ticket['message']); ?>
            </div>
        </div>

        <!-- Conversation Thread -->
        <div style="margin-bottom: 20px;">
            <h3 style="margin: 0 0 15px 0; font-size: 18px;">💬 Conversation</h3>
            
            <?php if ($replies && $replies->num_rows > 0): ?>
                <div style="display: grid; gap: 15px;">
                    <?php while ($reply = $replies->fetch_assoc()): ?>
                        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; border-left: 4px solid <?php echo $reply['sender_type'] === 'admin' ? '#28a745' : '#007bff'; ?>;">
                            <div style="display: flex; gap: 12px; margin-bottom: 10px;">
                                <div>
                                    <?php 
                                    $avatar = $reply['avatar'] ?? '';
                                    $avatar_src = !empty($avatar) ? (strpos($avatar, 'http') === 0 ? $avatar : BASE_URL . ltrim($avatar, '/')) : BASE_URL . 'assets/images/default-avatar.png';
                                    ?>
                                    <img src="<?php echo htmlspecialchars($avatar_src); ?>" alt="Avatar" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                                </div>
                                <div style="flex: 1;">
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <strong><?php echo htmlspecialchars($reply['first_name'] . ' ' . $reply['last_name']); ?></strong>
                                        <?php if ($reply['sender_type'] === 'admin'): ?>
                                            <span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">Admin</span>
                                        <?php else: ?>
                                            <span style="background: #6c757d; color: white; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">User</span>
                                        <?php endif; ?>
                                    </div>
                                    <small style="color: #999;"><?php echo date('M d, Y H:i', strtotime($reply['created_at'])); ?></small>
                                </div>
                            </div>
                            <div style="background: #f9fafb; padding: 12px; border-radius: 6px; white-space: pre-wrap; word-wrap: break-word; color: #333;">
                                <?php echo htmlspecialchars($reply['message']); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 20px;">No replies yet</p>
            <?php endif; ?>
        </div>

        <!-- Reply Form -->
        <?php if ($ticket['status'] !== 'closed' || $user_type === 'admin'): ?>
            <div style="background: white; border-radius: 8px; padding: 20px; border: 1px solid #e5e7eb;">
                <h3 style="margin: 0 0 15px 0; font-size: 16px;">Add Reply</h3>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success" style="padding: 12px; background: #d4edda; border-left: 4px solid #28a745; color: #155724; border-radius: 4px; margin-bottom: 15px;">
                        ✓ <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" style="padding: 12px; background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; border-radius: 4px; margin-bottom: 15px;">
                        ✗ <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" style="display: grid; gap: 15px;">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="hidden" name="action" value="reply">
                    
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Your Message *</label>
                        <textarea name="message" required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; font-family: inherit; resize: vertical; min-height: 120px;" placeholder="Type your message here..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="padding: 12px 30px; font-size: 14px; cursor: pointer; border: none; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border-radius: 6px;">
                        💬 Send Reply
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Back Button -->
        <div style="margin-top: 20px;">
            <a href="<?php echo BASE_URL; ?>my-support-tickets.php" style="color: #007bff; text-decoration: none; font-weight: 500;">
                ← Back to My Tickets
            </a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>