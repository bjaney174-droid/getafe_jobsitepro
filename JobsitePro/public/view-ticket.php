<?php
require_once '../config/config.php';
requireLogin();

$user_id = getUserId();
$ticket_id = (int)($_GET['id'] ?? $_GET['ticket_id'] ?? 0);
$message = '';

// Fetch ticket
$stmt = $conn->prepare("SELECT * FROM support_tickets WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $ticket_id, $user_id);
$stmt->execute();
$ticket_result = $stmt->get_result();
$ticket = $ticket_result->fetch_assoc();
$stmt->close();

if (!$ticket) {
    header("Location: my-support-tickets.php");
    exit();
}

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = '<div class="alert alert-danger">Security error</div>';
    } else {
        $reply_message = sanitize($_POST['reply_message'] ?? '');
        
        if (empty($reply_message)) {
            $message = '<div class="alert alert-danger">Reply message cannot be empty</div>';
        } else {
            // Add user reply
            $sender_type = 'user';
            $stmt = $conn->prepare("INSERT INTO support_replies (ticket_id, sender_id, sender_type, message) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $ticket_id, $user_id, $sender_type, $reply_message);
            
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">✓ Reply sent successfully!</div>';
                // Update ticket status to in_progress if it was open
                if ($ticket['status'] === 'open') {
                    $conn->query("UPDATE support_tickets SET status = 'in_progress' WHERE id = $ticket_id");
                    $ticket['status'] = 'in_progress';
                }
                // Clear the input
                $_POST['reply_message'] = '';
            } else {
                $message = '<div class="alert alert-danger">Error sending reply. Please try again.</div>';
            }
            $stmt->close();
        }
    }
}

// Fetch all replies for this ticket
$replies_result = $conn->query("
    SELECT sr.*, u.first_name, u.last_name, u.avatar 
    FROM support_replies sr
    LEFT JOIN users u ON sr.sender_id = u.id
    WHERE sr.ticket_id = $ticket_id
    ORDER BY sr.created_at ASC
");

$page_title = 'Ticket ' . htmlspecialchars($ticket['ticket_id']) . ' - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="ticket-view-wrap" style="max-width: 900px; margin: 30px auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #e5e7eb; padding-bottom: 20px;">
            <div>
                <h1 style="margin: 0 0 5px 0; font-size: 28px;">
                    <?php echo htmlspecialchars($ticket['ticket_id']); ?>
                </h1>
                <p style="margin: 0; color: #666;">
                    <?php echo htmlspecialchars($ticket['subject']); ?>
                </p>
            </div>
            <div>
                <span class="status-badge" style="display: inline-block; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: bold;">
                    <?php 
                    $status_text = ucfirst(str_replace('_', ' ', $ticket['status']));
                    $status_colors = [
                        'open' => ['bg' => '#fef3c7', 'text' => '#78350f'],
                        'in_progress' => ['bg' => '#dbeafe', 'text' => '#0c4a6e'],
                        'closed' => ['bg' => '#d1fae5', 'text' => '#065f46']
                    ];
                    $colors = $status_colors[strtolower($ticket['status'])] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
                    echo $status_text;
                    ?>
                </span>
            </div>
        </div>

        <!-- Original Ticket -->
        <div class="ticket-message original-message" style="background: #f8f9fa; border-left: 4px solid #007bff; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div>
                    <strong style="font-size: 16px;">Your Message</strong>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;">
                        <?php echo date('M d, Y \a\t h:i A', strtotime($ticket['created_at'])); ?>
                    </p>
                </div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 6px; white-space: pre-wrap; word-wrap: break-word; line-height: 1.6;">
                <?php echo htmlspecialchars($ticket['message']); ?>
            </div>
        </div>

        <!-- Conversation Thread -->
        <?php if ($replies_result && $replies_result->num_rows > 0): ?>
            <div class="conversation-thread" style="margin-bottom: 30px;">
                <h3 style="margin-bottom: 20px; font-size: 18px;">💬 Conversation History</h3>
                
                <?php while ($reply = $replies_result->fetch_assoc()): ?>
                    <div class="reply-message" style="margin-bottom: 15px; padding: 20px; background: <?php echo $reply['sender_type'] === 'admin' ? '#d4f5dd' : '#f0fdf4'; ?>; border-left: 4px solid <?php echo $reply['sender_type'] === 'admin' ? '#22c55e' : '#10b981'; ?>; border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <div>
                                <strong style="font-size: 15px;">
                                    <?php 
                                    if ($reply['sender_type'] === 'admin') {
                                        echo '👨‍💼 ' . htmlspecialchars($reply['first_name'] . ' ' . $reply['last_name']) . ' (Admin)';
                                    } else {
                                        echo '👤 You';
                                    }
                                    ?>
                                </strong>
                                <p style="margin: 5px 0 0 0; color: #666; font-size: 12px;">
                                    <?php echo date('M d, Y \a\t h:i A', strtotime($reply['created_at'])); ?>
                                </p>
                            </div>
                            <?php if ($reply['sender_type'] === 'admin'): ?>
                                <span style="background: #22c55e; color: white; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold;">✓ STAFF REPLY</span>
                            <?php endif; ?>
                        </div>
                        <div style="background: white; padding: 12px; border-radius: 6px; white-space: pre-wrap; word-wrap: break-word; line-height: 1.6; color: #333;">
                            <?php echo htmlspecialchars($reply['message']); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="background: #fef3c7; border-left: 4px solid #eab308; padding: 15px; border-radius: 6px; margin-bottom: 30px; text-align: center; color: #78350f;">
                <strong>⏳ No replies yet</strong><br>
                <small>The admin will respond to your message soon.</small>
            </div>
        <?php endif; ?>

        <!-- Reply Form (only if ticket is not closed) -->
        <?php if ($ticket['status'] !== 'closed'): ?>
            <div class="reply-form-section" style="background: white; border: 2px solid #e5e7eb; padding: 25px; border-radius: 8px; margin-bottom: 30px;">
                <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 18px;">📝 Reply to Ticket</h3>
                
                <?php echo $message; ?>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    
                    <div class="form-group">
                        <label for="reply_message">Your Reply *</label>
                        <textarea 
                            id="reply_message"
                            name="reply_message" 
                            required 
                            rows="6" 
                            placeholder="Type your reply here..."
                            style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-family: inherit; font-size: 14px;"
                        ></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary">Send Reply</button>
                        <a href="my-support-tickets.php" class="btn btn-secondary">← Back to Tickets</a>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div style="background: #d1fae5; border-left: 4px solid #065f46; padding: 15px; border-radius: 6px; margin-bottom: 30px;">
                <strong>✓ This ticket has been closed.</strong> If you need further assistance, please create a new ticket.
            </div>
            <a href="my-support-tickets.php" class="btn btn-secondary">← Back to Tickets</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>
