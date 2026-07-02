<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin(); // Only admins can access this page

$article_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$article_id) {
    $_SESSION['error'] = 'Invalid news article ID.';
    redirect('/admin/news');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') { // Or POST, depending on how you want to handle deletion
    try {
        $stmt = $pdo->prepare("DELETE FROM news WHERE id = :id");
        $stmt->execute([':id' => $article_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = 'News article deleted successfully!';
        } else {
            $_SESSION['error'] = 'News article not found or could not be deleted.';
        }
    } catch (PDOException $e) {
        error_log("Error deleting news article: " . $e->getMessage());
        $_SESSION['error'] = 'Failed to delete news article. Please try again.';
    }
}

redirect('/admin/news');
?>