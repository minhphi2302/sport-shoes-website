# Tài liệu Chức năng (Feature Documentation)

Thư mục này chứa tài liệu mô tả luồng logic (Logic Flow) của các tính năng đã hoàn thiện trong dự án. 

## Tại sao cần thư mục này?
Thay vì phải mở hàng chục file Controller, Model, View để hiểu cách chức năng "Thanh toán" hoạt động, một Lập trình viên mới (hoặc một AI Agent) chỉ cần mở file `docs/features/checkout_flow.md` để nắm được bức tranh tổng quan.

## Quy tắc
Sau khi một Task lớn (thuộc về một chức năng cốt lõi) được chuyển sang trạng thái `Done`, Dev (hoặc AI) cần tổng hợp logic của chức năng đó và viết thành 1 file markdown ở đây.

**Cấu trúc file Feature gợi ý:**
1. **Mô tả chức năng:** Tính năng này làm gì? (VD: Khách hàng thêm sp vào giỏ, nhập địa chỉ, thanh toán).
2. **Database & Bảng liên quan:** Dùng những bảng nào?
3. **Các file bị ảnh hưởng (Core files):** Controller nào? Model nào? View nào?
4. **Luồng xử lý (Logic Flow):** Sơ đồ các bước từ lúc người dùng click đến lúc lưu Database.
5. **Business Rules đặc thù:** Ví dụ "Chỉ cho mua tối đa 5 sản phẩm/lần" hay "Trừ kho bằng UPDATE có điều kiện".
