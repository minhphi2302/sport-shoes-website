# Kiến Trúc Hệ Thống (System Design)

Tài liệu này mô tả cấu trúc cốt lõi (Core) của dự án. 

## 1. Vòng đời Request (Request Lifecycle)
1. **Entry Point:** Mọi HTTP Request đều đi qua `public/index.php`. File này đóng vai trò là Front Controller, nạp autoload và khởi tạo môi trường.
2. **Routing:** Request được chuyển tới `app/Core/Router.php`. Router sẽ phân tích URL, đối chiếu với route map tĩnh hoặc động để tìm ra đúng Controller và phương thức (Action).
3. **Controller:** Controller (`app/Controllers/`) nhận request. Nó có nhiệm vụ:
   - Validate Input cơ bản.
   - Kiểm tra phân quyền (nếu chưa qua Middleware).
   - Gọi Model.
4. **Model:** Model (`app/Models/`) thực thi **Business Rule**. Nó gọi lớp `app/Core/Database.php` để tương tác với MySQL.
5. **View:** Controller lấy data từ Model, gọi phương thức `render()` để nạp View (`views/`) hiển thị giao diện HTML (kết hợp Bootstrap 5), hoặc trả về JSON.

## 2. Core Components (app/Core/)
Do không dùng Framework, hệ thống sử dụng các Core do nhóm tự xây dựng:
- **`Database.php`**: Áp dụng Singleton pattern. Quản lý 1 kết nối PDO duy nhất, hỗ trợ transaction và prepared statements chuẩn.
- **`Model.php`**: Lớp cơ sở cung cấp các hàm `findAll()`, `findById()`, `insert()`, `update()`, `delete()`.
- **`Controller.php`**: Lớp cơ sở cung cấp hàm `render($view, $data)`, `json($data)` và `redirect()`.
- **`Env.php`**: Parser đơn giản đọc cấu hình từ file `.env`. (Được sử dụng như phương án dự phòng do Composer trên XAMPP bị lỗi SSL).

## 3. Database Migration
Không sử dụng duy nhất một file `schema.sql` tĩnh. 
- Mọi thay đổi về cấu trúc bảng phải tạo file SQL riêng biệt trong `database/migrations/` (đánh số tự tăng, VD: `001_create_users.sql`).
- Script `migrate.php` đảm nhiệm việc đồng bộ schema.

## 4. Xử lý Concurrency & Transaction
- **Transaction:** Áp dụng bắt buộc (`beginTransaction()`, `commit()`, `rollBack()`) cho mọi thao tác Ghi đa bảng (như Checkout, Cập nhật kho).
- **Concurrency (Tồn kho):** Sử dụng khóa mức Row (Row-level lock) của InnoDB bằng cách dùng `UPDATE có điều kiện (WHERE quantity >= X)`. Không sử dụng kiểu query `SELECT kiểm tra -> UPDATE` để tránh race condition.
