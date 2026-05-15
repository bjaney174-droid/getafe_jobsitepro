<?php
if (function_exists('mail')) {
    echo "✅ Mail function is available";
    
    // Try sending a test email
    $test = mail('test@example.com', 'Test', 'Test message');
    echo $test ? " - Sent" : " - Failed to send";
} else {
    echo "❌ Mail function is NOT available on this server";
}
?>