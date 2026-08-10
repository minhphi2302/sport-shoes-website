<?php
require_once __DIR__ . '/../app/bootstrap.php';
try {
    $userModel = new \App\Models\User();
    $userId = $userModel->create([
        'name' => 'Test User',
        'email' => 'test' . time() . '@test.com',
        'password' => '123456',
        'role' => 'customer',
        'status' => 'active'
    ]);
    echo "User created: " . ($userId ? "Success, ID: $userId" : "Failed") . "<br>";
} catch (\Exception $e) {
    echo "User error: " . $e->getMessage() . "<br>";
}

try {
    $productModel = new \App\Models\Product();
    $productId = $productModel->create([
        'sku' => 'TEST' . time(),
        'name' => 'Test Product',
        'description' => 'Test',
        'category_id' => 1,
        'brand_id' => 1,
        'price' => 1000,
        'sale_price' => null,
        'quantity' => 10,
        'image_url' => null
    ]);
    echo "Product created: " . ($productId ? "Success, ID: $productId" : "Failed") . "<br>";
} catch (\Exception $e) {
    echo "Product error: " . $e->getMessage() . "<br>";
}
