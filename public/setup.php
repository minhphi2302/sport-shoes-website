<?php
$host = '127.0.0.1';
$port = 3306;
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Giữ nguyên DB, chỉ tạo nếu chưa có
    $pdo->exec("CREATE DATABASE IF NOT EXISTS shop_giay CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Kiểm tra database 'shop_giay' thành công!<br>";
    
    // Đã tạo DB xong, giờ chạy migrate
    $pdoDb = new PDO("mysql:host=$host;port=$port;dbname=shop_giay", $user, $pass);
    $pdoDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = file_get_contents(__DIR__ . '/../database/migrations/001_create_migrations_table.sql');
    $pdoDb->exec($sql);
    echo "Created migrations table.<br>";

    $stmt = $pdoDb->query("SELECT migration_name FROM migrations");
    $appliedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $files = glob(__DIR__ . '/../database/migrations/*.sql');
    sort($files);

    foreach ($files as $file) {
        $filename = basename($file);
        if (!in_array($filename, $appliedMigrations)) {
            $sql = file_get_contents($file);
            $pdoDb->exec($sql);
            $stmt = $pdoDb->prepare("INSERT INTO migrations (migration_name, executed_at) VALUES (?, NOW())");
            $stmt->execute([$filename]);
            echo "Successfully applied $filename<br>";
        }
    }
    echo "Tất cả migration đã chạy thành công!<br>";
    // Tạo tài khoản admin mặc định
    $stmt = $pdoDb->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    if ($stmt->fetchColumn() == 0) {
        $adminPass = password_hash('123456', PASSWORD_BCRYPT);
        $pdoDb->exec("INSERT INTO users (name, email, password, role, status) VALUES ('Admin', 'admin@example.com', '$adminPass', 'admin', 'active')");
        echo "Đã tạo tài khoản admin: Email: <b>admin@example.com</b> | Mật khẩu: <b>123456</b><br>";
    }
    echo "<a href='/sport-shoes-website-main/public/'>Quay lại trang chủ</a>";
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
