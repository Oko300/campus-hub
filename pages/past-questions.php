<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Search and filter logic
$search_query = sanitizeInput($_GET['search'] ?? '');
$description_filter = sanitizeInput($_GET['description'] ?? '');

$sql = "SELECT pq.id, pq.title, pq.description, pq.file_path, pq.uploaded_by, pq.download_count, pq.created_at, u.username as uploader_name FROM past_questions pq JOIN users u ON pq.uploaded_by = u.id WHERE 1=1";
$params = [];

if (!empty($search_query)) {
    $sql .= " AND (pq.title ILIKE :search_query OR pq.description ILIKE :search_query)";
    $params[':search_query'] = '%' . $search_query . '%';
}
if (!empty($description_filter)) {
    $sql .= " AND pq.description ILIKE :description_filter";
    $params[':description_filter'] = '%' . $description_filter . '%';
}

$sql .= " ORDER BY pq.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $past_questions = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching past questions for students: " . $e->getMessage());
    $_SESSION['error'] = 'Could not load past questions.';
    $past_questions = [];
}

// Fetch distinct descriptions for filters
try {
    $descriptions_stmt = $pdo->query("SELECT DISTINCT description FROM past_questions ORDER BY description ASC");
    $available_descriptions = $descriptions_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    error_log("Error fetching filter options: " . $e->getMessage());
    $available_descriptions = [];
}

?>

<h1 class="mb-4">Past Questions</h1>

<?php flash('success'); ?>
<?php flash('error'); ?>

<div class="card mb-4">
    <div class="card-header">
        <h5>Search & Filter</h5>
    </div>
    <div class="card-body">
        <form action="/past-questions" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Search by title or description" value="<?php echo htmlspecialchars($search_query); ?>">
            </div>
            <div class="col-md-3">
                <select name="description" class="form-select">
                    <option value="">All Courses & Years</option>
                    <?php foreach ($available_descriptions as $description): ?>
                        <option value="<?php echo htmlspecialchars($description); ?>" <?php echo ($description_filter === $description) ? 'selected' : ''; ?>><?php echo htmlspecialchars($description); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Search</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Course & Year</th>
                                <th>Uploader</th>
                                <th>Downloads</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($past_questions)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No past questions found matching your criteria.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($past_questions as $pq): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($pq['title']); ?></td>
                                        <td><?php echo htmlspecialchars($pq['description']); ?></td>
                                        <td><?php echo htmlspecialchars($pq['uploader_name']); ?></td>
                                        <td><?php echo htmlspecialchars($pq['download_count']); ?></td>
                                        <td>
                                            <a href="/download?type=past_question&id=<?php echo $pq['id']; ?>" class="btn btn-sm btn-success"><i class="fas fa-download"></i> Download</a>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>