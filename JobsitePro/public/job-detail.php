<?php
require_once '../config/config.php';
requireLogin();

$job_id = (int)($_GET['id'] ?? 0);

// Get job details
$sql = "SELECT j.*, u.id as employer_id, u.first_name, u.last_name, u.email, u.phone, 
               u.company, u.company_info, u.company_website, u.company_logo,
               (SELECT COUNT(*) FROM saved_jobs WHERE job_id = j.id AND user_id = " . (int)getUserId() . ") as is_saved
        FROM jobs j 
        JOIN users u ON j.posted_by = u.id 
        WHERE j.id = $job_id AND j.is_active = 1";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    $_SESSION['error'] = 'Job not found';
    header("Location: home.php");
    exit();
}

$job = $result->fetch_assoc();

// Handle save/unsave job first
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save-job') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Security error. Please try again.';
    } else if (getUserType() !== 'jobseeker') {
        $_SESSION['error'] = 'Only job seekers can save jobs.';
    } else {
        $uid = (int)getUserId();
        $check_saved = $conn->query("SELECT id FROM saved_jobs WHERE user_id = $uid AND job_id = $job_id");

        if ($check_saved && $check_saved->num_rows > 0) {
            $conn->query("DELETE FROM saved_jobs WHERE user_id = $uid AND job_id = $job_id");
            $_SESSION['success'] = 'Job removed from saved jobs.';
        } else {
            $conn->query("INSERT INTO saved_jobs (user_id, job_id) VALUES ($uid, $job_id)");
            $_SESSION['success'] = 'Job saved successfully.';
        }
    }

    header("Location: job-detail.php?id=" . $job_id);
    exit();
}

// Reliable views: count only one view per user per day
$uid = (int)getUserId();
$today = date('Y-m-d');
$conn->query("INSERT IGNORE INTO job_views (job_id, user_id, viewed_date) VALUES ($job_id, $uid, '$today')");
if ($conn->affected_rows > 0) {
    $conn->query("UPDATE jobs SET views_count = views_count + 1 WHERE id = $job_id");
    // refresh job data to display latest view count
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $job = $result->fetch_assoc();
    }
}

// Check if already applied
$already_applied = false;
if (getUserType() === 'jobseeker') {
    $check = $conn->query("SELECT id FROM applications WHERE job_id = $job_id AND user_id = " . (int)getUserId());
    if ($check) {
        $already_applied = $check->num_rows > 0;
    }
}

// Handle apply
$apply_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'apply') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $apply_message = '<div class="alert alert-danger">Security error. Please try again.</div>';
    } else if (getUserType() !== 'jobseeker') {
        $apply_message = '<div class="alert alert-danger">Only job seekers can apply</div>';
    } else if ($already_applied) {
        $apply_message = '<div class="alert alert-warning">You already applied for this job</div>';
    } else {
        $cover_letter = sanitize($_POST['cover_letter'] ?? '');
        $user_id = (int)getUserId();

        $sql_insert = "INSERT INTO applications (job_id, user_id, cover_letter) 
                       VALUES ($job_id, $user_id, '$cover_letter')";

        if ($conn->query($sql_insert)) {
            $conn->query("UPDATE jobs SET applications_count = applications_count + 1 WHERE id = $job_id");

            // Send email notification
            require_once '../config/email.php';
            $email = new EmailSender();
            $email->sendApplicationConfirmation($_SESSION['email'], $_SESSION['first_name'], $job['title'], $job['company']);

            log_action('apply_job', "User applied for job ID: $job_id");

            $apply_message = '<div class="alert alert-success">✓ Application submitted successfully!</div>';
            $already_applied = true;

            // Refresh job data
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                $job = $result->fetch_assoc();
            }
        } else {
            $apply_message = '<div class="alert alert-danger">Error submitting application</div>';
        }
    }
}

$page_title = htmlspecialchars($job['title']) . ' - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <div class="job-detail-container">
        <div class="job-detail-main">
            <a href="home.php" class="btn btn-sm btn-secondary">← Back to Jobs</a>

            <?php echo $apply_message; ?>

            <div class="job-detail-header">
                <div class="job-detail-title">
                    <?php if (!empty($job['company_logo'])): ?>
                        <img src="<?php echo BASE_URL . ltrim($job['company_logo'], '/'); ?>" alt="<?php echo htmlspecialchars($job['company']); ?>" class="detail-logo">
                    <?php else: ?>
                        <div class="detail-logo-placeholder">
                            <?php echo strtoupper(substr($job['company'], 0, 2)); ?>
                        </div>
                    <?php endif; ?>

                    <div>
                        <h1><?php echo htmlspecialchars($job['title']); ?></h1>
                        <p class="company-name"><?php echo htmlspecialchars($job['company']); ?></p>
                    </div>
                </div>

                <?php if (getUserType() === 'jobseeker'): ?>
                    <form method="POST" class="save-job-form" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="action" value="save-job">
                        <button type="submit" class="btn btn-sm btn-outline">
                            <?php echo ((int)$job['is_saved'] > 0) ? '❤️ Saved' : '🤍 Save'; ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="job-meta-grid">
                <div class="meta-box">
                    <small>Location</small>
                    <p>📍 <?php echo htmlspecialchars($job['location']); ?></p>
                </div>

                <div class="meta-box">
                    <small>Job Type</small>
                    <p>💼 <?php echo ucfirst(str_replace('-', ' ', $job['job_type'])); ?></p>
                </div>

                <div class="meta-box">
                    <small>Category</small>
                    <p>🏷️ <?php echo htmlspecialchars($job['category']); ?></p>
                </div>

                <div class="meta-box">
                    <small>Experience Level</small>
                    <p>📈 <?php echo ucfirst($job['experience_level']); ?></p>
                </div>

                <?php if (!empty($job['salary_min']) && !empty($job['salary_max'])): ?>
                    <div class="meta-box">
                        <small>Salary Range</small>
                        <p>💰 <?php echo formatSalary($job['salary_min'], $job['salary_max']); ?>/month</p>
                    </div>
                <?php endif; ?>

                <div class="meta-box">
                    <small>Posted</small>
                    <p>📅 <?php echo timeAgo($job['created_at']); ?></p>
                </div>
            </div>

            <hr>

            <section class="job-section">
                <h2>Job Description</h2>
                <div class="job-description">
                    <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                </div>
            </section>

            <?php if (!empty($job['requirements'])): ?>
                <section class="job-section">
                    <h2>Requirements</h2>
                    <ul class="requirements-list">
                        <?php foreach (explode("\n", $job['requirements']) as $req): ?>
                            <?php if (!empty(trim($req))): ?>
                                <li><?php echo htmlspecialchars(trim($req)); ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>

            <?php if (!empty($job['benefits'])): ?>
                <section class="job-section">
                    <h2>Benefits</h2>
                    <ul class="benefits-list">
                        <?php foreach (explode("\n", $job['benefits']) as $benefit): ?>
                            <?php if (!empty(trim($benefit))): ?>
                                <li>✓ <?php echo htmlspecialchars(trim($benefit)); ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>
        </div>

        <div class="job-detail-sidebar">
            <div class="company-card">
                <h3>About <?php echo htmlspecialchars($job['company']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($job['company_info'] ?? 'No information provided')); ?></p>

                <div class="contact-info">
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($job['email']); ?></p>
                    <?php if (!empty($job['phone'])): ?>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($job['phone']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($job['company_website'])): ?>
                        <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($job['company_website']); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($job['company_website']); ?></a></p>
                    <?php endif; ?>
                </div>

                <?php if (getUserType() !== 'employer' || (int)$job['posted_by'] !== (int)getUserId()): ?>
                    <a href="mailto:<?php echo htmlspecialchars($job['email']); ?>" class="btn btn-secondary btn-block">Contact Employer</a>
                <?php else: ?>
                    <a href="job-applications.php?id=<?php echo (int)$job['id']; ?>" class="btn btn-secondary btn-block">View Applications</a>
                <?php endif; ?>
            </div>

            <?php if (getUserType() === 'jobseeker' && !$already_applied): ?>
                <div class="apply-card">
                    <h3>Apply Now</h3>
                    <form method="POST" class="apply-form">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="action" value="apply">

                        <div class="form-group">
                            <label>Cover Letter</label>
                            <textarea name="cover_letter" rows="6" placeholder="Tell the employer why you're interested in this position..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-block">Submit Application</button>
                    </form>
                </div>
            <?php elseif (getUserType() === 'jobseeker' && $already_applied): ?>
                <div class="apply-card applied">
                    <p>✓ You have already applied for this job</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</div>

</body>
</html>