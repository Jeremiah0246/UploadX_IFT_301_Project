<?php
require_once 'includes/config.php';
requireLogin();

$conn = getDBConnection();
$user_id = getUserId();
$username = getUsername();

// Get user stats
$stmt = $conn->prepare("SELECT COUNT(*) as total_files, COALESCE(SUM(file_size), 0) as total_size FROM files WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - UploadX</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-container">
                <a href="dashboard.php" class="navbar-logo">
                    <img src="assets/images/UploadX-logo.png" alt="UploadX" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                    <span style="display: none;">UploadX</span>
                </a>
                <ul class="navbar-menu">
                    <li><a href="profile.php" class="navbar-link">Profile</a></li>
                    <li><a href="logout.php" class="btn btn-secondary btn-sm">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard -->
    <div class="dashboard-page">
        <div class="dashboard-container">
            <!-- Welcome Section - Left Aligned, No Box -->
            <div class="dashboard-welcome">
                <div class="welcome-header">
                    <div class="welcome-emoji">👋</div>
                    <h1 class="welcome-title">Welcome back, <?php echo htmlspecialchars($username); ?>!</h1>
                </div>
                <p class="welcome-subtitle">What would you like to do today?</p>
            </div>

            <!-- Action Cards - Centered, Side by Side -->
            <div class="action-cards">
                <div class="action-card" onclick="window.location.href='files.php'">
                    <div class="action-card-icon">📤</div>
                    <h3 class="action-card-title">Upload Files</h3>
                    <p class="action-card-desc">Add new files to your collection</p>
                </div>
                
                <div class="action-card" onclick="window.location.href='files.php#download'">
                    <div class="action-card-icon">📥</div>
                    <h3 class="action-card-title">Download</h3>
                    <p class="action-card-desc">Get your files anytime, anywhere</p>
                </div>
                
                <div class="action-card" onclick="window.location.href='files.php#delete'">
                    <div class="action-card-icon">🗑️</div>
                    <h3 class="action-card-title">Manage Files</h3>
                    <p class="action-card-desc">Organize and delete files</p>
                </div>
            </div>

            <!-- Statistics - Side by Side, No Box -->
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-label">Total Files</div>
                    <div class="stat-value"><?php echo number_format($stats['total_files']); ?></div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">Storage Used</div>
                    <div class="stat-value"><?php echo formatBytes($stats['total_size']); ?></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
