<?php
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin(); // Only admins can access this page

$errors = [];
$title = '';
$course = '';
$year = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $course = sanitizeInput($_POST['course'] ?? '');
    $year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT);

    // Basic validation
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }
    if (empty($course)) {
        $errors[] = 'Course is required.';
    }
    if (!$year || $year < 1900 || $year > date('Y') + 5) { // Allow up to 5 years in the future for planning
        $errors[] = 'Valid year is required.';
    }

    // File upload handling
    $file_path = null;
    if (isset($_FILES['question_file']) && $_FILES['question_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['question_file']['tmp_name'];
        $file_name = basename($_FILES['question_file']['name']);
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = 'Only PDF, DOC, DOCX, JPG, JPEG, PNG files are allowed.';
        } else {
            $unique_file_name = uniqid('pq_', true) . '.' . $file_extension;
            $upload_dir = __DIR__ . '/../../uploads/';
            $destination = $upload_dir . $unique_file_name;

            if (!move_uploaded_file($file_tmp_name, $destination)) {
                $errors[] = 'Failed to upload file.';
                error_log("File upload failed for: " . $file_name . " to " . $destination);
            } else {
                $file_path = $unique_file_name; // Store only the filename in DB
            }
        }
    } else {
        $errors[] = 'Question file is required.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO past_questions (title, course, year, file_path, uploader_id) VALUES (:title, :course, :year, :file_path, :uploader_id)");
            $stmt->execute([
                ':title' => $title,
                ':course' => $course,
                ':year' => $year,
                ':file_path' => $file_path,
                ':uploader_id' => $_SESSION['user_id'] // Assuming admin is logged in
            ]);
            $_SESSION['success'] = 'Past question uploaded successfully!';
            redirect('/admin/past-questions');
        } catch (PDOException $e) {
            error_log("Error uploading past question: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to upload past question. Please try again.';
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
        <h2 class="mb-4">Upload New Past Question</h2>

        <?php flash('success'); ?>
        <?php flash('error'); ?>

        <div class="card">
            <div class="card-body">
                <form action="/admin/past-questions/create" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="course" class="form-label">Course</label>
                        <input type="text" class="form-control" id="course" name="course" value="<?php echo htmlspecialchars($course); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="year" class="form-label">Year</label>
                        <input type="number" class="form-control" id="year" name="year" value="<?php echo htmlspecialchars($year); ?>" min="1900" max="<?php echo date('Y') + 5; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="question_file" class="form-label">Question File (PDF, DOC, DOCX, JPG, PNG)</label>
                        <input type="file" class="form-control" id="question_file" name="question_file" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Question</button>
                    <a href="/admin/past-questions" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>