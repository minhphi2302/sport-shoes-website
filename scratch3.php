<?php
require_once 'app/Core/Database.php';
$db = \App\Core\Database::getInstance();
$stmt = $db->query("SHOW CREATE TABLE product_variants");
echo $stmt->fetchColumn(1);
