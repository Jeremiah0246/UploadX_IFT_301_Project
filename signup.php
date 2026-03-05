<?php
require_once 'includes/config.php';

// Redirect if already logged in
redirectIfLoggedIn();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username)) {
        $errors[] = 'Username is required';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($errors)) {
        $conn = getDBConnection();
        
        // Check if username or email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = 'Username or email already exists';
        } else {
            // Hash password and create user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $success = true;
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
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
    <title>Sign Up - UploadX</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-container">
            <a href="index.php" class="auth-link-back">
                ← Back to Home
            </a>
            
            <div class="auth-card">
                <?php if ($success): ?>
                    <div class="auth-success-page">
                        <div class="success-icon">✓</div>
                        <h2 class="success-title">Account Created!</h2>
                        <p class="success-text">
                            Your account has been successfully created.<br>
                            You can now login and start managing your files.
                        </p>
                        <a href="login.php" class="btn btn-primary btn-lg">Go to Login</a>
                    </div>
                <?php else: ?>
                    <div class="auth-header">
                        <div class="auth-logo">
                            <img src="assets/images/UploadX-logo.png" alt="UploadX" onerror="this.style.display='none';">
                        </div>
                        <h1 class="auth-title">Create Account</h1>
                        <p class="auth-subtitle">Join UploadX and start managing your files</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="auth-error-message">
                            <span>⚠️</span>
                            <div>
                                <?php foreach ($errors as $error): ?>
                                    <div><?php echo htmlspecialchars($error); ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="auth-form" id="signupForm">
                        <div class="form-group">
                            <label for="username" class="form-label">Username</label>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                class="form-input" 
                                placeholder="johndoe"
                                value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                required
                                autofocus
                            >
                            <div class="form-hint">Letters, numbers, and underscores only</div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-input" 
                                placeholder="john@example.com"
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="password-toggle">
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    class="form-input" 
                                    placeholder="••••••••"
                                    required
                                >
                                <button type="button" class="password-toggle-btn" onclick="togglePassword('password')">
                                    👁️
                                </button>
                            </div>
                            
                            <div class="password-strength">
                                <div class="password-strength-bar" id="passwordStrengthBar"></div>
                            </div>
                            
                            <div class="password-requirements">
                                <strong>Password must contain:</strong>
                                <ul id="passwordRequirements">
                                    <li id="req-length">At least 8 characters</li>
                                    <li id="req-uppercase">One uppercase letter</li>
                                    <li id="req-lowercase">One lowercase letter</li>
                                    <li id="req-number">One number</li>
                                </ul>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <div class="password-toggle">
                                <input 
                                    type="password" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    class="form-input" 
                                    placeholder="••••••••"
                                    required
                                >
                                <button type="button" class="password-toggle-btn" onclick="togglePassword('confirm_password')">
                                    👁️
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Create Account
                        </button>
                    </form>

                    <div class="auth-footer">
                        Already have an account? <a href="login.php">Login here</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="assets/js/auth.js"></script>
</body>
</html>
