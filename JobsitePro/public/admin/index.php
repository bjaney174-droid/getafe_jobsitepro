<?php
require_once '../../config/config.php';
requireAdmin();

// Redirect to dashboard
header("Location: dashboard.php");
exit();
?>
