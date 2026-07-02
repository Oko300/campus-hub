<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin(); // Only admins can access this page

$errors = [];
$article = null;
$article_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$article_id) {
    $_SESSION['error'] = 'Invalid news article ID.';
    redirect('/admin/news');
}

// Fetch the existing article data
try {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = :id");
    $stmt->execute([':id' => $article_id]);
    $article = $stmt->fetch();

    if (!$article) {
        $_SESSION['error'] = 'News article not found.';
        redirect('/admin/news');
    }
} catch (PDOException $e) {
    error_log("Error fetching news article for edit: " . $e->getMessage());
    $_SESSION['error'] = 'Could not load news article for editing.';
    redirect('/admin/news');
}

// Handle form submission for updating the article
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = sanitizeInput($_POST['content'] ?? '');
    $image_url = sanitizeInput($_POST['image_url'] ?? '');

    // Basic validation
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }
    if (empty($content)) {
        $errors[] = 'Content is required.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE news SET title = :title, content = :content, image_url = :image_url, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
            $stmt->execute([
                ':title' => $title,
                ':content' => $content,
                ':image_url' => $image_url,
                ':id' => $article_id
            ]);
            $_SESSION['success'] = 'News article updated successfully!';
            redirect('/admin/news');
        } catch (PDOException $e) {
            error_log("Error updating news article: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to update news article. Please try again.';
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
        <h2 class="mb-4">Edit News Article</h2>

        <?php flash('success'); ?>
        <?php flash('error'); ?>

        <div class="card">
            <div class="card-body">
                <form action="/admin/news/edit?id=<?php echo $article_id; ?>" method="POST">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($article['content']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image_url" class="form-label">Image URL (Optional)</label>
                        <input type="url" class="form-control" id="image_url" name="image_url" value="<?php echo htmlspecialchars($article['image_url'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Article</button>
                    <a href="/admin/news" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>