<?php
require __DIR__ . '/app/Core/Database.php';
require __DIR__ . '/app/Core/Env.php';
\App\Core\Env::load(__DIR__ . '/.env');

$pdo = \App\Core\Database::getInstance();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE gender = 'female'");
$stmt->execute();
echo "Total female products: " . $stmt->fetchColumn() . "\n";

$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE gender = 'male'");
$stmt->execute();
echo "Total male products: " . $stmt->fetchColumn() . "\n";

require __DIR__ . '/app/Models/Product.php';
$model = new \App\Models\Product();
$prods = $model->getProductsFiltered(['gender' => 'female'], 1, 10);
echo "Filtered female products count: " . count($prods) . "\n";
