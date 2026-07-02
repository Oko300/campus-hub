<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin(); // Only admins can access this page

$pq_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$pq_id) {
    $_SESSION['error'] = 'Invalid past question ID.';
    redirect('/admin/past-questions');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') { // Or POST, depending on how you want to handle deletion
    try {
        // First, get the file path to delete the actual file
        $stmt = $pdo->prepare("SELECT file_path FROM past_questions WHERE id = :id");
        $stmt->execute([':id' => $pq_id]);
        $past_question = $stmt->fetch();

        if ($past_question) {
            $file_to_delete = __DIR__ . '/../../uploads/' . $past_question['file_path'];
            if (file_exists($file_to_delete)) {
                unlink($file_to_delete); // Delete the file from the server
            }

            // Then, delete the record from the database
            $stmt = $pdo->prepare("DELETE FROM past_questions WHERE id = :id");
            $stmt->execute([':id' => $pq_id]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = 'Past question deleted successfully!';
            } else {
                $_SESSION['error'] = 'Past question not found or could not be deleted from database.';
            }
        } else {
            $_SESSION['error'] = 'Past question not found.';
        }
    } catch (PDOException $e) {
        error_log("Error deleting past question: " . $e->getMessage());
        $_SESSION['error'] = 'Failed to delete past question. Please try again.';
    }
}

redirect('/admin/past-questions');
?>