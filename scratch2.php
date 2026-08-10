<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance();
$stmt = $db->query("SHOW COLUMNS FROM products LIKE 'gender'");
echo $stmt->rowCount();
