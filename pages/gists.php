<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Handle gist submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_gist'])) {
    requireLogin(); // Only logged-in users can submit gists

    $gist_content = sanitizeInput($_POST['gist_content'] ?? '');

    if (empty($gist_content)) {
        $_SESSION['error'] = 'Gist content cannot be empty.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO gists (user_id, content, status) VALUES (:user_id, :content, 'pending')");
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':content' => $gist_content
            ]);
            $_SESSION['success'] = 'Your gist has been submitted for moderation and will be visible once approved.';
            redirect('/gists');
        } catch (PDOException $e) {
            error_log("Error submitting gist: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to submit gist. Please try again.';
        }
    }
}

// Fetch approved gists
try {
    $stmt = $pdo->query("SELECT g.*, u.username as author_name FROM gists g JOIN users u ON g.user_id = u.id WHERE g.status = 'approved' ORDER BY g.created_at DESC");
    $approved_gists = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching approved gists: " . $e->getMessage());
    $_SESSION['error'] = 'Could not load gists.';
    $approved_gists = [];
}

?>

<h1 class="mb-4">Campus Gists</h1>

<?php flash('success'); ?>
<?php flash('error'); ?>

<div class="row">
    <div class="col-md-8">
        <h2 class="mb-3">Latest Approved Gists</h2>
        <?php if (empty($approved_gists)): ?>
            <p class="text-center">No approved gists available at the moment.</p>
        <?php else: ?>
            <?php foreach ($approved_gists as $gist): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($gist['content'])); ?></p>
                        <p class="card-text"><small class="text-muted">Submitted by <?php echo htmlspecialchars($gist['author_name']); ?> on <?php echo date('M d, Y H:i', strtotime($gist['created_at'])); ?></small></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="col-md-4">
        <?php if (isLoggedIn()): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Submit Your Gist</h5>
                </div>
                <div class="card-body">
                    <form action="/gists" method="POST">
                        <div class="mb-3">
                            <label for="gist_content" class="form-label">What's happening on campus?</label>
                            <textarea class="form-control" id="gist_content" name="gist_content" rows="5" required></textarea>
                        </div>
                        <button type="submit" name="submit_gist" class="btn btn-primary w-100">Submit Gist</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-body text-center">
                    <p>Login to submit your own campus gists!</p>
                    <a href="/login" class="btn btn-primary">Login</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>