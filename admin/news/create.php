<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin(); // Only admins can access this page

$errors = [];
$title = '';
$content = '';
$image_url = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = sanitizeInput($_POST['content'] ?? '');
    $image_url = sanitizeInput($_POST['image_url'] ?? ''); // For simplicity, using URL for now

    // Basic validation
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }
    if (empty($content)) {
        $errors[] = 'Content is required.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO news (title, content, author_id, image_url) VALUES (:title, :content, :author_id, :image_url)");
            $stmt->execute([
                ':title' => $title,
                ':content' => $content,
                ':author_id' => $_SESSION['user_id'], // Assuming admin is logged in
                ':image_url' => $image_url
            ]);
            $_SESSION['success'] = 'News article created successfully!';
            redirect('/admin/news');
        } catch (PDOException $e) {
            error_log("Error creating news article: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to create news article. Please try again.';
        }
    } else {
        $_SESSION['error'] = 'Please correct the following errors: <ul>';
        foreach ($errors as $error) {
            $_SESSION['error'] .= '<li>' . $error . '</li>';
        }
        $_SESSION['error'] .= '</ul>';
    }
}

?>

<div class="row">
    <div class="col-md-3">
        <!-- Admin Sidebar -->
        <?php include __DIR__ . '/../sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h2 class="mb-4">Create New News Article</h2>

        <?php flash('success'); ?>
        <?php flash('error'); ?>

        <div class="card">
            <div class="card-body">
                <form action="/admin/news/create" method="POST">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($content); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image_url" class="form-label">Image URL (Optional)</label>
                        <input type="url" class="form-control" id="image_url" name="image_url" value="<?php echo htmlspecialchars($image_url); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Create Article</button>
                    <a href="/admin/news" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>