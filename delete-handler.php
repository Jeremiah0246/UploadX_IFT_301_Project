<?php
require_once 'includes/config.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$file_id = $data['file_id'] ?? null;

if (!$file_id || !is_numeric($file_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file ID']);
    exit();
}

$file_id = intval($file_id);
$user_id = getUserId();

$conn = getDBConnection();

// Get file info and verify ownership
$stmt = $conn->prepare("SELECT stored_name FROM files WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $file_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'File not found or access denied']);
    exit();
}

$file = $result->fetch_assoc();
$file_path = UPLOAD_DIR . $file['stored_name'];

// Delete from database
$stmt = $conn->prepare("DELETE FROM files WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $file_id, $user_id);

if ($stmt->execute()) {
    // Delete physical file
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    echo json_encode(['success' => true, 'message' => 'File deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete file']);
}

$stmt->close();
$conn->close();
