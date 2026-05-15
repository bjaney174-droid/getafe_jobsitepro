<?php

// Check if application exists
function hasApplied($job_id, $user_id) {
    global $conn;
    $result = $conn->query("SELECT id FROM applications WHERE job_id = $job_id AND user_id = $user_id");
    return $result->num_rows > 0;
}

// Get job count for employer
function getEmployerJobCount($user_id) {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE posted_by = $user_id");
    return $result->fetch_assoc()['count'];
}

// Get total applications for employer
function getEmployerApplicationCount($user_id) {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as count FROM applications a 
                           JOIN jobs j ON a.job_id = j.id WHERE j.posted_by = $user_id");
    return $result->fetch_assoc()['count'];
}

// Get user applications count
function getUserApplicationCount($user_id) {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as count FROM applications WHERE user_id = $user_id");
    return $result->fetch_assoc()['count'];
}

// Get saved jobs count
function getSavedJobsCount($user_id) {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as count FROM saved_jobs WHERE user_id = $user_id");
    return $result->fetch_assoc()['count'];
}

// Get featured jobs
function getFeaturedJobs($limit = 5) {
    global $conn;
    $sql = "SELECT j.*, u.company FROM jobs j 
            JOIN users u ON j.posted_by = u.id 
            WHERE j.is_featured = 1 AND j.is_active = 1
            ORDER BY j.featured_until DESC LIMIT $limit";
    return $conn->query($sql);
}

// Send notification email
function sendNotificationEmail($to, $subject, $template, $data = []) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: " . getSetting('site_email') . "\r\n";

    // Simple template rendering
    $message = file_get_contents(__DIR__ . '/../emails/' . $template . '.html');
    foreach ($data as $key => $value) {
        $message = str_replace('[' . strtoupper($key) . ']', $value, $message);
    }

    return mail($to, $subject, $message, $headers);
}

// Paginate results
function paginate($total, $current_page, $per_page) {
    $total_pages = ceil($total / $per_page);
    return [
        'total' => $total,
        'current_page' => $current_page,
        'per_page' => $per_page,
        'total_pages' => $total_pages,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages
    ];
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate URL
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
}

// Truncate text
function truncateText($text, $limit = 100, $suffix = '...') {
    if (strlen($text) > $limit) {
        return substr($text, 0, $limit) . $suffix;
    }
    return $text;
}

// Get percentage
function getPercentage($current, $total) {
    if ($total == 0) return 0;
    return round(($current / $total) * 100, 2);
}

?>