<?php
require_once 'includes/config.php';
requireLogin();

$conn = getDBConnection();
$user_id = getUserId();
$username = getUsername();

// Get photos
$stmt_photos = $conn->prepare("SELECT * FROM files WHERE user_id = ? AND file_type = 'photo' ORDER BY upload_date DESC");
$stmt_photos->bind_param("i", $user_id);
$stmt_photos->execute();
$photos = $stmt_photos->get_result();

// Get documents
$stmt_docs = $conn->prepare("SELECT * FROM files WHERE user_id = ? AND file_type = 'document' ORDER BY upload_date DESC");
$stmt_docs->bind_param("i", $user_id);
$stmt_docs->execute();
$documents = $stmt_docs->get_result();

// Get stats
$stmt_stats = $conn->prepare("
    SELECT 
        COUNT(*) as total_files,
        COALESCE(SUM(file_size), 0) as total_size,
        SUM(CASE WHEN file_type = 'photo' THEN 1 ELSE 0 END) as photo_count,
        SUM(CASE WHEN file_type = 'document' THEN 1 ELSE 0 END) as doc_count
    FROM files WHERE user_id = ?
");
$stmt_stats->bind_param("i", $user_id);
$stmt_stats->execute();
$stats = $stmt_stats->get_result()->fetch_assoc();

$stmt_photos->close();
$stmt_docs->close();
$stmt_stats->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Files - UploadX</title>
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
                    <li><a href="dashboard.php" class="navbar-link">Dashboard</a></li>
                    <li><a href="profile.php" class="navbar-link">Profile</a></li>
                    <li><a href="logout.php" class="btn btn-secondary btn-sm">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- File Manager -->
    <div class="file-manager-page">
        <!-- Header -->
        <div class="file-manager-header">
            <div class="container">
                <div class="file-manager-title-section">
                    <h1 class="file-manager-title">
                        📁 <?php echo htmlspecialchars($username); ?>'s Files
                    </h1>
                    <div class="file-manager-actions">
                        <button class="btn btn-secondary" id="selectBtn" onclick="toggleSelectMode()">
                            Select Files
                        </button>
                        <button class="btn btn-primary" id="downloadBtn" style="display: none;" onclick="downloadSelected()">
                            Download Selected
                        </button>
                        <button class="btn btn-danger" id="deleteBtn" style="display: none;" onclick="deleteSelected()">
                            Delete Selected
                        </button>
                        <button class="btn btn-ghost" id="cancelBtn" style="display: none;" onclick="cancelSelectMode()">
                            Cancel
                        </button>
                    </div>
                </div>

                <div class="file-manager-stats">
                    <div class="stat-item">
                        <span class="stat-label">Total Files</span>
                        <span class="stat-value"><?php echo number_format($stats['total_files']); ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Storage Used</span>
                        <span class="stat-value"><?php echo formatBytes($stats['total_size']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Section -->
        <div class="upload-section">
            <div class="container">
                <div class="upload-zone" id="uploadZone">
                    <div class="upload-icon">☁️</div>
                    <h3 class="upload-title">Drop files here or click to upload</h3>
                    <p class="upload-subtitle">Supports images and documents up to 50MB</p>
                    <input type="file" id="fileInput" multiple accept="<?php echo implode(',', array_merge(
                        array_map(function($ext) { return '.' . $ext; }, ALLOWED_PHOTO_TYPES),
                        array_map(function($ext) { return '.' . $ext; }, ALLOWED_DOCUMENT_TYPES)
                    )); ?>">
                    <button class="btn btn-primary btn-lg" onclick="document.getElementById('fileInput').click()">
                        Choose Files
                    </button>
                </div>

                <div class="upload-progress" id="uploadProgress">
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill" id="progressBar"></div>
                    </div>
                    <div class="progress-text" id="progressText">Uploading...</div>
                </div>
            </div>
        </div>

        <!-- File Tabs -->
        <div class="file-tabs">
            <div class="container">
                <ul class="file-tabs-list">
                    <li class="file-tab active" data-tab="photos">
                        📷 Photos
                        <span class="file-tab-count"><?php echo $stats['photo_count']; ?></span>
                    </li>
                    <li class="file-tab" data-tab="documents">
                        📄 Documents
                        <span class="file-tab-count"><?php echo $stats['doc_count']; ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- File Controls -->
        <div class="file-controls">
            <div class="container" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div class="file-search">
                    <span class="file-search-icon">🔍</span>
                    <input type="text" id="searchInput" placeholder="Search files...">
                </div>

                <div class="file-view-toggle">
                    <button class="view-toggle-btn active" data-view="grid" title="Grid View">
                        <span style="font-size: 1.25rem;">⊞</span>
                    </button>
                    <button class="view-toggle-btn" data-view="list" title="List View">
                        <span style="font-size: 1.25rem;">☰</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Photos Tab Content -->
        <div class="files-container" id="photosTab">
            <div class="container">
                <?php if ($photos->num_rows > 0): ?>
                    <div class="files-grid" id="photosGrid">
                        <?php while ($file = $photos->fetch_assoc()): ?>
                            <div class="file-card" data-id="<?php echo $file['id']; ?>" data-name="<?php echo htmlspecialchars($file['original_name']); ?>">
                                <input type="checkbox" class="file-card-checkbox" value="<?php echo $file['id']; ?>">
                                <div class="file-card-thumbnail" onclick="previewFile(<?php echo $file['id']; ?>, '<?php echo htmlspecialchars($file['stored_name']); ?>', '<?php echo htmlspecialchars($file['original_name']); ?>', '<?php echo $file['file_extension']; ?>')">
                                    <img src="uploads/<?php echo htmlspecialchars($file['stored_name']); ?>" alt="<?php echo htmlspecialchars($file['original_name']); ?>">
                                </div>
                                <div class="file-card-name" title="<?php echo htmlspecialchars($file['original_name']); ?>">
                                    <?php echo htmlspecialchars($file['original_name']); ?>
                                </div>
                                <div class="file-card-meta">
                                    <span><?php echo formatBytes($file['file_size']); ?></span>
                                    <span><?php echo date('M d, Y', strtotime($file['upload_date'])); ?></span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📷</div>
                        <h3 class="empty-state-title">No Photos Yet</h3>
                        <p class="empty-state-text">Upload your first photo to get started!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Documents Tab Content -->
        <div class="files-container" id="documentsTab" style="display: none;">
            <div class="container">
                <?php if ($documents->num_rows > 0): ?>
                    <div class="files-grid" id="documentsGrid">
                        <?php while ($file = $documents->fetch_assoc()): ?>
                            <div class="file-card" data-id="<?php echo $file['id']; ?>" data-name="<?php echo htmlspecialchars($file['original_name']); ?>">
                                <input type="checkbox" class="file-card-checkbox" value="<?php echo $file['id']; ?>">
                                <div class="file-card-thumbnail" onclick="downloadFile(<?php echo $file['id']; ?>)">
                                    <span style="font-size: 4rem;"><?php echo getFileIcon($file['file_extension']); ?></span>
                                </div>
                                <div class="file-card-name" title="<?php echo htmlspecialchars($file['original_name']); ?>">
                                    <?php echo htmlspecialchars($file['original_name']); ?>
                                </div>
                                <div class="file-card-meta">
                                    <span><?php echo formatBytes($file['file_size']); ?></span>
                                    <span><?php echo date('M d, Y', strtotime($file['upload_date'])); ?></span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📄</div>
                        <h3 class="empty-state-title">No Documents Yet</h3>
                        <p class="empty-state-text">Upload your first document to get started!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal" id="previewModal" style="display: none;">
        <div class="modal-content" id="modalContent">
            <button class="modal-close" onclick="closePreview()">×</button>
            <img id="previewImage" class="modal-image" style="display: none;">
        </div>
    </div>

    <script src="assets/js/files.js"></script>
</body>
</html>
