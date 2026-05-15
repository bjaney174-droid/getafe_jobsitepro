<?php
require_once '../../config/config.php';
requireAdmin();

$msg_id = (int)($_GET['id'] ?? 0);
$result = $conn->query("SELECT * FROM contact_messages WHERE id = $msg_id");
$message = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($message);
?>