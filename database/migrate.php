<?php

// database/migrate.php

try {
    // Kết nối MySQL để tạo DB nếu chưa có
    $pdo = new PDO("mysql:host=127.0.0.1;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Tạo database shop_giay
    $pdo->exec("CREATE DATABASE IF NOT EXISTS shop_giay CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Database 'shop_giay' đã sẵn sàng.\n";
    
    // Kết nối lại vào database shop_giay
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=shop_giay;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Đảm bảo bảng migrations đã được tạo (File 001)
    $migrationPath = __DIR__ . '/migrations';
    $files = glob($migrationPath . '/*.sql');
    sort($files);
    
    if (empty($files)) {
        echo "Không tìm thấy file migration nào trong thư mục database/migrations/.\n";
        exit;
    }
    
    // Chạy file 001 đầu tiên để chắc chắn có bảng migrations
    $firstFile = $files[0];
    if (basename($firstFile) === '001_create_migrations_table.sql') {
        $sql = file_get_contents($firstFile);
        $pdo->exec($sql);
    }
    
    // Lấy danh sách migration đã chạy
    $stmt = $pdo->query("SELECT migration_name FROM migrations");
    $executedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $runCount = 0;
    
    // Bắt đầu chạy các file chưa được apply
    foreach ($files as $file) {
        $filename = basename($file);
        
        // Bỏ qua file 001 nếu đã chạy ở trên nhưng vẫn insert vào bảng nếu cần
        if (!in_array($filename, $executedMigrations)) {
            echo "Đang chạy migration: $filename...\n";
            $sql = file_get_contents($file);
            
            try {
                // Chỉ chạy nội dung SQL nếu nó không trống
                if (trim($sql) !== '') {
                    $pdo->exec($sql);
                }
                
                $stmt = $pdo->prepare("INSERT INTO migrations (migration_name) VALUES (?)");
                $stmt->execute([$filename]);
                
                $runCount++;
                echo "-> Xong: $filename\n";
            } catch (Exception $e) {
                echo "-> LỖI KHI CHẠY MIGRATION $filename:\n";
                echo $e->getMessage() . "\n";
                echo "Hủy bỏ toàn bộ quá trình migration.\n";
                exit(1);
            }
        }
    }
    
    if ($runCount === 0) {
        echo "Không có migration nào mới. Database đã up-to-date.\n";
    } else {
        echo "Hoàn thành chạy $runCount file migration mới!\n";
    }
    
} catch (PDOException $e) {
    echo "Lỗi database: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Lỗi hệ thống: " . $e->getMessage() . "\n";
}
