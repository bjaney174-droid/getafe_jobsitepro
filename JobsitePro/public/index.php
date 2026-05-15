<?php
require_once '../config/config.php';

if (isLoggedIn()) {
    header("Location: home.php");
    exit();
}

$page_title = getSetting('site_name', 'Getafe Jobsite') . ' - Find Your Dream Job';
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1>🏢 <?php echo getSetting('site_name', 'Getafe Jobsite'); ?></h1>
            <p><?php echo getSetting('site_description', 'Find your dream job in Getafe, Bohol'); ?></p>
            
            <div class="hero-buttons">
                <a href="login.php" class="btn btn-primary btn-large">I'm Looking for a Job</a>
                <a href="register.php" class="btn btn-secondary btn-large">I'm Hiring</a>
            </div>
        </div>
    </section>

    <!-- Quick Stats -->
    <section class="quick-stats">
        <div class="stats-container">
            <div class="stat-item">
                <div class="stat-number">
                    <?php 
                    $total_jobs = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE is_active = 1")->fetch_assoc()['count'];
                    echo $total_jobs;
                    ?>
                </div>
                <div class="stat-label">Active Jobs</div>
            </div>

            <div class="stat-item">
                <div class="stat-number">
                    <?php 
                    $total_companies = $conn->query("SELECT COUNT(DISTINCT company) as count FROM jobs WHERE is_active = 1")->fetch_assoc()['count'];
                    echo $total_companies;
                    ?>
                </div>
                <div class="stat-label">Companies Hiring</div>
            </div>

            <div class="stat-item">
                <div class="stat-number">
                    <?php 
                    $total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'jobseeker'")->fetch_assoc()['count'];
                    echo $total_users;
                    ?>
                </div>
                <div class="stat-label">Job Seekers</div>
            </div>

            <div class="stat-item">
                <div class="stat-number">
                    <?php 
                    $total_applications = $conn->query("SELECT COUNT(*) as count FROM applications")->fetch_assoc()['count'];
                    echo $total_applications;
                    ?>
                </div>
                <div class="stat-label">Placements</div>
            </div>
        </div>
    </section>

    <!-- Featured Jobs -->
    <section class="featured-section">
        <h2>⭐ Featured Jobs</h2>
        <p class="section-subtitle">Check out our featured job opportunities</p>

        <div class="jobs-grid">
            <?php
            $featured_sql = "SELECT j.*, u.company, u.company_logo 
                           FROM jobs j 
                           JOIN users u ON j.posted_by = u.id 
                           WHERE j.is_featured = 1 AND j.is_active = 1 
                           ORDER BY j.featured_until DESC LIMIT 6";
            $featured_jobs = $conn->query($featured_sql);

            if ($featured_jobs->num_rows === 0):
            ?>
                <div class="no-results" style="grid-column: 1/-1;">
                    <p>No featured jobs at the moment.</p>
                </div>
            <?php else: ?>
                <?php while ($job = $featured_jobs->fetch_assoc()): ?>
                    <div class="job-card featured">
                        <span class="featured-badge">⭐ Featured</span>
                        
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
                        </div>

                        <div class="job-tags">
                            <span class="tag">📍 <?php echo $job['location']; ?></span>
                            <span class="tag">💼 <?php echo ucfirst(str_replace('-', ' ', $job['job_type'])); ?></span>
                            <span class="tag">🏷️ <?php echo $job['category']; ?></span>
                        </div>

                        <p class="job-description"><?php echo substr(strip_tags($job['description']), 0, 120) . '...'; ?></p>

                        <div class="job-footer">
                            <small><?php echo timeAgo($job['created_at']); ?></small>
                            <a href="job-detail.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-primary">View</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="home.php" class="btn btn-primary btn-large">Browse All Jobs</a>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works">
        <h2>How It Works</h2>

        <div class="steps-grid">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Register</h3>
                <p>Create your account as a job seeker or employer</p>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <h3>Browse/Post</h3>
                <p>Find jobs or post job opportunities for your company</p>
            </div>

            <div class="step">
                <div class="step-number">3</div>
                <h3>Apply/Review</h3>
                <p>Apply for jobs or review applications from candidates</p>
            </div>

            <div class="step">
                <div class="step-number">4</div>
                <h3>Get Hired!</h3>
                <p>Connect with opportunities or find the perfect candidate</p>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="categories-section">
        <h2>Job Categories</h2>

        <div class="categories-grid">
            <?php
            $categories = ['IT', 'Sales', 'Education', 'Healthcare', 'Manufacturing', 'Agriculture', 'Hospitality', 'Construction'];
            
            foreach ($categories as $category):
                $count = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE category = '$category' AND is_active = 1")->fetch_assoc()['count'];
            ?>
                <a href="home.php?category=<?php echo urlencode($category); ?>" class="category-card">
                    <h3><?php echo $category; ?></h3>
                    <p><?php echo $count; ?> Jobs Available</p>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <h2>Ready to Start?</h2>
        <p>Join thousands of job seekers and employers in Getafe, Bohol</p>

        <div class="cta-buttons">
            <a href="register.php?type=jobseeker" class="btn btn-primary btn-large">Register as Job Seeker</a>
            <a href="register.php?type=employer" class="btn btn-secondary btn-large">Register as Employer</a>
        </div>
    </section>

    <?php require_once '../includes/footer.php'; ?>
</div>

<style>
.quick-stats {
    background: white;
    padding: 40px 0;
    margin: 40px 0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 30px;
    text-align: center;
}

.stat-item {
    padding: 20px;
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 10px;
}

.stat-label {
    color: #666;
    font-size: 14px;
}

.featured-section {
    margin: 40px 0;
}

.featured-section h2 {
    margin-bottom: 10px;
}

.section-subtitle {
    color: #666;
    margin-bottom: 30px;
}

.jobs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.how-it-works {
    background: #f8f9fa;
    padding: 40px;
    border-radius: 8px;
    margin: 40px 0;
}

.how-it-works h2 {
    text-align: center;
    margin-bottom: 40px;
}

.steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
}

.step {
    text-align: center;
    padding: 20px;
}

.step-number {
    width: 50px;
    height: 50px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: bold;
    margin: 0 auto 15px;
}

.step h3 {
    margin: 15px 0 10px;
}

.step p {
    color: #666;
    font-size: 14px;
}

.categories-section {
    margin: 40px 0;
}

.categories-section h2 {
    margin-bottom: 30px;
    text-align: center;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.category-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    color: #333;
}

.category-card:hover {
    background: #007bff;
    color: white;
    transform: translateY(-2px);
}

.category-card h3 {
    margin: 0 0 10px;
    font-size: 18px;
}

.category-card p {
    margin: 0;
    font-size: 13px;
    opacity: 0.8;
}

.cta-section {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    padding: 60px 40px;
    border-radius: 8px;
    text-align: center;
    margin: 40px 0;
}

.cta-section h2 {
    font-size: 32px;
    margin-bottom: 15px;
}

.cta-section p {
    font-size: 18px;
    margin-bottom: 30px;
    opacity: 0.9;
}

.cta-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .quick-stats {
        padding: 20px 0;
    }

    .stats-container {
        gap: 15px;
    }

    .how-it-works {
        padding: 20px;
    }

    .steps-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .cta-section {
        padding: 40px 20px;
    }

    .cta-section h2 {
        font-size: 24px;
    }

    .cta-buttons {
        flex-direction: column;
    }

    .cta-buttons .btn {
        width: 100%;
    }

    .jobs-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>