<?php
require_once '../config/config.php';
requireLogin();

$category = sanitize($_GET['category'] ?? '');
$job_type = sanitize($_GET['job_type'] ?? '');
$search = sanitize($_GET['search'] ?? '');
$page = (int)($_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

// Build query
$sql = "SELECT j.*, u.first_name, u.last_name, u.company, u.company_logo,
               (SELECT COUNT(*) FROM saved_jobs WHERE job_id = j.id AND user_id = " . getUserId() . ") as is_saved
        FROM jobs j 
        JOIN users u ON j.posted_by = u.id 
        WHERE j.is_active = 1 AND u.status = 'active'";

if (!empty($category)) {
    $sql .= " AND j.category = '$category'";
}

if (!empty($job_type)) {
    $sql .= " AND j.job_type = '$job_type'";
}

if (!empty($search)) {
    $sql .= " AND (j.title LIKE '%$search%' OR j.description LIKE '%$search%' OR u.company LIKE '%$search%')";
}

$sql_count = str_replace("SELECT j.*, u.first_name, u.last_name, u.company, u.company_logo,
               (SELECT COUNT(*) FROM saved_jobs WHERE job_id = j.id AND user_id = " . getUserId() . ") as is_saved", 
               "SELECT COUNT(*) as total", $sql);

$count_result = $conn->query($sql_count);
$count_row = $count_result->fetch_assoc();
$total_jobs = $count_row['total'];
$total_pages = ceil($total_jobs / $limit);

$sql .= " ORDER BY j.is_featured DESC, j.created_at DESC LIMIT $limit OFFSET $offset";
$jobs = $conn->query($sql);

$page_title = 'Jobs - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <section class="hero-section">
        <div class="hero-content">
            <h1>Find Your Dream Job</h1>
            <p>Discover thousands of job opportunities in Getafe, Bohol</p>
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Job title, company, or keywords..."
                       value="<?php echo $search; ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    </section>

    <section class="jobs-section">
        <div class="sidebar-filters">
            <h3>Filters</h3>

            <div class="filter-group">
                <label>Category</label>
                <select name="category" onchange="applyFilters(this)">
                    <option value="">All Categories</option>
                    <option value="IT" <?php echo $category === 'IT' ? 'selected' : ''; ?>>IT</option>
                    <option value="Sales" <?php echo $category === 'Sales' ? 'selected' : ''; ?>>Sales</option>
                    <option value="Education" <?php echo $category === 'Education' ? 'selected' : ''; ?>>Education</option>
                    <option value="Healthcare" <?php echo $category === 'Healthcare' ? 'selected' : ''; ?>>Healthcare</option>
                    <option value="Manufacturing" <?php echo $category === 'Manufacturing' ? 'selected' : ''; ?>>Manufacturing</option>
                    <option value="Agriculture" <?php echo $category === 'Agriculture' ? 'selected' : ''; ?>>Agriculture</option>
                    <option value="Hospitality" <?php echo $category === 'Hospitality' ? 'selected' : ''; ?>>Hospitality</option>
                    <option value="Construction" <?php echo $category === 'Construction' ? 'selected' : ''; ?>>Construction</option>
                    <option value="Other" <?php echo $category === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Job Type</label>
                <select name="job_type" onchange="applyFilters(this)">
                    <option value="">All Types</option>
                    <option value="full-time" <?php echo $job_type === 'full-time' ? 'selected' : ''; ?>>Full-time</option>
                    <option value="part-time" <?php echo $job_type === 'part-time' ? 'selected' : ''; ?>>Part-time</option>
                    <option value="contract" <?php echo $job_type === 'contract' ? 'selected' : ''; ?>>Contract</option>
                    <option value="temporary" <?php echo $job_type === 'temporary' ? 'selected' : ''; ?>>Temporary</option>
                    <option value="internship" <?php echo $job_type === 'internship' ? 'selected' : ''; ?>>Internship</option>
                </select>
            </div>
        </div>

        <div class="jobs-list">
            <?php if ($jobs->num_rows === 0): ?>
                <div class="no-results">
                    <p>No jobs found matching your criteria.</p>
                    <a href="home.php" class="btn btn-primary">Clear Filters</a>
                </div>
            <?php else: ?>
                <?php while ($job = $jobs->fetch_assoc()): ?>
                    <div class="job-card <?php echo $job['is_featured'] ? 'featured' : ''; ?>">
                        <?php if ($job['is_featured']): ?>
                            <span class="featured-badge">⭐ Featured</span>
                        <?php endif; ?>

                        <div class="job-header">
                            <?php if ($job['company_logo']): ?>
                                <img src="<?php echo BASE_URL . $job['company_logo']; ?>" alt="<?php echo $job['company']; ?>" class="company-logo">
                            <?php else: ?>
                                <div class="company-logo-placeholder">
                                    <?php echo strtoupper(substr($job['company'], 0, 2)); ?>
                                </div>
                            <?php endif; ?>

                            <div class="job-header-content">
                                <h3><a href="job-detail.php?id=<?php echo $job['id']; ?>"><?php echo $job['title']; ?></a></h3>
                                <p class="company"><?php echo $job['company']; ?></p>
                            </div>

                            <?php if (getUserType() === 'jobseeker'): ?>
                                <button class="save-job-btn" data-job-id="<?php echo $job['id']; ?>" 
                                        title="Save this job">
                                    <?php echo $job['is_saved'] ? '❤️' : '🤍'; ?>
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="job-tags">
                            <span class="tag">📍 <?php echo $job['location']; ?></span>
                            <span class="tag">💼 <?php echo ucfirst(str_replace('-', ' ', $job['job_type'])); ?></span>
                            <span class="tag">🏷️ <?php echo $job['category']; ?></span>
                            <?php if ($job['salary_min'] && $job['salary_max']): ?>
                                <span class="tag">💰 <?php echo formatSalary($job['salary_min'], $job['salary_max']); ?></span>
                            <?php endif; ?>
                        </div>

                        <p class="job-description"><?php echo substr(strip_tags($job['description']), 0, 150) . '...'; ?></p>

                        <div class="job-footer">
                            <small>👁️ <?php echo $job['views_count']; ?> views • 📋 <?php echo $job['applications_count']; ?> applications • <?php echo timeAgo($job['created_at']); ?></small>
                            <a href="job-detail.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&category=<?php echo $category; ?>&job_type=<?php echo $job_type; ?>&search=<?php echo $search; ?>" class="btn btn-sm">← Previous</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&category=<?php echo $category; ?>&job_type=<?php echo $job_type; ?>&search=<?php echo $search; ?>" 
                               class="btn btn-sm <?php echo $i === $page ? 'btn-primary' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&category=<?php echo $category; ?>&job_type=<?php echo $job_type; ?>&search=<?php echo $search; ?>" class="btn btn-sm">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <?php require_once '../includes/footer.php'; ?>
</div>

<script src="<?php echo BASE_URL; ?>js/main.js"></script>
<?php require_once '../includes/footer.php'; ?>
</body>
</html>