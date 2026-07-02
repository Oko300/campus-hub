<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php'; // For database access
require_once __DIR__ . '/../includes/functions.php'; // For utility functions
?>

<div class="jumbotron text-center bg-light p-5 rounded mb-4">
    <h1 class="display-4">Welcome to CampusHub!</h1>
    <p class="lead">Your one-stop portal for campus news, gists, past questions, and discussions.</p>
    <hr class="my-4">
    <p>Stay informed, share your thoughts, and ace your exams!</p>
    <?php if (!isLoggedIn()): ?>
        <a class="btn btn-primary btn-lg" href="/register" role="button">Join Us Today</a>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-newspaper fa-2x"></i></h5>
                <p class="card-text">Latest News</p>
                <h3 class="card-text">50+</h3> <!-- Placeholder for dynamic count -->
                <a href="/news" class="btn btn-sm btn-outline-primary">View All News</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-lightbulb fa-2x"></i></h5>
                <p class="card-text">Campus Gists</p>
                <h3 class="card-text">120+</h3> <!-- Placeholder for dynamic count -->
                <a href="/gists" class="btn btn-sm btn-outline-primary">Read Gists</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-question-circle fa-2x"></i></h5>
                <p class="card-text">Past Questions</p>
                <h3 class="card-text">200+</h3> <!-- Placeholder for dynamic count -->
                <a href="/past-questions" class="btn btn-sm btn-outline-primary">Download PQs</a>
            </div>
        </div>
    </div>
</div>

<!-- Latest News Section -->
<h2 class="mt-5 mb-3">Latest News</h2>
<div class="row">
    <!-- Example News Item -->
    <div class="col-md-4">
        <div class="card">
            <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="News Image">
            <div class="card-body">
                <h5 class="card-title">Campus Event Highlights</h5>
                <p class="card-text">Summary of the latest campus events and activities.</p>
                <a href="#" class="btn btn-primary">Read More</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="News Image">
            <div class="card-body">
                <h5 class="card-title">Academic Calendar Updates</h5>
                <p class="card-text">Important dates and deadlines for the current semester.</p>
                <a href="#" class="btn btn-primary">Read More</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="News Image">
            <div class="card-body">
                <h5 class="card-title">Student Achievements</h5>
                <p class="card-text">Celebrating outstanding accomplishments by our students.</p>
                <a href="#" class="btn btn-primary">Read More</a>
            </div>
        </div>
    </div>
</div>

<!-- Latest Gists Section -->
<h2 class="mt-5 mb-3">Latest Campus Gists</h2>
<div class="row">
    <!-- Example Gist Item -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">New Cafeteria Menu!</h5>
                <p class="card-text">Students are raving about the new additions to the cafeteria menu. Try the jollof rice!</p>
                <p class="card-text"><small class="text-muted">Submitted by Anonymous on <?php echo date('M d, Y'); ?></small></p>
                <a href="#" class="btn btn-primary">View Gist</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Library Extended Hours</h5>
                <p class="card-text">Good news for night owls! The main library will now be open until midnight during exam periods.</p>
                <p class="card-text"><small class="text-muted">Submitted by Admin on <?php echo date('M d, Y'); ?></small></p>
                <a href="#" class="btn btn-primary">View Gist</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>