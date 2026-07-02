<?php

/**
 * Loads environment variables from a .env file.
 * This function is a simple implementation and might not cover all edge cases
 * of a full-fledged .env parser.
 */
function loadEnv() {
    $envFile = __DIR__ . '/../.env';

    if (!file_exists($envFile)) {
        // In production (e.g., Render), environment variables are set directly
        // and a .env file might not exist.
        return;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        // Parse the line
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Set environment variable if not already set
        if (!isset($_SERVER[$name]) && !isset($_ENV[$name])) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

/**
 * Redirects to a specified URL.
 *
 * @param string $url The URL to redirect to.
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Hashes a password using a strong, one-way hashing algorithm.
 *
 * @param string $password The plain-text password.
 * @return string The hashed password.
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verifies a password against a stored hash.
 *
 * @param string $password The plain-text password.
 * @param string $hash The stored hash.
 * @return bool True if the password matches the hash, false otherwise.
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Sanitizes input data to prevent XSS attacks.
 *
 * @param string $data The input data to sanitize.
 * @return string The sanitized data.
 */
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Checks if a user is logged in.
 *
 * @return bool True if a user is logged in, false otherwise.
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Checks if the logged-in user has an admin role.
 *
 * @return bool True if the user is an admin, false otherwise.
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Displays a flash message.
 *
 * @param string $name The name of the flash message (e.g., 'success', 'error').
 */
function flash($name = '') {
    if (!empty($name)) {
        if (isset($_SESSION[$name])) {
            echo '<div class="alert alert-' . $name . '">' . $_SESSION[$name] . '</div>';
            unset($_SESSION[$name]);
        }
    }
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}