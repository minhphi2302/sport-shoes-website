# TESTCASE - HỆ THỐNG BÁN GIÀY THỂ THAO

> **Tổng số testcase:** 95
> **Phân loại:** 48 dễ (50%), 28 trung bình (30%), 19 khó (20%)

---

## 1. MODULE: AUTHENTICATION & AUTHORIZATION (15 testcase)

### 1.1 Đăng ký tài khoản (DỄ: 3 testcase)

**TC-AUTH-001: Đăng ký thành công với thông tin hợp lệ**
- Mức độ: ⭐ DỄ
- Input: Email chưa tồn tại, password ≥ 6 ký tự, tên đầy đủ
- Expected: Tạo tài khoản customer, redirect về trang login với thông báo thành công
- Business Rule: USERS - Email duy nhất, mật khẩu ≥ 6 ký tự

**TC-AUTH-002: Đăng ký thất bại - Email đã tồn tại**
- Mức độ: ⭐ DỄ
- Input: Email đã có trong DB
- Expected: Hiện lỗi "Email đã được sử dụng"
- Business Rule: USERS - Email duy nhất

**TC-AUTH-003: Đăng ký thất bại - Password < 6 ký tự**
- Mức độ: ⭐ DỄ
- Input: Password có 5 ký tự
- Expected: Hiện lỗi "Mật khẩu phải có ít nhất 6 ký tự"
- Business Rule: USERS - mật khẩu tối thiểu 6 ký tự

### 1.2 Đăng nhập (DỄ: 4, TRUNG BÌNH: 2, KHÓ: 1 testcase)

**TC-AUTH-004: Đăng nhập thành công customer**
- Mức độ: ⭐ DỄ
- Input: Email + password đúng, role = customer, status = active
- Expected: Session được tạo với user_id, redirect về trang chủ, session_regenerate_id() được gọi
- Security Rule: Session regenerate sau login

**TC-AUTH-005: Đăng nhập thành công admin**
- Mức độ: ⭐ DỄ
- Input: Email + password đúng, role = admin
- Expected: Redirect về /admin/dashboard
- Business Rule: Chỉ 2 vai trò Admin/Customer

**TC-AUTH-006: Đăng nhập thất bại - Password sai**
- Mức độ: ⭐ DỄ
- Input: Email đúng, password sai
- Expected: Ghi nhận 1 lần sai vào DB, hiển thị cảnh báo chi tiết: "Email hoặc mật khẩu không đúng. Bạn đã nhập sai X/5 lần (còn lại Y lần thử)."
- Security Rule: Rate limit đăng nhập + Cảnh báo chi tiết số lần nhập sai

**TC-AUTH-007: Đăng nhập thất bại - Tài khoản bị khóa**
- Mức độ: ⭐ DỄ
- Input: Email đúng, password đúng, status = locked
- Expected: Hiện lỗi "Tài khoản bị khóa", không cho phép login
- Business Rule: USERS - status = locked không được đăng nhập

**TC-AUTH-008: Rate limit - Block sau 5 lần đăng nhập sai**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Đăng nhập sai 5 lần liên tiếp trong 15 phút
- Expected: Nhập sai lần 5 lập tức báo tạm khóa. Các lần thử tiếp theo bị block với thông báo "Bạn đã nhập sai 5 lần liên tiếp. Tài khoản bị tạm khóa đăng nhập trong 15 phút, vui lòng thử lại sau."
- Security Rule: Rate limit 5 lần/15 phút theo email

**TC-AUTH-009: Rate limit - Reset sau khi đăng nhập thành công**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Đăng nhập sai 3 lần, lần thứ 4 thành công
- Expected: Xóa login_attempts, cho phép tiếp tục
- Security Rule: Clear login_attempts sau login thành công

**TC-AUTH-010: Session timeout sau 30 phút không hoạt động**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: User đăng nhập, không tương tác gì trong > 30 phút
- Expected: Session tự động logout, redirect về login page
- Security Rule: Session timeout = 1800 giây (30 phút)

### 1.3 Quên mật khẩu & Reset (DỄ: 2, TRUNG BÌNH: 1 testcase)

**TC-AUTH-011: Gửi email reset password**
- Mức độ: ⭐ DỄ
- Input: Email tồn tại trong hệ thống
- Expected: Tạo reset_token, gửi email chứa link reset
- Tech: PHPMailer hoặc tương đương

**TC-AUTH-012: Reset password thành công**
- Mức độ: ⭐ DỄ
- Input: Token hợp lệ, chưa hết hạn, password mới ≥ 6 ký tự
- Expected: Cập nhật password (password_hash), xóa reset_token
- Security Rule: password_hash(), không lưu plaintext

**TC-AUTH-013: Reset password thất bại - Token hết hạn**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Token đã quá thời gian expires_at
- Expected: Hiện lỗi "Token đã hết hạn, vui lòng yêu cầu lại"
- Business Rule: Token có thời gian sống giới hạn

### 1.4 Authorization & Middleware (TRUNG BÌNH: 2 testcase)

**TC-AUTH-014: Truy cập trang admin khi chưa đăng nhập**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Chưa có session, truy cập /admin/products
- Expected: Redirect về /login hoặc 401 Unauthorized
- Architecture Rule: AdminController bắt buộc kiểm tra session + role

**TC-AUTH-015: Truy cập trang admin với role customer**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Đăng nhập role = customer, truy cập /admin/dashboard
- Expected: 403 Forbidden hoặc redirect về trang chủ
- Architecture Rule: AdminController check role admin

---

## 2. MODULE: CATEGORY & BRAND (12 testcase)

### 2.1 Quản lý Danh mục (DỄ: 4, TRUNG BÌNH: 1, KHÓ: 1 testcase)

**TC-CAT-001: Tạo danh mục mới thành công**
- Mức độ: ⭐ DỄ
- Input: Tên danh mục không trống, description
- Expected: INSERT vào categories, redirect về danh sách với thông báo thành công
- Business Rule: CATEGORIES - Tên không trống

**TC-CAT-002: Tạo danh mục thất bại - Tên trống**
- Mức độ: ⭐ DỄ
- Input: Tên = ""
- Expected: ValidationException, hiện lỗi "Tên danh mục không được để trống"
- Business Rule: CATEGORIES - Tên không trống

**TC-CAT-003: Sửa danh mục thành công**
- Mức độ: ⭐ DỄ
- Input: category_id hợp lệ, tên mới
- Expected: UPDATE categories, redirect với thông báo thành công
- Business Rule: N/A

**TC-CAT-004: Xóa danh mục thành công khi không còn sản phẩm**
- Mức độ: ⭐ DỄ
- Input: category_id không có sản phẩm nào tham chiếu
- Expected: DELETE categories thành công
- Business Rule: CATEGORIES - Không xóa nếu còn sản phẩm thuộc về nó

**TC-CAT-005: Xóa danh mục thất bại - Còn sản phẩm tham chiếu**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: category_id có ít nhất 1 sản phẩm
- Expected: CannotDeleteException, hiện lỗi "Không thể xóa danh mục đang có sản phẩm"
- Business Rule: CATEGORIES - Không xóa nếu còn sản phẩm thuộc về nó

**TC-CAT-006: SQL Injection attack vào tên danh mục**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: Tên = "'; DROP TABLE categories; --"
- Expected: Prepared statement ngăn chặn, tên được lưu như chuỗi thường
- Security Rule: 100% SQL dùng prepared statement

### 2.2 Quản lý Thương hiệu (DỄ: 3, TRUNG BÌNH: 1, KHÓ: 2 testcase)

**TC-BRAND-001: Tạo thương hiệu không có ảnh**
- Mức độ: ⭐ DỄ
- Input: Tên thương hiệu duy nhất, không upload ảnh
- Expected: INSERT brands với image_url = null
- Business Rule: BRANDS - Tên duy nhất

**TC-BRAND-002: Tạo thương hiệu có upload ảnh hợp lệ**
- Mức độ: ⭐ DỄ
- Input: Tên duy nhất, upload file jpg < 2MB
- Expected: Lưu ảnh vào public/uploads/brands/, đổi tên file (uniqid), INSERT brands với image_url
- Upload Rule: jpg/png/webp, max 2MB, đổi tên file

**TC-BRAND-003: Upload ảnh thất bại - File > 2MB**
- Mức độ: ⭐ DỄ
- Input: File ảnh 3MB
- Expected: ValidationException "Ảnh vượt quá 2MB"
- Upload Rule: Giới hạn dung lượng 2MB

**TC-BRAND-004: Upload ảnh thất bại - Định dạng không hợp lệ**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: File .exe đổi extension thành .jpg
- Expected: Kiểm tra MIME type thật (finfo_file), reject với lỗi "Định dạng không hợp lệ"
- Upload Rule: Kiểm tra MIME type thật, không tin đuôi file

**TC-BRAND-005: Tạo thương hiệu trùng tên**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: Tên đã tồn tại trong DB
- Expected: ValidationException "Tên thương hiệu đã tồn tại"
- Business Rule: BRANDS - Tên duy nhất

**TC-BRAND-006: XSS attack qua tên thương hiệu**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: Tên = "<script>alert('XSS')</script>"
- Expected: Lưu DB bình thường, khi hiển thị phải qua htmlspecialchars()
- Security Rule: Mọi output HTML qua htmlspecialchars()

---

## 3. MODULE: PRODUCT & VARIANTS (20 testcase)

### 3.1 Quản lý sản phẩm (DỄ: 6, TRUNG BÌNH: 3, KHÓ: 2 testcase)

**TC-PROD-001: Tạo sản phẩm thành công**
- Mức độ: ⭐ DỄ
- Input: SKU duy nhất, name, price > 0, category_id, brand_id, quantity ≥ 0
- Expected: INSERT products, SKU tự động UPPER, redirect thành công
- Business Rule: PRODUCTS - SKU duy nhất, giá > 0, tồn kho không âm

**TC-PROD-002: Tạo sản phẩm thất bại - SKU trùng**
- Mức độ: ⭐ DỄ
- Input: SKU đã tồn tại
- Expected: ValidationException "Mã sản phẩm đã tồn tại"
- Business Rule: PRODUCTS - SKU duy nhất

**TC-PROD-003: Tạo sản phẩm thất bại - Giá = 0**
- Mức độ: ⭐ DỄ
- Input: price = 0
- Expected: ValidationException "Giá bán phải lớn hơn 0"
- Business Rule: PRODUCTS - Giá bán > 0

**TC-PROD-004: Tạo sản phẩm thất bại - Giá âm**
- Mức độ: ⭐ DỄ
- Input: price = -100
- Expected: ValidationException "Giá bán phải lớn hơn 0"
- Business Rule: PRODUCTS - Giá bán > 0

**TC-PROD-005: Tạo sản phẩm thất bại - Tồn kho âm**
- Mức độ: ⭐ DỄ
- Input: quantity = -5
- Expected: ValidationException "Tồn kho không được âm"
- Business Rule: PRODUCTS - Tồn kho không âm

**TC-PROD-006: Tạo sản phẩm thành công - Giá khuyến mãi < Giá bán**
- Mức độ: ⭐ DỄ
- Input: price = 500000, sale_price = 450000
- Expected: INSERT thành công
- Business Rule: PRODUCTS - Giá khuyến mãi ≤ giá bán

**TC-PROD-007: Tạo sản phẩm thất bại - Giá khuyến mãi > Giá bán**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: price = 500000, sale_price = 600000
- Expected: ValidationException "Giá khuyến mãi không được lớn hơn giá bán"
- Business Rule: PRODUCTS - Giá khuyến mãi ≤ giá bán

**TC-PROD-008: Upload ảnh sản phẩm - File PHP giả dạng ảnh**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: File PHP có MIME type image/jpeg (bypass)
- Expected: Kiểm tra MIME type thật, reject, thư mục uploads không thực thi script
- Upload Rule: Kiểm tra MIME, thư mục chặn thực thi PHP

**TC-PROD-009: Phân trang danh sách sản phẩm**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: 50 sản phẩm trong DB, page = 2, perPage = 20
- Expected: Trả về 20 sản phẩm (offset 20), totalPages = 3
- Performance Rule: Bắt buộc phân trang LIMIT + OFFSET

**TC-PROD-010: Tìm kiếm sản phẩm theo tên**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: search = "Nike"
- Expected: WHERE name LIKE '%Nike%' OR sku = 'Nike', trả về kết quả khớp
- Business Rule: Tìm kiếm theo tên hoặc SKU chính xác

**TC-PROD-011: Index trên category_id và brand_id**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: Query sản phẩm theo category_id
- Expected: Query sử dụng index, EXPLAIN không FULL TABLE SCAN
- Performance Rule: Index bắt buộc cho cột FK

### 3.2 Quản lý biến thể (DỄ: 2, TRUNG BÌNH: 3, KHÓ: 4 testcase)

**TC-VAR-001: Thêm biến thể mới cho sản phẩm**
- Mức độ: ⭐ DỄ
- Input: product_id hợp lệ, model, size, color, price ≤ giá gốc, quantity ≥ 0
- Expected: INSERT product_variants, cập nhật tổng quantity vào products
- Business Rule: Biến thể price không vượt giá gốc

**TC-VAR-002: Thêm biến thể thất bại - Giá biến thể > Giá gốc**
- Mức độ: ⭐ DỄ
- Input: Giá gốc = 500k, giá biến thể = 600k
- Expected: Giá tự động giới hạn về 500k (theo code: if price > basePrice → price = basePrice)
- Business Rule: Biến thể price không vượt giá gốc

**TC-VAR-003: Thêm biến thể trùng lặp trong cùng lần submit**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Gửi form có 2 biến thể cùng model+size+color
- Expected: ValidationException "Biến thể trùng lặp"
- Business Rule: Không cho phép trùng model+size+màu

**TC-VAR-004: Cập nhật biến thể đã tồn tại**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Biến thể đã có trong DB, gửi form với quantity mới
- Expected: UPDATE quantity (ghi đè, không cộng dồn), cập nhật tổng quantity products
- Business Rule: Edit biến thể = ghi đè, không cộng dồn

**TC-VAR-005: Xóa biến thể không còn trong form**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: DB có 3 biến thể, form chỉ gửi 2
- Expected: DELETE biến thể thứ 3, cập nhật tổng quantity products
- Business Rule: Biến thể không có trong form = bị xóa

**TC-VAR-006: Tạo sản phẩm có nhiều biến thể, tổng quantity tự động**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: 3 biến thể (qty: 10, 15, 5)
- Expected: products.quantity = 30 (tổng tự động)
- Business Rule: Tổng quantity = tổng của các biến thể

**TC-VAR-007: Race condition - 2 request cùng lúc thêm biến thể trùng**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: 2 request đồng thời thêm cùng model+size+color
- Expected: 1 thành công, 1 bị ValidationException (phụ thuộc unique constraint hoặc check trong transaction)
- Concurrency Rule: Transaction bảo vệ tính nhất quán

**TC-VAR-008: SKU biến thể tự động hoặc custom**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: Một số biến thể có SKU custom, một số để trống
- Expected: SKU trống = null, SKU custom được lưu UPPER
- Business Rule: SKU biến thể optional

**TC-VAR-009: Hiển thị danh sách biến thể của sản phẩm**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: product_id có 10 biến thể
- Expected: Truy vấn JOIN hoặc subquery, hiển thị đầy đủ variant_sizes, variant_colors
- Architecture Rule: Không N+1 query

---

## 4. MODULE: CART & ORDER (25 testcase)

### 4.1 Giỏ hàng (DỄ: 5, TRUNG BÌNH: 2 testcase)

**TC-CART-001: Thêm sản phẩm vào giỏ hàng (chưa đăng nhập)**
- Mức độ: ⭐ DỄ
- Input: Chưa login, thêm product_id, quantity, size, color
- Expected: Lưu vào $_SESSION['cart']
- Tech: Session-based cart

**TC-CART-002: Thêm sản phẩm đã có trong giỏ - Cộng dồn quantity**
- Mức độ: ⭐ DỄ
- Input: Giỏ đã có product A (qty=2), thêm lại (qty=3)
- Expected: Tổng qty = 5
- Business Rule: Cùng product+size+color → cộng dồn

**TC-CART-003: Thêm sản phẩm vào giỏ - Quantity vượt tồn kho**
- Mức độ: ⭐ DỄ
- Input: Tồn kho = 5, thêm qty = 10
- Expected: Hiện cảnh báo "Không đủ hàng, chỉ còn 5 sản phẩm"
- Business Rule: Không cho thêm vượt tồn kho

**TC-CART-004: Cập nhật quantity trong giỏ**
- Mức độ: ⭐ DỄ
- Input: Giỏ có product A (qty=3), cập nhật qty=1
- Expected: Qty = 1 (ghi đè, không cộng dồn)
- Business Rule: Update = ghi đè

**TC-CART-005: Xóa sản phẩm khỏi giỏ**
- Mức độ: ⭐ DỄ
- Input: Giỏ có 3 sản phẩm, xóa 1
- Expected: Còn lại 2 sản phẩm
- Business Rule: N/A

**TC-CART-006: Session cart merge khi đăng nhập**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Chưa login có giỏ hàng 2 sản phẩm, sau đó login
- Expected: Session cart được giữ nguyên, không mất dữ liệu
- Tech: Session cart không bind với user_id

**TC-CART-007: XSS attack qua ghi chú đơn hàng**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: notes = "<script>alert('XSS')</script>"
- Expected: Lưu DB bình thường, hiển thị qua htmlspecialchars()
- Security Rule: Mọi output HTML qua htmlspecialchars()

### 4.2 Đặt hàng (DỄ: 4, TRUNG BÌNH: 3, KHÓ: 4 testcase)

**TC-ORDER-001: Đặt hàng thành công**
- Mức độ: ⭐ DỄ
- Input: User đăng nhập, giỏ có 2 sản phẩm, điền đầy đủ thông tin giao hàng
- Expected: Tạo order (status=pending), tạo order_details, trừ tồn kho products + product_variants
- Business Rule: ORDERS - Thuộc 1 khách hàng, có ít nhất 1 sản phẩm

**TC-ORDER-002: Đặt hàng thất bại - Giỏ hàng trống**
- Mức độ: ⭐ DỄ
- Input: Giỏ hàng = []
- Expected: Hiện lỗi "Giỏ hàng trống"
- Business Rule: ORDERS - Có ít nhất 1 sản phẩm

**TC-ORDER-003: Đặt hàng thất bại - Thông tin giao hàng thiếu**
- Mức độ: ⭐ DỄ
- Input: Tên rỗng hoặc SĐT rỗng
- Expected: ValidationException "Vui lòng nhập đầy đủ thông tin"
- Business Rule: Validate ở Controller

**TC-ORDER-004: Tính tổng tiền đơn hàng đúng**
- Mức độ: ⭐ DỄ
- Input: Sản phẩm A (giá 500k, qty=2), sản phẩm B (giá 300k, qty=1)
- Expected: subtotal = 1,300k, shipping_fee (nếu COD < threshold), total = subtotal + shipping
- Business Rule: ORDERS - Tổng tiền = tổng chi tiết + phí ship

**TC-ORDER-005: Giá lưu tại thời điểm đặt hàng**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Sản phẩm giá 500k khi đặt, sau đó admin đổi thành 600k
- Expected: order_details.unit_price = 500k (không thay đổi)
- Business Rule: ORDER_DETAILS - Đơn giá lưu tại thời điểm đặt hàng

**TC-ORDER-006: Phí ship COD - Miễn phí nếu >= 1 triệu**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Tổng đơn = 1,200k, COD
- Expected: shipping_fee = 0
- Business Rule: FREE_COD_THRESHOLD = 1,000,000

**TC-ORDER-007: Phí ship COD - Thu 30k nếu < 1 triệu**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Tổng đơn = 800k, COD
- Expected: shipping_fee = 30,000
- Business Rule: COD_FEE = 30,000

**TC-ORDER-008: Transaction rollback khi trừ tồn kho thất bại**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: Giỏ có 2 sản phẩm, sản phẩm thứ 2 không đủ tồn kho
- Expected: Rollback transaction, không tạo order, không trừ tồn kho sản phẩm thứ 1
- Transaction Rule: Mọi thao tác nhiều bước phải atomic

**TC-ORDER-009: Race condition - 2 user cùng đặt sản phẩm cuối cùng**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: Tồn kho = 1, 2 request đồng thời đặt quantity = 1
- Expected: 1 thành công, 1 throw InsufficientStockException
- Concurrency Rule: UPDATE có điều kiện (quantity >= :qty), không "check rồi update"

**TC-ORDER-010: Trừ tồn kho đúng cả products và product_variants**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: Đặt biến thể (size 42, màu đỏ, qty=2)
- Expected: products.quantity -=2, product_variants (size 42, màu đỏ).quantity -=2
- Business Rule: Đồng bộ tồn kho products và variants

**TC-ORDER-011: N+1 query khi load danh sách đơn hàng**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: Load 10 đơn hàng + thông tin khách hàng
- Expected: 1 query JOIN users, không query riêng cho từng đơn
- Performance Rule: Không N+1 query

### 4.3 Quản lý đơn hàng (DỄ: 3, TRUNG BÌNH: 2, KHÓ: 2 testcase)

**TC-ORDER-012: Admin xem danh sách đơn hàng**
- Mức độ: ⭐ DỄ
- Input: Admin login, truy cập /admin/orders
- Expected: Hiển thị danh sách orders + phân trang
- Authorization Rule: Chỉ admin

**TC-ORDER-013: Admin xem chi tiết đơn hàng**
- Mức độ: ⭐ DỄ
- Input: Admin click vào order_id
- Expected: Hiển thị thông tin order + order_details (JOIN products)
- Architecture Rule: N/A

**TC-ORDER-014: Admin cập nhật trạng thái: Pending → Confirmed**
- Mức độ: ⭐ DỄ
- Input: Đơn hàng status = pending, admin chuyển sang confirmed
- Expected: UPDATE orders.status = confirmed thành công
- Business Rule: ORDERS - Chỉ chuyển Pending → Confirmed → Completed hoặc Pending → Cancelled

**TC-ORDER-015: Admin cập nhật trạng thái: Confirmed → Completed**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Đơn hàng status = confirmed, admin chuyển sang completed
- Expected: UPDATE thành công
- Business Rule: Transition hợp lệ

**TC-ORDER-016: Admin cập nhật trạng thái thất bại - Transition không hợp lệ**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Đơn hàng status = completed, admin chuyển sang pending
- Expected: InvalidOrderTransitionException "Không thể chuyển từ completed sang pending"
- Business Rule: ORDERS - Trạng thái chỉ chuyển theo luồng quy định

**TC-ORDER-017: Hủy đơn hàng - Hoàn lại tồn kho**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: Đơn hàng status = pending, admin chuyển sang cancelled
- Expected: UPDATE status, hoàn lại quantity cho products + product_variants (transaction)
- Business Rule: Hủy đơn = hoàn tồn kho

**TC-ORDER-018: Race condition khi hủy đơn hàng**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: 2 admin cùng lúc hủy 1 đơn hàng
- Expected: Chỉ 1 request thành công, hoàn tồn kho 1 lần, request còn lại fail hoặc idempotent
- Concurrency Rule: Transaction + kiểm tra status trước khi hủy

---

## 5. MODULE: USER & PROFILE (10 testcase)

### 5.1 Quản lý thông tin cá nhân (DỄ: 5, TRUNG BÌNH: 1 testcase)

**TC-USER-001: Xem trang tài khoản**
- Mức độ: ⭐ DỄ
- Input: User đăng nhập, truy cập /account
- Expected: Hiển thị thông tin user + danh sách đơn hàng của user
- Business Rule: N/A

**TC-USER-002: Cập nhật thông tin cá nhân (name, phone, address)**
- Mức độ: ⭐ DỄ
- Input: Tên mới, SĐT, địa chỉ (AJAX endpoint)
- Expected: UPDATE users, cập nhật $_SESSION['user'], trả về JSON success
- Tech: AJAX, JSON response

**TC-USER-003: Đổi mật khẩu thành công**
- Mức độ: ⭐ DỄ
- Input: Mật khẩu hiện tại đúng, mật khẩu mới ≥ 6 ký tự, confirm khớp
- Expected: UPDATE password (password_hash), hiện thông báo thành công
- Security Rule: password_verify() trước khi đổi

**TC-USER-004: Đổi mật khẩu thất bại - Mật khẩu hiện tại sai**
- Mức độ: ⭐ DỄ
- Input: Current password sai
- Expected: Hiện lỗi "Mật khẩu hiện tại không đúng"
- Security Rule: password_verify() bắt buộc

**TC-USER-005: Đổi mật khẩu thất bại - Confirm không khớp**
- Mức độ: ⭐ DỄ
- Input: new_password ≠ confirm_password
- Expected: Hiện lỗi "Mật khẩu xác nhận không khớp"
- Validate Rule: Defense in depth

**TC-USER-006: Xóa tài khoản customer**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Customer nhập password đúng, xác nhận xóa
- Expected: Transaction: DELETE login_attempts → order_details → orders → users, session_destroy()
- Business Rule: Không cho xóa admin, phải verify password

### 5.2 Quản lý khách hàng (Admin) (DỄ: 2, TRUNG BÌNH: 1, KHÓ: 1 testcase)

**TC-USER-007: Admin xem danh sách khách hàng**
- Mức độ: ⭐ DỄ
- Input: Admin login, truy cập /admin/customers
- Expected: Hiển thị danh sách users (role = customer) + phân trang
- Authorization Rule: Chỉ admin

**TC-USER-008: Admin khóa/mở khóa tài khoản khách hàng**
- Mức độ: ⭐ DỄ
- Input: Admin click toggle status của customer
- Expected: UPDATE users.status (active ↔ locked)
- Business Rule: USERS - status = locked không được đăng nhập

**TC-USER-009: Admin không thể tự khóa chính mình**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Admin cố khóa tài khoản của chính mình
- Expected: Hiện lỗi "Bạn không thể tự khóa chính mình"
- Business Rule: Ngăn admin khóa chính mình

**TC-USER-010: Tự động xóa khách hàng không hoạt động**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: Customer status = locked hoặc không có đơn hàng trong 1 năm
- Expected: Tự động DELETE khi admin truy cập /admin/customers (autoDeleteInactiveCustomers)
- Business Rule: Tự động dọn dẹp khách hàng không hoạt động

---

## 6. MODULE: ADMIN DASHBOARD & REPORTS (5 testcase)

**TC-DASH-001: Hiển thị tổng doanh thu tháng**
- Mức độ: ⭐ DỄ
- Input: Admin truy cập /admin/dashboard
- Expected: SELECT SUM(total_amount) FROM orders WHERE status = completed AND tháng hiện tại
- Business Rule: Chỉ tính đơn hoàn thành

**TC-DASH-002: Hiển thị số đơn hàng hôm nay**
- Mức độ: ⭐ DỄ
- Input: Admin truy cập /admin/dashboard
- Expected: SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()
- Business Rule: N/A

**TC-DASH-003: Hiển thị danh sách đơn hàng pending mới nhất**
- Mức độ: ⭐ DỄ
- Input: Admin truy cập /admin/dashboard
- Expected: SELECT * FROM orders WHERE status = pending ORDER BY created_at DESC LIMIT 5
- Business Rule: N/A

**TC-DASH-004: Authorization - Customer không truy cập được dashboard**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Customer login, truy cập /admin/dashboard
- Expected: 403 Forbidden hoặc redirect
- Authorization Rule: AdminController check role

**TC-DASH-005: Performance - Dashboard load < 1s**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: DB có 10,000 đơn hàng
- Expected: Dashboard load < 1 giây (sử dụng index, query tối ưu)
- Performance Rule: Index, LIMIT query

---

## 7. MODULE: SECURITY & ERROR HANDLING (8 testcase)

### 7.1 SQL Injection (KHÓ: 3 testcase)

**TC-SEC-001: SQL Injection qua search query**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: search = "' OR 1=1 --"
- Expected: Prepared statement ngăn chặn, trả về kết quả tìm kiếm chuỗi đó
- Security Rule: 100% SQL dùng prepared statement

**TC-SEC-002: SQL Injection qua email login**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: email = "admin' OR '1'='1"
- Expected: Prepared statement ngăn chặn, login thất bại
- Security Rule: 100% SQL dùng prepared statement

**TC-SEC-003: Blind SQL Injection qua order_id**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: /admin/orders/1' AND SLEEP(5)--
- Expected: Prepared statement bảo vệ, không delay
- Security Rule: 100% SQL dùng prepared statement

### 7.2 XSS (Cross-Site Scripting) (TRUNG BÌNH: 2, KHÓ: 1 testcase)

**TC-SEC-004: XSS qua tên sản phẩm**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Tên sản phẩm = "<img src=x onerror=alert('XSS')>"
- Expected: Lưu DB bình thường, khi hiển thị qua htmlspecialchars() → hiện chuỗi text
- Security Rule: Mọi output HTML qua htmlspecialchars()

**TC-SEC-005: XSS qua địa chỉ giao hàng**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Địa chỉ = "<script>document.location='http://evil.com'</script>"
- Expected: Lưu DB, hiển thị qua htmlspecialchars()
- Security Rule: Mọi output HTML qua htmlspecialchars()

**TC-SEC-006: Stored XSS qua description**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: Admin tạo sản phẩm, description chứa script
- Expected: Lưu DB, view hiển thị qua htmlspecialchars() hoặc HTML Purifier
- Security Rule: Output escape bắt buộc

### 7.3 File Upload & Session (TRUNG BÌNH: 1, KHÓ: 1 testcase)

**TC-SEC-007: Session cookie flags**
- Mức độ: ⭐⭐ TRUNG BÌNH
- Input: Đăng nhập, check cookie session
- Expected: HttpOnly=true, Secure=true (nếu HTTPS), SameSite=Lax
- Security Rule: Session cookie bắt buộc set flags

**TC-SEC-008: Upload file thực thi**
- Mức độ: ⭐⭐⭐ KHÓ
- Input: Upload file .php vào thư mục uploads/products/, truy cập trực tiếp
- Expected: Thư mục có .htaccess chặn thực thi PHP, hoặc file nằm ngoài webroot
- Upload Rule: Thư mục chặn thực thi script

---

## TỔNG KẾT

### Phân bố theo mức độ:
- **⭐ DỄ (48 testcase - 50%)**: TC cơ bản, happy path, validate đơn giản
- **⭐⭐ TRUNG BÌNH (28 testcase - 30%)**: Business rule phức tạp, authorization, performance cơ bản
- **⭐⭐⭐ KHÓ (19 testcase - 20%)**: Race condition, transaction, security, concurrency

### Phân bố theo module:
1. Authentication & Authorization: 15 TC
2. Category & Brand: 12 TC
3. Product & Variants: 20 TC
4. Cart & Order: 25 TC (module quan trọng nhất)
5. User & Profile: 10 TC
6. Admin Dashboard: 5 TC
7. Security & Error Handling: 8 TC

### Ưu tiên test:
1. **CRITICAL**: TC-ORDER-008, TC-ORDER-009 (transaction + race condition)
2. **HIGH**: TC-AUTH-010, TC-SEC-001, TC-SEC-002, TC-SEC-003 (security)
3. **MEDIUM**: Các TC về business rule validation
4. **LOW**: Các TC happy path đơn giản

### Checklist bắt buộc trước khi release:
- [ ] Tất cả TC security (SEC-001 đến SEC-008) phải pass
- [ ] Tất cả TC transaction (ORDER-008, ORDER-017, ORDER-018) phải pass
- [ ] Tất cả TC race condition (ORDER-009, VAR-007) phải pass
- [ ] Rate limit (AUTH-008, AUTH-009) phải hoạt động
- [ ] Session timeout (AUTH-010) phải hoạt động
- [ ] Upload file (BRAND-004, PROD-008, SEC-008) phải bảo mật

---

**Ghi chú:**
- Testcase này dựa trên phân tích source code thực tế
- Tuân thủ business rules trong AGENTS.md mục 10
- Ưu tiên security theo checklist mục 14 (Security Reviewer)
- Tất cả testcase phải chạy trên môi trường có MySQL InnoDB, PHP 8.1+
