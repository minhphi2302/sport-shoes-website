# Website Bán Giày Thể Thao — Kiến Trúc Hệ Thống & Phân Công Nhóm

> Dựa trên SRS (Nhóm 1), tech stack: PHP Core, MySQL, HTML5/CSS3/JS, Bootstrap 5, mô hình MVC.
> Nhóm 3 người: Phí Văn Minh (Trưởng nhóm), Nguyễn Mạnh Hưng, Nguyễn Chí Thảo.

---

## 1. Kiến trúc hệ thống

### 1.1 Cấu trúc thư mục đề xuất

```
shop-giay/
├── app/
│   ├── Controllers/        # Xử lý request, gọi Model, trả View
│   │   ├── AuthController.php
│   │   ├── ProductController.php
│   │   ├── CartController.php
│   │   ├── OrderController.php
│   │   └── Admin/
│   │       ├── CategoryController.php
│   │       ├── BrandController.php
│   │       ├── ProductAdminController.php
│   │       ├── OrderAdminController.php
│   │       ├── CustomerAdminController.php
│   │       └── DashboardController.php
│   ├── Models/              # Truy vấn DB, business rules
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Brand.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   └── OrderDetail.php
│   ├── Views/               # Template hiển thị (tách theo module)
│   │   ├── client/          # Guest + Customer
│   │   └── admin/           # Admin
│   └── Core/                # Router, base Controller/Model, DB connection
├── config/
│   ├── database.php
│   └── config.php
├── public/                  # Entry point (index.php), assets (css/js/img)
├── database/
│   ├── schema.sql           # Cấu trúc bảng (nguồn chân lý duy nhất)
│   └── seed.sql             # Dữ liệu mẫu để cả nhóm test giống nhau
├── .env.example
├── .gitignore
└── README.md
```

### 1.2 Luồng xử lý (MVC)

```
Request (browser)
   → public/index.php (router)
   → Controller (validate input, gọi Model)
   → Model (truy vấn/ghi MySQL, áp business rules)
   → Controller nhận kết quả
   → View (render HTML trả về browser)
```

Nguyên tắc bắt buộc:
- **View không truy vấn DB trực tiếp** — chỉ nhận dữ liệu Controller truyền vào.
- **Controller không viết SQL trực tiếp** — chỉ gọi hàm của Model.
- **Model không import Controller/View**.
- Mọi ràng buộc nghiệp vụ ở mục 3.2.3 SRS (email unique, giá > 0, giảm giá ≤ giá bán, trạng thái đơn hàng theo đúng luồng Pending → Confirmed → Completed / Pending → Cancelled...) đặt trong **Model**, không đặt rải rác trong Controller.

### 1.3 Cơ sở dữ liệu

6 bảng theo ERD trong SRS: `USERS`, `CATEGORIES`, `BRANDS`, `PRODUCTS`, `ORDERS`, `ORDER_DETAILS`.

Quy tắc:
- File `database/schema.sql` là **nguồn duy nhất** định nghĩa cấu trúc bảng — mọi thay đổi schema phải commit vào file này trước, không sửa DB local rồi thôi.
- Ai đổi schema → cập nhật `schema.sql` trong cùng PR, báo nhóm trong kênh chat để mọi người `re-import` DB local.

---

## 2. Phân công công việc (3 người)

Vì đây là ứng dụng PHP thuần MVC (không tách BE/FE như SPA), cách chia hiệu quả nhất là chia theo **module dọc** (mỗi người làm trọn Model + Controller + View của module mình phụ trách), tránh 2 người cùng sửa 1 file.

### Người 1 — Phí Văn Minh (Trưởng nhóm)
**Phụ trách: Tài khoản, Kiến trúc & Quản lý sản phẩm (Admin)**
- Thiết lập khung project (router, Core, kết nối DB, cấu trúc thư mục)
- Đăng ký / Đăng nhập / Đăng xuất / Đổi mật khẩu / Thiết lập lại mật khẩu, phân quyền Guest–Customer–Admin
- Admin: Quản lý danh mục, Quản lý thương hiệu, Quản lý sản phẩm (CRUD + tồn kho)
- Review code, merge PR vào `develop`, quản lý schema DB

### Người 2 — Nguyễn Mạnh Hưng
**Phụ trách: Trải nghiệm mua hàng (Guest/Customer)**
- Trang chủ, danh sách/chi tiết sản phẩm, tìm kiếm, lọc theo danh mục/thương hiệu
- Giỏ hàng: thêm/sửa/xóa/xem tổng tiền
- Đặt hàng: nhập thông tin nhận hàng, chọn thanh toán, xác nhận, tạo đơn hàng + trừ tồn kho
- Quản lý thông tin cá nhân (Customer)

### Người 3 — Nguyễn Chí Thảo
**Phụ trách: Đơn hàng & Thống kê**
- Customer: xem lịch sử đơn hàng, chi tiết đơn hàng
- Admin: danh sách đơn hàng, chi tiết, cập nhật trạng thái, tìm kiếm đơn hàng
- Admin: Quản lý khách hàng (xem danh sách, khóa tài khoản)
- Dashboard: tổng sản phẩm, khách hàng, đơn hàng, doanh thu
- Viết test thủ công / checklist QA cho luồng end-to-end

> Gợi ý: mỗi người tự viết Model của module mình để tránh đụng file, nhưng khi 2 module cùng cần 1 bảng (ví dụ Order & OrderDetail dùng chung), thống nhất ai "sở hữu" file Model đó, người còn lại chỉ gọi qua public method, không sửa trực tiếp.

---

## 3. Đồng bộ code khi 3 người dùng IDE khác nhau (VD: VS Code, PhpStorm + AI coding assistant)

Vấn đề chính khi dùng AI coding assistant (Copilot, Cursor, Claude Code...) là các công cụ này generate code nhanh nhưng dễ **không đồng nhất style** và dễ **đụng file** giữa 3 người. Cách xử lý:

### 3.1 Git là trung tâm — không gửi code qua Zalo/Drive
- Tạo 1 repo chung (GitHub/GitLab), cả 3 người `git clone` về máy, dùng IDE riêng nhưng cùng 1 repo.
- **Không ai làm việc trực tiếp trên `main`**.

### 3.2 Branch strategy
```
main        ← luôn chạy được, chỉ merge từ develop khi release
develop     ← nhánh tích hợp chung
├── feat/dangky-dangnhap        (Minh)
├── feat/quanly-sanpham         (Minh)
├── feat/gio-hang-datHang       (Hưng)
├── feat/tim-kiem-loc           (Hưng)
├── feat/quanly-donhang         (Thảo)
└── feat/dashboard-thongke      (Thảo)
```
- Mỗi tính năng → 1 branch riêng, đặt tên `feat/<mô tả>` hoặc `fix/<mô tả>`.
- Xong tính năng → tạo Pull Request vào `develop`, **1 người khác review trước khi merge** (đừng để trưởng nhóm review 100%, luân phiên).

### 3.3 Quy ước code chung để AI trong IDE tạo code nhất quán
Đưa 1 file cấu hình vào gốc repo để mọi AI assistant (Copilot/Cursor/Claude) đọc và tuân theo, ví dụ nội dung tối thiểu:
- Coding convention: PascalCase cho Class (Controller/Model), camelCase cho hàm/biến, snake_case cho tên cột DB
- Comment giải thích **tại sao**, không giải thích **cái gì**
- Không viết SQL raw string nối chuỗi trực tiếp từ input người dùng (dùng prepared statement để tránh SQL injection) — bắt buộc với mọi Model
- Mọi input từ form phải validate ở Controller trước khi gọi Model

Việc có 1 file quy ước này quan trọng hơn cả AI assistant, vì nó là "luật chung" giúp 3 IDE khác nhau tạo code theo cùng 1 chuẩn thay vì mỗi AI tự đoán style.

### 3.4 Format & lint tự động — tránh conflict do khác style
- Thêm `.editorconfig` (khai báo tab/space, indent size, charset) — VS Code lẫn PhpStorm đều tự đọc file này.
- Dùng **PHP CS Fixer** hoặc **PHP_CodeSniffer** + chạy trước khi commit (pre-commit hook bằng `husky`-style hoặc Git hook đơn giản), để code luôn được format giống nhau bất kể ai/IDE nào viết ra.
- Thêm `.gitattributes` để chuẩn hoá line-ending (tránh lỗi do Windows/Mac khác nhau).

### 3.5 Đồng bộ database giữa 3 máy
- **Không** commit file `.env` (chứa mật khẩu DB local) — chỉ commit `.env.example`.
- Mỗi khi schema đổi: người sửa cập nhật `database/schema.sql`, push lên, nhắn nhóm; người khác `git pull` rồi import lại schema vào MySQL local.
- Cân nhắc dùng **Docker Compose** (1 file `docker-compose.yml` chung chứa PHP + MySQL version cố định) để cả 3 người chạy đúng 1 môi trường giống hệt nhau, tránh lỗi "chạy trên máy tôi thì được".

### 3.6 Commit message thống nhất
```
feat(auth): thêm chức năng đăng nhập
feat(product): CRUD sản phẩm cho admin
fix(cart): sửa lỗi tính sai tổng tiền giỏ hàng
refactor(order): tách logic tính subtotal ra Model
```
Format: `<type>(<module>): <mô tả ngắn>` — giúp dễ theo dõi ai làm gì khi xem lịch sử Git, kể cả code do AI hỗ trợ sinh ra.

### 3.7 Checklist trước khi merge vào `develop`
- [ ] Code chạy được, không lỗi cú pháp
- [ ] Đã tự test luồng chính (happy path) + 1 edge case
- [ ] Không có SQL injection (dùng prepared statement)
- [ ] Đặt tên biến/hàm/class đúng convention
- [ ] Không commit `.env`, file rác, `vendor/` (nếu dùng Composer)
- [ ] Có ít nhất 1 người review approve

---

## 4. Timeline gợi ý (nếu cần chia theo tuần)

| Tuần | Minh | Hưng | Thảo |
|---|---|---|---|
| 1 | Khung project, DB schema, Auth | Setup UI Bootstrap, trang chủ, danh sách SP | Thiết kế UI đơn hàng/dashboard |
| 2 | Quản lý danh mục/thương hiệu (Admin) | Tìm kiếm, lọc, chi tiết sản phẩm | Lịch sử đơn hàng (Customer) |
| 3 | Quản lý sản phẩm (Admin) + tồn kho | Giỏ hàng | Quản lý đơn hàng (Admin) |
| 4 | Review, fix bug, quản lý khách hàng | Đặt hàng + thanh toán | Dashboard thống kê |
| 5 | Tích hợp, test toàn hệ thống, viết báo cáo | Tích hợp, test | Tích hợp, test |
