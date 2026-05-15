<?php
require_once '../config/config.php';

if (isLoggedIn()) {
    header("Location: home.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Security token expired. Please try again.';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Email and password required';
        } else {
            $sql = "SELECT * FROM users WHERE email = '$email' AND status = 'active'";
            $result = $conn->query($sql);

            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (verify_password($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['user_type'] = $user['user_type'];
                    $_SESSION['avatar'] = $user['avatar'];

                    log_action('login', "User logged in: $email");

                    $redirect = $_SESSION['redirect_after_login'] ?? 'home.php';
                    unset($_SESSION['redirect_after_login']);
                    header("Location: $redirect");
                    exit();
                } else {
                    $error = 'Invalid email or password';
                }
            } else {
                $error = 'Account not found or inactive';
            }
        }
    }
}

$page_title = 'Login - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <div class="auth-container">
        <div class="auth-box">
            <h2>Login</h2>
            <p class="text-center mb-3">Welcome back to <?php echo getSetting('site_name', 'Getafe Jobsite'); ?></p>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="form">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" required
                           value="<?php echo $_POST['email'] ?? ''; ?>"
                           placeholder="your@email.com">
                </div>

                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required
                           placeholder="Enter your password">
                </div>

                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Remember me</label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <p class="text-center mt-3">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>