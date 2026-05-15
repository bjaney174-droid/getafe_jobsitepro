<?php
require_once '../../config/config.php';
requireAdmin();

// Backup folder inside /public/backups
$backup_dir = dirname(__DIR__) . '/backups';
if (!is_dir($backup_dir)) {
    if (!mkdir($backup_dir, 0755, true) && !is_dir($backup_dir)) {
        $_SESSION['error'] = 'Could not create backup folder.';
        header("Location: settings.php");
        exit();
    }
}

$filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
$backup_file = $backup_dir . '/' . $filename;

// DB credentials (same as config/database.php)
$db_name = 'getafe_jobsite_pro';
$db_user = 'root';
$db_pass = '';
$db_host = 'localhost';

// Try mysqldump paths (XAMPP + system PATH)
$possible_dump_bins = [
    'C:\\xampp\\mysql\\bin\\mysqldump.exe',
    'mysqldump'
];

$dump_bin = null;
foreach ($possible_dump_bins as $bin) {
    if ($bin === 'mysqldump' || file_exists($bin)) {
        $dump_bin = $bin;
        break;
    }
}

if (!function_exists('exec')) {
    $_SESSION['error'] = 'Backup failed: exec() is disabled on this PHP setup.';
    header("Location: settings.php");
    exit();
}

if (!$dump_bin) {
    $_SESSION['error'] = 'Backup failed: mysqldump not found.';
    header("Location: settings.php");
    exit();
}

// Build command safely
$cmd = '"' . $dump_bin . '"'
    . ' --user=' . escapeshellarg($db_user)
    . ' --host=' . escapeshellarg($db_host);

if ($db_pass !== '') {
    $cmd .= ' --password=' . escapeshellarg($db_pass);
}

$cmd .= ' ' . escapeshellarg($db_name)
    . ' > ' . escapeshellarg($backup_file) . ' 2>&1';

exec($cmd, $output, $return_var);

if ($return_var === 0 && file_exists($backup_file) && filesize($backup_file) > 0) {
    log_action('backup_database', 'Database backed up: ' . $filename);

    header('Content-Description: File Transfer');
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($backup_file));
    header('Pragma: public');
    header('Cache-Control: must-revalidate');
    readfile($backup_file);
    exit();
}

// If failed, send useful error back to settings
$error_text = !empty($output) ? implode(' | ', $output) : 'Unknown mysqldump error.';
$_SESSION['error'] = 'Backup failed: ' . $error_text;
header("Location: settings.php");
exit();
?>