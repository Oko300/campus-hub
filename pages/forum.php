<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin(); // Users must be logged in to access the forum

$errors = [];
$title = '';
$content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = sanitizeInput($_POST['content'] ?? '');

    if (empty($title)) {
        $errors[] = 'Thread title is required.';
    }
    if (empty($content)) {
        $errors[] = 'Thread content is required.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO forum_threads (user_id, title, content) VALUES (:user_id, :title, :content)");
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':title' => $title,
                ':content' => $content
            ]);
            $_SESSION['success'] = 'New thread created successfully!';
            redirect('/forum');
        } catch (PDOException $e) {
            error_log("Error creating forum thread: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to create thread. Please try again.';
        }
    } else {
        $_SESSION['error'] = 'Please correct the following errors: <ul>';
        foreach ($errors as $error) {
            $_SESSION['error'] .= '<li>' . $error . '</li>';
        }
        $_SESSION['error'] .= '</ul>';
    }
}

// Fetch all forum threads with author information and reply counts
try {
    $stmt = $pdo->query("
        SELECT 
            ft.*, 
            u.username as author_name,
            COUNT(fr.id) as reply_count
        FROM 
            forum_threads ft 
        JOIN 
            users u ON ft.user_id = u.id
        LEFT JOIN
            forum_replies fr ON ft.id = fr.thread_id
        GROUP BY
            ft.id, ft.user_id, ft.title, ft.content, ft.created_at, ft.updated_at, u.username
        ORDER BY 
            ft.updated_at DESC
    ");
    $threads = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching forum threads: " . $e->getMessage());
    $_SESSION['error'] = 'Could not load forum threads.';
    $threads = [];
}

?>

<h1 class="mb-4">Discussion Forum</h1>

<?php flash('success'); ?>
<?php flash('error'); ?>

<div class="card mb-4">
    <div class="card-header">
        <h5>Create New Thread</h5>
    </div>
    <div class="card-body">
        <form action="/forum" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Thread Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="5" required><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create Thread</button>
        </form>
    </div>
</div>

<h2 class="mb-3">Latest Threads</h2>
<div class="list-group">
    <?php if (empty($threads)): ?>
        <p class="text-center">No discussion threads found. Be the first to create one!</p>
    <?php else: ?>
        <?php foreach ($threads as $thread): ?>
            <a href="/thread?id=<?php echo htmlspecialchars($thread['id']); ?>" class="list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1"><?php echo htmlspecialchars($thread['title']); ?></h5>
                    <small><?php echo time_elapsed_string($thread['created_at']); ?></small>
                </div>
                <p class="mb-1"><?php echo substr(htmlspecialchars($thread['content']), 0, 150); ?>...</p>
                <small>By <?php echo htmlspecialchars($thread['author_name']); ?> &bull; <?php echo htmlspecialchars($thread['reply_count']); ?> Replies</small>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>