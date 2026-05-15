<?php
class ApplicationEmailNotifications {
    private $from;
    private $site_name;
    private $site_url;

    public function __construct() {
        $this->from = getSetting('site_email', 'info@getafejobsite.com');
        $this->site_name = getSetting('site_name', 'Getafe Jobsite');
        $this->site_url = BASE_URL;
    }

    /**
     * Send email when application status changes
     */
    public function sendApplicationStatusChanged($to, $jobseeker_name, $job_title, $company, $new_status, $employer_notes = '') {
        $status_text = ucfirst(str_replace('_', ' ', $new_status));
        $status_colors = [
            'reviewed' => '#0c4a6e',
            'approved' => '#15803d',
            'rejected' => '#7f1d1d',
            'pending' => '#78350f'
        ];
        $status_color = $status_colors[$new_status] ?? '#333';

        $subject = "Application Status Update - $job_title at $company";

        $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .email-container { max-width: 600px; margin: 0 auto; background: #f5f5f5; padding: 20px; }
                    .email-header { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; padding: 25px; text-align: center; border-radius: 8px 8px 0 0; }
                    .email-body { background: white; padding: 30px; border-radius: 0 0 8px 8px; }
                    .status-badge { display: inline-block; background: $status_color; color: white; padding: 10px 20px; border-radius: 4px; font-weight: bold; font-size: 16px; margin: 15px 0; }
                    .job-info { background: #f0f7ff; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0; border-radius: 4px; }
                    .notes-box { background: #fffbeb; border-left: 4px solid #fbbf24; padding: 15px; margin: 15px 0; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word; }
                    .email-footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; margin-top: 20px; border-radius: 4px; }
                    .btn { display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-top: 15px; }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <h2>📋 Application Status Update</h2>
                    </div>
                    <div class='email-body'>
                        <p>Hi $jobseeker_name,</p>
                        <p>We have an update on your application for <strong>$job_title</strong> at <strong>$company</strong>.</p>
                        
                        <div class='status-badge'>Status: $status_text</div>
                        
                        <div class='job-info'>
                            <p><strong>Job Position:</strong> $job_title</p>
                            <p><strong>Company:</strong> $company</p>
                            <p><strong>New Status:</strong> $status_text</p>
                        </div>";

        if (!empty($employer_notes)) {
            $message .= "
                        <div class='notes-box'>
                            <strong>Message from Employer:</strong><br><br>
                            " . htmlspecialchars($employer_notes) . "
                        </div>";
        }

        $message .= "
                        <p>You can view more details and reply directly in your application dashboard.</p>
                        <a href='{$this->site_url}my-applications.php' class='btn'>View Application</a>
                        
                        <p style='margin-top: 30px; color: #666;'>Best regards,<br>The {$this->site_name} Team</p>
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

    /**
     * Send email when interview is scheduled
     */
    public function sendInterviewScheduled($to, $jobseeker_name, $job_title, $company, $interview_date, $interview_location, $employer_name, $message = '') {
        $interview_datetime = date('F d, Y \a\t h:i A', strtotime($interview_date));

        $subject = "Interview Scheduled - $job_title at $company";

        $email_message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .email-container { max-width: 600px; margin: 0 auto; background: #f5f5f5; padding: 20px; }
                    .email-header { background: linear-gradient(135deg, #22c55e 0%, #15803d 100%); color: white; padding: 25px; text-align: center; border-radius: 8px 8px 0 0; }
                    .email-body { background: white; padding: 30px; border-radius: 0 0 8px 8px; }
                    .interview-box { background: #f0fdf4; border-left: 4px solid #22c55e; padding: 20px; margin: 20px 0; border-radius: 4px; }
                    .interview-detail { margin: 10px 0; font-size: 16px; }
                    .interview-detail strong { color: #15803d; }
                    .message-box { background: #fffbeb; border-left: 4px solid #fbbf24; padding: 15px; margin: 15px 0; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word; }
                    .email-footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; margin-top: 20px; border-radius: 4px; }
                    .btn { display: inline-block; background: #22c55e; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-top: 15px; }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <h2>📅 Interview Scheduled</h2>
                    </div>
                    <div class='email-body'>
                        <p>Hi $jobseeker_name,</p>
                        <p>Great news! An interview has been scheduled for your application to <strong>$job_title</strong> at <strong>$company</strong>.</p>
                        
                        <div class='interview-box'>
                            <div class='interview-detail'>
                                <strong>📅 Date & Time:</strong><br>
                                $interview_datetime
                            </div>
                            <div class='interview-detail'>
                                <strong>📍 Location:</strong><br>
                                $interview_location
                            </div>
                            <div class='interview-detail'>
                                <strong>👤 Interviewer:</strong><br>
                                $employer_name
                            </div>
                        </div>";

        if (!empty($message)) {
            $email_message .= "
                        <div class='message-box'>
                            <strong>Message from Employer:</strong><br><br>
                            " . htmlspecialchars($message) . "
                        </div>";
        }

        $email_message .= "
                        <p><strong>Please make sure to be on time and prepared for the interview.</strong></p>
                        <p>If you need to reschedule, please contact the employer as soon as possible.</p>
                        
                        <a href='{$this->site_url}my-applications.php' class='btn'>View Application Details</a>
                        
                        <p style='margin-top: 30px; color: #666;'>Best regards,<br>The {$this->site_name} Team</p>
                    </div>
                    <div class='email-footer'>
                        <p>© 2026 {$this->site_name}. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        return $this->send($to, $subject, $email_message);
    }

    /**
     * Send email when new message is received in application
     */
    public function sendApplicationMessage($to, $recipient_name, $sender_name, $job_title, $company, $message_preview, $user_type = 'jobseeker') {
        $header_text = ($user_type === 'jobseeker') ? "New Message from Employer" : "New Message from Jobseeker";
        $subject = "New Message - $job_title Application";

        $email_message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .email-container { max-width: 600px; margin: 0 auto; background: #f5f5f5; padding: 20px; }
                    .email-header { background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); color: white; padding: 25px; text-align: center; border-radius: 8px 8px 0 0; }
                    .email-body { background: white; padding: 30px; border-radius: 0 0 8px 8px; }
                    .message-box { background: #f0f9ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word; max-height: 200px; overflow: hidden; }
                    .message-truncated { color: #666; font-style: italic; margin-top: 10px; }
                    .email-footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; margin-top: 20px; border-radius: 4px; }
                    .btn { display: inline-block; background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-top: 15px; }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <h2>💬 $header_text</h2>
                    </div>
                    <div class='email-body'>
                        <p>Hi $recipient_name,</p>
                        <p><strong>$sender_name</strong> has sent you a message regarding your application to <strong>$job_title</strong> at <strong>$company</strong>.</p>
                        
                        <div class='message-box'>
                            " . htmlspecialchars(substr($message_preview, 0, 300)) . "
                            " . (strlen($message_preview) > 300 ? "<div class='message-truncated'>[Message truncated - view full message on website]</div>" : "") . "
                        </div>
                        
                        <p>Click the button below to view the full message and reply:</p>
                        <a href='{$this->site_url}my-applications.php' class='btn'>View Message</a>
                        
                        <p style='margin-top: 30px; color: #666;'>Best regards,<br>The {$this->site_name} Team</p>
                    </div>
                    <div class='email-footer'>
                        <p>© 2026 {$this->site_name}. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        return $this->send($to, $subject, $email_message);
    }

    /**
     * Send email when employer receives new application
     */
    public function sendNewApplicationNotification($to, $employer_name, $jobseeker_name, $job_title, $jobseeker_email, $jobseeker_phone = '') {
        $subject = "New Application - $job_title";

        $email_message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; color: #333; }
                    .email-container { max-width: 600px; margin: 0 auto; background: #f5f5f5; padding: 20px; }
                    .email-header { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white; padding: 25px; text-align: center; border-radius: 8px 8px 0 0; }
                    .email-body { background: white; padding: 30px; border-radius: 0 0 8px 8px; }
                    .applicant-box { background: #faf5ff; border-left: 4px solid #8b5cf6; padding: 15px; margin: 20px 0; border-radius: 4px; }
                    .applicant-detail { margin: 10px 0; }
                    .email-footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; margin-top: 20px; border-radius: 4px; }
                    .btn { display: inline-block; background: #8b5cf6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-top: 15px; }
                </style>
            </head>
            <body>
                <div class='email-container'>
                    <div class='email-header'>
                        <h2>🎉 New Application Received</h2>
                    </div>
                    <div class='email-body'>
                        <p>Hi $employer_name,</p>
                        <p>You have received a new application for <strong>$job_title</strong>!</p>
                        
                        <div class='applicant-box'>
                            <div class='applicant-detail'>
                                <strong>👤 Applicant Name:</strong><br>
                                $jobseeker_name
                            </div>
                            <div class='applicant-detail'>
                                <strong>📧 Email:</strong><br>
                                <a href='mailto:$jobseeker_email'>$jobseeker_email</a>
                            </div>";

        if (!empty($jobseeker_phone)) {
            $email_message .= "
                            <div class='applicant-detail'>
                                <strong>📱 Phone:</strong><br>
                                $jobseeker_phone
                            </div>";
        }

        $email_message .= "
                        </div>
                        
                        <p>Log in to your employer dashboard to review the application, view the cover letter, and manage the candidate.</p>
                        <a href='{$this->site_url}my-applications.php' class='btn'>Review Application</a>
                        
                        <p style='margin-top: 30px; color: #666;'>Best regards,<br>The {$this->site_name} Team</p>
                    </div>
                    <div class='email-footer'>
                        <p>© 2026 {$this->site_name}. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        return $this->send($to, $subject, $email_message);
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
