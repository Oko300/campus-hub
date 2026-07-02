<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

// Load environment variables
loadEnv();

// Basic routing
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($request_uri) {
    case '/':
        require_once __DIR__ . '/pages/home.php';
        break;
    case '/news':
        require __DIR__ . '/pages/news.php';
        break;
    case '/gists':
        require __DIR__ . '/pages/gists.php';
        break;
    case '/past-questions':
        require __DIR__ . '/pages/past-questions.php';
        break;
    case '/download':
        require __DIR__ . '/pages/download.php';
        break;
    case '/forum':
        require __DIR__ . '/pages/forum.php';
        break;
    case '/thread':
        require __DIR__ . '/pages/thread.php';
        break;
    case '/login':
        require __DIR__ . '/pages/login.php';
        break;
    case '/register':
        require __DIR__ . '/pages/register.php';
        break;
    case '/logout':
        require __DIR__ . '/pages/logout.php';
        break;
    case '/search':
        require __DIR__ . '/pages/search.php';
        break;
    // Add more routes as needed
    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}