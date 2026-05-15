<?php
require_once '../config/config.php';
requireEmployer();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = '<div class="alert alert-danger">Security error</div>';
    } else {
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $category = sanitize($_POST['category'] ?? '');
        $company = sanitize($_POST['company'] ?? '');
        $location = sanitize($_POST['location'] ?? 'Getafe, Bohol');
        $salary_min = (int)($_POST['salary_min'] ?? 0);
        $salary_max = (int)($_POST['salary_max'] ?? 0);
        $job_type = sanitize($_POST['job_type'] ?? '');
        $experience_level = sanitize($_POST['experience_level'] ?? 'mid');
        $requirements = sanitize($_POST['requirements'] ?? '');
        $benefits = sanitize($_POST['benefits'] ?? '');

        if (empty($title) || empty($description) || empty($category) || empty($job_type)) {
            $message = '<div class="alert alert-danger">Missing required fields</div>';
        } else {
            $user_id = getUserId();
            $sql = "INSERT INTO jobs (title, description, category, company, location, salary_min, salary_max, 
                    job_type, experience_level, requirements, benefits, posted_by) 
                    VALUES ('$title', '$description', '$category', '$company', '$location', $salary_min, $salary_max, 
                    '$job_type', '$experience_level', '$requirements', '$benefits', $user_id)";
            
            if ($conn->query($sql)) {
                $job_id = $conn->insert_id;
                
                // Send email notification
                require_once '../config/email.php';
                $email = new EmailSender();
                $email->sendJobPostedNotification($_SESSION['email'], $_SESSION['first_name'], $title);
                
                log_action('post_job', "Posted job: $title");
                
                $message = '<div class="alert alert-success">✓ Job posted successfully! Redirecting...</div>';
                header("refresh:2;url=my-jobs.php");
            } else {
                $message = '<div class="alert alert-danger">Error: ' . htmlspecialchars($conn->error) . '</div>';
            }
        }
    }
}

$page_title = 'Post a Job - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <div class="post-job-container">
        <h1>Post a New Job</h1>
        <p class="subtitle">Fill in the details below to post your job opening</p>

        <?php echo $message; ?>

        <form method="POST" class="job-form">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <fieldset>
                <legend>Basic Information</legend>

                <div class="form-row">
                    <div class="form-group">
                        <label>Job Title *</label>
                        <input type="text" name="title" required placeholder="e.g., Senior PHP Developer"
                               value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Company Name *</label>
                        <input type="text" name="company" required placeholder="Your company name"
                               value="<?php echo isset($_POST['company']) ? htmlspecialchars($_POST['company']) : ''; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" required>
                            <option value="">Select a category</option>
                            <option value="IT" <?php echo (isset($_POST['category']) && $_POST['category'] === 'IT') ? 'selected' : ''; ?>>IT & Technology</option>
                            <option value="Sales" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Sales') ? 'selected' : ''; ?>>Sales & Marketing</option>
                            <option value="Education" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Education') ? 'selected' : ''; ?>>Education</option>
                            <option value="Healthcare" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Healthcare') ? 'selected' : ''; ?>>Healthcare</option>
                            <option value="Manufacturing" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Manufacturing') ? 'selected' : ''; ?>>Manufacturing</option>
                            <option value="Agriculture" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Agriculture') ? 'selected' : ''; ?>>Agriculture</option>
                            <option value="Hospitality" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Hospitality') ? 'selected' : ''; ?>>Hospitality & Tourism</option>
                            <option value="Construction" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Construction') ? 'selected' : ''; ?>>Construction</option>
                            <option value="Other" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Location *</label>
                        <input type="text" name="location" required placeholder="Getafe, Bohol" 
                               value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : 'Getafe, Bohol'; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Job Type *</label>
                        <select name="job_type" required>
                            <option value="">Select job type</option>
                            <option value="full-time" <?php echo (isset($_POST['job_type']) && $_POST['job_type'] === 'full-time') ? 'selected' : ''; ?>>Full-time</option>
                            <option value="part-time" <?php echo (isset($_POST['job_type']) && $_POST['job_type'] === 'part-time') ? 'selected' : ''; ?>>Part-time</option>
                            <option value="contract" <?php echo (isset($_POST['job_type']) && $_POST['job_type'] === 'contract') ? 'selected' : ''; ?>>Contract</option>
                            <option value="temporary" <?php echo (isset($_POST['job_type']) && $_POST['job_type'] === 'temporary') ? 'selected' : ''; ?>>Temporary</option>
                            <option value="internship" <?php echo (isset($_POST['job_type']) && $_POST['job_type'] === 'internship') ? 'selected' : ''; ?>>Internship</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Experience Level *</label>
                        <select name="experience_level" required>
                            <option value="entry" <?php echo (isset($_POST['experience_level']) && $_POST['experience_level'] === 'entry') ? 'selected' : ''; ?>>Entry Level</option>
                            <option value="mid" <?php echo (isset($_POST['experience_level']) && $_POST['experience_level'] === 'mid') ? 'selected' : 'selected'; ?>>Mid Level</option>
                            <option value="senior" <?php echo (isset($_POST['experience_level']) && $_POST['experience_level'] === 'senior') ? 'selected' : ''; ?>>Senior</option>
                            <option value="executive" <?php echo (isset($_POST['experience_level']) && $_POST['experience_level'] === 'executive') ? 'selected' : ''; ?>>Executive</option>
                        </select>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Compensation</legend>

                <div class="form-row">
                    <div class="form-group">
                        <label>Minimum Salary (₱)</label>
                        <input type="number" name="salary_min" placeholder="e.g., 15000"
                               value="<?php echo isset($_POST['salary_min']) ? htmlspecialchars($_POST['salary_min']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Maximum Salary (₱)</label>
                        <input type="number" name="salary_max" placeholder="e.g., 30000"
                               value="<?php echo isset($_POST['salary_max']) ? htmlspecialchars($_POST['salary_max']) : ''; ?>">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Job Details</legend>

                <div class="form-group">
                    <label>Job Description *</label>
                    <textarea name="description" required rows="10" placeholder="Describe the job responsibilities, day-to-day tasks, and what you're looking for..."><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Requirements</label>
                    <textarea name="requirements" rows="6" placeholder="List key requirements (one per line)&#10;Bachelor's degree&#10;3+ years experience&#10;Strong communication skills"><?php echo isset($_POST['requirements']) ? htmlspecialchars($_POST['requirements']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Benefits</label>
                    <textarea name="benefits" rows="6" placeholder="What benefits do you offer? (one per line)&#10;Health insurance&#10;Paid time off&#10;Professional development"><?php echo isset($_POST['benefits']) ? htmlspecialchars($_POST['benefits']) : ''; ?></textarea>
                </div>
            </fieldset>

            <div class="form-actions">
                <button type="submit" class="btn btn-success btn-large">Post Job</button>
                <a href="my-jobs.php" class="btn btn-secondary btn-large">Cancel</a>
            </div>
        </form>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>