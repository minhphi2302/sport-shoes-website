# Changelog - Sửa Lỗi UI và Authentication

**Ngày:** 11/08/2026  
**Version:** 2.2.0  
**Loại:** Bug Fix - UI/UX + Auth/Session

---

## 🐛 Các Lỗi Đã Sửa

### 1. Ảnh Sản Phẩm Bị Mờ

**Vấn đề:**
- Ảnh sản phẩm hiển thị bị mờ, kém chất lượng
- Upload ảnh không thấy hiệu ứng gì

**Nguyên nhân:**
- CSS không có image rendering optimization
- Object-fit cover có thể gây mờ ảnh trên một số trình duyệt

**Giải pháp:**
```css
.product-img-wrapper img {
    /* ... existing styles ... */
    
    /* ✅ Tối ưu rendering ảnh */
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
}
```

---

### 2. Trang Account Lỗi 404

**Vấn đề:**
- Click vào "Tài khoản của tôi" → 404 Not Found
- URL: `/account` không tồn tại trong route

**Nguyên nhân:**
- Chỉ có route `/profile`, không có alias `/account`
- Header dropdown link đến `/profile` nhưng một số nơi có thể link đến `/account`

**Giải pháp:**
```php
// public/index.php
$router->get('/profile', [App\Controllers\ProfileController::class, 'index']);
$router->get('/account', [App\Controllers\ProfileController::class, 'index']); // ✅ Alias cho /profile
```

---

### 3. Sau Login Phải F5 Mới Hiển Thị Tên User

**Vấn đề:**
- Đăng nhập thành công → Redirect về trang chủ
- Header vẫn hiển thị "Đăng nhập / Đăng ký" thay vì tên user
- Phải F5 (refresh) mới thấy tên user

**Nguyên nhân:**
- Thiếu `session_regenerate_id(true)` sau khi login
- Session ID cũ chưa được refresh → Browser cache session cũ

**Giải pháp:**
```php
// app/Controllers/AuthController.php
Auth::login($user);

// ✅ Regenerate session ID để tránh session fixation và update cache
session_regenerate_id(true);

$this->redirect('/');
```

**Business Rule:** Theo `AGENTS.md` mục 5 - Session Security:
- Sau khi login thành công → gọi `session_regenerate_id(true)` (chống session fixation)
- Cookie session phải set flags: `HttpOnly`, `Secure`, `SameSite=Lax`

---

### 4. Giao Diện Bị Đè Nhau (Dropdown Menu)

**Vấn đề:**
- Dropdown menu user account không hiển thị hoặc bị đè dưới navbar
- Một số phần tử UI bị chồng lên nhau

**Nguyên nhân:**
- Thiếu z-index cho navbar và dropdown menu
- Dropdown menu có z-index thấp hơn navbar

**Giải pháp:**
```css
/* Navbar */
.navbar-main {
    z-index: 1030; /* ✅ Navbar cao hơn content */
}

.navbar-brand-logo {
    z-index: 1031; /* ✅ Logo luôn ở trên cùng */
}

/* Dropdown menus */
.custom-dropdown-menu {
    z-index: 1050; /* ✅ Dropdown cao hơn navbar */
}

.auth-dropdown-menu, 
.dropdown-menu {
    z-index: 1055; /* ✅ User dropdown cao nhất */
}
```

**Z-Index Hierarchy:**
```
1031 - Logo
1030 - Navbar
1050 - Custom Dropdown Menu
1055 - User Account Dropdown (cao nhất)
```

---

## 📊 Chi Tiết Thay Đổi

### A. Files Đã Sửa

#### 1. `public/index.php`
```diff
  // ── Profile ───────────────────────────────────────────────────
  $router->get('/profile',                 [App\Controllers\ProfileController::class, 'index']);
+ $router->get('/account',                 [App\Controllers\ProfileController::class, 'index']); // Alias
  $router->post('/profile/password',       [App\Controllers\ProfileController::class, 'updatePassword']);
```

#### 2. `app/Controllers/AuthController.php`
```diff
  $this->userModel->clearLoginAttempts($email);
  Auth::login($user);
  
+ // Regenerate session ID để tránh session fixation
+ session_regenerate_id(true);
  
  if ($user['role'] === 'admin') {
```

#### 3. `public/assets/css/style.css`
```diff
  .product-img-wrapper img {
      /* ... */
+     image-rendering: -webkit-optimize-contrast;
+     image-rendering: crisp-edges;
  }
  
  .navbar-main {
      /* ... */
+     z-index: 1030;
  }
  
  .dropdown-menu {
      /* ... */
+     z-index: 1055;
  }
```

---

## 🧪 Cách Test

### Test 1: Ảnh Sản Phẩm
```
1. Vào trang sản phẩm: /products
2. Kiểm tra: Ảnh có còn mờ không?
3. Thử upload ảnh mới trong admin
4. Xem ảnh ở cả admin và client
```

### Test 2: Route /account
```
1. Đăng nhập thành công
2. Click dropdown user → "Tài khoản của tôi"
3. Kiểm tra: Có bị 404 không?
4. Thử truy cập trực tiếp: /account
5. Kết quả mong đợi: Hiển thị trang profile
```

### Test 3: Session Sau Login
```
1. Mở Incognito window
2. Đăng nhập tài khoản
3. Redirect về trang chủ
4. Kiểm tra header NGAY LẬP TỨC (không F5)
5. Kết quả mong đợi: Hiển thị tên user, dropdown "Tài khoản của tôi"
```

### Test 4: Dropdown Menu
```
1. Đăng nhập
2. Click vào tên user ở header
3. Kiểm tra: Dropdown có hiển thị đầy đủ không?
4. Kiểm tra: Dropdown có bị đè dưới navbar không?
5. Thử click từng item trong dropdown
```

---

## 🔐 Security & Session (Business Rules)

Theo `AGENTS.md` mục 5 - Session & Authentication Security:

### ✅ Đã Implement:
- [x] `session_regenerate_id(true)` sau login (chống session fixation)
- [x] Cookie flags: `HttpOnly`, `Secure`, `SameSite=Lax` (trong Auth.php)
- [x] Password hash bằng `password_hash()` / `password_verify()`
- [x] Rate limit đăng nhập (5 lần/15 phút)
- [x] Session timeout (auto logout sau X phút không hoạt động)

### 📝 Ghi Chú:
- Session ID được tạo mới sau mỗi lần login thành công
- Browser sẽ nhận session ID mới → cập nhật header ngay lập tức
- Không cần F5 để thấy thay đổi

---

## 🎨 CSS Z-Index Hierarchy

```
Layer 1 (1000-1029): Content, Cards, Buttons
Layer 2 (1030-1039): Navbar, Fixed Headers
Layer 3 (1040-1059): Dropdowns, Tooltips, Popovers
Layer 4 (9999+): Modals, Back to Top Button
```

**Quy tắc:**
- Navbar: 1030
- Dropdown Menu: 1050+
- Modal Overlay: 1060+
- Back to Top: 9999

---

## 📁 Files Đã Thay Đổi

```
modified:   public/index.php (thêm route /account)
modified:   app/Controllers/AuthController.php (session_regenerate_id)
modified:   public/assets/css/style.css (image rendering, z-index)
new file:   CHANGELOG_UI_AND_AUTH_FIX.md
```

---

## 🚀 Deployment Checklist

- [ ] Backup database trước khi deploy
- [ ] Clear browser cache sau deploy (Ctrl + Shift + Delete)
- [ ] Test đầy đủ 4 test cases ở trên
- [ ] Test trên nhiều browser: Chrome, Firefox, Edge
- [ ] Test responsive: Mobile, Tablet, Desktop
- [ ] Monitor session log sau deploy (kiểm tra không có session fixation)

---

## 📝 Known Issues (Vấn đề đã biết)

### ⚠️ Cần theo dõi sau deploy:
- **Upload ảnh lớn (>2MB):** Có thể vượt quá `upload_max_filesize` của PHP → Báo lỗi rõ ràng
- **Session timeout:** Nếu user idle quá lâu → Auto logout → Redirect về /login với thông báo

### 💡 Future Improvements:
- [ ] Thêm loading spinner khi upload ảnh
- [ ] Compress ảnh tự động trước khi lưu (giảm dung lượng)
- [ ] Lazy load ảnh sản phẩm (chỉ load khi scroll đến)
- [ ] Remember me checkbox (lưu session lâu hơn)

---

**Developer:** AI Assistant  
**Reviewer:** Minh/Hưng/Thảo  
**Status:** ✅ Ready for Testing
