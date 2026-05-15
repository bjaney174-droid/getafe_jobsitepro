<?php 
require_once '../config/config.php';

$message = ''; 
$ticket_id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) { 
        $message = '<div class="alert alert-danger">Security error. Please try again.</div>'; 
    } else { 
        $name = sanitize($_POST['name'] ?? ''); 
        $email = sanitize($_POST['email'] ?? ''); 
        $subject = sanitize($_POST['subject'] ?? ''); 
        $priority = sanitize($_POST['priority'] ?? 'medium');
        $message_text = sanitize($_POST['message'] ?? ''); 
        $user_id = isLoggedIn() ? getUserId() : NULL;

        if (empty($name) || empty($email) || empty($subject) || empty($message_text)) { 
            $message = '<div class="alert alert-danger">⚠️ All fields are required</div>'; 
        } else if (!isValidEmail($email)) {
            $message = '<div class="alert alert-danger">⚠️ Please enter a valid email address</div>';
        } else { 
            try {
                $ticket_num = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
                $generated_ticket_id = 'TKT-' . $ticket_num;

                // Prepare statement with 'i' for integer user_id (can be NULL)
                $stmt = $conn->prepare("INSERT INTO support_tickets (ticket_id, user_id, name, email, subject, message, priority, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'open')");
                
                if ($stmt === false) {
                    throw new Exception("Database prepare failed: " . $conn->error);
                }
                
                // Bind with correct types: s=string, i=integer, s=string, s=string, s=string, s=string, s=string
                $stmt->bind_param("sisssss", $generated_ticket_id, $user_id, $name, $email, $subject, $message_text, $priority);
                
                if ($stmt->execute()) { 
                    $ticket_id = $generated_ticket_id;
                    
                    if (isLoggedIn()) {
                        log_action('create_support_ticket', "Created support ticket: $generated_ticket_id");
                    }
                    
                    @mail($email, "Support Ticket Created - $generated_ticket_id", 
                        "Hi $name,\n\nYour support ticket has been created.\n\nTicket ID: $generated_ticket_id\nSubject: $subject\n\nWe will review your message and get back to you soon.\n\nBest regards,\nGetafe Jobsite Team");
                    
                    $admin_email = getSetting('site_email', 'info@getafejobsite.com');
                    @mail($admin_email, "[NEW TICKET] $generated_ticket_id - $subject", 
                        "New support ticket submitted.\n\nTicket ID: $generated_ticket_id\nFrom: $name ($email)\nSubject: $subject\nPriority: $priority\n\nPlease log in to the admin panel to view and respond.");
                    
                    $message = '<div class="alert alert-success">✅ <strong>Message Sent Successfully!</strong><br>';
                    $message .= 'Your Ticket ID: <strong style="font-size: 1.2em; color: #007bff;">' . $generated_ticket_id . '</strong><br>';
                    $message .= 'We will review your message and get back to you soon. ';
                    if (isLoggedIn()) {
                        $message .= 'You can track your ticket in <a href="my-support-tickets.php" style="color: #007bff; text-decoration: underline;">My Support Tickets</a>.';
                    } else {
                        $message .= 'Check your email for updates.';
                    }
                    $message .= '</div>';
                } else { 
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                
                $stmt->close();
            } catch (Exception $e) {
                $message = '<div class="alert alert-danger">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</div>'; 
            }
        } 
    } 
} 

$page_title = 'Contact Us - ' . getSetting('site_name', 'Getafe Jobsite'); 
require_once '../includes/header.php'; 
?> 

<div class="container"> 
    <?php require_once '../includes/navbar.php'; ?> 
    
    <div class="contact-page"> 
        <h1>Contact Us</h1> 
        <p class="subtitle">Have a question? We'd love to hear from you.</p> 
        
        <div class="contact-container"> 
            <div class="contact-form-section"> 
                <?php echo $message; ?> 
                <form method="POST" class="contact-form"> 
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>"> 
                    
                    <div class="form-group"> 
                        <label>Name *</label> 
                        <input type="text" name="name" required placeholder="Your name"> 
                    </div> 
                    
                    <div class="form-group"> 
                        <label>Email *</label> 
                        <input type="email" name="email" required placeholder="your@email.com"> 
                    </div> 
                    
                    <div class="form-group"> 
                        <label>Subject *</label> 
                        <input type="text" name="subject" required placeholder="What is this about?"> 
                    </div>

                    <div class="form-group">
                        <label>Priority *</label>
                        <select name="priority" required>
                            <option value="low">Low - General Question</option>
                            <option value="medium" selected>Medium - Urgent Help Needed</option>
                            <option value="high">High - Issue / Bug Report</option>
                        </select>
                    </div>
                    
                    <div class="form-group"> 
                        <label>Message *</label> 
                        <textarea name="message" required rows="8" placeholder="Your message here..."></textarea> 
                    </div> 
                    
                    <button type="submit" class="btn btn-primary btn-large">Send Message</button> 
                </form> 
            </div> 
            
            <div class="contact-info-section"> 
                <h3>Get In Touch</h3> 
                <div class="contact-info-box"> 
                    <h4>📧 Email</h4> 
                    <p><?php echo getSetting('site_email', 'info@getafejobsite.com'); ?></p> 
                </div> 
                
                <div class="contact-info-box"> 
                    <h4>📱 Phone</h4> 
                    <p><?php echo getSetting('contact_phone', '+63 970 191 8626'); ?></p> 
                </div> 
                
                <div class="contact-info-box"> 
                    <h4>📍 Address</h4> 
                    <p><?php echo getSetting('address', 'Getafe, Bohol, Philippines 6334'); ?></p> 
                </div> 
                
                <div class="contact-info-box"> 
                    <h4>🌐 Hours</h4> 
                    <p>Monday - Friday: 9:00 AM - 5:00 PM</p> 
                    <p>Saturday - Sunday: Closed</p> 
                </div> 
            </div> 
        </div> 
    </div> 
</div> 

<?php require_once '../includes/footer.php'; ?>
</body> 
</html>
s