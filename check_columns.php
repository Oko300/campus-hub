<?php
require_once 'includes/db.php';
$stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'past_questions' ORDER BY ordinal_position");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo '<pre>';
print_r($columns);
echo '</pre>';
?>