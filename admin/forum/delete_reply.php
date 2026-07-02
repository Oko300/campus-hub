<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin(); // Only admins can access this page

$reply_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$reply_id) {
    $_SESSION['error'] = 'Invalid reply ID.';
    redirect('/admin/forum');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') { // Or POST, depending on how you want to handle deletion
    try {
        $stmt = $pdo->prepare("DELETE FROM forum_replies WHERE id = :id");
        $stmt->execute([':id' => $reply_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = 'Reply deleted successfully!';
        } else {
            $_SESSION['error'] = 'Reply not found or could not be deleted.';
        }
    } catch (PDOException $e) {
        error_log("Error deleting forum reply: " . $e->getMessage());
        $_SESSION['error'] = 'Failed to delete reply. Please try again.';
    }
}

redirect('/admin/forum');
?>