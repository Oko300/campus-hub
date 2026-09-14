<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Search and filter logic
$search_query = sanitizeInput($_GET['search'] ?? '');
$course_filter = sanitizeInput($_GET['course'] ?? '');
$year_filter = filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT);

$sql = "SELECT pq.*, u.username as uploader_name FROM past_questions pq JOIN users u ON pq.uploader_id = u.id WHERE 1=1";
$params = [];

if (!empty($search_query)) {
    $sql .= " AND (pq.title ILIKE :search_query OR pq.course ILIKE :search_query)";
    $params[':search_query'] = '%' . $search_query . '%';
}
if (!empty($course_filter)) {
    $sql .= " AND pq.course = :course_filter";
    $params[':course_filter'] = $course_filter;
}
if ($year_filter) {
    $sql .= " AND pq.year = :year_filter";
    $params[':year_filter'] = $year_filter;
}

$sql .= " ORDER BY pq.uploaded_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $past_questions = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching past questions for students: " . $e->getMessage());
    $_SESSION['error'] = 'Could not load past questions.';
    $past_questions = [];
}

// Fetch distinct courses and years for filters
try {
    $courses_stmt = $pdo->query("SELECT DISTINCT course FROM past_questions ORDER BY course ASC");
    $available_courses = $courses_stmt->fetchAll(PDO::FETCH_COLUMN);

    $years_stmt = $pdo->query("SELECT DISTINCT year FROM past_questions ORDER BY year DESC");
    $available_years = $years_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    error_log("Error fetching filter options: " . $e->getMessage());
    $available_courses = [];
    $available_years = [];
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
                <input type="text" class="form-control" name="search" placeholder="Search by title or course" value="<?php echo htmlspecialchars($search_query); ?>">
            </div>
            <div class="col-md-3">
                <select name="course" class="form-select">
                    <option value="">All Courses</option>
                    <?php foreach ($available_courses as $course): ?>
                        <option value="<?php echo htmlspecialchars($course); ?>" <?php echo ($course_filter === $course) ? 'selected' : ''; ?>><?php echo htmlspecialchars($course); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="year" class="form-select">
                    <option value="">All Years</option>
                    <?php foreach ($available_years as $year): ?>
                        <option value="<?php echo htmlspecialchars($year); ?>" <?php echo ($year_filter === $year) ? 'selected' : ''; ?>><?php echo htmlspecialchars($year); ?></option>
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
                                <th>Course</th>
                                <th>Year</th>
                                <th>Uploader</th>
                                <th>Downloads</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($past_questions)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No past questions found matching your criteria.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($past_questions as $pq): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($pq['title']); ?></td>
                                        <td><?php echo htmlspecialchars($pq['course']); ?></td>
                                        <td><?php echo htmlspecialchars($pq['year']); ?></td>
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