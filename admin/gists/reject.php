<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin(); // Only admins can access this page

$gist_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$gist_id) {
    $_SESSION['error'] = 'Invalid gist ID.';
    redirect('/admin/gists');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') { // Or POST, depending on how you want to handle rejection
    try {
        $stmt = $pdo->prepare("UPDATE gists SET status = 'rejected', updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        $stmt->execute([':id' => $gist_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = 'Gist rejected successfully!';
        } else {
            $_SESSION['error'] = 'Gist not found or could not be rejected.';
        }
    } catch (PDOException $e) {
        error_log("Error rejecting gist: " . $e->getMessage());
        $_SESSION['error'] = 'Failed to reject gist. Please try again.';
    }
}

redirect('/admin/gists');
?>