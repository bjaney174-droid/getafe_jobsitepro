<?php
require_once '../config/config.php';
requireLogin();

$user_id = (int) getUserId();
$message = '';

// Get current user data
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result ? $result->fetch_assoc() : null;

if (!$user) {
    $user = [];
    $message = '<div class="alert alert-danger">User profile not found.</div>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = '<div class="alert alert-danger">Security error</div>';
    } else {
        $first_name = sanitize($_POST['first_name'] ?? '');
        $last_name = sanitize($_POST['last_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $location = sanitize($_POST['location'] ?? '');
        $bio = sanitize($_POST['bio'] ?? '');
        $skills = sanitize($_POST['skills'] ?? '');
        $company = sanitize($_POST['company'] ?? '');
        $company_website = sanitize($_POST['company_website'] ?? '');
        $company_info = sanitize($_POST['company_info'] ?? '');

        // Handle avatar upload
        $avatar = $user['avatar'] ?? '';

        if (isset($_FILES['avatar']) && ($_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if (($_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                $message = '<div class="alert alert-danger">Upload error code: ' . (int)$_FILES['avatar']['error'] . '</div>';
            } elseif (($_FILES['avatar']['size'] ?? 0) > MAX_AVATAR_SIZE) {
                $message = '<div class="alert alert-danger">Avatar size too large (max 2MB)</div>';
            } else {
                $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));

                if (!in_array($ext, ALLOWED_IMAGE_TYPES, true)) {
                    $message = '<div class="alert alert-danger">Invalid image format</div>';
                } else {
                    $avatar_name = 'avatar_' . $user_id . '_' . time() . '.' . $ext;

                    // Ensure upload directory exists
                    $upload_dir_fs = rtrim(AVATAR_PATH, '/\\') . DIRECTORY_SEPARATOR;
                    if (!is_dir($upload_dir_fs)) {
                        if (!mkdir($upload_dir_fs, 0755, true) && !is_dir($upload_dir_fs)) {
                            $message = '<div class="alert alert-danger">Could not create avatar upload folder</div>';
                        }
                    }

                    if (empty($message)) {
                        $target_fs = $upload_dir_fs . $avatar_name;

                        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_fs)) {
                            // Save web path in DB (relative to public/)
                            $avatar = 'uploads/avatars/' . $avatar_name;

                            // Delete old avatar file if it exists and is in avatars folder
                            if (!empty($user['avatar']) && strpos($user['avatar'], 'uploads/avatars/') === 0) {
                                $old_fs = dirname(__DIR__) . DIRECTORY_SEPARATOR
                                    . str_replace('/', DIRECTORY_SEPARATOR, ltrim($user['avatar'], '/'));
                                if (is_file($old_fs)) {
                                    @unlink($old_fs);
                                }
                            }
                        } else {
                            $message = '<div class="alert alert-danger">Failed to save uploaded file. Check folder permissions.</div>';
                        }
                    }
                }
            }
        }

        if (empty($message)) {
            $sql = "UPDATE users SET
                    first_name = '$first_name',
                    last_name = '$last_name',
                    phone = '$phone',
                    location = '$location',
                    avatar = '$avatar',
                    bio = '$bio',
                    skills = '$skills',
                    company = '$company',
                    company_website = '$company_website',
                    company_info = '$company_info'
                    WHERE id = $user_id";

            if ($conn->query($sql)) {
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                $_SESSION['avatar'] = $avatar;

                $message = '<div class="alert alert-success">✓ Profile updated successfully!</div>';

                $result = $conn->query("SELECT * FROM users WHERE id = $user_id");
                $user = $result ? $result->fetch_assoc() : $user;
            } else {
                $message = '<div class="alert alert-danger">Error updating profile</div>';
            }
        }
    }
}

$page_title = 'Edit Profile - ' . getSetting('site_name', 'Getafe Jobsite');
require_once '../includes/header.php';
?>

<div class="container">
    <?php require_once '../includes/navbar.php'; ?>

    <div class="edit-profile-container">
        <h1>Edit Profile</h1>

        <?php echo $message; ?>

        <form action="edit-profile.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div class="form-section">
                <h3>Profile Picture</h3>
                <div class="avatar-upload">
                    <img
                        src="<?php echo !empty($user['avatar']) ? BASE_URL . ltrim($user['avatar'], '/') : BASE_URL . 'assets/images/default-avatar.png'; ?>"
                        alt="Profile"
                        class="avatar-preview"
                        id="avatarPreview"
                    >
                    <div class="upload-input">
                        <input type="file" name="avatar" id="avatarInput" accept="image/*">
                        <label for="avatarInput" class="btn btn-secondary">Upload Photo</label>
                        <small>Max 2MB. Formats: JPG, PNG, GIF</small>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Personal Information</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" name="first_name" required value="<?php echo htmlspecialchars($user['first_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="form-group">
                        <label>Last Name *</label>
                        <input type="text" name="last_name" required value="<?php echo htmlspecialchars($user['last_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" value="<?php echo htmlspecialchars($user['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
            </div>

            <?php if (getUserType() === 'jobseeker'): ?>
                <div class="form-section">
                    <h3>Professional Information</h3>

                    <div class="form-group">
                        <label>Bio</label>
                        <textarea name="bio" rows="5" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Skills</label>
                        <input type="text" name="skills"
                               placeholder="Enter skills separated by commas (e.g., PHP, JavaScript, MySQL)"
                               value="<?php echo htmlspecialchars($user['skills'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <small>Separate multiple skills with commas</small>
                    </div>
                </div>
            <?php elseif (getUserType() === 'employer'): ?>
                <div class="form-section">
                    <h3>Company Information</h3>

                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="company" value="<?php echo htmlspecialchars($user['company'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="form-group">
                        <label>Company Website</label>
                        <input type="url" name="company_website" value="<?php echo htmlspecialchars($user['company_website'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="form-group">
                        <label>Company Description</label>
                        <textarea name="company_info" rows="5"
                                  placeholder="Tell potential employees about your company..."><?php echo htmlspecialchars($user['company_info'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-large">Save Changes</button>
                <a href="profile.php" class="btn btn-secondary btn-large">Cancel</a>
            </div>
        </form>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</div>

<script>
document.getElementById('avatarInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (event) {
            document.getElementById('avatarPreview').src = event.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>