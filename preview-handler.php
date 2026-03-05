<?php
require_once 'includes/config.php';
requireLogin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid file ID');
}

$file_id = intval($_GET['id']);
$user_id = getUserId();

$conn = getDBConnection();

// Get file info and verify ownership
$stmt = $conn->prepare("SELECT stored_name, file_extension FROM files WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $file_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    die('File not found or access denied');
}

$file = $result->fetch_assoc();
$stmt->close();
$conn->close();

$file_path = UPLOAD_DIR . $file['stored_name'];

if (!file_exists($file_path)) {
    die('File not found on server');
}

// Only allow text files for preview
$allowed_extensions = ['txt', 'csv'];
if (!in_array(strtolower($file['file_extension']), $allowed_extensions)) {
    die('File type not supported for preview');
}

// Read and output file content
header('Content-Type: text/plain; charset=utf-8');
echo file_get_contents($file_path);
exit();