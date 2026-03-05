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
$stmt = $conn->prepare("SELECT original_name, stored_name, file_extension, file_size, mime_type FROM files WHERE id = ? AND user_id = ?");
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

// Clear output buffer
if (ob_get_level()) {
    ob_end_clean();
}

// Set headers for download
header('Content-Description: File Transfer');
header('Content-Type: ' . $file['mime_type']);
header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));

// Read and output file
$handle = fopen($file_path, 'rb');
if ($handle === false) {
    die('Cannot open file');
}

while (!feof($handle)) {
    echo fread($handle, 8192);
    flush();
}

fclose($handle);
exit();
