<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin(); // Only admins can access this page

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
    error_log("Error fetching forum threads for admin: " . $e->getMessage());
    $_SESSION['error'] = 'Could not load forum threads.';
    $threads = [];
}

// Fetch all forum replies with author and thread information
try {
    $stmt = $pdo->query("
        SELECT 
            fr.*, 
            u.username as author_name,
            ft.title as thread_title
        FROM 
            forum_replies fr 
        JOIN 
            users u ON fr.user_id = u.id
        JOIN
            forum_threads ft ON fr.thread_id = ft.id
        ORDER BY 
            fr.created_at DESC
    ");
    $replies = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching forum replies for admin: " . $e->getMessage());
    $_SESSION['error'] = 'Could not load forum replies.';
    $replies = [];
}

?>

<div class="row">
    <div class="col-md-3">
        <!-- Admin Sidebar -->
        <?php include __DIR__ . '/../sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h2 class="mb-4">Forum Moderation</h2>

        <?php flash('success'); ?>
        <?php flash('error'); ?>

        <ul class="nav nav-tabs mb-4" id="forumTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="threads-tab" data-bs-toggle="tab" data-bs-target="#threads" type="button" role="tab" aria-controls="threads" aria-selected="true">Threads</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="replies-tab" data-bs-toggle="tab" data-bs-target="#replies" type="button" role="tab" aria-controls="replies" aria-selected="false">Replies</button>
            </li>
        </ul>
        <div class="tab-content" id="forumTabsContent">
            <div class="tab-pane fade show active" id="threads" role="tabpanel" aria-labelledby="threads-tab">
                <h3 class="mb-3">Manage Threads</h3>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Replies</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($threads)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No threads found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($threads as $thread): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($thread['id']); ?></td>
                                                <td><a href="/thread?id=<?php echo htmlspecialchars($thread['id']); ?>" target="_blank"><?php echo substr(htmlspecialchars($thread['title']), 0, 50); ?>...</a></td>
                                                <td><?php echo htmlspecialchars($thread['author_name']); ?></td>
                                                <td><?php echo htmlspecialchars($thread['reply_count']); ?></td>
                                                <td><?php echo date('M d, Y H:i', strtotime($thread['created_at'])); ?></td>
                                                <td>
                                                    <a href="/admin/forum/delete_thread?id=<?php echo $thread['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this thread and all its replies?');"><i class="fas fa-trash-alt"></i> Delete</a>
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
            <div class="tab-pane fade" id="replies" role="tabpanel" aria-labelledby="replies-tab">
                <h3 class="mb-3">Manage Replies</h3>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Content</th>
                                        <th>Author</th>
                                        <th>Thread</th>
                                        <th>Posted At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($replies)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No replies found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($replies as $reply): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($reply['id']); ?></td>
                                                <td><?php echo substr(htmlspecialchars($reply['content']), 0, 100); ?>...</td>
                                                <td><?php echo htmlspecialchars($reply['author_name']); ?></td>
                                                <td><a href="/thread?id=<?php echo htmlspecialchars($reply['thread_id']); ?>" target="_blank"><?php echo substr(htmlspecialchars($reply['thread_title']), 0, 50); ?>...</a></td>
                                                <td><?php echo date('M d, Y H:i', strtotime($reply['created_at'])); ?></td>
                                                <td>
                                                    <a href="/admin/forum/delete_reply?id=<?php echo $reply['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this reply?');"><i class="fas fa-trash-alt"></i> Delete</a>
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
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>