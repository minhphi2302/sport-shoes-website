# TASK-002: Authentication — Đăng ký / Đăng nhập / Đăng xuất

## 1. Thông tin chung
- **Trạng thái:** To Do
- **Người được giao:** Phí Văn Minh (Trưởng nhóm)
- **Role AI khuyên dùng:** 🛒 Feature Developer
- **Ngày tạo:** 2026-07-22
- **Branch:** `feat/dangky-dangnhap`
- **Phụ thuộc:** TASK-001 (cần bảng `users` tồn tại)
- **Ưu tiên:** 🔴 HIGH — Các module khác cần session/auth để hoạt động

## 2. Mô tả yêu cầu (Yêu cầu nghiệp vụ)

Xây dựng toàn bộ luồng xác thực người dùng:

1. **Đăng ký (Guest → Customer):** Form nhập tên, email, password, confirm password. Tạo tài khoản với role `customer` và status `active`.
2. **Đăng nhập:** Form nhập email + password. Kiểm tra status tài khoản. Phân biệt redirect sau login theo role (admin → `/admin/dashboard`, customer → trang trước hoặc trang chủ).
3. **Đăng xuất:** Hủy session, redirect về trang chủ.
4. **Middleware phân quyền:** Base class kiểm tra session ở `AdminController` — chặn toàn bộ admin routes nếu chưa đăng nhập hoặc không có role `admin`.
5. **Rate limit đăng nhập:** Giới hạn 5 lần sai / 15 phút theo email (lưu trong bảng `login_attempts`).

## 3. Tiêu chí nghiệm thu (Acceptance Criteria)

- [ ] AC1: Guest đăng ký thành công → tài khoản tạo với role `customer`, redirect đến trang đăng nhập với thông báo thành công
- [ ] AC2: Đăng ký email đã tồn tại → thông báo lỗi rõ ràng, không tạo duplicate
- [ ] AC3: Password < 6 ký tự → validate phía server trả về lỗi (không chỉ dựa HTML5 required)
- [ ] AC4: Đăng nhập đúng thông tin → session tạo, redirect đúng theo role
- [ ] AC5: Đăng nhập sai password → thông báo "Email hoặc mật khẩu không đúng" (không tiết lộ trường nào sai)
- [ ] AC6: Đăng nhập tài khoản `status = locked` → thông báo "Tài khoản đã bị khóa" và không cho vào
- [ ] AC7: Đăng nhập sai quá 5 lần trong 15 phút → hiển thị "Quá nhiều lần thử, vui lòng thử lại sau X phút"
- [ ] AC8: Đăng xuất → session bị hủy hoàn toàn, không thể dùng back button để vào lại
- [ ] AC9: Truy cập URL admin khi chưa đăng nhập → redirect về `/login`
- [ ] AC10: Truy cập URL admin khi đăng nhập với role `customer` → trả về 403 Forbidden
- [ ] AC11: Session tự hết hạn sau 30 phút không hoạt động

## 4. Ghi chú kỹ thuật & Ràng buộc (Tech Notes)

### Model/Bảng liên quan:
- `users` — đọc/ghi thông tin người dùng
- `login_attempts` — bảng mới cần thêm migration: `attempt_id`, `email`, `ip_address`, `attempted_at`

> ⚠️ **Cần thêm migration mới:** Tạo file `database/migrations/008_create_login_attempts_table.sql` trong cùng PR này.

### File dự kiến cần tạo/sửa:
- `app/Models/User.php` — các method: `findByEmail()`, `create()`, `verifyPassword()`, `isLocked()`
- `app/Controllers/AuthController.php` — actions: `showLogin()`, `login()`, `showRegister()`, `register()`, `logout()`
- `app/Core/Auth.php` — helper tĩnh: `Auth::check()`, `Auth::user()`, `Auth::id()`, `Auth::login($user)`, `Auth::logout()`
- `app/Core/AdminController.php` — abstract base class kiểm tra session + role admin trong `__construct()`
- `views/client/auth/login.php`
- `views/client/auth/register.php`
- `public/index.php` — đăng ký các routes: `GET /login`, `POST /login`, `GET /register`, `POST /register`, `POST /logout`
- `database/migrations/008_create_login_attempts_table.sql`

### Security bắt buộc (AGENTS.md mục 5):
```php
// Sau login thành công — bắt buộc
session_regenerate_id(true);

// Cookie flags — set trong config
session_set_cookie_params([
    'lifetime' => 0,
    'httponly' => true,
    'samesite' => 'Lax',
    // 'secure' => true, // Bật khi có HTTPS
]);

// Lưu last_activity để timeout
$_SESSION['last_activity'] = time();
```

### Rate limit đơn giản (không cần Redis):
```php
// Kiểm tra trong bảng login_attempts
SELECT COUNT(*) FROM login_attempts 
WHERE email = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
```

### Business Rules liên quan (AGENTS.md mục 10 — USERS):
- Email duy nhất, password tối thiểu 6 ký tự
- Tài khoản `status = locked` không được đăng nhập
- Chỉ 2 vai trò: `admin`, `customer`

### Pattern AdminController:
```php
// app/Core/AdminController.php
abstract class AdminController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || Auth::user()['role'] !== 'admin') {
            // Nếu chưa login → redirect login
            // Nếu đã login nhưng không phải admin → 403
        }
    }
}
```

## 5. Security Checklist bắt buộc cho Task này
- [ ] Mật khẩu lưu bằng `password_hash()`, verify bằng `password_verify()` — không tự viết hash
- [ ] `session_regenerate_id(true)` ngay sau khi login thành công
- [ ] Cookie HttpOnly, SameSite=Lax (Secure khi có HTTPS)
- [ ] Có chống SQL Injection (Prepared Statement PDO) trong tất cả query của `User.php`
- [ ] Có chống XSS (`htmlspecialchars`) cho mọi output ra form (VD: re-fill email sau lỗi)
- [ ] Không lộ thông tin nội bộ (tên bảng, stack trace) ra response khi production
- [ ] Rate limit đăng nhập đã implement và test
- [ ] Phân quyền Admin check ở tầng base class, không check rải rác ở từng action

## 6. PHPUnit Test cần viết (AGENTS.md mục 11)

```
tests/Unit/UserModelTest.php:
- test_register_success_creates_hashed_password
- test_register_duplicate_email_throws_exception
- test_register_password_too_short_throws_validation_exception
- test_login_locked_account_throws_exception
- test_rate_limit_blocks_after_5_failed_attempts
```
