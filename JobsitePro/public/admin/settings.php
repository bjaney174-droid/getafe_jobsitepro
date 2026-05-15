<?php
require_once '../../config/config.php';
requireAdmin();

$message = '';

// Get current settings
$settings = [];
$result = $conn->query("SELECT * FROM settings");
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = '<div class="alert alert-danger">Security error</div>';
    } else {
        $site_name = sanitize($_POST['site_name'] ?? '');
        $site_description = sanitize($_POST['site_description'] ?? '');
        $site_email = sanitize($_POST['site_email'] ?? '');
        $contact_phone = sanitize($_POST['contact_phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $facebook_url = sanitize($_POST['facebook_url'] ?? '');
        $twitter_url = sanitize($_POST['twitter_url'] ?? '');
        $linkedin_url = sanitize($_POST['linkedin_url'] ?? '');
        $featured_job_price = (int)($_POST['featured_job_price'] ?? 0);
        $max_applications_per_day = (int)($_POST['max_applications_per_day'] ?? 10);

        $settings_to_update = [
            'site_name' => $site_name,
            'site_description' => $site_description,
            'site_email' => $site_email,
            'contact_phone' => $contact_phone,
            'address' => $address,
            'facebook_url' => $facebook_url,
            'twitter_url' => $twitter_url,
            'linkedin_url' => $linkedin_url,
            'featured_job_price' => $featured_job_price,
            'max_applications_per_day' => $max_applications_per_day
        ];

        foreach ($settings_to_update as $key => $value) {
            $check = $conn->query("SELECT id FROM settings WHERE setting_key = '$key'");
            if ($check && $check->num_rows > 0) {
                $conn->query("UPDATE settings SET setting_value = '$value', updated_at = NOW() WHERE setting_key = '$key'");
            } else {
                $conn->query("INSERT INTO settings (setting_key, setting_value) VALUES ('$key', '$value')");
            }
        }

        log_action('update_settings', 'Site settings updated');
        $message = '<div class="alert alert-success">✓ Settings updated successfully!</div>';

        // Refresh settings
        $result = $conn->query("SELECT * FROM settings");
        $settings = [];
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
}

$page_title = 'Settings - Admin';
require_once '../../includes/header.php';
?>

<div class="container">
    <?php require_once '../../includes/navbar.php'; ?>

    <div class="admin-wrap">
        <div class="admin-head">
            <h1>Site Settings</h1>
            <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
        </div>

        <?php echo $message; ?>

        <form method="POST" class="admin-card settings-form">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="settings-section">
                <h3>General Settings</h3>

                <div class="form-group">
                    <label>Site Name *</label>
                    <input type="text" name="site_name" required
                           value="<?php echo htmlspecialchars($settings['site_name'] ?? 'Getafe Jobsite', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="form-group">
                    <label>Site Description *</label>
                    <textarea name="site_description" rows="3" required><?php echo htmlspecialchars($settings['site_description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Contact Email *</label>
                        <input type="email" name="site_email" required
                               value="<?php echo htmlspecialchars($settings['site_email'] ?? 'info@getafejobsite.com', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="form-group">
                        <label>Contact Phone *</label>
                        <input type="tel" name="contact_phone" required
                               value="<?php echo htmlspecialchars($settings['contact_phone'] ?? '0701918626', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Address *</label>
                    <input type="text" name="address" required
                           value="<?php echo htmlspecialchars($settings['address'] ?? 'Getafe, Bohol, Philippines', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="settings-section">
                <h3>Social Links</h3>

                <div class="form-group">
                    <label>Facebook URL</label>
                    <input type="url" name="facebook_url" placeholder="https://facebook.com/yourpage"
                           value="<?php echo htmlspecialchars($settings['facebook_url'] ?? 'https://facebook.com/', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="form-group">
                    <label>Twitter/X URL</label>
                    <input type="url" name="twitter_url" placeholder="https://twitter.com/yourhandle"
                           value="<?php echo htmlspecialchars($settings['twitter_url'] ?? 'https://twitter.com/', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="form-group">
                    <label>LinkedIn URL</label>
                    <input type="url" name="linkedin_url" placeholder="https://linkedin.com/in/yourprofile"
                           value="<?php echo htmlspecialchars($settings['linkedin_url'] ?? 'https://linkedin.com/', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
            </div>

            <div class="settings-section">
                <h3>Business Settings</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>Featured Job Price (₱) *</label>
                        <input type="number" name="featured_job_price" required
                               value="<?php echo htmlspecialchars((string)($settings['featured_job_price'] ?? 500), ENT_QUOTES, 'UTF-8'); ?>">
                        <small>Price employers pay to feature a job</small>
                    </div>

                    <div class="form-group">
                        <label>Max Applications Per Day *</label>
                        <input type="number" name="max_applications_per_day" required
                               value="<?php echo htmlspecialchars((string)($settings['max_applications_per_day'] ?? 10), ENT_QUOTES, 'UTF-8'); ?>">
                        <small>Maximum applications a job seeker can submit per day</small>
                    </div>
                </div>
            </div>

            <div class="settings-section">
                <h3>System Information</h3>
                <div class="info-box">
                    <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                    <p><strong>MySQL Version:</strong> <?php echo $conn->get_server_info(); ?></p>
                    <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
                    <p><strong>Hostname:</strong> <?php echo gethostname(); ?></p>
                </div>
            </div>

            <div class="settings-section">
                <h3>Database Backup</h3>
                <div class="backup-box">
                    <p>Download a backup of your database for safety.</p>
                    <a href="backup.php" class="btn btn-primary">Download Backup</a>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success btn-large">Save Settings</button>
                <a href="dashboard.php" class="btn btn-secondary btn-large">Cancel</a>
            </div>
        </form>
    </div>

    <?php require_once '../../includes/footer.php'; ?>
</div>

<style>
.admin-wrap { margin-top: 20px; }
.admin-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }

.admin-card {
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:16px;
    margin-bottom:14px;
}

.settings-section {
    margin-bottom: 24px;
    padding-bottom: 18px;
    border-bottom: 1px solid #edf0f4;
}
.settings-section:last-of-type {
    border-bottom: 0;
    margin-bottom: 0;
}
.settings-section h3 {
    margin: 0 0 12px;
    color: #0d6efd;
}

.form-group {
    margin-bottom: 12px;
}
.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
}
.form-group input,
.form-group textarea {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 10px;
}
.form-group small {
    color: #6b7280;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.info-box, .backup-box {
    background: #f8fafc;
    border: 1px solid #e7edf5;
    border-radius: 10px;
    padding: 12px;
}
.info-box p { margin: 6px 0; }

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 8px;
}
.form-actions .btn {
    flex: 1;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

</body>
</html>