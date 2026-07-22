# Tài Liệu Dự Án (Project Documentation)

Thư mục này đóng vai trò là "Bộ nhớ dài hạn" (Long-term Memory) cho cả đội ngũ Lập trình viên lẫn các AI Agent. Mục tiêu là giúp bất kỳ ai (hoặc AI nào) khi join vào dự án đều có thể hiểu ngay luồng xử lý mà không cần phải quét hàng ngàn dòng code.

## Cấu trúc thư mục

1. **`/architecture/`**: 
   - Lưu trữ kiến trúc hệ thống tổng thể, thiết kế Core MVC, Database Schema, và các nguyên tắc vận hành cốt lõi.
   - **Tác dụng:** Giúp AI/Dev hiểu framework chạy như thế nào (Router, Model, Controller kết nối ra sao).

2. **`/features/`**: 
   - Lưu trữ các tài liệu đặc tả luồng chạy của các **chức năng đã hoàn thiện** (Ví dụ: `auth_flow.md`, `checkout_process.md`).
   - **Tác dụng:** Khi cần nâng cấp chức năng (ví dụ: Thêm mã giảm giá vào chức năng Thanh toán), Dev/AI chỉ cần đọc file `checkout_process.md` để biết logic cũ thay vì phải đọc file Controller, Model dài ngoằng.

3. **`/tasks/`**: 
   - Lưu trữ các Task Spec (file giao việc). Khi task chuyển trạng thái thành `Done`, những thông tin quan trọng của task đó nên được tổng hợp lại thành một file trong mục `/features/`.

4. **`/templates/`**: 
   - Lưu các file mẫu (Task Template, Feature Template) để duy trì sự chuẩn mực.

---
> **⚡ INSTRUCTION FOR AI AGENTS ⚡**
> - Khi bạn nhận yêu cầu bảo trì hoặc sửa đổi tính năng cũ, hãy dùng tool `list_dir` kiểm tra trong `docs/features/` xem có tài liệu của tính năng đó không.
> - Hãy đọc tài liệu trong `docs/features/` trước. Chỉ khi tài liệu chưa đủ chi tiết, bạn mới nên dùng `view_file` để quét vào mã nguồn gốc.
> - Khi hoàn thiện một tính năng lớn mới, hãy chủ động nhắc nhở người dùng: "Có cần tôi tạo file tổng hợp logic vào thư mục `docs/features/` để dễ bảo trì sau này không?".
