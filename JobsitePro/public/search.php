<?php
require_once '../config/config.php';

$search = sanitize($_GET['q'] ?? '');
$category = sanitize($_GET['category'] ?? '');
$job_type = sanitize($_GET['job_type'] ?? '');
$page = (int)($_GET['page'] ?? 1);
$limit = 10;
$offset = ($page - 1) * $limit;

// Build search query
$sql = "SELECT j.*, u.first_name, u.last_name, u.company, u.company_logo
        FROM jobs j 
        JOIN users u ON j.posted_by = u.id 
        WHERE j.is_active = 1 AND u.status = 'active'";

// Add search conditions
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (j.title LIKE '%$search%' 
              OR j.description LIKE '%$search%' 
              OR u.company LIKE '%$search%'
              OR j.category LIKE '%$search%')";
}

if (!empty($category)) {
    $category = $conn->real_escape_string($category);
    $sql .= " AND j.category = '$category'";
}

if (!empty($job_type)) {
    $job_type = $conn->real_escape_string($job_type);
    $sql .= " AND j.job_type = '$job_type'";
}

// Get count
$count_sql = str_replace('j.*, u.first_name, u.last_name, u.company, u.company_logo', 'COUNT(*) as total', $sql);
$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$total_jobs = $count_row['total'];
$total_pages = ceil($total_jobs / $limit);

// Add sorting and limit
$sql .= " ORDER BY j.is_featured DESC, j.created_at DESC LIMIT $limit OFFSET $offset";
$jobs = $conn->query($sql);

$page_title = 'Search Results - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <div class="search-results-page">
        <div class="search-header">
            <h1>Search Results</h1>
            <p class="results-count">
                <?php 
                if (!empty($search)) {
                    echo "Found " . $total_jobs . " result" . ($total_jobs !== 1 ? "s" : "") . " for \"<strong>" . htmlspecialchars($search) . "</strong>\"";
                } else {
                    echo "Showing all jobs";
                }
                ?>
            </p>
        </div>

        <div class="search-filters">
            <form method="GET" class="filter-form">
                <input type="hidden" name="q" value="<?php echo htmlspecialchars($search); ?>">

                <div class="filter-row">
                    <div class="form-group">
                        <input type="text" name="q" placeholder="Search jobs..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>

                    <div class="form-group">
                        <select name="category">
                            <option value="">All Categories</option>
                            <option value="IT" <?php echo $category === 'IT' ? 'selected' : ''; ?>>IT</option>
                            <option value="Sales" <?php echo $category === 'Sales' ? 'selected' : ''; ?>>Sales</option>
                            <option value="Education" <?php echo $category === 'Education' ? 'selected' : ''; ?>>Education</option>
                            <option value="Healthcare" <?php echo $category === 'Healthcare' ? 'selected' : ''; ?>>Healthcare</option>
                            <option value="Manufacturing" <?php echo $category === 'Manufacturing' ? 'selected' : ''; ?>>Manufacturing</option>
                            <option value="Agriculture" <?php echo $category === 'Agriculture' ? 'selected' : ''; ?>>Agriculture</option>
                            <option value="Hospitality" <?php echo $category === 'Hospitality' ? 'selected' : ''; ?>>Hospitality</option>
                            <option value="Construction" <?php echo $category === 'Construction' ? 'selected' : ''; ?>>Construction</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <select name="job_type">
                            <option value="">All Job Types</option>
                            <option value="full-time" <?php echo $job_type === 'full-time' ? 'selected' : ''; ?>>Full-time</option>
                            <option value="part-time" <?php echo $job_type === 'part-time' ? 'selected' : ''; ?>>Part-time</option>
                            <option value="contract" <?php echo $job_type === 'contract' ? 'selected' : ''; ?>>Contract</option>
                            <option value="temporary" <?php echo $job_type === 'temporary' ? 'selected' : ''; ?>>Temporary</option>
                            <option value="internship" <?php echo $job_type === 'internship' ? 'selected' : ''; ?>>Internship</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>

        <?php if ($total_jobs === 0): ?>
            <div class="no-results">
                <p>No jobs found matching your search criteria.</p>
                <a href="home.php" class="btn btn-primary">Browse All Jobs</a>
            </div>
        <?php else: ?>
            <div class="search-results">
                <?php while ($job = $jobs->fetch_assoc()): ?>
                    <div class="job-card <?php echo $job['is_featured'] ? 'featured' : ''; ?>">
                        <?php if ($job['is_featured']): ?>
                            <span class="featured-badge">⭐ Featured</span>
                        <?php endif; ?>

                        <div class="job-header">
                            <?php if ($job['company_logo']): ?>
                                <img src="<?php echo BASE_URL . $job['company_logo']; ?>" 
                                     alt="<?php echo $job['company']; ?>" class="company-logo">
                            <?php else: ?>
                                <div class="company-logo-placeholder">
                                    <?php echo strtoupper(substr($job['company'], 0, 2)); ?>
                                </div>
                            <?php endif; ?>

                            <div class="job-header-content">
                                <h3><a href="job-detail.php?id=<?php echo $job['id']; ?>"><?php echo $job['title']; ?></a></h3>
                                <p class="company"><?php echo $job['company']; ?></p>
                            </div>

                            <?php if (isLoggedIn() && getUserType() === 'jobseeker'): ?>
                                <button class="save-job-btn" data-job-id="<?php echo $job['id']; ?>" title="Save this job">
                                    🤍
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
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?q=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&job_type=<?php echo urlencode($job_type); ?>&page=<?php echo $page - 1; ?>" class="btn btn-sm">← Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?q=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&job_type=<?php echo urlencode($job_type); ?>&page=<?php echo $i; ?>" 
                           class="btn btn-sm <?php echo $i === $page ? 'btn-primary' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?q=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&job_type=<?php echo urlencode($job_type); ?>&page=<?php echo $page + 1; ?>" class="btn btn-sm">Next →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</div>

<style>
.search-results-page {
    padding: 30px 0;
}

.search-header {
    margin-bottom: 30px;
}

.search-header h1 {
    margin-bottom: 10px;
}

.results-count {
    color: #666;
    font-size: 14px;
}

.search-filters {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.filter-form .form-group {
    margin-bottom: 0;
}

.filter-form input,
.filter-form select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.filter-form button {
    padding: 10px 20px;
}

.search-results {
    display: grid;
    gap: 20px;
}

@media (max-width: 768px) {
    .filter-form {
        grid-template-columns: 1fr;
    }

    .filter-form button {
        width: 100%;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>