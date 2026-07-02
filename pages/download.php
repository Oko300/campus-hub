<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Ensure user is logged in to download
requireLogin();

$type = sanitizeInput($_GET['type'] ?? '');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || empty($type)) {
    $_SESSION['error'] = 'Invalid download request.';
    redirect('/'); // Redirect to homepage or an error page
}

$file_path = null;
$original_filename = null;

try {
    if ($type === 'past_question') {
        $stmt = $pdo->prepare("SELECT file_path, title FROM past_questions WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch();

        if ($item) {
            $file_path = __DIR__ . '/../uploads/' . $item['file_path'];
            $original_filename = $item['title'] . '.' . pathinfo($item['file_path'], PATHINFO_EXTENSION);

            // Increment download count
            $update_stmt = $pdo->prepare("UPDATE past_questions SET download_count = download_count + 1 WHERE id = :id");
            $update_stmt->execute([':id' => $id]);
        }
    }
    // Add other downloadable types here if needed in the future
    // else if ($type === 'another_type') { ... }

} catch (PDOException $e) {
    error_log("Error handling download for type {$type}, ID {$id}: " . $e->getMessage());
    $_SESSION['error'] = 'An error occurred during download. Please try again.';
    redirect('/');
}

if ($file_path && file_exists($file_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $original_filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit;
} else {
    $_SESSION['error'] = 'File not found or access denied.';
    redirect('/past-questions'); // Redirect back to past questions page
}
?>