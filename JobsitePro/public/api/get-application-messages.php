<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$app_id = (int)($_GET['app_id'] ?? 0);
$user_id = (int)getUserId();

// Verify application ownership
$app_check = $conn->query("
    SELECT a.*, j.posted_by FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    WHERE a.id = $app_id
");

if (!$app_check || $app_check->num_rows === 0) {
    echo json_encode(['success' => false, 'messages' => []]);
    exit();
}

$app = $app_check->fetch_assoc();

// Verify authorization
$is_jobseeker = ($app['user_id'] == $user_id);
$is_employer = ($app['posted_by'] == $user_id);

if (!$is_jobseeker && !$is_employer) {
    echo json_encode(['success' => false, 'messages' => []]);
    exit();
}

// Fetch messages with user info
$messages = $conn->query("
    SELECT am.*, u.first_name, u.last_name, u.avatar 
    FROM application_messages am 
    LEFT JOIN users u ON am.sender_id = u.id 
    WHERE am.application_id = $app_id 
    ORDER BY am.created_at ASC
");

$messages_array = [];
while ($msg = $messages->fetch_assoc()) {
    $messages_array[] = [
        'id' => $msg['id'],
        'sender_id' => $msg['sender_id'],
        'sender_type' => $msg['sender_type'],
        'sender_name' => ($msg['first_name'] ?? 'Unknown') . ' ' . ($msg['last_name'] ?? ''),
        'avatar' => $msg['avatar'] ?? '',
        'message' => htmlspecialchars($msg['message']),
        'created_at' => $msg['created_at'],
        'is_read' => (bool)$msg['is_read']
    ];
}

echo json_encode([
    'success' => true,
    'messages' => $messages_array,
    'unread_count' => $conn->query("
        SELECT COUNT(*) as count FROM application_messages 
        WHERE application_id = $app_id 
        AND sender_id != $user_id 
        AND is_read = 0
    ")->fetch_assoc()['count']
]);
?>
