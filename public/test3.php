<?php
require_once __DIR__ . '/../app/bootstrap.php';
try {
    $db = \App\Core\Database::getInstance();
    $stmt = $db->query("SELECT * FROM migrations");
    print_r($stmt->fetchAll());
} catch (\Exception $e) {
    echo $e->getMessage();
}
