<?php
// This file is included in admin pages, so auth.php and functions.php are already loaded.
// No need to require them again here.
?>
<div class="admin-sidebar card">
    <div class="card-header">
        <h4 class="text-white">Admin Panel</h4>
    </div>
    <div class="card-body p-0">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo (str_contains($_SERVER['REQUEST_URI'], '/admin') && !str_contains($_SERVER['REQUEST_URI'], '/admin/news') && !str_contains($_SERVER['REQUEST_URI'], '/admin/gists') && !str_contains($_SERVER['REQUEST_URI'], '/admin/past-questions') && !str_contains($_SERVER['REQUEST_URI'], '/admin/forum') && !str_contains($_SERVER['REQUEST_URI'], '/admin/users')) ? 'active' : ''; ?>" href="/admin">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo str_contains($_SERVER['REQUEST_URI'], '/admin/news') ? 'active' : ''; ?>" href="/admin/news">
                    <i class="fas fa-newspaper me-2"></i> News Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo str_contains($_SERVER['REQUEST_URI'], '/admin/gists') ? 'active' : ''; ?>" href="/admin/gists">
                    <i class="fas fa-lightbulb me-2"></i> Gist Moderation
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo str_contains($_SERVER['REQUEST_URI'], '/admin/past-questions') ? 'active' : ''; ?>" href="/admin/past-questions">
                    <i class="fas fa-question-circle me-2"></i> Past Questions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo str_contains($_SERVER['REQUEST_URI'], '/admin/forum') ? 'active' : ''; ?>" href="/admin/forum">
                    <i class="fas fa-comments me-2"></i> Forum Moderation
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo str_contains($_SERVER['REQUEST_URI'], '/admin/users') ? 'active' : ''; ?>" href="/admin/users">
                    <i class="fas fa-users me-2"></i> User Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/logout">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>