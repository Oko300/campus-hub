<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin(); // Only admins can access this page

$thread_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$thread_id) {
    $_SESSION['error'] = 'Invalid thread ID.';
    redirect('/admin/forum');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') { // Or POST, depending on how you want to handle deletion
    try {
        // Start a transaction to ensure both thread and replies are deleted or neither
        $pdo->beginTransaction();

        // Delete all replies associated with the thread
        $stmt_replies = $pdo->prepare("DELETE FROM forum_replies WHERE thread_id = :thread_id");
        $stmt_replies->execute([':thread_id' => $thread_id]);

        // Delete the thread itself
        $stmt_thread = $pdo->prepare("DELETE FROM forum_threads WHERE id = :id");
        $stmt_thread->execute([':id' => $thread_id]);

        if ($stmt_thread->rowCount() > 0) {
            $pdo->commit();
            $_SESSION['success'] = 'Thread and its replies deleted successfully!';
        } else {
            $pdo->rollBack();
            $_SESSION['error'] = 'Thread not found or could not be deleted.';
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Error deleting forum thread: " . $e->getMessage());
        $_SESSION['error'] = 'Failed to delete thread. Please try again.';
    }
}

redirect('/admin/forum');
?>