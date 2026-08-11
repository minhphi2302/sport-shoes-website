<?php
/**
 * DEMO: Business Rules cho Biến Thể Sản Phẩm
 * 
 * Chạy file này để xem minh họa các quy tắc kiểm tra trùng lặp
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo: Quy tắc Biến Thể Sản Phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .rule-card { border-left: 4px solid; }
        .rule-card.success { border-left-color: #28a745; }
        .rule-card.error { border-left-color: #dc3545; }
        .code-box { background: #f8f9fa; padding: 1rem; border-radius: 8px; font-family: 'Courier New', monospace; font-size: 0.9rem; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold">📦 Quy tắc Biến Thể Sản Phẩm</h1>
            <p class="lead text-muted">Hệ thống kiểm tra trùng lặp thông minh cho Sport Shoes Shop</p>
        </div>

        <!-- Rule 1 -->
        <div class="card rule-card success mb-4 shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-check-circle-fill"></i> ✅ RULE 1: Biến thể khác nhau → Cho phép thêm</h5>
            </div>
            <div class="card-body">
                <p class="mb-3">Khi <strong>Model, Gender, Size, hoặc Color</strong> khác nhau → Được phép thêm</p>
                
                <div class="code-box mb-3">
                    <strong>Ví dụ hợp lệ:</strong><br>
                    ✅ Biến thể 1: Mặc định | Nam | 39 | Trắng | giảm 5% | SL 10<br>
                    ✅ Biến thể 2: Mặc định | Nam | 40 | Trắng | giảm 5% | SL 10 (KHÁC size)<br>
                    ✅ Biến thể 3: Mặc định | Nữ | 39 | Trắng | giảm 5% | SL 10 (KHÁC gender)<br>
                    ✅ Biến thể 4: Mặc định | Nam | 39 | Đen   | giảm 5% | SL 10 (KHÁC màu)
                </div>
                
                <div class="alert alert-success mb-0">
                    <strong>Kết quả:</strong> 4 biến thể được tạo thành công
                </div>
            </div>
        </div>

        <!-- Rule 2 -->
        <div class="card rule-card error mb-4 shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-x-circle-fill"></i> ❌ RULE 2: Trùng biến thể nhưng % giảm khác → BÁO LỖI</h5>
            </div>
            <div class="card-body">
                <p class="mb-3">Khi <strong>Model + Gender + Size + Color GIỐNG NHAU</strong> nhưng <strong>% giảm giá KHÁC NHAU</strong> → <span class="text-danger fw-bold">KHÔNG CHO PHÉP</span></p>
                
                <div class="code-box mb-3">
                    <strong>Ví dụ BỊ TỪ CHỐI:</strong><br>
                    ❌ Biến thể 1: Mặc định | Nam | 39 | Trắng | giảm <strong class="text-primary">5%</strong> (950k) | SL 10<br>
                    ❌ Biến thể 2: Mặc định | Nam | 39 | Trắng | giảm <strong class="text-danger">10%</strong> (900k) | SL 5
                </div>
                
                <div class="alert alert-danger mb-0">
                    <strong>Thông báo lỗi:</strong><br>
                    <code>Biến thể trùng lặp: Mặc định - Nam - 39 - Trắng đã tồn tại với mức giá khác (giảm 5% vs 10%). Không được có cùng một đôi giày với 2 mức giá khác nhau.</code>
                </div>
                
                <div class="mt-3 p-3 bg-light rounded">
                    <strong>🎯 Tại sao?</strong><br>
                    Tránh tình trạng cùng 1 đôi giày <em>Nam – Size 39 – Màu Trắng</em> nhưng lại có 2 mức giá khác nhau trong kho. Điều này sẽ gây nhầm lẫn cho khách hàng và nhân viên bán hàng.
                </div>
            </div>
        </div>

        <!-- Rule 3 -->
        <div class="card rule-card success mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-arrow-repeat"></i> ✅ RULE 3: Trùng hoàn toàn (cả % giảm) → Cộng dồn số lượng</h5>
            </div>
            <div class="card-body">
                <p class="mb-3">Khi <strong>Model + Gender + Size + Color + % Giảm giá</strong> GIỐNG NHAU HOÀN TOÀN → <span class="text-success fw-bold">Gộp thành 1 biến thể, cộng dồn số lượng</span></p>
                
                <div class="code-box mb-3">
                    <strong>Ví dụ:</strong><br>
                    📥 Input:<br>
                    &nbsp;&nbsp;• Biến thể 1: Mặc định | Nam | 39 | Trắng | giảm 5% | <strong>SL 10</strong><br>
                    &nbsp;&nbsp;• Biến thể 2: Mặc định | Nam | 39 | Trắng | giảm 5% | <strong>SL 5</strong><br><br>
                    
                    📤 Output:<br>
                    &nbsp;&nbsp;• Biến thể: Mặc định | Nam | 39 | Trắng | giảm 5% | <strong class="text-success">SL 15</strong> (10 + 5)
                </div>
                
                <div class="alert alert-info mb-0">
                    <strong>Lợi ích:</strong> Tự động gộp số lượng khi nhập từ nhiều nguồn khác nhau (VD: nhập hàng từ 2 lô khác nhau nhưng cùng đặc tính)
                </div>
            </div>
        </div>

        <!-- Rule 4 -->
        <div class="card rule-card success mb-4 shadow-sm">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="mb-0"><i class="bi bi-grid-3x3"></i> ✅ RULE 4: Mix Case - Xử lý hỗn hợp</h5>
            </div>
            <div class="card-body">
                <p class="mb-3">Hệ thống xử lý thông minh khi có cả biến thể trùng hoàn toàn và biến thể mới</p>
                
                <div class="code-box mb-3">
                    <strong>Ví dụ phức tạp:</strong><br>
                    📥 Input (3 biến thể):<br>
                    &nbsp;&nbsp;1️⃣ Mặc định | Nam | 39 | Trắng | 5% | SL 10<br>
                    &nbsp;&nbsp;2️⃣ Mặc định | Nam | 39 | Trắng | 5% | SL 5 &nbsp;<span class="badge bg-warning text-dark">TRÙNG #1 → Gộp</span><br>
                    &nbsp;&nbsp;3️⃣ Mặc định | Nam | 40 | Đen | 10% | SL 8 &nbsp;<span class="badge bg-success">MỚI → Thêm</span><br><br>
                    
                    📤 Output (2 biến thể):<br>
                    &nbsp;&nbsp;• Mặc định | Nam | 39 | Trắng | 5% | <strong>SL 15</strong> (gộp #1 + #2)<br>
                    &nbsp;&nbsp;• Mặc định | Nam | 40 | Đen | 10% | <strong>SL 8</strong> (mới)
                </div>
            </div>
        </div>

        <!-- Implementation Details -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-code-slash"></i> Chi tiết kỹ thuật</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold">📍 Vị trí code:</h6>
                        <ul class="list-unstyled">
                            <li><code>app/Models/Product.php</code></li>
                            <li>→ Method: <code>saveVariants()</code></li>
                            <li>→ Line: ~100-150</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">🔑 Key logic:</h6>
                        <ul class="list-unstyled">
                            <li>• Tính % giảm giá từ giá gốc</li>
                            <li>• Tạo unique key: <code>model|size|color</code></li>
                            <li>• So sánh % discount để phát hiện trùng</li>
                            <li>• Throw <code>ValidationException</code> nếu trùng với % khác</li>
                        </ul>
                    </div>
                </div>

                <hr>

                <h6 class="fw-bold">🧪 Test tự động:</h6>
                <div class="code-box">
                    <code>$ php test_variant_duplicate.php</code><br><br>
                    <span class="text-success">✅ All tests PASSED</span>
                </div>
            </div>
        </div>

        <!-- Business Impact -->
        <div class="alert alert-primary">
            <h5 class="alert-heading"><i class="bi bi-lightbulb-fill"></i> Lợi ích nghiệp vụ</h5>
            <ul class="mb-0">
                <li>✅ Tránh nhầm lẫn giá bán cùng 1 sản phẩm</li>
                <li>✅ Tự động gộp số lượng khi nhập hàng nhiều lần</li>
                <li>✅ Giảm lỗi thao tác của nhân viên kho</li>
                <li>✅ Khách hàng thấy giá nhất quán trên website</li>
                <li>✅ Tuân thủ business rules theo SRS</li>
            </ul>
        </div>

        <div class="text-center mt-4">
            <a href="/sport-shoes-website/admin/products/create" class="btn btn-primary btn-lg">
                <i class="bi bi-box-seam"></i> Thử ngay trên Admin Panel
            </a>
            <a href="test_variant_duplicate.php" class="btn btn-outline-secondary btn-lg ms-2">
                <i class="bi bi-terminal"></i> Chạy CLI Test
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
