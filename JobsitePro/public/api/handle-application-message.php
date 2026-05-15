<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$action = sanitize($_POST['action'] ?? '');
$app_id = (int)($_POST['app_id'] ?? 0);
$user_id = (int)getUserId();
$user_type = getUserType();

// Verify CSRF token
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Security error']);
    exit();
}

// Verify application ownership
$app_check = $conn->query("
    SELECT a.*, j.posted_by, u.email, u.first_name, u.last_name, u.phone 
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    JOIN users u ON a.user_id = u.id 
    WHERE a.id = $app_id
");

if (!$app_check || $app_check->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Application not found']);
    exit();
}

$app = $app_check->fetch_assoc();

// Verify authorization
$is_jobseeker = ($app['user_id'] == $user_id && $user_type === 'jobseeker');
$is_employer = ($app['posted_by'] == $user_id && $user_type === 'employer');

if (!$is_jobseeker && !$is_employer) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Handle different actions
switch ($action) {
    case 'send_message':
        handleSendMessage($app_id, $user_id, $app, $is_jobseeker);
        break;
    
    case 'schedule_interview':
        if (!$is_employer) {
            echo json_encode(['success' => false, 'message' => 'Only employers can schedule interviews']);
            exit();
        }
        handleScheduleInterview($app_id, $user_id, $app);
        break;
    
    case 'update_status':
        if (!$is_employer) {
            echo json_encode(['success' => false, 'message' => 'Only employers can update status']);
            exit();
        }
        handleUpdateStatus($app_id, $user_id, $app);
        break;
    
    case 'mark_read':
        handleMarkRead($app_id, $user_id);
        break;
    
    case 'upload_attachment':
        handleUploadAttachment($app_id, $user_id, $is_jobseeker);
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit();
}

function handleSendMessage($app_id, $user_id, $app, $is_jobseeker) {
    global $conn;
    
    $message = sanitize($_POST['message'] ?? '');
    $sender_type = $is_jobseeker ? 'jobseeker' : 'employer';
    
    if (empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
        exit();
    }
    
    // Insert message
    $stmt = $conn->prepare("
        INSERT INTO application_messages (application_id, sender_id, sender_type, message) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iiss", $app_id, $user_id, $sender_type, $message);
    
    if ($stmt->execute()) {
        // Update last message date
        $conn->query("UPDATE applications SET last_message_date = NOW() WHERE id = $app_id");
        
        // Send email notification
        require_once '../../config/email-notifications.php';
        $emailer = new ApplicationEmailNotifications();
        
        // Get job details
        $job_query = $conn->query("
            SELECT j.title, j.company FROM applications a 
            JOIN jobs j ON a.job_id = j.id 
            WHERE a.id = $app_id
        ");
        $job = $job_query->fetch_assoc();
        
        // Get employer name if message from employer
        if (!$is_jobseeker) {
            $employer = $conn->query("SELECT first_name, last_name FROM users WHERE id = $user_id")->fetch_assoc();
            $sender_name = $employer['first_name'] . ' ' . $employer['last_name'];
            $recipient_email = $app['email'];
            $recipient_name = $app['first_name'] . ' ' . $app['last_name'];
        } else {
            // Get employer email
            $employer = $conn->query("SELECT email, first_name, last_name FROM users WHERE id = " . $app['posted_by'])->fetch_assoc();
            $sender_name = $app['first_name'] . ' ' . $app['last_name'];
            $recipient_email = $employer['email'];
            $recipient_name = $employer['first_name'] . ' ' . $employer['last_name'];
        }
        
        $emailer->sendApplicationMessage(
            $recipient_email,
            $recipient_name,
            $sender_name,
            $job['title'],
            $job['company'],
            $message,
            !$is_jobseeker ? 'jobseeker' : 'employer'
        );
        
        log_action('application_message_sent', "Application ID: $app_id, Message sent");
        
        echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error sending message']);
    }
    $stmt->close();
}

function handleScheduleInterview($app_id, $user_id, $app) {
    global $conn;
    
    $interview_date = sanitize($_POST['interview_date'] ?? '');
    $interview_location = sanitize($_POST['interview_location'] ?? '');
    $interview_message = sanitize($_POST['interview_message'] ?? '');
    
    if (empty($interview_date) || empty($interview_location)) {
        echo json_encode(['success' => false, 'message' => 'Date and location are required']);
        exit();
    }
    
    // Convert to database format
    $interview_datetime = date('Y-m-d H:i:s', strtotime($interview_date));
    
    // Update application with interview details
    $stmt = $conn->prepare("
        UPDATE applications 
        SET interview_scheduled_date = ?, interview_location = ?, status = 'reviewed' 
        WHERE id = ?
    ");
    $stmt->bind_param("ssi", $interview_datetime, $interview_location, $app_id);
    
    if ($stmt->execute()) {
        // Add interview message to conversation
        if (!empty($interview_message)) {
            $msg_type = 'employer';
            $conn->query("
                INSERT INTO application_messages (application_id, sender_id, sender_type, message) 
                VALUES ($app_id, $user_id, '$msg_type', '$interview_message')
            ");
        }
        
        // Send email notification
        require_once '../../config/email-notifications.php';
        $emailer = new ApplicationEmailNotifications();
        
        // Get full data
        $full_data = $conn->query("
            SELECT a.*, j.title, j.company, u.first_name as emp_first, u.last_name as emp_last 
            FROM applications a 
            JOIN jobs j ON a.job_id = j.id 
            JOIN users u ON j.posted_by = u.id 
            WHERE a.id = $app_id
        ")->fetch_assoc();
        
        $employer_name = $full_data['emp_first'] . ' ' . $full_data['emp_last'];
        
        $emailer->sendInterviewScheduled(
            $app['email'],
            $app['first_name'] . ' ' . $app['last_name'],
            $full_data['title'],
            $full_data['company'],
            $interview_datetime,
            $interview_location,
            $employer_name,
            $interview_message
        );
        
        log_action('interview_scheduled', "Application ID: $app_id, Interview scheduled for $interview_datetime at $interview_location");
        
        echo json_encode(['success' => true, 'message' => 'Interview scheduled and email sent']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error scheduling interview']);
    }
    $stmt->close();
}

function handleUpdateStatus($app_id, $user_id, $app) {
    global $conn;
    
    $new_status = sanitize($_POST['status'] ?? '');
    $employer_notes = sanitize($_POST['notes'] ?? '');
    
    if (!in_array($new_status, ['pending', 'reviewed', 'approved', 'rejected', 'withdrawn'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit();
    }
    
    $stmt = $conn->prepare("
        UPDATE applications 
        SET status = ?, employer_notes = ? 
        WHERE id = ?
    ");
    $stmt->bind_param("ssi", $new_status, $employer_notes, $app_id);
    
    if ($stmt->execute()) {
        // Send email notification
        require_once '../../config/email-notifications.php';
        $emailer = new ApplicationEmailNotifications();
        
        // Get job details
        $job_data = $conn->query("
            SELECT j.title, j.company FROM applications a 
            JOIN jobs j ON a.job_id = j.id 
            WHERE a.id = $app_id
        ")->fetch_assoc();
        
        $emailer->sendApplicationStatusChanged(
            $app['email'],
            $app['first_name'] . ' ' . $app['last_name'],
            $job_data['title'],
            $job_data['company'],
            $new_status,
            $employer_notes
        );
        
        log_action('application_status_updated', "Application ID: $app_id, Status changed to $new_status");
        
        echo json_encode(['success' => true, 'message' => 'Status updated and email sent']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating status']);
    }
    $stmt->close();
}

function handleMarkRead($app_id, $user_id) {
    global $conn;
    
    $conn->query("
        UPDATE application_messages 
        SET is_read = 1 
        WHERE application_id = $app_id 
        AND sender_id != $user_id 
        AND is_read = 0
    ");
    
    // Update unread count
    $unread = $conn->query("
        SELECT COUNT(*) as count FROM application_messages 
        WHERE application_id = $app_id 
        AND sender_id != $user_id 
        AND is_read = 0
    ")->fetch_assoc();
    
    $has_unread = $unread['count'] > 0 ? 1 : 0;
    
    $conn->query("
        UPDATE applications 
        SET unread_count = {$unread['count']}, has_unread_messages = $has_unread 
        WHERE id = $app_id
    ");
    
    echo json_encode(['success' => true, 'unread_count' => $unread['count']]);
}

function handleUploadAttachment($app_id, $user_id, $is_jobseeker) {
    global $conn;
    
    if (!isset($_FILES['file'])) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded']);
        exit();
    }
    
    $file = $_FILES['file'];
    $allowed_types = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Validate file
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'File type not allowed']);
        exit();
    }
    
    if ($file['size'] > $max_size) {
        echo json_encode(['success' => false, 'message' => 'File too large (max 5MB)']);
        exit();
    }
    
    // Create upload directory if not exists
    $upload_dir = UPLOAD_PATH . 'application_attachments/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $filename = 'app_' . $app_id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $file_ext;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Store in database
        $stmt = $conn->prepare("
            INSERT INTO application_attachments (application_id, file_name, file_path, file_type, uploaded_by) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $relative_path = 'uploads/application_attachments/' . $filename;
        $stmt->bind_param("isssi", $app_id, $file['name'], $relative_path, $file_ext, $user_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'File uploaded successfully',
                'file_id' => $conn->insert_id,
                'file_name' => $file['name']
            ]);
        } else {
            unlink($filepath); // Delete file if DB insert fails
            echo json_encode(['success' => false, 'message' => 'Error saving file info']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error uploading file']);
    }
}
?>
