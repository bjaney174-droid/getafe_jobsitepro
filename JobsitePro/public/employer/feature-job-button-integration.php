<?php
// FILE: public/employer/add-feature-button.php
// INTEGRATION CODE - Add this to your my-jobs.php button section

// This snippet shows how to add the "Feature Job" button to each job listing
// Add this in your job listing loop where you have the action buttons

// EXAMPLE BUTTON HTML (replace your existing Featured button with this):
?>

<!-- Feature Job Button - Replace or add to your my-jobs.php -->
<button class="btn btn-primary" onclick="openFeatureJobModal(<?php echo $job['id']; ?>)">
    ⭐ Feature Job (₱500)
</button>

<!-- Feature Job Modal - Add this to your my-jobs.php page -->
<div id="featureJobModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeFeatureModal()">&times;</span>
        <h2>⭐ Feature Your Job</h2>
        <p>Make your job stand out and get more applications</p>

        <div class="feature-info">
            <div class="info-box">
                <h4>📌 What is Featured?</h4>
                <p>Featured jobs appear at the top of search results and get highlighted. They receive significantly more visibility and applications.</p>
            </div>

            <div class="pricing-table">
                <h4>💰 Pricing Options</h4>
                <table>
                    <tr>
                        <th>Duration</th>
                        <th>Price</th>
                        <th>Visibility</th>
                    </tr>
                    <tr>
                        <td>7 Days</td>
                        <td><strong>₱500</strong></td>
                        <td>High</td>
                    </tr>
                    <tr>
                        <td>30 Days</td>
                        <td><strong>₱500</strong></td>
                        <td>Very High</td>
                    </tr>
                    <tr>
                        <td>60 Days</td>
                        <td><strong>₱1,000</strong></td>
                        <td>Excellent</td>
                    </tr>
                    <tr>
                        <td>90 Days</td>
                        <td><strong>₱1,500</strong></td>
                        <td>Maximum</td>
                    </tr>
                </table>
            </div>
        </div>

        <form method="POST" action="public/employer/submit-feature-request.php">
            <input type="hidden" name="job_id" id="modalJobId">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-group">
                <label>Select Duration *</label>
                <select name="days" required onchange="updatePrice()">
                    <option value="">-- Choose Duration --</option>
                    <option value="7" data-price="500">7 Days - ₱500</option>
                    <option value="30" data-price="500" selected>30 Days - ₱500</option>
                    <option value="60" data-price="1000">60 Days - ₱1,000</option>
                    <option value="90" data-price="1500">90 Days - ₱1,500</option>
                </select>
            </div>

            <div class="form-group">
                <label>Total Amount to Pay</label>
                <div class="amount-display">
                    <span class="currency">₱</span>
                    <span id="priceDisplay">500.00</span>
                </div>
            </div>

            <div class="info-box success-bg">
                <h5>✓ After Requesting:</h5>
                <ul>
                    <li>Admin will review your request</li>
                    <li>You'll receive payment instructions via email</li>
                    <li>Send payment and get approved within 1-2 hours</li>
                    <li>Your job will be featured immediately after approval</li>
                </ul>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success btn-large">✓ Request Featured Job</button>
                <button type="button" class="btn btn-secondary" onclick="closeFeatureModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openFeatureJobModal(jobId) {
    document.getElementById('modalJobId').value = jobId;
    document.getElementById('featureJobModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeFeatureModal() {
    document.getElementById('featureJobModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function updatePrice() {
    const select = document.querySelector('select[name="days"]');
    const price = select.options[select.selectedIndex].getAttribute('data-price');
    document.getElementById('priceDisplay').textContent = parseFloat(price).toLocaleString('en-PH', {minimumFractionDigits: 2});
}
</script>

<style>
/* Feature Modal Styles */
#featureJobModal .modal-content {
    max-width: 600px;
}

.feature-info {
    margin: 20px 0;
}

.info-box {
    background: #f0f9ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}

.info-box h4 {
    margin: 0 0 10px 0;
    color: #1e40af;
}

.info-box p {
    margin: 0;
    color: #1e3a8a;
    font-size: 13px;
    line-height: 1.6;
}

.info-box.success-bg {
    background: #f0fdf4;
    border-color: #bbf7d0;
}

.info-box.success-bg h5,
.info-box.success-bg ul {
    color: #15803d;
}

.pricing-table {
    margin: 20px 0;
}

.pricing-table h4 {
    margin: 0 0 10px 0;
}

.pricing-table table {
    width: 100%;
    border-collapse: collapse;
}

.pricing-table th {
    background: #f9fafb;
    padding: 10px;
    text-align: left;
    font-weight: 600;
    border-bottom: 2px solid #e5e7eb;
}

.pricing-table td {
    padding: 10px;
    border-bottom: 1px solid #e5e7eb;
}

.pricing-table strong {
    color: #10b981;
    font-size: 16px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
}

.amount-display {
    font-size: 32px;
    color: #10b981;
    font-weight: bold;
    display: flex;
    align-items: baseline;
    gap: 8px;
    padding: 15px;
    background: #f0fdf4;
    border-radius: 8px;
}

.currency {
    font-size: 24px;
    color: #6b7280;
}
</style>
