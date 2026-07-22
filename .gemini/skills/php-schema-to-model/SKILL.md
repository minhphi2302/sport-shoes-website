---
name: schema-to-model
description: Đọc file schema.sql và tự động sinh file Model.php với đầy đủ các thuộc tính.
---

# Kỹ năng: Sinh Model từ CSDL (schema-to-model)

Kỹ năng này giúp AI tự động đọc cấu trúc của bảng trong `database/schema.sql` và tạo ra Base Model chứa các thuộc tính (properties) phù hợp.

## Yêu cầu

- Yêu cầu đọc file `database/schema.sql` (bằng `view_file` hoặc `grep_search`).
- Tìm câu lệnh `CREATE TABLE` của bảng tương ứng.
- Viết file `app/Models/[TableNameSingular].php`.
- Bổ sung PHPDoc cho các property dựa trên kiểu dữ liệu của cột MySQL.
