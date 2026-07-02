<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin(); // Only admins can access this page

// Fetch all gists with author information
try {
    $stmt = $pdo->query("SELECT g.*, u.username as author_name FROM gists g JOIN users u ON g.user_id = u.id ORDER BY g.created_at DESC");
    $gists = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching gists: " . $e->getMessage());
    $_SESSION['error'] = 'Could not load gists.';
    $gists = [];
}

?>

<div class="row">
    <div class="col-md-3">
        <!-- Admin Sidebar -->
        <?php include __DIR__ . '/../sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h2 class="mb-4">Gist Moderation</h2>

        <?php flash('success'); ?>
        <?php flash('error'); ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Content</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Submitted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($gists)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No gists found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($gists as $gist): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($gist['id']); ?></td>
                                        <td><?php echo substr(htmlspecialchars($gist['content']), 0, 100); ?>...</td>
                                        <td><?php echo htmlspecialchars($gist['author_name']); ?></td>
                                        <td>
                                            <span class="badge <?php
                                                if ($gist['status'] === 'approved') echo 'bg-success';
                                                elseif ($gist['status'] === 'rejected') echo 'bg-danger';
                                                else echo 'bg-warning text-dark';
                                            ?>">
                                                <?php echo ucfirst(htmlspecialchars($gist['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($gist['created_at'])); ?></td>
                                        <td>
                                            <?php if ($gist['status'] === 'pending'): ?>
                                                <a href="/admin/gists/approve?id=<?php echo $gist['id']; ?>" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Approve</a>
                                                <a href="/admin/gists/reject?id=<?php echo $gist['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-times"></i> Reject</a>
                                            <?php endif; ?>
                                            <a href="/admin/gists/delete?id=<?php echo $gist['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this gist?');"><i class="fas fa-trash-alt"></i> Delete</a>
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