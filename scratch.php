<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance();
$stmt = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'product_variants' AND REFERENCED_TABLE_NAME = 'products'");
echo $stmt->fetchColumn();
