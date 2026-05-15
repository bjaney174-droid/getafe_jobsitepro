
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (isLoggedIn()) {
    header("Location: home.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Security token expired. Please try again.';
    } else {
        $first_name = sanitize($_POST['first_name'] ?? '');
        $last_name = sanitize($_POST['last_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $user_type = sanitize($_POST['user_type'] ?? '');

        // Validation
        if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($user_type)) {
            $error = 'All fields are required';
        } else if (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters';
        } else if ($password !== $confirm_password) {
            $error = 'Passwords do not match';
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format';
        } else if (!in_array($user_type, ['jobseeker', 'employer'])) {
            $error = 'Invalid user type';
        } else {
            // Check if email exists
            $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
            if ($check && $check->num_rows > 0) {
                $error = 'Email already registered';
            } else {
                // Hash password and insert
                $hashed_password = hash_password($password);
                $sql = "INSERT INTO users (first_name, last_name, email, password, user_type) 
                        VALUES ('$first_name', '$last_name', '$email', '$hashed_password', '$user_type')";
                
                if ($conn->query($sql)) {
                    $success = 'Registration successful! Redirecting to login...';
                    log_action('new_registration', "User registered: $email as $user_type");
                    header("refresh:2;url=login.php");
                } else {
                    $error = 'Error registering: ' . $conn->error;
                }
            }
        }
    }
}

$page_title = 'Register - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <div class="auth-container">
        <div class="auth-box">
            <h2>Create Account</h2>
            <p class="text-center mb-3">Join <?php echo getSetting('site_name', 'Getafe Jobsite'); ?> today</p>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" class="form">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" name="first_name" required 
                               value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>"
                               placeholder="John">
                    </div>

                    <div class="form-group">
                        <label>Last Name *</label>
                        <input type="text" name="last_name" required
                               value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>"
                               placeholder="Doe">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           placeholder="john@example.com">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" name="password" required
                               placeholder="Min. 8 characters">
                        <small class="text-muted">At least 8 characters</small>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password *</label>
                        <input type="password" name="confirm_password" required
                               placeholder="Confirm password">
                    </div>
                </div>

                <div class="form-group">
                    <label>I am a: *</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="user_type" value="jobseeker" required> 
                            Job Seeker
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="user_type" value="employer"> 
                            Employer
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>

            <p class="text-center mt-3">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>