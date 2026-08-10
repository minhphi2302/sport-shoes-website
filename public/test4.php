<?php
require_once __DIR__ . '/../app/bootstrap.php';
try {
    $db = \App\Core\Database::getInstance();
    $stmt = $db->query("SHOW CREATE TABLE products");
    print_r($stmt->fetch());
    
    $stmt2 = $db->query("SHOW CREATE TABLE users");
    print_r($stmt2->fetch());
} catch (\Exception $e) {
    echo $e->getMessage();
}
