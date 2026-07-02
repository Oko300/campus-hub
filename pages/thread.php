<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin(); // Users must be logged in to access the forum

$thread_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$thread_id) {
    $_SESSION['error'] = 'Invalid thread ID.';
    redirect('/forum');
}

// Fetch thread details
try {
    $stmt = $pdo->prepare("SELECT ft.*, u.username as author_name FROM forum_threads ft JOIN users u ON ft.user_id = u.id WHERE ft.id = :id");
    $stmt->execute([':id' => $thread_id]);
    $thread = $stmt->fetch();

    if (!$thread) {
        $_SESSION['error'] = 'Thread not found.';
        redirect('/forum');
    }
} catch (PDOException $e) {
    error_log("Error fetching forum thread: " . $e->getMessage());
    $_SESSION['error'] = 'Could not load thread.';
    redirect('/forum');
}

$errors = [];
$reply_content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reply_content = sanitizeInput($_POST['reply_content'] ?? '');

    if (empty($reply_content)) {
        $errors[] = 'Reply content cannot be empty.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO forum_replies (thread_id, user_id, content) VALUES (:thread_id, :user_id, :content)");
            $stmt->execute([
                ':thread_id' => $thread_id,
                ':user_id' => $_SESSION['user_id'],
                ':content' => $reply_content
            ]);

            // Update thread's updated_at to bring it to the top of the list
            $update_thread_stmt = $pdo->prepare("UPDATE forum_threads SET updated_at = CURRENT_TIMESTAMP WHERE id = :id");
            $update_thread_stmt->execute([':id' => $thread_id]);

            $_SESSION['success'] = 'Reply posted successfully!';
            redirect('/thread?id=' . $thread_id);
        } catch (PDOException $e) {
            error_log("Error posting reply: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to post reply. Please try again.';
        }
    } else {
        $_SESSION['error'] = 'Please correct the following errors: <ul>';
        foreach ($errors as $error) {
            $_SESSION['error'] .= '<li>' . $error . '</li>';
        }
        $_SESSION['error'] .= '</ul>';
    }
}

// Fetch replies for the thread
try {
    $stmt = $pdo->prepare("SELECT fr.*, u.username as author_name FROM forum_replies fr JOIN users u ON fr.user_id = u.id WHERE fr.thread_id = :thread_id ORDER BY fr.created_at ASC");
    $stmt->execute([':thread_id' => $thread_id]);
    $replies = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching forum replies: " . $e->getMessage());
    $_SESSION['error'] = 'Could not load replies.';
    $replies = [];
}

?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/forum">Forum</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($thread['title']); ?></li>
    </ol>
</nav>

<h1 class="mb-4"><?php echo htmlspecialchars($thread['title']); ?></h1>

<?php flash('success'); ?>
<?php flash('error'); ?>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Thread by <?php echo htmlspecialchars($thread['author_name']); ?></h5>
        <small>Posted <?php echo time_elapsed_string($thread['created_at']); ?></small>
    </div>
    <div class="card-body">
        <p><?php echo nl2br(htmlspecialchars($thread['content'])); ?></p>
    </div>
</div>

<h2 class="mb-3">Replies</h2>
<div class="replies-section">
    <?php if (empty($replies)): ?>
        <p class="text-center">No replies yet. Be the first to respond!</p>
    <?php else: ?>
        <?php foreach ($replies as $reply): ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Reply by <?php echo htmlspecialchars($reply['author_name']); ?></h6>
                    <small>Posted <?php echo time_elapsed_string($reply['created_at']); ?></small>
                </div>
                <div class="card-body">
                    <p><?php echo nl2br(htmlspecialchars($reply['content'])); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5>Post a Reply</h5>
    </div>
    <div class="card-body">
        <form action="/thread?id=<?php echo htmlspecialchars($thread_id); ?>" method="POST">
            <div class="mb-3">
                <label for="reply_content" class="form-label">Your Reply</label>
                <textarea class="form-control" id="reply_content" name="reply_content" rows="3" required><?php echo htmlspecialchars($reply_content); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Post Reply</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>