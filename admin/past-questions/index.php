<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin(); // Only admins can access this page

// Fetch all past questions
try {
    $stmt = $pdo->query("SELECT pq.id, pq.title, pq.description, pq.file_path, pq.uploaded_by, pq.download_count, pq.created_at, u.username as uploader_name FROM past_questions pq JOIN users u ON pq.uploaded_by = u.id ORDER BY pq.created_at DESC");
    $past_questions = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching past questions: " . $e->getMessage());
    $_SESSION['error'] = 'Could not load past questions.';
    $past_questions = [];
}

?>

<div class="row">
    <div class="col-md-3">
        <!-- Admin Sidebar -->
        <?php include __DIR__ . '/../sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Past Questions</h2>
            <a href="/admin/past-questions/create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Upload New Question</a>
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
                                <th>Course & Year</th>
                                <th>Uploader</th>
                                <th>Downloads</th>
                                <th>Uploaded At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($past_questions)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No past questions found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($past_questions as $pq): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($pq['id']); ?></td>
                                        <td><?php echo htmlspecialchars($pq['title']); ?></td>
                                        <td><?php echo htmlspecialchars($pq['description']); ?></td>
                                        <td><?php echo htmlspecialchars($pq['uploader_name']); ?></td>
                                        <td><?php echo htmlspecialchars($pq['download_count']); ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($pq['created_at'])); ?></td>
                                        <td>
                                            <a href="/uploads/<?php echo htmlspecialchars($pq['file_path']); ?>" class="btn btn-sm btn-info" target="_blank"><i class="fas fa-eye"></i> View</a>
                                            <a href="/admin/past-questions/delete.php?id=<?php echo $pq['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this past question?');"><i class="fas fa-trash-alt"></i> Delete</a>
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