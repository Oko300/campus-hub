<?php

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php'; // Assuming $pdo is available from db.php

/**
 * Registers a new user.
 *
 * @param string $username The user's chosen username.
 * @param string $email The user's email address.
 * @param string $password The user's plain-text password.
 * @param string $role The user's role (e.g., 'student', 'admin').
 * @return bool True on successful registration, false otherwise.
 */
function registerUser($username, $email, $password, $role = 'student') {
    global $pdo;

    // Sanitize inputs
    $username = sanitizeInput($username);
    $email = sanitizeInput($email);
    $password = sanitizeInput($password);
    $role = sanitizeInput($role);

    // Hash the password
    $hashedPassword = hashPassword($password);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role
        ]);
        return true;
    } catch (PDOException $e) {
        error_log("User registration error: " . $e->getMessage());
        return false;
    }
}

/**
 * Logs in a user.
 *
 * @param string $email The user's email address.
 * @param string $password The user's plain-text password.
 * @return bool True on successful login, false otherwise.
 */
function loginUser($email, $password) {
    global $pdo;

    // Sanitize inputs
    $email = sanitizeInput($email);
    $password = sanitizeInput($password);

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && verifyPassword($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        error_log("User login error: " . $e->getMessage());
        return false;
    }
}

/**
 * Logs out the current user.
 */
function logoutUser() {
    session_unset();
    session_destroy();
    redirect('/login'); // Redirect to login page after logout
}

/**
 * Requires a user to be logged in to access a page.
 * If not logged in, redirects to the login page.
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'You must be logged in to view this page.';
        redirect('/login');
    }
}

/**
 * Requires the logged-in user to have an admin role to access a page.
 * If not an admin, redirects to the home page.
 */
function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['error'] = 'You do not have permission to view this page.';
        redirect('/');
    }
}