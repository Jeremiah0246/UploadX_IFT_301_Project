<?php
require_once 'includes/config.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit();
}

$file = $_FILES['file'];
$file_size = $file['size'];
$file_tmp = $file['tmp_name'];
$original_name = basename($file['name']);
$file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

// Validate file size
if ($file_size > MAX_FILE_SIZE) {
    echo json_encode(['success' => false, 'message' => 'File size exceeds maximum allowed (' . formatBytes(MAX_FILE_SIZE) . ')']);
    exit();
}

// Determine file type
$file_type = '';
if (in_array($file_ext, ALLOWED_PHOTO_TYPES)) {
    $file_type = 'photo';
} elseif (in_array($file_ext, ALLOWED_DOCUMENT_TYPES)) {
    $file_type = 'document';
} else {
    echo json_encode(['success' => false, 'message' => 'File type not allowed']);
    exit();
}

// Create uploads directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Generate unique filename
$stored_name = uniqid() . '_' . time() . '.' . $file_ext;
$destination = UPLOAD_DIR . $stored_name;

// Get MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file_tmp);
finfo_close($finfo);

// Move uploaded file
if (move_uploaded_file($file_tmp, $destination)) {
    // Save to database
    $conn = getDBConnection();
    $user_id = getUserId();
    
    $stmt = $conn->prepare("INSERT INTO files (user_id, original_name, stored_name, file_extension, file_size, file_type, mime_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $original_name, $stored_name, $file_ext, $file_size, $file_type, $mime_type);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'File uploaded successfully!',
            'file' => [
                'id' => $stmt->insert_id,
                'name' => $original_name,
                'size' => $file_size,
                'type' => $file_type
            ]
        ]);
    } else {
        unlink($destination);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
}
