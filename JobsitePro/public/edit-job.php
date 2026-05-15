<?php
require_once '../config/config.php';
requireEmployer();

$job_id = (int)($_GET['id'] ?? 0);
$user_id = (int)getUserId();
$message = '';

// Get job data and ownership check
$job_result = $conn->query("SELECT * FROM jobs WHERE id = $job_id AND posted_by = $user_id");
if (!$job_result || $job_result->num_rows === 0) {
    $_SESSION['error'] = 'Job not found or access denied.';
    header("Location: my-jobs.php");
    exit();
}

$job = $job_result->fetch_assoc();

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

        if (empty($title) || empty($description) || empty($category) || empty($job_type) || empty($company) || empty($location)) {
            $message = '<div class="alert alert-danger">Please fill all required fields.</div>';
        } elseif ($salary_min > 0 && $salary_max > 0 && $salary_min > $salary_max) {
            $message = '<div class="alert alert-danger">Minimum salary cannot be higher than maximum salary.</div>';
        } else {
            $sql = "UPDATE jobs SET
                    title = '$title',
                    description = '$description',
                    category = '$category',
                    company = '$company',
                    location = '$location',
                    salary_min = $salary_min,
                    salary_max = $salary_max,
                    job_type = '$job_type',
                    experience_level = '$experience_level',
                    requirements = '$requirements',
                    benefits = '$benefits',
                    updated_at = NOW()
                    WHERE id = $job_id AND posted_by = $user_id";

            if ($conn->query($sql)) {
                log_action('edit_job', "Edited job ID: $job_id");
                $_SESSION['success'] = 'Job updated successfully.';
                header("Location: my-jobs.php");
                exit();
            } else {
                $message = '<div class="alert alert-danger">Error updating job.</div>';
            }
        }

        // keep submitted values on validation error
        $job['title'] = $_POST['title'] ?? $job['title'];
        $job['description'] = $_POST['description'] ?? $job['description'];
        $job['category'] = $_POST['category'] ?? $job['category'];
        $job['company'] = $_POST['company'] ?? $job['company'];
        $job['location'] = $_POST['location'] ?? $job['location'];
        $job['salary_min'] = $_POST['salary_min'] ?? $job['salary_min'];
        $job['salary_max'] = $_POST['salary_max'] ?? $job['salary_max'];
        $job['job_type'] = $_POST['job_type'] ?? $job['job_type'];
        $job['experience_level'] = $_POST['experience_level'] ?? $job['experience_level'];
        $job['requirements'] = $_POST['requirements'] ?? $job['requirements'];
        $job['benefits'] = $_POST['benefits'] ?? $job['benefits'];
    }
}

$page_title = 'Edit Job - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <div class="post-job-container">
        <h1>Edit Job</h1>
        <p class="subtitle">Update your job posting details</p>

        <?php echo $message; ?>

        <form method="POST" class="job-form">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <fieldset>
                <legend>Basic Information</legend>

                <div class="form-row">
                    <div class="form-group">
                        <label>Job Title *</label>
                        <input type="text" name="title" required
                               value="<?php echo htmlspecialchars($job['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="form-group">
                        <label>Company Name *</label>
                        <input type="text" name="company" required
                               value="<?php echo htmlspecialchars($job['company'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" required>
                            <?php $cat = $job['category'] ?? ''; ?>
                            <option value="">Select a category</option>
                            <option value="IT" <?php echo $cat === 'IT' ? 'selected' : ''; ?>>IT & Technology</option>
                            <option value="Sales" <?php echo $cat === 'Sales' ? 'selected' : ''; ?>>Sales & Marketing</option>
                            <option value="Education" <?php echo $cat === 'Education' ? 'selected' : ''; ?>>Education</option>
                            <option value="Healthcare" <?php echo $cat === 'Healthcare' ? 'selected' : ''; ?>>Healthcare</option>
                            <option value="Manufacturing" <?php echo $cat === 'Manufacturing' ? 'selected' : ''; ?>>Manufacturing</option>
                            <option value="Agriculture" <?php echo $cat === 'Agriculture' ? 'selected' : ''; ?>>Agriculture</option>
                            <option value="Hospitality" <?php echo $cat === 'Hospitality' ? 'selected' : ''; ?>>Hospitality & Tourism</option>
                            <option value="Construction" <?php echo $cat === 'Construction' ? 'selected' : ''; ?>>Construction</option>
                            <option value="Other" <?php echo $cat === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Location *</label>
                        <input type="text" name="location" required
                               value="<?php echo htmlspecialchars($job['location'] ?? 'Getafe, Bohol', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Job Type *</label>
                        <select name="job_type" required>
                            <?php $jt = $job['job_type'] ?? ''; ?>
                            <option value="">Select job type</option>
                            <option value="full-time" <?php echo $jt === 'full-time' ? 'selected' : ''; ?>>Full-time</option>
                            <option value="part-time" <?php echo $jt === 'part-time' ? 'selected' : ''; ?>>Part-time</option>
                            <option value="contract" <?php echo $jt === 'contract' ? 'selected' : ''; ?>>Contract</option>
                            <option value="temporary" <?php echo $jt === 'temporary' ? 'selected' : ''; ?>>Temporary</option>
                            <option value="internship" <?php echo $jt === 'internship' ? 'selected' : ''; ?>>Internship</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Experience Level *</label>
                        <select name="experience_level" required>
                            <?php $el = $job['experience_level'] ?? 'mid'; ?>
                            <option value="entry" <?php echo $el === 'entry' ? 'selected' : ''; ?>>Entry Level</option>
                            <option value="mid" <?php echo $el === 'mid' ? 'selected' : ''; ?>>Mid Level</option>
                            <option value="senior" <?php echo $el === 'senior' ? 'selected' : ''; ?>>Senior</option>
                            <option value="executive" <?php echo $el === 'executive' ? 'selected' : ''; ?>>Executive</option>
                        </select>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Compensation</legend>

                <div class="form-row">
                    <div class="form-group">
                        <label>Minimum Salary (₱)</label>
                        <input type="number" name="salary_min"
                               value="<?php echo htmlspecialchars((string)($job['salary_min'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="form-group">
                        <label>Maximum Salary (₱)</label>
                        <input type="number" name="salary_max"
                               value="<?php echo htmlspecialchars((string)($job['salary_max'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Job Details</legend>

                <div class="form-group">
                    <label>Job Description *</label>
                    <textarea name="description" required rows="10"><?php echo htmlspecialchars($job['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Requirements</label>
                    <textarea name="requirements" rows="6"><?php echo htmlspecialchars($job['requirements'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Benefits</label>
                    <textarea name="benefits" rows="6"><?php echo htmlspecialchars($job['benefits'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
            </fieldset>

            <div class="form-actions">
                <button type="submit" class="btn btn-success btn-large">Update Job</button>
                <a href="my-jobs.php" class="btn btn-secondary btn-large">Cancel</a>
            </div>
        </form>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</div>

</body>
</html>