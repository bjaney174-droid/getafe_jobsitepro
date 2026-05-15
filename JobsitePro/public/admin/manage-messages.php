<?php 
require_once '../../config/config.php'; 
requireAdmin(); 

// Handle Delete Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $msg_id = (int)$_POST['msg_id'];
        $conn->query("DELETE FROM contact_messages WHERE id = $msg_id");
        $_SESSION['success'] = "Message deleted.";
    }
    header("Location: manage-messages.php");
    exit();
}

// Handle Mark as Read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'mark_read') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $msg_id = (int)$_POST['msg_id'];
        $conn->query("UPDATE contact_messages SET status = 'read' WHERE id = $msg_id");
        $_SESSION['success'] = "Message marked as read.";
    }
    header("Location: manage-messages.php");
    exit();
}

// Handle Mark as Replied
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'mark_replied') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $msg_id = (int)$_POST['msg_id'];
        $conn->query("UPDATE contact_messages SET status = 'replied' WHERE id = $msg_id");
        $_SESSION['success'] = "Message marked as replied.";
    }
    header("Location: manage-messages.php");
    exit();
}

// Fetch Messages
$messages = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$page_title = 'Manage Messages - Admin';
require_once '../../includes/header.php'; 
?>

<div class="container">
    <?php require_once '../../includes/navbar.php'; ?>
    
    <div class="admin-wrap">
        <div class="admin-head">
            <h1>📧 Customer Inquiries</h1>
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
                            <th>Status</th>
                            <th>Date</th>
                            <th>From</th>
                            <th>Subject</th>
                            <th>Message Preview</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($messages && $messages->num_rows > 0): ?>
                            <?php while ($msg = $messages->fetch_assoc()): ?>
                                <tr style="background: <?php echo $msg['status'] === 'unread' ? '#fef3c7' : '#fff'; ?>;">
                                    <td>
                                        <span class="status-badge <?php echo $msg['status']; ?>">
                                            <?php echo ucfirst($msg['status']); ?>
                                        </span>
                                    </td>
                                    <td><small><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></small></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($msg['name']); ?></strong><br>
                                        <small class="muted"><?php echo htmlspecialchars($msg['email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                                    <td>
                                        <div style="max-width: 300px; font-size: 0.9em; color: #555;">
                                            <?php echo htmlspecialchars(substr($msg['message'], 0, 100)) . '...'; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewMessage(<?php echo $msg['id']; ?>)">View</button>
                                        <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" class="btn btn-sm btn-success" title="Reply via email">
                                            📧 Reply
                                        </a>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="msg_id" value="<?php echo $msg['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this message?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="muted">No messages yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Viewing Full Message -->
<div id="messageModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Message Details</h2>
        <div id="modalBody"></div>
        
        <hr>
        
        <div class="modal-actions">
            <a href="mailto:" id="replyEmail" class="btn btn-success" target="_blank">
                📧 Reply via Email
            </a>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="action" value="mark_replied">
                <input type="hidden" name="msg_id" id="markRepliedId">
                <button type="submit" class="btn btn-primary">✓ Mark as Replied</button>
            </form>
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

<style>
    .admin-wrap { margin-top: 20px; }
    .admin-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
    .admin-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:20px; }
    .admin-table { width:100%; border-collapse: collapse; }
    .admin-table th, .admin-table td { padding:12px; border-bottom:1px solid #f0f0f0; text-align:left; }
    .muted { color:#6b7280; }
    
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.85em;
        font-weight: bold;
    }
    
    .status-badge.unread {
        background: #fbbf24;
        color: #78350f;
    }
    
    .status-badge.read {
        background: #d1d5db;
        color: #374151;
    }
    
    .status-badge.replied {
        background: #86efac;
        color: #15803d;
    }
    
    .modal {
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        overflow-y: auto;
    }
    
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border-radius: 12px;
        width: 90%;
        max-width: 700px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    
    .close:hover {
        color: #000;
    }
    
    .message-info {
        background: #f3f4f6;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 15px;
    }
    
    .message-body {
        background: #f9fafb;
        padding: 15px;
        border-radius: 6px;
        border-left: 4px solid #007bff;
        margin-bottom: 15px;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    .modal-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 15px;
    }
    
    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
</style>

<script>
function viewMessage(msgId) {
    fetch('get-message.php?id=' + msgId)
        .then(response => response.json())
        .then(data => {
            const modalBody = document.getElementById('modalBody');
            
            let html = `
                <div class="message-info">
                    <p><strong>From:</strong> ${data.name}</p>
                    <p><strong>Email:</strong> <a href="mailto:${data.email}">${data.email}</a></p>
                    <p><strong>Subject:</strong> ${data.subject}</p>
                    <p><strong>Date:</strong> ${data.created_at}</p>
                </div>
                
                <h4>Message:</h4>
                <div class="message-body">${data.message}</div>
            `;
            
            modalBody.innerHTML = html;
            
            // Set email link for reply button
            document.getElementById('replyEmail').href = 'mailto:' + data.email + '?subject=Re: ' + encodeURIComponent(data.subject);
            document.getElementById('markRepliedId').value = msgId;
            
            document.getElementById('messageModal').style.display = 'block';
        })
        .catch(error => {
            alert('Error loading message: ' + error);
        });
}

function closeModal() {
    document.getElementById('messageModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('messageModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>
</body>
</html>