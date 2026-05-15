<?php
require_once '../config/config.php';
requireLogin();
require_once '../config/email-notifications.php';

$user_id = (int)getUserId();
$user_type = getUserType();
$app_id = (int)($_GET['id'] ?? 0);
$message = '';

// Fetch application with job and user details
$stmt = $conn->prepare("
    SELECT a.*, j.id as job_id, j.title, j.company, j.location, j.salary_min, j.salary_max,
           u.first_name, u.last_name, u.email, u.phone, u.skills, u.avatar
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN users u ON a.user_id = u.id
    WHERE a.id = ?
");
$stmt->bind_param("i", $app_id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();
$stmt->close();

if (!$application) {
    $_SESSION['error'] = 'Application not found';
    header("Location: my-applications.php");
    exit();
}

// Authorization check
if ($user_type === 'jobseeker' && $application['user_id'] != $user_id) {
    $_SESSION['error'] = 'You do not have permission to view this application';
    header("Location: my-applications.php");
    exit();
} elseif ($user_type === 'employer') {
    $check = $conn->query("SELECT posted_by FROM jobs WHERE id = " . (int)$application['job_id']);
    if (!$check || $check->num_rows === 0 || $check->fetch_assoc()['posted_by'] != $user_id) {
        $_SESSION['error'] = 'You do not have permission to view this application';
        header("Location: my-applications.php");
        exit();
    }
}

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = '<div class="alert alert-danger">Security error</div>';
    } else {
        $msg_text = sanitize($_POST['message'] ?? '');
        
        if (empty($msg_text)) {
            $message = '<div class="alert alert-danger">Message cannot be empty</div>';
        } else {
            $stmt = $conn->prepare("
                INSERT INTO application_messages (application_id, sender_id, sender_type, message)
                VALUES (?, ?, ?, ?)
            ");
            $sender_type = ($user_type === 'jobseeker') ? 'jobseeker' : 'employer';
            $stmt->bind_param("iiss", $app_id, $user_id, $sender_type, $msg_text);
            
            if ($stmt->execute()) {
                // Update application last_message_date
                $conn->query("UPDATE applications SET last_message_date = NOW() WHERE id = $app_id");
                
                // Log action
                $action = ($user_type === 'jobseeker') ? 'application_message_sent' : 'application_reply_sent';
                log_action($action, "Application ID: $app_id");
                
                $message = '<div class="alert alert-success">✓ Message sent successfully!</div>';
                $_POST['message'] = '';
            } else {
                $message = '<div class="alert alert-danger">Error sending message. Please try again.</div>';
            }
            $stmt->close();
        }
    }
}

// Handle status update (employer only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    if ($user_type !== 'employer') {
        $message = '<div class="alert alert-danger">Only employers can update status</div>';
    } elseif (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = '<div class="alert alert-danger">Security error</div>';
    } else {
        $new_status = sanitize($_POST['status'] ?? '');
        
        if (in_array($new_status, ['pending', 'reviewed', 'approved', 'rejected'], true)) {
            $conn->query("UPDATE applications SET status = '$new_status', updated_at = NOW() WHERE id = $app_id");
            log_action('application_status_updated', "Application ID: $app_id, Status: $new_status");
            
            // Reload application data
            $stmt = $conn->prepare("
                SELECT a.*, j.id as job_id, j.title, j.company, j.location, j.salary_min, j.salary_max,
                       u.first_name, u.last_name, u.email, u.phone, u.skills, u.avatar
                FROM applications a
                JOIN jobs j ON a.job_id = j.id
                JOIN users u ON a.user_id = u.id
                WHERE a.id = ?
            ");
            $stmt->bind_param("i", $app_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $application = $result->fetch_assoc();
            $stmt->close();
            
            $message = '<div class="alert alert-success">✓ Application status updated!</div>';
        }
    }
}

// Fetch all messages for this application
$messages_result = $conn->query("
    SELECT am.*, u.first_name, u.last_name, u.avatar
    FROM application_messages am
    LEFT JOIN users u ON am.sender_id = u.id
    WHERE am.application_id = $app_id
    ORDER BY am.created_at ASC
");

$page_title = 'Application - ' . htmlspecialchars($application['title']) . ' - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>
    
    <div class="application-view-wrap" style="max-width: 1000px; margin: 30px auto;">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #e5e7eb; padding-bottom: 20px;">
            <div>
                <a href="my-applications.php" class="btn btn-sm btn-secondary">← Back</a>
                <h1 style="margin: 10px 0 5px 0; font-size: 28px;">
                    <?php 
                    if ($user_type === 'employer') {
                        echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']);
                    } else {
                        echo htmlspecialchars($application['title']);
                    }
                    ?>
                </h1>
                <p style="margin: 0; color: #666;">
                    <?php 
                    if ($user_type === 'employer') {
                        echo htmlspecialchars($application['title']) . ' at ' . htmlspecialchars($application['company']);
                    } else {
                        echo htmlspecialchars($application['company']);
                    }
                    ?>
                </p>
            </div>
            <div>
                <span class="status-badge" style="display: inline-block; padding: 10px 20px; border-radius: 20px; font-size: 14px; font-weight: bold;">
                    <?php 
                    $status_colors = [
                        'pending' => ['bg' => '#fef3c7', 'text' => '#78350f'],
                        'reviewed' => ['bg' => '#dbeafe', 'text' => '#0c4a6e'],
                        'approved' => ['bg' => '#d1fae5', 'text' => '#065f46'],
                        'rejected' => ['bg' => '#fee2e2', 'text' => '#991b1b']
                    ];
                    $colors = $status_colors[$application['status']] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
                    echo strtoupper($application['status']);
                    ?>
                </span>
            </div>
        </div>

        <?php echo $message; ?>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-bottom: 30px;">
            <!-- Applicant/Job Card -->
            <div style="background: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
                <?php if ($user_type === 'employer'): ?>
                    <!-- Jobseeker Card for Employer -->
                    <h3 style="margin-top: 0;">Applicant Information</h3>
                    <?php if (!empty($application['avatar'])): ?>
                        <img src="<?php echo BASE_URL . ltrim($application['avatar'], '/'); ?>" 
                             style="width: 80px; height: 80px; border-radius: 50%; margin-bottom: 15px; object-fit: cover;">
                    <?php endif; ?>
                    <p><strong><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></strong></p>
                    <p style="margin: 8px 0;">
                        📧 <a href="mailto:<?php echo htmlspecialchars($application['email']); ?>">
                            <?php echo htmlspecialchars($application['email']); ?>
                        </a>
                    </p>
                    <?php if (!empty($application['phone'])): ?>
                        <p style="margin: 8px 0;">📱 <?php echo htmlspecialchars($application['phone']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($application['skills'])): ?>
                        <p style="margin: 8px 0;"><strong>Skills:</strong><br>
                        <?php echo htmlspecialchars($application['skills']); ?></p>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Job Card for Jobseeker -->
                    <h3 style="margin-top: 0;"><?php echo htmlspecialchars($application['title']); ?></h3>
                    <p style="margin: 5px 0;"><strong><?php echo htmlspecialchars($application['company']); ?></strong></p>
                    <p style="margin: 5px 0; color: #666;">📍 <?php echo htmlspecialchars($application['location']); ?></p>
                    <?php if (!empty($application['salary_min']) && !empty($application['salary_max'])): ?>
                        <p style="margin: 5px 0; color: #666;">💰 ₱<?php echo number_format($application['salary_min']); ?> - ₱<?php echo number_format($application['salary_max']); ?>/month</p>
                    <?php endif; ?>
                    <hr style="margin: 15px 0;">
                    <p style="margin: 5px 0;"><strong>Applied:</strong> <?php echo date('M d, Y \a\t h:i A', strtotime($application['applied_at'])); ?></p>
                    <p style="margin: 5px 0;"><strong>Status Updated:</strong> <?php echo date('M d, Y \a\t h:i A', strtotime($application['updated_at'])); ?></p>
                <?php endif; ?>
            </div>

            <!-- Cover Letter -->
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
                <h3 style="margin-top: 0; margin-bottom: 15px;">Cover Letter</h3>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; white-space: pre-wrap; word-wrap: break-word; line-height: 1.6; color: #333;">
                    <?php echo htmlspecialchars($application['cover_letter']); ?>
                </div>
            </div>
        </div>

        <!-- Status Update Form (Employer Only) -->
        <?php if ($user_type === 'employer'): ?>
            <div style="background: #f0fdf4; border: 2px solid #86efac; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
                <h3 style="margin-top: 0; margin-bottom: 15px;">📊 Update Application Status</h3>
                <form method="POST" style="display: flex; gap: 10px; align-items: center;">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="hidden" name="action" value="update_status">
                    
                    <select name="status" style="padding: 10px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;">
                        <option value="pending" <?php echo $application['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="reviewed" <?php echo $application['status'] === 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                        <option value="approved" <?php echo $application['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $application['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Messages Section -->
        <div style="margin-bottom: 30px;">
            <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 18px;">💬 Application Messages</h3>
            
            <?php if ($messages_result && $messages_result->num_rows > 0): ?>
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 20px; max-height: 400px; overflow-y: auto;">
                    <?php while ($msg = $messages_result->fetch_assoc()): ?>
                        <div style="margin-bottom: 15px; padding: 15px; background: <?php echo $msg['sender_type'] === 'employer' ? '#e0f2fe' : '#f0fdf4'; ?>; border-radius: 6px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <strong style="font-size: 14px;">
                                    <?php 
                                    if ($msg['sender_type'] === 'employer') {
                                        echo '👨‍💼 Employer';
                                    } else {
                                        echo '👤 Applicant';
                                    }
                                    ?>
                                </strong>
                                <small style="color: #666;">
                                    <?php echo date('M d, Y \a\t h:i A', strtotime($msg['created_at'])); ?>
                                </small>
                            </div>
                            <div style="background: white; padding: 10px; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word; line-height: 1.6; color: #333;">
                                <?php echo htmlspecialchars($msg['message']); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div style="background: #fef3c7; border-left: 4px solid #eab308; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; color: #78350f;">
                    <strong>No messages yet</strong><br>
                    <small>Start a conversation by sending a message below.</small>
                </div>
            <?php endif; ?>
        </div>

        <!-- Message Input Form -->
        <div style="background: white; border: 2px solid #e5e7eb; border-radius: 8px; padding: 25px;">
            <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 18px;">📝 Send Message</h3>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="action" value="send_message">
                
                <div style="margin-bottom: 15px;">
                    <label for="message" style="display: block; margin-bottom: 8px; font-weight: bold;">Message *</label>
                    <textarea 
                        id="message"
                        name="message" 
                        required 
                        rows="5" 
                        placeholder="Type your message here..."
                        style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-family: inherit; font-size: 14px; resize: vertical;"
                    ></textarea>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">Send Message</button>
                    <a href="my-applications.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .application-view-wrap {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    }
    
    .alert {
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }
    
    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    
    .btn {
        display: inline-block;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .btn-primary {
        background: #007bff;
        color: white;
    }
    
    .btn-primary:hover {
        background: #0056b3;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
    }
    
    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }
</style>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>
