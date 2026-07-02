<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin(); // Only admins can access this page

// Fetch all news articles
try {
    $stmt = $pdo->query("SELECT n.*, u.username as author_name FROM news n JOIN users u ON n.author_id = u.id ORDER BY n.created_at DESC");
    $news_articles = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching news articles: " . $e->getMessage());
    $_SESSION['error'] = 'Could not load news articles.';
    $news_articles = [];
}

?>

<div class="row">
    <div class="col-md-3">
        <!-- Admin Sidebar -->
        <?php include __DIR__ . '/../sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage News Articles</h2>
            <a href="/admin/news/create" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Article</a>
        </div>

        <?php flash('success'); ?>
        <?php flash('error'); ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($news_articles)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No news articles found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($news_articles as $article): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($article['id']); ?></td>
                                        <td><?php echo htmlspecialchars($article['title']); ?></td>
                                        <td><?php echo htmlspecialchars($article['author_name']); ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($article['created_at'])); ?></td>
                                        <td>
                                            <a href="/admin/news/edit?id=<?php echo $article['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="/admin/news/delete?id=<?php echo $article['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this article?');"><i class="fas fa-trash-alt"></i> Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>