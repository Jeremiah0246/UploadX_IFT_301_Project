<?php
require_once 'includes/config.php';
redirectIfLoggedIn();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                
                $stmt_update = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt_update->bind_param("i", $user['id']);
                $stmt_update->execute();
                $stmt_update->close();
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Invalid username or password';
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
    <title>Login - UploadX</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-container">
            <a href="index.php" class="auth-link-back">← Back to Home</a>
            
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-logo">
                        <img src="assets/images/UploadX-logo.png" alt="UploadX" onerror="this.style.display='none';">
                    </div>
                    <h1 class="auth-title">Welcome Back</h1>
                    <p class="auth-subtitle">Login to access your files</p>
                </div>

                <?php if ($error): ?>
                    <div class="auth-error-message">
                        <span>⚠️</span>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="username" class="form-label">Username or Email</label>
                        <input type="text" id="username" name="username" class="form-input" 
                               placeholder="johndoe or john@example.com" required autofocus
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-toggle">
                            <input type="password" id="password" name="password" class="form-input" 
                                   placeholder="••••••••" required>
                            <button type="button" class="password-toggle-btn" onclick="togglePassword('password')">👁️</button>
                        </div>
                    </div>

                    <div style="text-align: right; margin-bottom: 1.5rem;">
                        <a href="forgot-password.php" style="font-size: 0.875rem; color: var(--primary-blue);">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
                </form>

                <div class="auth-footer">
                    Don't have an account? <a href="signup.php">Sign up here</a>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/auth.js"></script>
</body>
</html>
