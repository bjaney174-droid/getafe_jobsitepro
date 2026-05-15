<?php
class EmailSender {
    private $from;
    private $site_name;
    private $site_url;

    public function __construct() {
        $this->from = getSetting('site_email', 'info@getafejobsite.com');
        $this->site_name = getSetting('site_name', 'Getafe Jobsite');
        $this->site_url = BASE_URL;
    }

    public function sendApplicationConfirmation($to, $user_name, $job_title, $company) {
        $subject = "Application Received - $job_title";
        
        $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .email-container { max-width: 600px; margin: 0 auto; background: #f5f5f5; padding: 20px; }
                    .email-header { background: #007bff; color: white; padding: 20px; text-align: center; }
                    .email-body { background: white; padding: 30px; }
                    .email-footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <h2>🏢 {$this->site_name}</h2>
                    </div>
                    <div class='email-body'>
                        <p>Hi $user_name,</p>
                        <p>Thank you for applying to the position of <strong>$job_title</strong> at <strong>$company</strong>.</p>
                        <p>We have received your application and will review it shortly. We'll contact you soon with updates.</p>
                        <p>Best regards,<br>The {$this->site_name} Team</p>
                    </div>
                    <div class='email-footer'>
                        <p>© 2026 {$this->site_name}. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        return $this->send($to, $subject, $message);
    }

    public function sendJobPostedNotification($to, $employer_name, $job_title) {
        $subject = "Your Job Posting is Live - $job_title";
        
        $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .email-container { max-width: 600px; margin: 0 auto; background: #f5f5f5; padding: 20px; }
                    .email-header { background: #28a745; color: white; padding: 20px; text-align: center; }
                    .email-body { background: white; padding: 30px; }
                    .email-footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <h2>✓ Job Posted Successfully</h2>
                    </div>
                    <div class='email-body'>
                        <p>Hi $employer_name,</p>
                        <p>Your job posting for <strong>$job_title</strong> is now live!</p>
                        <p>Candidates can now see and apply to your position.</p>
                        <p>Best regards,<br>The {$this->site_name} Team</p>
                    </div>
                    <div class='email-footer'>
                        <p>© 2026 {$this->site_name}. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        return $this->send($to, $subject, $message);
    }

    public function sendSupportTicketConfirmation($to, $user_name, $ticket_id, $subject) {
        $subject_line = "Support Ticket Created - $ticket_id";
        
        $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .email-container { max-width: 600px; margin: 0 auto; background: #f5f5f5; padding: 20px; }
                    .email-header { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; padding: 25px; text-align: center; border-radius: 8px 8px 0 0; }
                    .email-body { background: white; padding: 30px; border-radius: 0 0 8px 8px; }
                    .ticket-info { background: #f0f7ff; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0; border-radius: 4px; }
                    .ticket-id { font-size: 24px; font-weight: bold; color: #007bff; }
                    .email-footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; margin-top: 20px; border-radius: 4px; }
                    .btn { display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-top: 15px; }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <h2>🎫 Support Ticket Received</h2>
                    </div>
                    <div class='email-body'>
                        <p>Hi $user_name,</p>
                        <p>Thank you for contacting us! We have received your support request.</p>
                        
                        <div class='ticket-info'>
                            <p><strong>Ticket ID:</strong><br><span class='ticket-id'>$ticket_id</span></p>
                            <p><strong>Subject:</strong><br>$subject</p>
                        </div>

                        <p>Our team will review your message and get back to you as soon as possible. Please keep your ticket ID for reference when following up.</p>
                        
                        <p>Best regards,<br>The {$this->site_name} Team</p>
                    </div>
                    <div class='email-footer'>
                        <p>© 2026 {$this->site_name}. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        return $this->send($to, $subject_line, $message);
    }

    public function sendAdminNewTicketNotification($to, $ticket_id, $user_name, $user_email, $subject, $priority) {
        $subject_line = "[NEW TICKET] $ticket_id - $subject ($priority priority)";
        
        $priority_color = $priority === 'high' ? '#dc3545' : ($priority === 'medium' ? '#ffc107' : '#28a745');
        
        $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .email-container { max-width: 600px; margin: 0 auto; background: #f5f5f5; padding: 20px; }
                    .email-header { background: #1a1a1a; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                    .email-body { background: white; padding: 30px; border-radius: 0 0 8px 8px; }
                    .ticket-info { background: #f9f9f9; border-left: 4px solid $priority_color; padding: 15px; margin: 15px 0; border-radius: 4px; }
                    .priority-badge { display: inline-block; background: $priority_color; color: white; padding: 5px 10px; border-radius: 3px; font-weight: bold; font-size: 12px; }
                    .email-footer { background: #333; color: white; padding: 15px; text-align: center; font-size: 11px; margin-top: 20px; }
                    .btn { display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-top: 15px; }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <h2>🎫 New Support Ticket</h2>
                    </div>
                    <div class='email-body'>
                        <p><strong>A new support ticket has been submitted.</strong></p>
                        
                        <div class='ticket-info'>
                            <p><strong>Ticket ID:</strong> $ticket_id</p>
                            <p><strong>From:</strong> $user_name ($user_email)</p>
                            <p><strong>Subject:</strong> $subject</p>
                            <p><strong>Priority:</strong> <span class='priority-badge'>".strtoupper($priority)."</span></p>
                        </div>

                        <p>Please log in to the admin panel to view and respond to this ticket.</p>
                        
                        <p>Best regards,<br>{$this->site_name} System</p>
                    </div>
                    <div class='email-footer'>
                        <p>This is an automated notification. Do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        return $this->send($to, $subject_line, $message);
    }

    public function sendUserTicketReplyNotification($to, $user_name, $ticket_id, $admin_reply) {
        $subject_line = "Response to Your Support Ticket - $ticket_id";
        
        $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .email-container { max-width: 600px; margin: 0 auto; background: #f5f5f5; padding: 20px; }
                    .email-header { background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); color: white; padding: 25px; text-align: center; border-radius: 8px 8px 0 0; }
                    .email-body { background: white; padding: 30px; border-radius: 0 0 8px 8px; }
                    .reply-box { background: #f0f7ff; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word; }
                    .email-footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; margin-top: 20px; border-radius: 4px; }
                    .btn { display: inline-block; background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-top: 15px; }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <h2>✓ Your Support Ticket Has Been Answered</h2>
                    </div>
                    <div class='email-body'>
                        <p>Hi $user_name,</p>
                        <p>We have responded to your support ticket <strong>$ticket_id</strong>.</p>
                        
                        <div class='reply-box'>
                            <strong>Admin Response:</strong><br><br>
                            $admin_reply
                        </div>

                        <p>You can view the complete conversation and reply on our website.</p>
                        
                        <p>Thank you for your patience!<br>The {$this->site_name} Team</p>
                    </div>
                    <div class='email-footer'>
                        <p>© 2026 {$this->site_name}. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        return $this->send($to, $subject_line, $message);
    }

    public function sendAdminUserReplyNotification($to, $ticket_id, $user_name, $user_reply) {
        $subject_line = "User Reply - Support Ticket $ticket_id";
        
        $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .email-container { max-width: 600px; margin: 0 auto; background: #f5f5f5; padding: 20px; }
                    .email-header { background: #1a1a1a; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                    .email-body { background: white; padding: 30px; border-radius: 0 0 8px 8px; }
                    .reply-box { background: #f9f9f9; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word; }
                    .email-footer { background: #333; color: white; padding: 15px; text-align: center; font-size: 11px; margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <h2>📝 User Reply on Ticket $ticket_id</h2>
                    </div>
                    <div class='email-body'>
                        <p><strong>$user_name</strong> has replied to support ticket <strong>$ticket_id</strong>.</p>
                        
                        <div class='reply-box'>
                            <strong>User Message:</strong><br><br>
                            $user_reply
                        </div>

                        <p>Please log in to the admin panel to view and respond.</p>
                    </div>
                    <div class='email-footer'>
                        <p>This is an automated notification. Do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        return $this->send($to, $subject_line, $message);
    }

    private function send($to, $subject, $message) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: " . $this->from . "\r\n";
        $headers .= "Reply-To: " . $this->from . "\r\n";

        return mail($to, $subject, $message, $headers);
    }
}
?>
