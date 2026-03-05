<?php
require_once 'includes/config.php';
redirectIfLoggedIn();

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
            $stmt->bind_param("ssi", $token, $expiry, $user['id']);
            $stmt->execute();
            
            $reset_link = APP_URL . "/reset-password.php?token=" . $token;
            
            // In production, send actual email using PHPMailer
            // For now, we'll show success
            $success = true;
        } else {
            // Don't reveal if email exists (security)
            $success = true;
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - UploadX</title>
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
                        <div class="success-icon">📧</div>
                        <h2 class="success-title">Check Your Email</h2>
                        <p class="success-text">
                            If an account exists with that email, we've sent password reset instructions.
                            <br><br>
                            Check your inbox and follow the link to reset your password.
                        </p>
                        <a href="login.php" class="btn btn-primary btn-lg">Back to Login</a>
                    </div>
                <?php else: ?>
                    <div class="auth-header">
                        <div class="auth-logo">
                            <img src="assets/images/UploadX-logo.png" alt="UploadX" onerror="this.style.display='none';">
                        </div>
                        <h1 class="auth-title">Forgot Password?</h1>
                        <p class="auth-subtitle">Enter your email to receive reset instructions</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="auth-error-message">
                            <span>⚠️</span>
                            <div><?php echo htmlspecialchars($error); ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="auth-form">
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-input" 
                                   placeholder="john@example.com" required autofocus
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            <div class="form-hint">We'll send password reset instructions to this email</div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Send Reset Link
                        </button>
                    </form>

                    <div class="auth-footer">
                        Remember your password? <a href="login.php">Login here</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="assets/js/auth.js"></script>
</body>
</html>
