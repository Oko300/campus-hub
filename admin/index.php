<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin(); // Only admins can access this page

// Fetch dashboard statistics
try {
    $total_users_stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $total_users = $total_users_stmt->fetchColumn();

    $total_news_stmt = $pdo->query("SELECT COUNT(*) FROM news");
    $total_news = $total_news_stmt->fetchColumn();

    $total_gists_stmt = $pdo->query("SELECT COUNT(*) FROM gists");
    $total_gists = $total_gists_stmt->fetchColumn();

    $pending_gists_stmt = $pdo->query("SELECT COUNT(*) FROM gists WHERE status = 'pending'");
    $pending_gists = $pending_gists_stmt->fetchColumn();

    $total_past_questions_stmt = $pdo->query("SELECT COUNT(*) FROM past_questions");
    $total_past_questions = $total_past_questions_stmt->fetchColumn();

    $total_threads_stmt = $pdo->query("SELECT COUNT(*) FROM forum_threads");
    $total_threads = $total_threads_stmt->fetchColumn();

    $total_replies_stmt = $pdo->query("SELECT COUNT(*) FROM forum_replies");
    $total_replies = $total_replies_stmt->fetchColumn();

} catch (PDOException $e) {
    error_log("Error fetching dashboard stats: " . $e->getMessage());
    $_SESSION['error'] = 'Could not load dashboard statistics.';
    // Set defaults to avoid errors
    $total_users = $total_news = $total_gists = $pending_gists = $total_past_questions = $total_threads = $total_replies = 0;
}

?>

<div class="row">
    <div class="col-md-3">
        <!-- Admin Sidebar -->
        <?php include __DIR__ . '/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h2 class="mb-4">Admin Dashboard</h2>

        <?php flash('success'); ?>
        <?php flash('error'); ?>

        <div class="row">
            <!-- Total Users Card -->
            <div class="col-md-4 mb-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0"><?php echo $total_users; ?></h3>
                                <p class="mb-0">Total Users</p>
                            </div>
                            <i class="fas fa-users fa-3x"></i>
                        </div>
                    </div>
                    <a href="#" class="card-footer text-white clearfix small z-1">
                        <span class="float-start">View Details</span>
                        <span class="float-end"><i class="fas fa-angle-right"></i></span>
                    </a>
                </div>
            </div>

            <!-- Total News Card -->
            <div class="col-md-4 mb-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0"><?php echo $total_news; ?></h3>
                                <p class="mb-0">Total News Articles</p>
                            </div>
                            <i class="fas fa-newspaper fa-3x"></i>
                        </div>
                    </div>
                    <a href="/admin/news" class="card-footer text-white clearfix small z-1">
                        <span class="float-start">Manage News</span>
                        <span class="float-end"><i class="fas fa-angle-right"></i></span>
                    </a>
                </div>
            </div>

            <!-- Total Gists Card -->
            <div class="col-md-4 mb-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0"><?php echo $total_gists; ?></h3>
                                <p class="mb-0">Total Gists</p>
                            </div>
                            <i class="fas fa-lightbulb fa-3x"></i>
                        </div>
                    </div>
                    <a href="/admin/gists" class="card-footer text-white clearfix small z-1">
                        <span class="float-start">Moderate Gists <?php echo ($pending_gists > 0) ? '(<span class="badge bg-warning text-dark">' . $pending_gists . ' Pending</span>)' : ''; ?></span>
                        <span class="float-end"><i class="fas fa-angle-right"></i></span>
                    </a>
                </div>
            </div>

            <!-- Total Past Questions Card -->
            <div class="col-md-4 mb-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0"><?php echo $total_past_questions; ?></h3>
                                <p class="mb-0">Past Questions</p>
                            </div>
                            <i class="fas fa-question-circle fa-3x"></i>
                        </div>
                    </div>
                    <a href="/admin/past-questions" class="card-footer text-white clearfix small z-1">
                        <span class="float-start">Manage Questions</span>
                        <span class="float-end"><i class="fas fa-angle-right"></i></span>
                    </a>
                </div>
            </div>

            <!-- Total Forum Threads Card -->
            <div class="col-md-4 mb-4">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0"><?php echo $total_threads; ?></h3>
                                <p class="mb-0">Forum Threads</p>
                            </div>
                            <i class="fas fa-comments fa-3x"></i>
                        </div>
                    </div>
                    <a href="/admin/forum" class="card-footer text-white clearfix small z-1">
                        <span class="float-start">Moderate Forum</span>
                        <span class="float-end"><i class="fas fa-angle-right"></i></span>
                    </a>
                </div>
            </div>

            <!-- Total Forum Replies Card -->
            <div class="col-md-4 mb-4">
                <div class="card text-white bg-secondary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0"><?php echo $total_replies; ?></h3>
                                <p class="mb-0">Forum Replies</p>
                            </div>
                            <i class="fas fa-reply-all fa-3x"></i>
                        </div>
                    </div>
                    <a href="/admin/forum" class="card-footer text-white clearfix small z-1">
                        <span class="float-start">Moderate Replies</span>
                        <span class="float-end"><i class="fas fa-angle-right"></i></span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Links / Recent Activity (Optional) -->
        <div class="row mt-4">
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-area me-1"></i>
                        Recent Activity (Placeholder)
                    </div>
                    <div class="card-body">
                        <p>This section can display recent news, gists, or forum activity.</p>
                        <!-- Example: List latest 5 gists -->
                        <h6>Latest Pending Gists:</h6>
                        <ul class="list-group">
                            <?php
                            try {
                                $latest_gists_stmt = $pdo->query("SELECT g.id, g.content, u.username FROM gists g JOIN users u ON g.user_id = u.id WHERE g.status = 'pending' ORDER BY g.created_at DESC LIMIT 5");
                                $latest_gists = $latest_gists_stmt->fetchAll();
                                if (empty($latest_gists)) {
                                    echo '<li class="list-group-item">No pending gists.</li>';
                                } else {
                                    foreach ($latest_gists as $gist) {
                                        echo '<li class="list-group-item"><a href="/admin/gists">' . substr(htmlspecialchars($gist['content']), 0, 50) . '...</a> by ' . htmlspecialchars($gist['username']) . '</li>';
                                    }
                                }
                            } catch (PDOException $e) {
                                error_log("Error fetching latest gists for dashboard: " . $e->getMessage());
                                echo '<li class="list-group-item text-danger">Error loading latest gists.</li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        System Information (Placeholder)
                    </div>
                    <div class="card-body">
                        <p>This section can display system health, logs, or other relevant info.</p>
                        <ul class="list-group">
                            <li class="list-group-item">PHP Version: <?php echo phpversion(); ?></li>
                            <li class="list-group-item">Database Type: PostgreSQL</li>
                            <li class="list-group-item">Server Time: <?php echo date('Y-m-d H:i:s'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>