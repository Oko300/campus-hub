<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Determine if a specific news article is being viewed
$article_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$viewing_single_article = ($article_id !== null && $article_id !== false);

if ($viewing_single_article) {
    // Fetch a single news article
    try {
        $stmt = $pdo->prepare("SELECT n.*, u.username as author_name FROM news n JOIN users u ON n.author_id = u.id WHERE n.id = :id");
        $stmt->execute([':id' => $article_id]);
        $article = $stmt->fetch();

        if (!$article) {
            $_SESSION['error'] = 'News article not found.';
            redirect('/news'); // Redirect to all news if not found
        }
    } catch (PDOException $e) {
        error_log("Error fetching single news article: " . $e->getMessage());
        $_SESSION['error'] = 'Could not load news article.';
        redirect('/news');
    }
} else {
    // Fetch all news articles
    try {
        $stmt = $pdo->query("SELECT n.*, u.username as author_name FROM news n JOIN users u ON n.author_id = u.id ORDER BY n.created_at DESC");
        $news_articles = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching news articles: " . $e->getMessage());
        $_SESSION['error'] = 'Could not load news articles.';
        $news_articles = [];
    }
}

?>

<div class="row">
    <div class="col-12">
        <?php flash('success'); ?>
        <?php flash('error'); ?>

        <?php if ($viewing_single_article): ?>
            <!-- Display single news article -->
            <div class="card mb-4">
                <?php if (!empty($article['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($article['image_url']); ?>" class="card-img-top" alt="News Image">
                <?php endif; ?>
                <div class="card-body">
                    <h1 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                    <p class="card-subtitle mb-2 text-muted">
                        By <?php echo htmlspecialchars($article['author_name']); ?> on <?php echo date('M d, Y H:i', strtotime($article['created_at'])); ?>
                    </p>
                    <div class="card-text">
                        <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                    </div>
                    <a href="/news" class="btn btn-primary mt-3"><i class="fas fa-arrow-left"></i> Back to All News</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Display list of news articles -->
            <h1 class="mb-4">Campus News</h1>
            <div class="row">
                <?php if (empty($news_articles)): ?>
                    <div class="col-12">
                        <p class="text-center">No news articles available at the moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($news_articles as $article): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <?php if (!empty($article['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($article['image_url']); ?>" class="card-img-top" alt="News Image" style="height: 200px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h5>
                                    <p class="card-text"><small class="text-muted">By <?php echo htmlspecialchars($article['author_name']); ?> on <?php echo date('M d, Y', strtotime($article['created_at'])); ?></small></p>
                                    <p class="card-text"><?php echo substr(htmlspecialchars($article['content']), 0, 150); ?>...</p>
                                    <a href="/news?id=<?php echo $article['id']; ?>" class="btn btn-primary">Read More</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>