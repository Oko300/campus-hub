<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$search_query = sanitizeInput($_GET['query'] ?? '');
$results = [
    'news' => [],
    'gists' => [],
    'past_questions' => []
];

if (!empty($search_query)) {
    $search_param = '%' . $search_query . '%';

    try {
        // Search News
        $news_stmt = $pdo->prepare("SELECT id, title, content, created_at FROM news WHERE title ILIKE :search OR content ILIKE :search ORDER BY created_at DESC LIMIT 5");
        $news_stmt->execute([':search' => $search_param]);
        $results['news'] = $news_stmt->fetchAll();

        // Search Gists (only approved ones)
        $gists_stmt = $pdo->prepare("SELECT g.id, g.content, g.created_at, u.username FROM gists g JOIN users u ON g.user_id = u.id WHERE g.status = 'approved' AND g.content ILIKE :search ORDER BY g.created_at DESC LIMIT 5");
        $gists_stmt->execute([':search' => $search_param]);
        $results['gists'] = $gists_stmt->fetchAll();

        // Search Past Questions
        $pq_stmt = $pdo->prepare("SELECT id, title, course, year, uploaded_at FROM past_questions WHERE title ILIKE :search OR course ILIKE :search ORDER BY uploaded_at DESC LIMIT 5");
        $pq_stmt->execute([':search' => $search_param]);
        $results['past_questions'] = $pq_stmt->fetchAll();

    } catch (PDOException $e) {
        error_log("Error during search: " . $e->getMessage());
        $_SESSION['error'] = 'An error occurred during search. Please try again.';
    }
}

?>

<h1 class="mb-4">Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>

<?php flash('error'); ?>

<?php if (empty($search_query)): ?>
    <p class="alert alert-info">Please enter a search query to see results.</p>
<?php elseif (empty($results['news']) && empty($results['gists']) && empty($results['past_questions'])): ?>
    <p class="alert alert-warning">No results found for "<?php echo htmlspecialchars($search_query); ?>".</p>
<?php else: ?>

    <!-- News Results -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="mb-0">News Articles</h3>
        </div>
        <div class="card-body">
            <?php if (empty($results['news'])): ?>
                <p>No news articles found.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($results['news'] as $item): ?>
                        <li class="list-group-item">
                            <a href="/news?id=<?php echo htmlspecialchars($item['id']); ?>"><?php echo htmlspecialchars($item['title']); ?></a>
                            <small class="text-muted float-end"><?php echo time_elapsed_string($item['created_at']); ?></small>
                            <p class="text-muted"><?php echo substr(htmlspecialchars($item['content']), 0, 100); ?>...</p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Gists Results -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="mb-0">Campus Gists</h3>
        </div>
        <div class="card-body">
            <?php if (empty($results['gists'])): ?>
                <p>No gists found.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($results['gists'] as $item): ?>
                        <li class="list-group-item">
                            <a href="/gists?id=<?php echo htmlspecialchars($item['id']); ?>"><?php echo substr(htmlspecialchars($item['content']), 0, 100); ?>...</a>
                            <small class="text-muted float-end">By <?php echo htmlspecialchars($item['username']); ?> <?php echo time_elapsed_string($item['created_at']); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Past Questions Results -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="mb-0">Past Questions</h3>
        </div>
        <div class="card-body">
            <?php if (empty($results['past_questions'])): ?>
                <p>No past questions found.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($results['past_questions'] as $item): ?>
                        <li class="list-group-item">
                            <a href="/past-questions?search=<?php echo htmlspecialchars($item['title']); ?>"><?php echo htmlspecialchars($item['title']); ?> (<?php echo htmlspecialchars($item['course']); ?> - <?php echo htmlspecialchars($item['year']); ?>)</a>
                            <small class="text-muted float-end">Uploaded <?php echo time_elapsed_string($item['uploaded_at']); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>