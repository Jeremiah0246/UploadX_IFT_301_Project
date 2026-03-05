<?php
require_once 'includes/config.php';
requireLogin();

$conn = getDBConnection();
$user_id = getUserId();

// Get user data
$stmt = $conn->prepare("SELECT username, email, created_at, last_login FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$success = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $new_username = sanitize($_POST['username'] ?? '');
        $new_email = sanitize($_POST['email'] ?? '');
        
        if (empty($new_username) || empty($new_email)) {
            $error = 'Username and email are required';
        } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format';
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
            
            if ($stmt->execute()) {
                $_SESSION['username'] = $new_username;
                $_SESSION['email'] = $new_email;
                $success = 'Profile updated successfully!';
                $user['username'] = $new_username;
                $user['email'] = $new_email;
            } else {
                $error = 'Failed to update profile';
            }
            $stmt->close();
        }
    }
    
    if ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($current_password) || empty($new_password)) {
            $error = 'All password fields are required';
        } elseif (strlen($new_password) < 8) {
            $error = 'New password must be at least 8 characters';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match';
        } else {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();
            $stmt->close();
            
            if (password_verify($current_password, $user_data['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $user_id);
                
                if ($stmt->execute()) {
                    $success = 'Password changed successfully!';
                } else {
                    $error = 'Failed to change password';
                }
                $stmt->close();
            } else {
                $error = 'Current password is incorrect';
            }
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - UploadX</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-container">
                <a href="dashboard.php" class="navbar-logo">
                    <img src="assets/images/UploadX-logo.png" alt="UploadX" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                    <span style="display: none;">UploadX</span>
                </a>
                <ul class="navbar-menu">
                    <li><a href="dashboard.php" class="navbar-link">Dashboard</a></li>
                    <li><a href="files.php" class="navbar-link">My Files</a></li>
                    <li><a href="logout.php" class="btn btn-secondary btn-sm">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="auth-page" style="min-height: calc(100vh - 80px);">
        <div class="container" style="max-width: 800px; padding: 2rem;">
            <?php if ($success): ?>
                <div class="auth-success-message" style="margin-bottom: 1.5rem;">
                    <span>✓</span>
                    <div><?php echo htmlspecialchars($success); ?></div>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="auth-error-message" style="margin-bottom: 1.5rem;">
                    <span>⚠️</span>
                    <div><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Account Information</h2>
                </div>
                
                <form method="POST" style="margin-bottom: 2rem;">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-input" 
                               value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-input" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>

                <div style="border-top: 1px solid var(--gray-200); padding-top: 2rem;">
                    <p style="color: var(--gray-600); font-size: 0.875rem; margin-bottom: 0.5rem;">
                        <strong>Member since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?>
                    </p>
                    <?php if ($user['last_login']): ?>
                    <p style="color: var(--gray-600); font-size: 0.875rem;">
                        <strong>Last login:</strong> <?php echo date('F j, Y g:i A', strtotime($user['last_login'])); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h2 class="card-title">Change Password</h2>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="action" value="change_password">
                    
                    <div class="form-group">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
