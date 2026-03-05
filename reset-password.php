<?php
require_once 'includes/config.php';
redirectIfLoggedIn();

$token = $_GET['token'] ?? '';
$error = '';
$success = false;
$valid_token = false;

if (empty($token)) {
    $error = 'Invalid or missing reset token';
} else {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $valid_token = true;
        $user = $result->fetch_assoc();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if (empty($password)) {
                $error = 'Password is required';
            } elseif (strlen($password) < 8) {
                $error = 'Password must be at least 8 characters';
            } elseif ($password !== $confirm_password) {
                $error = 'Passwords do not match';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $user['id']);
                
                if ($stmt->execute()) {
                    $success = true;
                } else {
                    $error = 'Failed to reset password. Please try again.';
                }
            }
        }
    } else {
        $error = 'Invalid or expired reset token';
    }
    
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - UploadX</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-container">
            <a href="login.php" class="auth-link-back">← Back to Login</a>
            
            <div class="auth-card">
                <?php if ($success): ?>
                    <div class="auth-success-page">
                        <div class="success-icon">✓</div>
                        <h2 class="success-title">Password Reset!</h2>
                        <p class="success-text">
                            Your password has been successfully reset.<br>
                            You can now login with your new password.
                        </p>
                        <a href="login.php" class="btn btn-primary btn-lg">Go to Login</a>
                    </div>
                <?php elseif ($valid_token): ?>
                    <div class="auth-header">
                        <div class="auth-logo">
                            <img src="assets/images/UploadX-logo.png" alt="UploadX" onerror="this.style.display='none';">
                        </div>
                        <h1 class="auth-title">Reset Password</h1>
                        <p class="auth-subtitle">Enter your new password</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="auth-error-message">
                            <span>⚠️</span>
                            <div><?php echo htmlspecialchars($error); ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="auth-form">
                        <div class="form-group">
                            <label for="password" class="form-label">New Password</label>
                            <div class="password-toggle">
                                <input type="password" id="password" name="password" class="form-input" 
                                       placeholder="••••••••" required autofocus>
                                <button type="button" class="password-toggle-btn" onclick="togglePassword('password')">👁️</button>
                            </div>
                            <div class="password-strength">
                                <div class="password-strength-bar" id="passwordStrengthBar"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <div class="password-toggle">
                                <input type="password" id="confirm_password" name="confirm_password" class="form-input" 
                                       placeholder="••••••••" required>
                                <button type="button" class="password-toggle-btn" onclick="togglePassword('confirm_password')">👁️</button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Reset Password
                        </button>
                    </form>
                <?php else: ?>
                    <div class="auth-error-message">
                        <span>⚠️</span>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    </div>
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <a href="forgot-password.php" class="btn btn-primary">Request New Reset Link</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="assets/js/auth.js"></script>
</body>
</html>
