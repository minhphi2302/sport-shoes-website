# Shop Giày AI — Project Rules & Conventions

> Website bán giày thể thao
> PHP Core, MySQL, MVC, Bootstrap

---

## 1. Tech Stack

| Layer | Công nghệ | Ghi chú |
|-------|-----------|---------|
| Ngôn ngữ backend | **PHP Core (không framework)** | PHP 8.x |
| Database | **MySQL** | Quan hệ, 6 bảng chính: Users, Categories, Brands, Products, Orders, Order_Details |
| Kiến trúc | **MVC (Model – View – Controller)** | Tự viết Router + Core, không dùng Laravel/CodeIgniter |
| Frontend | **HTML5, CSS3, JavaScript, Bootstrap 5** | Render qua View (server-side), không SPA |
| Quản lý phụ thuộc | **Composer** (nếu cần, ví dụ PHPMailer) | Không bắt buộc |
| Testing | **PHPUnit** | Test Model/business rules |
| Local env | **Docker Compose** (khuyến nghị) hoặc XAMPP/Laragon | Đảm bảo PHP + MySQL version giống nhau giữa 3 máy |

---

## 2. Quy tắc code

### 2.1 Naming — Backend (PHP)

```php
// Files & classes: PascalCase, tên file = tên class
ProductController.php
class ProductController { ... }

OrderDetail.php
class OrderDetail { ... }

// Methods & variables: camelCase
public function getProductById(int $productId): ?array { ... }
$totalAmount = 0;

// Constants: UPPER_SNAKE_CASE
define('MIN_PASSWORD_LENGTH', 6);
const ORDER_STATUS_PENDING = 'pending';

// Database: snake_case cho tên bảng và cột
// products, order_details, category_id, created_at
```

### 2.2 Naming — Frontend (View/Bootstrap)

```
// View files: snake_case, đặt theo action, nhóm theo module
views/client/product_list.php
views/client/product_detail.php
views/admin/category_manage.php

// CSS class tự đặt thêm (ngoài Bootstrap): kebab-case, prefix theo module
.product-card { ... }
.order-status-badge { ... }

// JS: camelCase cho biến/hàm, file JS đặt theo module
// public/assets/js/cart.js
function addToCart(productId) { ... }
```

### 2.3 Docstring / Comment (PHPDoc bắt buộc cho method public trong Model & Controller)

```php
/**
 * Tính tổng tiền đơn hàng dựa trên chi tiết đơn hàng.
 *
 * Subtotal = Quantity x Price, tổng các dòng trừ đi giảm giá (nếu có).
 *
 * @param int $orderId Mã đơn hàng.
 * @return float Tổng tiền cuối cùng.
 * @throws OrderNotFoundException Khi không tìm thấy đơn hàng.
 */
public function calculateTotalAmount(int $orderId): float
{
    ...
}
```

### 2.4 Comments

- Comment **tại sao**, không comment **cái gì** (code tự nói lên nó làm gì)
- Mọi ràng buộc nghiệp vụ (business rule) phải có comment trích dẫn quy tắc tương ứng
- Comment nghiệp vụ bằng **tiếng Việt**, comment kỹ thuật thuần (thuật toán, thư viện) bằng tiếng Anh

```php
// Giá khuyến mãi không được lớn hơn giá bán (business rule PRODUCTS)
if ($salePrice > $price) {
    throw new InvalidProductDataException('sale_price', 'Giá khuyến mãi vượt giá bán');
}

// Dùng prepared statement để tránh SQL injection
$stmt = $pdo->prepare('SELECT * FROM products WHERE product_id = :id');
```

### 2.5 Error Handling (Backend)

- **KHÔNG** dùng `@` để nuốt lỗi PHP, không `catch (Exception $e) {}` rỗng
- Dữ liệu không hợp lệ → `throw` custom Exception (ví dụ `InvalidProductDataException`, `InsufficientStockException`), **KHÔNG** âm thầm trả `false`/`null` giả vờ thành công
- Validate input ở **Controller** trước khi gọi Model; Model validate lại business rule trước khi ghi DB (defense in depth)
- Mọi truy vấn DB **bắt buộc dùng prepared statement** (PDO hoặc mysqli với bind param) — không nối chuỗi SQL trực tiếp từ input người dùng

```php
// ✅ Đúng
if ($quantity <= 0) {
    throw new InvalidOrderDetailException('quantity', 'Số lượng phải lớn hơn 0');
}

// ❌ Sai — nuốt lỗi
if ($quantity <= 0) {
    $quantity = 1; // âm thầm sửa, che giấu lỗi input
}

// ❌ Sai — SQL injection risk
$sql = "SELECT * FROM products WHERE name = '" . $_GET['name'] . "'";

// ✅ Đúng
$stmt = $pdo->prepare('SELECT * FROM products WHERE product_name LIKE :name');
$stmt->execute(['name' => '%' . $productName . '%']);
```

### 2.6 View / Bootstrap

```php
<!-- Mỗi view chỉ hiển thị dữ liệu Controller truyền vào, KHÔNG query DB -->
<div class="card product-card">
  <h5><?= htmlspecialchars($product['product_name']) ?></h5>
  <span class="badge bg-<?= $statusClass ?>"><?= $statusLabel ?></span>
</div>
```

- **Luôn** dùng `htmlspecialchars()` (hoặc tương đương) khi in dữ liệu người dùng ra HTML để tránh XSS
- Ưu tiên component có sẵn của Bootstrap (card, badge, modal, table) trước khi viết CSS custom
- Style riêng của dự án đặt trong `public/assets/css/custom.css`, không sửa trực tiếp Bootstrap gốc

---

## 3. Git Workflow

### 3.1 Branch Strategy

```
main                       ← luôn chạy được, chỉ merge từ develop khi release
├── develop                ← nhánh tích hợp chung
│   ├── feat/<module>            # Tính năng mới
│   ├── fix/<module>-<bug>       # Sửa lỗi
│   └── refactor/<scope>         # Refactor
```

### 3.2 Branch Naming

```
feat/dangky-dangnhap        # Đăng ký / đăng nhập
feat/quanly-sanpham         # Admin quản lý sản phẩm
feat/gio-hang               # Giỏ hàng
feat/dat-hang               # Đặt hàng
feat/quanly-donhang         # Quản lý đơn hàng
feat/dashboard              # Thống kê
fix/cart-tong-tien-sai      # Sửa lỗi tính sai tổng tiền giỏ hàng
```

### 3.3 Commit Message

Format: `<type>(<module>): <mô tả ngắn>`

```
feat(auth): thêm chức năng đăng nhập
feat(product): CRUD sản phẩm cho admin
feat(order): tạo đơn hàng và trừ tồn kho
fix(cart): sửa lỗi tính sai tổng tiền
refactor(order): tách logic tính subtotal ra Model
test(product): thêm test cho ràng buộc giá khuyến mãi
chore: cập nhật schema.sql
```

Types: `feat`, `fix`, `refactor`, `docs`, `test`, `chore`

### 3.4 Pull Request

- Mỗi PR vào `develop` phải có **ít nhất 1 người khác review approve** (không tự merge PR của chính mình)
- PR description nêu: thay đổi gì, tại sao, test thế nào (mô tả các case đã thử)
- Không merge nếu code lỗi cú pháp hoặc chưa test luồng chính
- Nếu đổi `database/schema.sql` → ghi rõ trong PR để cả nhóm biết cần re-import DB local

### 3.5 Đồng bộ môi trường & format code

- Dùng chung `.editorconfig` (indent, charset) để VS Code / PhpStorm tạo code giống nhau
- Chạy PHP CS Fixer (hoặc PHP_CodeSniffer) trước khi commit để format nhất quán, kể cả code do AI coding assistant (Copilot/Cursor/Claude...) sinh ra
- Không commit `.env`, chỉ commit `.env.example`
- Không commit `vendor/` nếu dùng Composer (thêm vào `.gitignore`)

---

## 4. Quy tắc kiến trúc

### 4.1 Ranh giới MVC

- **View** không được gọi Model hay truy vấn DB trực tiếp — chỉ nhận biến từ Controller
- **Controller** không viết SQL trực tiếp — chỉ gọi method public của Model, nhận kết quả, truyền cho View
- **Model** không `echo`/render HTML, không biết gì về Controller hay View
- Giao tiếp giữa các Controller/Model của module khác nhau qua **method public**, không truy cập thuộc tính/biến private của class khác

```php
// ✅ Đúng
$products = (new ProductModel())->getAllActive();

// ❌ Sai — Controller tự viết SQL
$products = $pdo->query("SELECT * FROM products")->fetchAll();
```

### 4.2 Data Flow

```
[Request] → Router → Controller (validate) → Model (business rule + DB) → Controller → View → [Response HTML]
```

- Dữ liệu chảy một chiều: Controller → Model → Controller → View
- View không được gọi ngược lại Controller trong lúc render (chỉ gửi request mới qua form/link)

### 4.3 Cấu hình

- Toàn bộ hằng số, ngưỡng (VD: `MIN_PASSWORD_LENGTH`, số sản phẩm/trang) đặt trong `config/config.php`
- Thông tin kết nối DB đặt trong `.env` (không commit), đọc qua `config/database.php`
- **KHÔNG** hardcode magic number rải rác trong Controller/Model

### 4.4 Bảo mật (bắt buộc)

- Mọi truy vấn SQL dùng prepared statement (PDO/mysqli bind param) — không nối chuỗi từ `$_GET`/`$_POST`
- Mật khẩu lưu bằng `password_hash()`, so sánh bằng `password_verify()` — không lưu plaintext, không tự viết hàm hash
- Mọi dữ liệu người dùng in ra HTML phải qua `htmlspecialchars()` để tránh XSS
- Route Admin phải kiểm tra session/role trước khi xử lý (không dựa vào việc ẩn link trên UI)
- CSRF token cho các form thay đổi dữ liệu (thêm/sửa/xóa)

---

## 5. Business Rules bắt buộc tuân thủ (theo SRS mục 3.2.3)

### USERS
- Email duy nhất, mật khẩu tối thiểu 6 ký tự
- Chỉ 2 vai trò: Admin, Customer
- Tài khoản bị khóa (`status = locked`) không được đăng nhập

### CATEGORIES / BRANDS
- Tên không được để trống, tên thương hiệu phải duy nhất
- Không xóa danh mục/thương hiệu nếu còn sản phẩm thuộc về nó — kiểm tra ràng buộc này ở Model trước khi `DELETE`

### PRODUCTS
- Mã sản phẩm duy nhất
- Giá bán > 0; giá khuyến mãi ≤ giá bán
- Số lượng tồn kho không được âm
- Mỗi sản phẩm thuộc đúng 1 danh mục và 1 thương hiệu (FK bắt buộc)

### ORDERS
- Mỗi đơn hàng thuộc về 1 khách hàng, có ít nhất 1 sản phẩm
- Tổng tiền = tổng chi tiết đơn hàng − giảm giá
- Trạng thái chỉ chuyển theo đúng luồng: `Pending → Confirmed → Completed` hoặc `Pending → Cancelled` — **không cho phép nhảy trạng thái khác trong Model**

### ORDER_DETAILS
- Số lượng mua > 0
- Đơn giá lưu tại thời điểm đặt hàng, không đổi theo giá sản phẩm sau này
- `Subtotal = Quantity × Price`

---

## 6. Testing Rules

- Mỗi Model chứa business rule (Product, Order, OrderDetail) **bắt buộc** có test PHPUnit cho: happy path, giá trị biên (giá = 0, số lượng = 0, tồn kho âm), trường hợp lỗi
- Test đặt trong `tests/`, không dùng dữ liệu production, dùng fixture riêng
- Tên test: `test_<method>_<scenario>`

```php
public function test_calculateTotalAmount_appliesDiscountCorrectly(): void { ... }
public function test_updateOrderStatus_rejectsInvalidTransition(): void { ... }
public function test_createProduct_rejectsSalePriceHigherThanPrice(): void { ... }
```

- Trước khi merge PR: chạy thủ công luồng chính liên quan (VD: sửa Cart → test lại thêm/sửa/xóa/đặt hàng)

---

## 7. Quy tắc UI (Bootstrap)

- Ngôn ngữ hiển thị: **Tiếng Việt**
- Trạng thái đơn hàng dùng màu Bootstrap rõ ràng:

| Trạng thái | Màu (Bootstrap) | Label |
|---|---|---|
| Pending | `bg-secondary` | Chờ xử lý |
| Confirmed | `bg-info` | Đã xác nhận |
| Completed | `bg-success` | Hoàn thành |
| Cancelled | `bg-danger` | Đã hủy |

- Responsive: dùng grid Bootstrap (`container`, `row`, `col-*`), test tối thiểu ở kích thước mobile và desktop
- Form luôn có validate phía client (HTML5 required/pattern) **và** validate lại phía server (không tin client)

---

## 8. Agent Role Definitions

> Khi nhiều người trong nhóm sử dụng nhiều AI coding assistant khác nhau (Copilot, Cursor, Claude, Gemini...), mỗi agent cần được gán đúng **vai trò (role)** để sinh code nhất quán, đúng chuẩn và phù hợp với nhiệm vụ đang thực hiện. Người dùng **phải khai báo role** ở đầu cuộc hội thoại hoặc trong system prompt của AI assistant.

### 8.1 Danh sách Role

| Role | Emoji | Mô tả ngắn | Ai nên dùng |
|---|---|---|---|
| PHP Architect | 🏗️ | Thiết kế kiến trúc, Core, schema DB | Minh (Trưởng nhóm) |
| Security Reviewer | 🔒 | Rà soát bảo mật, phát hiện lỗ hổng | Tất cả (khi review) |
| Feature Developer | 🛒 | Viết logic nghiệp vụ (Model/Controller/View) | Hưng, Thảo |
| QA Engineer | 🧪 | Viết test, tìm edge case, kiểm thử | Thảo |
| Code Reviewer | 📝 | Review PR, kiểm tra convention | Tất cả (khi review PR) |
| UI Developer | 🎨 | Viết View, Bootstrap, JS, responsive | Hưng |

### 8.2 Chi tiết từng Role

#### 🏗️ PHP Architect

**Mục tiêu:** Đảm bảo kiến trúc dự án **dễ mở rộng, tách lớp rõ ràng, không tạo nợ kỹ thuật**.

Khi nhận role này, agent phải:
- Luôn nghĩ "nếu thêm 1 module mới, cần sửa bao nhiêu file?" — càng ít càng tốt
- Ưu tiên **Dependency Injection** thay vì hardcode `new ClassName()` rải rác
- Thiết kế interface/abstract class trước khi viết implementation
- Không cho phép Controller chứa logic nghiệp vụ — phải đẩy xuống Model
- Khi thay đổi `database/schema.sql`, phải cập nhật tài liệu và thông báo nhóm
- Đặt mọi hằng số, ngưỡng trong `config/config.php` — không hardcode magic number

```
Prompt mẫu: "Bạn là PHP Architect cho dự án Shop Giày (PHP Core MVC). 
Hãy thiết kế [module/feature] với ưu tiên: mở rộng dễ, tách lớp rõ, bảo mật cao."
```

---

#### 🔒 Security Reviewer

**Mục tiêu:** Chủ động phát hiện và ngăn chặn **mọi lỗ hổng bảo mật** trước khi code được merge.

Khi nhận role này, agent phải:
- Quét mọi truy vấn SQL → đảm bảo 100% dùng **prepared statement** (PDO bind param)
- Kiểm tra mọi output ra HTML → đảm bảo có `htmlspecialchars()` chống XSS
- Kiểm tra mật khẩu → phải dùng `password_hash()` / `password_verify()`
- Kiểm tra mọi route Admin → phải có kiểm tra session + role, không dựa vào ẩn link UI
- Kiểm tra mọi form thay đổi dữ liệu → phải có CSRF token
- **KHÔNG** bỏ qua lỗi bằng `@` hoặc `catch` rỗng
- Đánh dấu mức độ nghiêm trọng: 🔴 Critical / 🟡 Warning / 🟢 Info

```
Prompt mẫu: "Bạn là Security Reviewer cho dự án PHP. 
Hãy review đoạn code/file sau và liệt kê mọi lỗ hổng bảo mật theo mức độ nghiêm trọng."
```

---

#### 🛒 Feature Developer

**Mục tiêu:** Viết code nghiệp vụ **đúng business rule**, đúng convention, đúng ranh giới MVC.

Khi nhận role này, agent phải:
- Đọc kỹ business rules ở mục 5 trước khi viết bất kỳ dòng code nào
- Validate input ở **Controller**, validate business rule ở **Model** (defense in depth)
- Dữ liệu không hợp lệ → `throw` custom Exception, **KHÔNG** âm thầm trả `false`/`null`
- Tuân thủ naming: PascalCase cho class, camelCase cho method/variable, snake_case cho DB
- Mọi method public trong Model/Controller **bắt buộc** có PHPDoc
- Comment nghiệp vụ bằng **tiếng Việt**, comment kỹ thuật bằng tiếng Anh
- View chỉ nhận biến từ Controller — **KHÔNG** query DB trong View

```
Prompt mẫu: "Bạn là Feature Developer cho dự án Shop Giày (PHP Core MVC).
Hãy viết [chức năng] tuân thủ business rules trong AGENTS.md mục 5."
```

---

#### 🧪 QA Engineer

**Mục tiêu:** Viết test **bao phủ mọi kịch bản**, đặc biệt là edge case và trường hợp lỗi.

Khi nhận role này, agent phải:
- Viết PHPUnit test cho mọi Model chứa business rule
- Bao gồm 3 loại test: **happy path**, **giá trị biên** (boundary), **trường hợp lỗi** (error)
- Tên test: `test_<method>_<scenario>` (VD: `test_createProduct_rejectsSalePriceHigherThanPrice`)
- Test đặt trong `tests/`, dùng fixture riêng, **KHÔNG** dùng dữ liệu production
- Chủ động nghĩ ra kịch bản mà developer có thể bỏ sót:
  - Giá = 0, giá âm, giá khuyến mãi > giá bán
  - Số lượng = 0, tồn kho âm sau khi trừ
  - Email trùng, mật khẩu < 6 ký tự
  - Chuyển trạng thái đơn hàng sai luồng (VD: Completed → Pending)
  - Xóa danh mục/thương hiệu khi còn sản phẩm con

```
Prompt mẫu: "Bạn là QA Engineer cho dự án Shop Giày.
Hãy viết PHPUnit test cho [Model/method], bao gồm happy path, boundary và error cases."
```

---

#### 📝 Code Reviewer

**Mục tiêu:** Đóng vai **reviewer nghiêm khắc**, kiểm tra code trước khi merge vào `develop`.

Khi nhận role này, agent phải kiểm tra theo checklist sau:

**Convention:**
- [ ] Tên file/class/method/variable đúng quy tắc (mục 2.1, 2.2)
- [ ] PHPDoc đầy đủ cho method public (mục 2.3)
- [ ] Comment giải thích **tại sao**, không giải thích **cái gì** (mục 2.4)

**Kiến trúc MVC:**
- [ ] View không gọi Model / không query DB trực tiếp
- [ ] Controller không viết SQL trực tiếp
- [ ] Model không echo/render HTML
- [ ] Không hardcode magic number — dùng config

**Bảo mật:**
- [ ] Mọi SQL dùng prepared statement
- [ ] Mọi output HTML dùng `htmlspecialchars()`
- [ ] Mật khẩu dùng `password_hash()` / `password_verify()`
- [ ] Route Admin kiểm tra session/role
- [ ] Form có CSRF token

**Business Rule:**
- [ ] Ràng buộc nghiệp vụ đặt trong Model (mục 5)
- [ ] Trạng thái đơn hàng chỉ chuyển đúng luồng

```
Prompt mẫu: "Bạn là Code Reviewer cho dự án Shop Giày.
Hãy review [file/PR] theo checklist trong AGENTS.md mục 8.2 và liệt kê mọi vấn đề tìm thấy."
```

---

#### 🎨 UI Developer

**Mục tiêu:** Viết View/HTML/JS **đẹp, responsive, an toàn XSS**, ưu tiên Bootstrap có sẵn.

Khi nhận role này, agent phải:
- Ưu tiên dùng component Bootstrap 5 (card, badge, modal, table, form) trước khi viết CSS custom
- CSS custom đặt trong `public/assets/css/custom.css`, class đặt tên kebab-case có prefix module
- Ngôn ngữ hiển thị: **Tiếng Việt**
- Luôn dùng `htmlspecialchars()` khi in dữ liệu người dùng ra HTML
- Form luôn có validate HTML5 (required, pattern) + validate lại phía server
- Dùng grid Bootstrap (`container`, `row`, `col-*`) đảm bảo responsive mobile + desktop
- JS đặt theo module trong `public/assets/js/`, dùng camelCase cho biến/hàm
- Trạng thái đơn hàng dùng đúng màu quy ước (mục 7)

```
Prompt mẫu: "Bạn là UI Developer cho dự án Shop Giày (Bootstrap 5, server-side render).
Hãy viết View cho [trang/chức năng] bằng tiếng Việt, responsive, escape XSS."
```

### 8.3 Quy tắc sử dụng Role

1. **Mỗi cuộc hội thoại chỉ dùng 1 role chính** — không trộn lẫn nhiều role để tránh agent bị "loãng" focus
2. **Có thể chuyển role giữa các cuộc hội thoại** — VD: Hưng dùng `Feature Developer` khi code, chuyển sang `Code Reviewer` khi review PR của Thảo
3. **Role `Security Reviewer` nên chạy bổ sung** sau khi hoàn thành feature — paste code đã viết vào 1 cuộc hội thoại mới với role Security để "tự review"
4. **Khi không chắc dùng role nào**, mặc định dùng `Feature Developer` — đây là role an toàn nhất cho công việc hàng ngày
5. **Prompt role phải đặt ở đầu cuộc hội thoại** hoặc trong system prompt/custom instructions của AI assistant để có hiệu lực xuyên suốt
