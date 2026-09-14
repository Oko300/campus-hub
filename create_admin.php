<?php

require_once __DIR__ . '/includes/functions.php';
loadEnv();

$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');
$db_port = getenv('DB_PORT');
$db_driver = getenv('DB_DRIVER');

try {
    $dsn = "$db_driver:host=$db_host;port=$db_port;dbname=$db_name";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $username = 'admin';
    $email = 'admin@campushub.com';
    $password = 'Admin1234!';
    $role = 'admin';

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if admin already exists to prevent duplicates
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetchColumn() > 0) {
        echo "Admin user 'admin@campushub.com' already exists.\n";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role
        ]);
        echo "Admin user created successfully!\n";
        echo "Username: $username\n";
        echo "Email: $email\n";
        echo "Password: $password\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
