<?php
require_once '../../config/config.php';
requireAdmin();

// Handle approve
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'approve') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $transaction_id = (int)($_POST['transaction_id'] ?? 0);
        $days = (int)($_POST['days'] ?? 30);
        $payment_method = sanitize($_POST['payment_method'] ?? 'cash');
        $notes = sanitize($_POST['notes'] ?? '');
        
        // Get transaction
        $trans = $conn->query("SELECT * FROM transactions WHERE id = $transaction_id AND status = 'pending'");
        $transaction = $trans->fetch_assoc();
        
        if ($transaction) {
            $job_id = (int)$transaction['job_id'];
            $featured_until = date('Y-m-d H:i:s', strtotime("+$days days"));
            
            // Update job
            $conn->query("UPDATE jobs SET is_featured = 1, featured_until = '$featured_until', featured_paid = 1 WHERE id = $job_id");
            
            // Update transaction
            $notes_escaped = $conn->real_escape_string($notes);
            $conn->query("UPDATE transactions SET status = 'completed', payment_method = '$payment_method', notes = '$notes_escaped', processed_at = NOW() WHERE id = $transaction_id");
            
            $_SESSION['success'] = "Featured job approved for $days days ✓";
            log_action('approve_featured', "Approved featured job ID: $job_id for $days days");
        }
    }
    header("Location: manage-featured-jobs.php");
    exit();
}

// Handle reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reject') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $transaction_id = (int)($_POST['transaction_id'] ?? 0);
        $reason = sanitize($_POST['reason'] ?? 'No reason provided');
        
        $reason_escaped = $conn->real_escape_string($reason);
        $conn->query("UPDATE transactions SET status = 'failed', notes = '$reason_escaped' WHERE id = $transaction_id");
        $_SESSION['success'] = "Featured request rejected ✗";
    }
    header("Location: manage-featured-jobs.php");
    exit();
}

// Handle renew featured
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'renew') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $job_id = (int)($_POST['job_id'] ?? 0);
        $days = (int)($_POST['days'] ?? 30);
        
        $featured_until = date('Y-m-d H:i:s', strtotime("+$days days"));
        $conn->query("UPDATE jobs SET featured_until = '$featured_until' WHERE id = $job_id");
        
        $_SESSION['success'] = "Featured period renewed for $days more days ✓";
        log_action('renew_featured', "Renewed featured job ID: $job_id");
    }
    header("Location: manage-featured-jobs.php");
    exit();
}

// Handle remove featured
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'remove') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $job_id = (int)($_POST['job_id'] ?? 0);
        $conn->query("UPDATE jobs SET is_featured = 0, featured_until = NULL WHERE id = $job_id");
        $_SESSION['success'] = "Featured status removed ✗";
    }
    header("Location: manage-featured-jobs.php");
    exit();
}

// Get stats
$stats_result = $conn->query("SELECT 
    (SELECT COUNT(*) FROM transactions WHERE transaction_type = 'featured_job' AND status = 'pending') as pending_count,
    (SELECT COUNT(*) FROM jobs WHERE is_featured = 1) as active_featured,
    (SELECT COUNT(*) FROM transactions WHERE transaction_type = 'featured_job' AND status = 'completed') as total_approved,
    (SELECT SUM(amount) FROM transactions WHERE transaction_type = 'featured_job' AND status = 'completed') as total_revenue
");
$stats = $stats_result->fetch_assoc();

// Get pending requests
$pending = $conn->query("SELECT t.*, u.first_name, u.last_name, u.email, u.company, u.phone, j.title, j.id as job_id
                        FROM transactions t
                        JOIN users u ON t.user_id = u.id
                        JOIN jobs j ON t.job_id = j.id
                        WHERE t.transaction_type = 'featured_job' AND t.status = 'pending'
                        ORDER BY t.created_at DESC");

// Get approved featured jobs
$approved = $conn->query("SELECT j.*, u.first_name, u.last_name, u.email, u.company, 
                         (SELECT COUNT(*) FROM applications WHERE job_id = j.id) as applications
                         FROM jobs j
                         JOIN users u ON j.posted_by = u.id
                         WHERE j.is_featured = 1
                         ORDER BY j.featured_until DESC");

// Get completed transactions
$completed = $conn->query("SELECT t.*, u.first_name, u.last_name, u.company, j.title
                          FROM transactions t
                          JOIN users u ON t.user_id = u.id
                          JOIN jobs j ON t.job_id = j.id
                          WHERE t.transaction_type = 'featured_job' AND t.status = 'completed'
                          ORDER BY t.processed_at DESC LIMIT 10");

$page_title = 'Manage Featured Jobs - Admin';
require_once '../../includes/header.php';
?>

<div class="container">
    <?php require_once '../../includes/navbar.php'; ?>
    
    <div class="admin-wrap">
        <div class="admin-head">
            <h1>⭐ Manage Featured Jobs</h1>
            <a href="dashboard.php" class="btn btn-secondary">← Dashboard</a>
        </div>

        <?php if ($_SESSION['success'] ?? false): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="featured-stats">
            <div class="stat-card">
                <p class="stat-label">🕐 Pending Requests</p>
                <p class="stat-value"><?php echo (int)($stats['pending_count'] ?? 0); ?></p>
            </div>
            <div class="stat-card">
                <p class="stat-label">⭐ Currently Featured</p>
                <p class="stat-value"><?php echo (int)($stats['active_featured'] ?? 0); ?></p>
            </div>
            <div class="stat-card">
                <p class="stat-label">✓ Approved (Total)</p>
                <p class="stat-value"><?php echo (int)($stats['total_approved'] ?? 0); ?></p>
            </div>
            <div class="stat-card">
                <p class="stat-label">💰 Total Revenue</p>
                <p class="stat-value">₱<?php echo number_format((float)($stats['total_revenue'] ?? 0), 2); ?></p>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="admin-tabs">
            <button class="tab-btn active" onclick="switchTab('pending')">🕐 Pending Requests</button>
            <button class="tab-btn" onclick="switchTab('active')">⭐ Active Featured</button>
            <button class="tab-btn" onclick="switchTab('history')">📋 Transaction History</button>
        </div>

        <!-- Pending Requests Tab -->
        <div id="pending" class="tab-content active">
            <div class="admin-card">
                <h2>Pending Feature Requests</h2>
                <?php if ($pending && $pending->num_rows > 0): ?>
                    <div class="table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Employer</th>
                                    <th>Contact</th>
                                    <th>Job Title</th>
                                    <th>Amount</th>
                                    <th>Requested</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $pending->fetch_assoc()): ?>
                                    <tr class="pending-row">
                                        <td>
                                            <strong><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></strong><br>
                                            <small class="muted"><?php echo htmlspecialchars($row['company']); ?></small>
                                        </td>
                                        <td>
                                            <small>📧 <?php echo htmlspecialchars($row['email']); ?><br>
                                            📞 <?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><strong class="text-primary">₱<?php echo number_format($row['amount'], 2); ?></strong></td>
                                        <td><small><?php echo timeAgo($row['created_at']); ?></small></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-info" onclick="openApproveModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['title']); ?>')">
                                                    ✓ Approve
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="openRejectModal(<?php echo $row['id']; ?>)">
                                                    ✗ Reject
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <p>✓ No pending requests</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Active Featured Jobs Tab -->
        <div id="active" class="tab-content" style="display: none;">
            <div class="admin-card">
                <h2>Currently Featured Jobs</h2>
                <?php if ($approved && $approved->num_rows > 0): ?>
                    <div class="table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Company</th>
                                    <th>Featured Until</th>
                                    <th>Applications</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $approved->fetch_assoc()): 
                                    $is_expired = strtotime($row['featured_until']) < time();
                                    $days_left = ceil((strtotime($row['featured_until']) - time()) / (60 * 60 * 24));
                                ?>
                                    <tr class="<?php echo $is_expired ? 'expired-row' : 'active-row'; ?>">
                                        <td>
                                            <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
                                            <small class="muted">Contact: <?php echo htmlspecialchars($row['email']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['company']); ?></td>
                                        <td>
                                            <strong><?php echo date('M d, Y', strtotime($row['featured_until'])); ?></strong><br>
                                            <small class="muted"><?php echo $is_expired ? '❌ Expired' : '⏳ ' . $days_left . ' days left'; ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-info"><?php echo (int)$row['applications']; ?> apps</span>
                                        </td>
                                        <td>
                                            <?php if ($is_expired): ?>
                                                <span class="badge badge-danger">Expired</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-warning" onclick="openRenewModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['title']); ?>')">
                                                    🔄 Renew
                                                </button>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                                    <input type="hidden" name="action" value="remove">
                                                    <input type="hidden" name="job_id" value="<?php echo $row['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remove featured status?')">
                                                        ✗ Remove
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <p>No featured jobs currently</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Transaction History Tab -->
        <div id="history" class="tab-content" style="display: none;">
            <div class="admin-card">
                <h2>Transaction History</h2>
                <?php if ($completed && $completed->num_rows > 0): ?>
                    <div class="table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Employer</th>
                                    <th>Job Title</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Approved Date</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $completed->fetch_assoc()): ?>
                                    <tr class="completed-row">
                                        <td>
                                            <strong><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></strong><br>
                                            <small class="muted"><?php echo htmlspecialchars($row['company']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td><strong>₱<?php echo number_format($row['amount'], 2); ?></strong></td>
                                        <td>
                                            <span class="badge badge-success"><?php echo ucfirst($row['payment_method'] ?? 'N/A'); ?></span>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($row['processed_at'])); ?></td>
                                        <td>
                                            <?php if (!empty($row['notes'])): ?>
                                                <small><?php echo htmlspecialchars(substr($row['notes'], 0, 50)); ?>...</small>
                                            <?php else: ?>
                                                <small class="muted">-</small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <p>No transaction history</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal('approveModal')">&times;</span>
            <h2>✓ Approve Featured Request</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="action" value="approve">
                <input type="hidden" name="transaction_id" id="approveTransactionId">
                
                <div class="form-group">
                    <label><strong id="approveJobTitle"></strong></label>
                </div>
                
                <div class="form-group">
                    <label>Duration (days) *</label>
                    <select name="days" required>
                        <option value="7">7 days - ₱500</option>
                        <option value="30" selected>30 days - ₱500</option>
                        <option value="60">60 days - ₱1000</option>
                        <option value="90">90 days - ₱1500</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Payment Method *</label>
                    <select name="payment_method" required>
                        <option value="">Select payment method</option>
                        <option value="cash">💵 Cash</option>
                        <option value="bank_transfer">🏦 Bank Transfer</option>
                        <option value="gcash">📱 GCash</option>
                        <option value="paypal">🌐 PayPal</option>
                        <option value="check">📃 Check</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Notes / Reference Number</label>
                    <textarea name="notes" rows="3" placeholder="Reference number, receipt ID, or payment notes..."></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-success btn-large">✓ Approve</button>
                    <button type="button" class="btn btn-secondary btn-large" onclick="closeModal('approveModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal('rejectModal')">&times;</span>
            <h2>✗ Reject Featured Request</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="transaction_id" id="rejectTransactionId">
                
                <div class="form-group">
                    <label>Reason for Rejection *</label>
                    <textarea name="reason" rows="4" placeholder="Explain why this request is being rejected..." required></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-danger btn-large">✗ Reject</button>
                    <button type="button" class="btn btn-secondary btn-large" onclick="closeModal('rejectModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Renew Modal -->
    <div id="renewModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal('renewModal')">&times;</span>
            <h2>🔄 Renew Featured Period</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="action" value="renew">
                <input type="hidden" name="job_id" id="renewJobId">
                
                <div class="form-group">
                    <label><strong id="renewJobTitle"></strong></label>
                </div>
                
                <div class="form-group">
                    <label>Additional Days *</label>
                    <select name="days" required>
                        <option value="7">7 days - ₱500</option>
                        <option value="30" selected>30 days - ₱500</option>
                        <option value="60">60 days - ₱1000</option>
                        <option value="90">90 days - ₱1500</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-warning btn-large">🔄 Renew</button>
                    <button type="button" class="btn btn-secondary btn-large" onclick="closeModal('renewModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <?php require_once '../../includes/footer.php'; ?>
</div>

<style>
.featured-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}

.stat-card {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);
}

.stat-label {
    margin: 0;
    font-size: 13px;
    opacity: 0.9;
}

.stat-value {
    margin: 8px 0 0;
    font-size: 28px;
    font-weight: bold;
}

.admin-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    border-bottom: 2px solid #e5e7eb;
}

.tab-btn {
    padding: 12px 20px;
    border: none;
    background: transparent;
    cursor: pointer;
    font-weight: 500;
    color: #6b7280;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

.tab-btn.active {
    color: #0d6efd;
    border-bottom-color: #0d6efd;
}

.tab-btn:hover {
    color: #0d6efd;
}

.tab-content {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.admin-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.table-wrap {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th, .admin-table td {
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
    text-align: left;
}

.admin-table th {
    background: #f9fafb;
    font-weight: 600;
}

.pending-row {
    background: #fef3c7;
}

.active-row {
    background: #f0fdf4;
}

.expired-row {
    background: #fef2f2;
    opacity: 0.7;
}

.completed-row {
    background: #f0f9ff;
}

.action-buttons {
    display: flex;
    gap: 6px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.btn-info {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
}

.btn-info:hover {
    transform: translateY(-2px);
}

.btn-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.btn-warning:hover {
    transform: translateY(-2px);
}

.no-data {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
}

.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: #fefefe;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 20px;
}

.close:hover {
    color: #000;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #1f2937;
}

.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-family: inherit;
    font-size: 14px;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn-large {
    flex: 1;
}

.muted {
    color: #6b7280;
}

.text-primary {
    color: #0d6efd;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.badge-info {
    background: #dbeafe;
    color: #1e40af;
}

.badge-success {
    background: #dcfce7;
    color: #15803d;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

@media (max-width: 768px) {
    .featured-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .admin-tabs {
        flex-direction: column;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<script>
function switchTab(tabName) {
    // Hide all tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.style.display = 'none');
    
    // Remove active class from all buttons
    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    // Show selected tab
    document.getElementById(tabName).style.display = 'block';
    event.target.classList.add('active');
}

function openApproveModal(transactionId, jobTitle) {
    document.getElementById('approveTransactionId').value = transactionId;
    document.getElementById('approveJobTitle').textContent = jobTitle;
    document.getElementById('approveModal').style.display = 'block';
}

function openRejectModal(transactionId) {
    document.getElementById('rejectTransactionId').value = transactionId;
    document.getElementById('rejectModal').style.display = 'block';
}

function openRenewModal(jobId, jobTitle) {
    document.getElementById('renewJobId').value = jobId;
    document.getElementById('renewJobTitle').textContent = jobTitle;
    document.getElementById('renewModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>
</body>
</html>
