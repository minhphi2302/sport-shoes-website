# Shop Giày AI — Project Rules & Conventions (v2 — Production-grade)

> Website bán giày thể thao
> PHP Core, MySQL, MVC, Bootstrap
> v2: bổ sung transaction/concurrency, upload security, session security, CI/CD, migration có version, static analysis.

---

## 1. Tech Stack

| Layer | Công nghệ | Ghi chú |
|-------|-----------|---------|
| Ngôn ngữ backend | **PHP Core (không framework)** | PHP 8.1+ |
| Database | **MySQL 8.0+**, engine **InnoDB bắt buộc** | InnoDB hỗ trợ transaction + row lock, MyISAM **không được dùng** |
| Kiến trúc | **MVC (Model – View – Controller)** | Tự viết Router + Core |
| Frontend | **HTML5, CSS3, JavaScript, Bootstrap 5** | Server-side render |
| Quản lý phụ thuộc | **Composer** | PHPMailer, PHPUnit, PHPStan qua Composer |
| Testing | **PHPUnit** | Test Model/business rules |
| Static analysis | **PHPStan (level ≥ 5)** | Bắt lỗi type, null-safety trước khi chạy |
| CI | **GitHub Actions** (hoặc GitLab CI) | Chạy lint + PHPStan + PHPUnit tự động trên mọi PR |
| Migration | **Custom migration script** (`database/migrations/`, đánh số thứ tự) | Không dùng 1 file `schema.sql` tĩnh duy nhất |
| Local env | **Docker Compose** (khuyến nghị) | Cố định PHP + MySQL version giữa 3 máy |

---

## 2. Quy tắc code

### 2.1 Naming — Backend (PHP)

```php
// Files & classes: PascalCase, tên file = tên class
ProductController.php
class ProductController { ... }

// Methods & variables: camelCase
public function getProductById(int $productId): ?array { ... }
$totalAmount = 0;

// Constants: UPPER_SNAKE_CASE
const ORDER_STATUS_PENDING = 'pending';

// Database: snake_case cho tên bảng và cột
// products, order_details, category_id, created_at
```

### 2.2 Naming — Frontend (View/Bootstrap)

```
views/client/product_list.php
views/admin/category_manage.php

.product-card { ... }          /* CSS custom: kebab-case, prefix module */

// public/assets/js/cart.js — camelCase
function addToCart(productId) { ... }
```

### 2.3 Docstring (PHPDoc bắt buộc cho method public trong Model & Controller)

```php
/**
 * Tính tổng tiền đơn hàng dựa trên chi tiết đơn hàng.
 *
 * @param int $orderId Mã đơn hàng.
 * @return float Tổng tiền cuối cùng.
 * @throws OrderNotFoundException Khi không tìm thấy đơn hàng.
 */
public function calculateTotalAmount(int $orderId): float { ... }
```

### 2.4 Comments

- Comment **tại sao**, không comment **cái gì**
- Mọi ràng buộc nghiệp vụ phải trích dẫn rule tương ứng (mục 10)
- Nghiệp vụ → tiếng Việt; kỹ thuật thuần (thuật toán, thư viện) → tiếng Anh

```php
// Giá khuyến mãi không được lớn hơn giá bán (business rule PRODUCTS)
if ($salePrice > $price) {
    throw new InvalidProductDataException('sale_price', 'Giá khuyến mãi vượt giá bán');
}
```

### 2.5 Error Handling

- **KHÔNG** dùng `@` để nuốt lỗi, không `catch (Exception $e) {}` rỗng
- Custom Exception theo phân cấp rõ ràng, không dùng `Exception` trần trụi:

```php
abstract class AppException extends \Exception {}

class ValidationException extends AppException {
    public function __construct(public readonly string $field, string $message) {
        parent::__construct($message);
    }
}

class InsufficientStockException extends AppException {}
class InvalidOrderTransitionException extends AppException {}
class RecordNotFoundException extends AppException {}
```

- Validate input ở **Controller**; Model validate lại business rule trước khi ghi DB (defense in depth)
- **Global error handler** (đặt trong `app/Core/ErrorHandler.php`, đăng ký bằng `set_exception_handler`):
  - Môi trường **production**: log lỗi chi tiết vào file (`storage/logs/app.log`), hiển thị cho user trang lỗi chung chung (không lộ stack trace, không lộ câu SQL)
  - Môi trường **local/dev**: hiển thị stack trace đầy đủ để debug
  - Phân biệt qua biến `APP_ENV` trong `.env`

```php
// ❌ Sai — lộ thông tin nội bộ ra production
catch (\PDOException $e) {
    echo $e->getMessage(); // có thể lộ tên bảng, cấu trúc DB
}

// ✅ Đúng
catch (\PDOException $e) {
    Logger::error($e->getMessage());
    throw new AppException('Có lỗi xảy ra, vui lòng thử lại sau.');
}
```

---

## 3. Transaction & Concurrency (bắt buộc — vá lỗ hổng thường gặp nhất)

### 3.1 Mọi thao tác nhiều bước phải nằm trong transaction

Đặt hàng = tạo `Order` + tạo `Order_Details` + trừ tồn kho `Products` → 3 write phải **atomic**. Nếu 1 bước lỗi, toàn bộ phải rollback.

```php
public function createOrder(int $userId, array $items): int
{
    $this->pdo->beginTransaction();
    try {
        $orderId = $this->insertOrder($userId);

        foreach ($items as $item) {
            // Trừ tồn kho có điều kiện — xem mục 3.2
            $this->decreaseStock($item['product_id'], $item['quantity']);
            $this->insertOrderDetail($orderId, $item);
        }

        $this->pdo->commit();
        return $orderId;
    } catch (\Throwable $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}
```

### 3.2 Chống race condition khi trừ tồn kho (bắt buộc)

**KHÔNG** làm theo kiểu "check rồi update" (kiểm tra còn hàng, sau đó mới trừ) — 2 request cùng lúc sẽ cùng pass check và làm âm kho:

```php
// ❌ Sai — race condition
$stock = $this->getStock($productId);
if ($stock >= $qty) {
    $this->updateStock($productId, $stock - $qty);
}
```

**Đúng** — dùng UPDATE có điều kiện, để MySQL tự đảm bảo tính atomic ở mức row, rồi kiểm tra `rowCount()`:

```php
// ✅ Đúng — UPDATE có điều kiện, atomic ở tầng DB
$stmt = $this->pdo->prepare(
    'UPDATE products SET quantity = quantity - :qty
     WHERE product_id = :id AND quantity >= :qty'
);
$stmt->execute(['qty' => $qty, 'id' => $productId]);

if ($stmt->rowCount() === 0) {
    // Hoặc sản phẩm không tồn tại, hoặc không đủ hàng
    throw new InsufficientStockException("Sản phẩm #{$productId} không đủ tồn kho");
}
```

Nếu cần đọc số lượng trước khi tính toán phức tạp hơn (VD: áp dụng nhiều điều kiện), dùng `SELECT ... FOR UPDATE` trong cùng transaction để khóa row:

```php
$stmt = $this->pdo->prepare('SELECT quantity FROM products WHERE product_id = :id FOR UPDATE');
```

### 3.3 Isolation level

- Dùng mức mặc định của InnoDB (`REPEATABLE READ`) — đủ cho quy mô dự án, không cần chỉnh trừ khi có lý do cụ thể (phải ghi comment giải thích nếu đổi)

---

## 4. Upload File (bắt buộc — Product image)

SRS yêu cầu Product có trường Hình ảnh. Quy tắc bắt buộc:

- Giới hạn định dạng: chỉ nhận `jpg`, `jpeg`, `png`, `webp` — kiểm tra bằng **MIME type thật** (`finfo_file()`), không chỉ tin đuôi file
- Giới hạn dung lượng (VD: tối đa 2MB), reject nếu vượt
- **Đổi tên file** khi lưu (VD: `uniqid() . '.' . $ext`) — không giữ tên gốc người dùng upload, tránh path traversal và tránh ghi đè file
- Lưu file vào thư mục **ngoài vùng thực thi PHP trực tiếp** hoặc thư mục `public/uploads/` có cấu hình chặn thực thi script (`.htaccess`: `php_flag engine off` hoặc tương đương) — để tránh trường hợp upload file `.php` giả dạng ảnh rồi truy cập trực tiếp gây RCE
- Validate kích thước ảnh (width/height) nếu cần chuẩn hóa hiển thị

```php
// ✅ Đúng
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $tmpPath);
$allowed = ['image/jpeg', 'image/png', 'image/webp'];

if (!in_array($mime, $allowed, true)) {
    throw new ValidationException('image', 'Định dạng ảnh không hợp lệ');
}
if (filesize($tmpPath) > 2 * 1024 * 1024) {
    throw new ValidationException('image', 'Ảnh vượt quá 2MB');
}

$newName = uniqid('product_', true) . '.' . pathinfo($originalName, PATHINFO_EXTENSION);
move_uploaded_file($tmpPath, PRODUCT_IMAGE_DIR . '/' . $newName);
```

---

## 5. Session & Authentication Security (bắt buộc)

- Sau khi login thành công → gọi `session_regenerate_id(true)` (chống session fixation)
- Cookie session bắt buộc set flags: `HttpOnly`, `Secure` (khi có HTTPS), `SameSite=Lax`
- Session timeout: tự động hết hạn sau X phút không hoạt động (lưu `last_activity` trong session, kiểm tra mỗi request)
- **Rate limit đăng nhập**: giới hạn số lần thử sai (VD: 5 lần/15 phút theo IP hoặc theo email) — lưu đếm trong bảng `login_attempts` hoặc cache đơn giản, không cần Redis cho quy mô đồ án
- Mật khẩu: `password_hash()` / `password_verify()` — không tự viết hàm hash, không lưu plaintext
- Route Admin: middleware kiểm tra session + role **ở tầng Controller base class**, không lặp lại check ở từng action riêng lẻ

```php
abstract class AdminController extends BaseController
{
    public function __construct()
    {
        if (!Auth::check() || Auth::user()['role'] !== 'admin') {
            throw new UnauthorizedException();
        }
    }
}
```

---

## 6. Git Workflow

### 6.1 Branch Strategy
```
main       ← luôn chạy được, chỉ merge từ develop khi release, bảo vệ bằng branch protection
develop    ← nhánh tích hợp chung
├── feat/<module>
├── fix/<module>-<bug>
└── refactor/<scope>
```

### 6.2 Commit Message
Format: `<type>(<module>): <mô tả ngắn>` — types: `feat`, `fix`, `refactor`, `docs`, `test`, `chore`

### 6.3 Pull Request — bắt buộc qua CI trước khi review thủ công

Branch `develop` và `main` bật **branch protection**: không cho merge nếu:
- [ ] CI (lint + PHPStan + PHPUnit) chưa pass
- [ ] Chưa có ít nhất 1 reviewer khác approve (không tự merge PR của chính mình)
- [ ] PR chưa điền template (mục 6.4)

### 6.4 PR Template bắt buộc (đặt tại `.github/PULL_REQUEST_TEMPLATE.md`)

```markdown
## Thay đổi gì
## Tại sao
## Đã test thế nào (liệt kê case cụ thể)
## Checklist bảo mật (tick trước khi xin review)
- [ ] Mọi SQL dùng prepared statement
- [ ] Mọi output HTML qua htmlspecialchars()
- [ ] Không có transaction thiếu rollback ở thao tác nhiều bước
- [ ] Có thay đổi schema? → đã thêm migration mới, chưa sửa migration cũ
```

### 6.5 CI Pipeline (`.github/workflows/ci.yml`) — chạy tự động, không phụ thuộc ý thức con người

```yaml
name: CI
on: [pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: shop_giay_test
        ports: ["3306:3306"]
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - run: composer install
      - run: vendor/bin/php-cs-fixer fix --dry-run --diff
      - run: vendor/bin/phpstan analyse --level=5 app/
      - run: php database/migrate.php   # chạy migration lên DB test
      - run: vendor/bin/phpunit
```

---

## 7. Database Migration (thay cho 1 file schema.sql tĩnh)

Vấn đề của 1 file `schema.sql` duy nhất: 3 người cùng sửa → conflict, không biết ai chạy version nào, không rollback được.

Thay bằng thư mục migration đánh số, mỗi thay đổi DB là 1 file mới, không sửa lại file cũ:

```
database/migrations/
├── 001_create_users_table.sql
├── 002_create_categories_table.sql
├── 003_create_brands_table.sql
├── 004_create_products_table.sql
├── 005_create_orders_table.sql
├── 006_create_order_details_table.sql
├── 007_add_status_to_users.sql       ← thay đổi sau này = file mới
└── migrate.php                        ← script chạy tuần tự migration chưa apply
```

Quy tắc:
- `migrate.php` lưu danh sách migration đã chạy vào bảng `migrations` (tên file + timestamp), chỉ chạy các file chưa có trong bảng đó
- **Không bao giờ sửa nội dung 1 file migration đã merge vào `develop`** — nếu sai, tạo migration mới để sửa (VD: `008_fix_products_price_column.sql`)
- Mỗi PR đổi schema → migration mới nằm trong cùng PR, review cùng lúc với code dùng nó

---

## 8. Quy tắc kiến trúc (MVC)

- **View**: chỉ nhận biến từ Controller, không query DB, không chứa business logic
- **Controller**: validate input, gọi method public của Model, không viết SQL trực tiếp, không chứa business rule (chỉ điều phối)
- **Model**: chứa toàn bộ business rule + truy vấn DB, không echo/render HTML, không biết Controller/View
- Giao tiếp giữa module khác nhau qua **method public**, không đụng thuộc tính private của class khác
- Toàn bộ hằng số/ngưỡng đặt trong `config/config.php`, không hardcode magic number
- Ưu tiên **constructor injection** cho Model/Service (VD: `new ProductController($pdo)`), tránh `new PDO(...)` rải rác nhiều nơi — dễ test, dễ mock

```php
// ✅ Đúng — dependency injection
class ProductController
{
    public function __construct(private ProductModel $productModel) {}
}

// ❌ Sai — tạo dependency ngay trong class, khó test
class ProductController
{
    public function index()
    {
        $model = new ProductModel(new PDO(...)); // hardcode, khó mock khi test
    }
}
```

---

## 9. Performance cơ bản (bắt buộc ở mức tối thiểu)

- Index bắt buộc cho: mọi cột FK (`category_id`, `brand_id`, `user_id`, `order_id`, `product_id`), cột dùng để tìm kiếm (`product_name` — dùng `FULLTEXT` hoặc ít nhất index thường nếu chỉ `LIKE 'x%'`)
- Danh sách sản phẩm / đơn hàng **bắt buộc phân trang** (`LIMIT` + `OFFSET`, hoặc keyset pagination nếu data lớn) — không `SELECT *` toàn bộ bảng
- Không N+1 query: khi hiển thị danh sách đơn hàng kèm tên khách hàng, dùng `JOIN` một lần thay vì query riêng trong vòng lặp

---

## 10. Business Rules bắt buộc (theo SRS mục 3.2.3)

### USERS
- Email duy nhất, mật khẩu tối thiểu 6 ký tự
- Chỉ 2 vai trò: Admin, Customer
- Tài khoản `status = locked` không được đăng nhập

### CATEGORIES / BRANDS
- Tên không trống, tên thương hiệu duy nhất
- Không xóa nếu còn sản phẩm thuộc về nó (check ở Model trước `DELETE`)

### PRODUCTS
- Mã sản phẩm duy nhất; giá bán > 0; giá khuyến mãi ≤ giá bán
- Tồn kho không âm (đảm bảo bằng UPDATE có điều kiện — mục 3.2)
- Thuộc đúng 1 danh mục và 1 thương hiệu

### ORDERS
- Thuộc 1 khách hàng, có ít nhất 1 sản phẩm
- Tổng tiền = tổng chi tiết − giảm giá
- Trạng thái chỉ chuyển: `Pending → Confirmed → Completed` hoặc `Pending → Cancelled` — validate transition trong Model, throw `InvalidOrderTransitionException` nếu sai luồng

### ORDER_DETAILS
- Số lượng > 0; đơn giá lưu tại thời điểm đặt hàng, không đổi theo giá sau này
- `Subtotal = Quantity × Price`

> **Khi SRS thay đổi** (thêm rule mới, VD: mã giảm giá): cập nhật mục này **trong cùng PR** với code hiện thực rule đó — không để rule ở SRS và code lệch nhau.

---

## 11. Testing Rules

- Mỗi Model có business rule **bắt buộc** PHPUnit test: happy path, boundary (giá=0, số lượng=0, tồn kho vừa đủ/vừa thiếu), error case
- Test transaction: giả lập lỗi giữa chừng (VD: mock exception ở bước insert order_detail) → assert rằng order và stock đều **rollback**, không tạo dữ liệu rác
- Test race condition (ít nhất 1 test): 2 lần gọi `decreaseStock` liên tiếp cho cùng sản phẩm khi tồn kho = 1 → 1 lần thành công, 1 lần throw `InsufficientStockException`
- Tên test: `test_<method>_<scenario>`
- CI chạy test tự động trên mọi PR (mục 6.5) — không dựa vào việc dev tự chạy tay

---

## 12. Quy tắc UI (Bootstrap)

- Ngôn ngữ: Tiếng Việt
- Trạng thái đơn hàng:

| Trạng thái | Màu | Label |
|---|---|---|
| Pending | `bg-secondary` | Chờ xử lý |
| Confirmed | `bg-info` | Đã xác nhận |
| Completed | `bg-success` | Hoàn thành |
| Cancelled | `bg-danger` | Đã hủy |

- Responsive bằng grid Bootstrap, test tối thiểu mobile + desktop
- Form: validate HTML5 phía client **và** validate lại phía server (không tin client)
- Mọi output dữ liệu người dùng ra HTML qua `htmlspecialchars()`

---

## 13. Definition of Done (DoD) — 1 feature coi như hoàn thành khi

- [ ] Đúng acceptance criteria tương ứng trong SRS (mục 2.x)
- [ ] Business rule liên quan (mục 10) được validate ở Model, có test PHPUnit
- [ ] Không có thao tác nhiều bước thiếu transaction
- [ ] CI pass (lint, PHPStan, PHPUnit)
- [ ] Đã qua ít nhất 1 review approve
- [ ] Nếu có UI: đã tự kiểm tra responsive mobile/desktop, escape XSS
- [ ] Nếu đổi schema: có migration mới, đã cập nhật trong PR

---

## 14. Agent Role Definitions (dùng khi làm việc với AI coding assistant)

> Khai báo role ở đầu cuộc hội thoại / system prompt. Mỗi cuộc hội thoại dùng **1 role chính**, tránh loãng focus.
> **Lưu ý:** role chỉ là công cụ hỗ trợ tư duy, **không thay thế CI/PR checklist** ở mục 6.4 và 6.5 — checklist bảo mật vẫn phải tick thật trong PR dù đã "hỏi" Security Reviewer.

| Role | Mô tả | Ai dùng |
|---|---|---|
| 🏗️ PHP Architect | Thiết kế kiến trúc, migration, dependency injection | Minh |
| 🔒 Security Reviewer | Rà bảo mật: SQL injection, XSS, upload, session, race condition | Chạy bổ sung sau mỗi feature, bất kể ai code |
| 🛒 Feature Developer | Code Model/Controller/View đúng business rule | Hưng, Thảo |
| 🧪 QA Engineer | Viết test, nghĩ edge case, test transaction/race condition | Thảo |
| 🎨 UI Developer | View/Bootstrap/JS, responsive, escape XSS | Hưng |

> Đã bỏ role "Code Reviewer" riêng (trùng việc với PR checklist ở mục 6.4) — review PR dùng checklist cố định thay vì role mơ hồ, tránh tình trạng "tưởng người khác check rồi".

### 🏗️ PHP Architect
- Ưu tiên dependency injection, tách interface trước khi code
- Thiết kế migration mới khi đổi schema, không sửa migration cũ
- Đảm bảo thêm module mới sửa càng ít file càng tốt

```
Prompt mẫu: "Bạn là PHP Architect dự án Shop Giày. Thiết kế [module] với DI,
migration riêng, và cách trừ tồn kho an toàn với race condition."
```

### 🔒 Security Reviewer
Checklist bắt buộc quét:
- [ ] SQL: 100% prepared statement
- [ ] Output HTML: 100% qua htmlspecialchars()
- [ ] Mật khẩu: password_hash/verify
- [ ] Session: regenerate_id sau login, cookie HttpOnly/Secure/SameSite
- [ ] Upload: kiểm MIME thật, giới hạn size, đổi tên file, thư mục chặn thực thi script
- [ ] Transaction: thao tác nhiều bước có rollback đầy đủ
- [ ] Trừ tồn kho: dùng UPDATE có điều kiện, không "check rồi update"
- Đánh dấu mức độ: 🔴 Critical / 🟡 Warning / 🟢 Info

```
Prompt mẫu: "Bạn là Security Reviewer. Review đoạn code sau theo checklist
đầy đủ ở AGENTS.md mục 14, liệt kê lỗi theo mức độ nghiêm trọng."
```

### 🛒 Feature Developer
- Đọc business rule mục 10 trước khi viết code
- Validate ở Controller + Model (defense in depth)
- Thao tác nhiều bước → bọc transaction (mục 3)
- PHPDoc đầy đủ, comment nghiệp vụ bằng tiếng Việt

### 🧪 QA Engineer
- Test happy path + boundary + error cho mọi Model có business rule
- **Bắt buộc** có test giả lập lỗi giữa transaction (assert rollback đúng)
- **Bắt buộc** có test race condition cho trừ tồn kho

### 🎨 UI Developer
- Ưu tiên component Bootstrap có sẵn
- htmlspecialchars() cho mọi output, validate HTML5 + server-side
- Responsive test mobile + desktop

### Quy tắc dùng role
1. Không trộn nhiều role trong 1 cuộc hội thoại
2. `Security Reviewer` chạy bổ sung sau mỗi feature — không thay thế PR checklist ở mục 6.4
3. Mặc định dùng `Feature Developer` khi không chắc chọn role nào
4. Đặt role ở đầu cuộc hội thoại hoặc system prompt để có hiệu lực xuyên suốt

---

## 15. AI-Driven Task Workflow (Quy trình giao việc tự động qua AI)

Nhằm tối ưu hóa việc phân công nhiệm vụ và đồng bộ thông tin trong nhóm (Minh, Hưng, Thảo), chúng ta áp dụng mô hình quản lý công việc lưu trữ dưới dạng File (Doc-as-Task) ngay trên repository này.

### Cấu trúc lưu trữ
- `docs/templates/task_template.md`: Template chuẩn quy định những thông tin cần có của một task.
- `docs/tasks/`: Thư mục chứa các file đặc tả nhiệm vụ (Task Spec). VD: `TASK-001-login.md`.

### Cách vận hành bằng AI Agent (Custom Skills)

1. **Dành cho Leader (Người giao việc - PM)**
   - Không cần tự viết file dài dòng. Gọi kỹ năng chuyên dụng cho AI Agent:
     > "@AI /create-task Hãy tạo yêu cầu tính năng Đăng nhập bằng Email/Password cho khách hàng"
   - AI sẽ tự phân tích, đối chiếu rule (mục 10) và sinh ra file `TASK-XXX.md` có đầy đủ Acceptance Criteria và Tech Notes. Sau đó Leader chỉ việc commit lên Git.

2. **Dành cho Developer / QA Engineer (Người thực thi)**
   - Sau khi pull code về, không cần copy thủ công hay giải thích lại requirement cho AI. Dùng kỹ năng:
     > "@AI /execute-task TASK-001"
   - AI sẽ tự động tìm file Spec, hiểu rõ "Acceptance Criteria", và tiến hành code (nếu là Feature Developer) hoặc viết Unit Test (nếu là QA Engineer).
   - Khi làm xong, AI tự động cập nhật trạng thái file Task thành `Ready for Test` hoặc `Done`.